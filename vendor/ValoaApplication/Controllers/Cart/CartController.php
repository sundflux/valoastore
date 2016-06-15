<?php

namespace ValoaApplication\Controllers\Cart;

use stdClass;
use Webvaloa\Controller\Redirect;
use Webvaloa\Helpers\PriceFormat;
use Webvaloa\Helpers\BabypandaCart;
use Webvaloa\Helpers\BabypandaDiscountcodes as DiscountHelper;
use Webvaloa\Helpers\Article as ArticleHelper;
use Webvaloa\Controller\Request\Response;
use Webvaloa\Security;

class CartController extends \Webvaloa\Application
{
    private $cart;

    private $defaultDeliveryPrice;
    private $freeDeliveryPrice;
    private $returnNumItems;

    public function __construct()
    {
        $this->cart = new BabypandaCart();
        $this->view->token = Security::getToken();

        $this->defaultDeliveryPrice = $this->cart->getDefaultDeliveryPrice();
        $this->freeDeliveryPrice = $this->cart->getFreeDeliveryPrice();
        $this->returnNumItems = $this->cart->getReturnNumItems();
    }

    public function index()
    {
        // Update cart values
        if (isset($_POST['update_cart'])) {
            Security::verify();

            $this->updateCart();

            Redirect::to(substr(\Webvaloa\Webvaloa::translate('CART_LINK', 'Babypanda'), 1));
        }

        // Remove product from cart
        if (isset($_GET['remove_product'])) {
            Security::verify();

            $this->removeProduct();
        }

        // Verify gift code
        if (isset($_POST['verify_giftcode'])) {
            Security::verify();

            $this->verifyGiftCode();
        }

        // Breadcrumb
        $this->view->custom->breadcrumb[0] = new stdClass();
        $this->view->custom->breadcrumb[0]->title = \Webvaloa\Webvaloa::translate('SHOPPING_CART', 'Babypanda');
        $this->view->custom->breadcrumb[0]->link = $this->view->custom->localePrefix.'/'.\Webvaloa\Webvaloa::translate('SHOPPING_CART_LINK', 'Babypanda');

        if ($this->cart->getNumProducts() == 0) {
            $this->view->cartEmpty = true;

            return;
        }

        $products = $this->cart->getCart();

        if (!isset($products->products)) {
            $this->view->cartEmpty = true;

            return;
        }

        foreach ($products->products as $k => $product) {
            if (!isset($product->id) || !is_numeric($product->id)) {
                continue;
            }

            $this->view->cart[$k] = $product;
            try {
                $articleHelper = new ArticleHelper($product->id);
                $this->view->cart[$k]->article = $articleHelper->article;

                if (isset($this->view->cart[$k]->article->fieldValues->product_image[0]) && !empty($this->view->cart[$k]->article->fieldValues->product_image[0])) {
                    $this->view->cart[$k]->thumbnail = \Webvaloa\Helpers\Imagemagick::crop($this->view->cart[$k]->article->fieldValues->product_image[0], 100, 100);
                }
            } catch (Exception $e) {
            }
        }

        if (PriceFormat::formatCountablePrice($this->cart->getTotalPrice()) >= $this->freeDeliveryPrice) {
            // Free delivery

            $this->cart->setDeliveryPrice(0);
        } elseif (PriceFormat::formatCountablePrice($this->cart->getTotalPrice()) < $this->freeDeliveryPrice && PriceFormat::formatCountablePrice($this->cart->getTotalPrice() > 0)) {
            // Default delivery price

            $this->cart->setDeliveryPrice($this->defaultDeliveryPrice);
        }

        // Discount code
        $this->view->discountCode = $this->cart->getDiscountCode();

        // Validate discount code
        $this->view->discountCodeIsValid = -1;

        if (!empty($this->view->discountCode)) {
            $discountHelper = new DiscountHelper($this->view->discountCode);

            if (!$discountHelper->isValid()) {
                // Not a valid code
                $this->view->discountCodeIsValid = 0;
                $this->cart->setDiscountCode('');
                $this->cart->setDiscountValue(0);
                $this->cart->setDiscount(0);

                $this->cart->updatePrice();
            } else {
                // Default to incorrect code in case something fails
                $this->view->discountCodeIsValid = 0;

                // Euro discount
                if ($discountHelper->isEuro() && $discountHelper->isValid()) {
                    $this->view->discountCodeIsValid = 1;

                    $this->cart->setDiscountType('euro');
                    $this->cart->setDiscount(PriceFormat::formatPrice($discountHelper->getValue()));
                    $this->cart->updatePrice();
                }

                // Percent discount
                if ($discountHelper->isPercent() && $discountHelper->isValid()) {
                    $this->view->discountCodeIsValid = 1;

                    $this->cart->setDiscountType('percent');
                    $this->cart->setDiscountValue($discountHelper->getValue());
                    $this->cart->setDiscount(0); // Make sure euro discount is reset
                    $this->cart->updatePrice();
                }
            }
        }

        $this->view->totalView = PriceFormat::formatPrice($this->cart->getTotalPrice());
        $this->view->deliveryPrice = PriceFormat::formatPrice($this->cart->getDeliveryPrice());
        $this->view->discountView = PriceFormat::formatPrice($this->cart->getDiscount());
        $this->view->priceBeforeDiscountView = PriceFormat::formatPrice($this->cart->getPriceBeforeDiscount());

        // Go to checkout
        if (isset($_POST['to_checkout']) && ($this->view->discountCodeIsValid != 0)) {
            Security::verify();

            $this->updateCart();

            Redirect::to(substr(\Webvaloa\Webvaloa::translate('CART_CHECKOUT', 'Babypanda'), 1));
        }
    }

    public function contents()
    {
        $cart = new stdClass();

        // Return number of _products_ or _items_ ?
        // (items = 1 product with 3 sizes in cart = count of 3. with
        //  product mode, value would be 1)
        if ($this->returnNumItems > 0) {
            $cart->products = $this->cart->getNumItems();
        } else {
            $cart->products = $this->cart->getNumProducts();
        }

        $cart->price = PriceFormat::formatPrice($this->cart->getPrice()).' €';

        if ($cart->products == 1) {
            $cart->text = \Webvaloa\Webvaloa::translate('PRODUCT', 'Babypanda');
        } else {
            $cart->text = \Webvaloa\Webvaloa::translate('PRODUCTS', 'Babypanda');
        }

        Response::JSON($cart);
    }

    public function add()
    {
        $response = new stdClass();
        $response->success = 0;

        if (isset($_POST['product_id']) && isset($_POST['product_amount'])) {
            $id = (int) $_POST['product_id'];
            $amount = (int) $_POST['product_amount'];
            $size = false;

            if (isset($_POST['product_size'])) {
                $size = $_POST['product_size'];
            }

            $url = false;
            if (isset($_POST['ref_url'])) {
                $url = $_POST['ref_url'];
            }

            $this->cart->addProduct(
                $id,      // Product id
                $amount,  // Product amount
                $size,    // Product size
                false,    // $updateAmount
                false,    // $sizeAsHash
                $url      // ref url
            );

            $response->success = 1;
        }

        Response::JSON($response);
    }

    public function emptyCart()
    {
        Security::verify();

        $this->cart->destroyCart();

        Redirect::to(substr(\Webvaloa\Webvaloa::translate('CART_LINK', 'Babypanda'), 1));
    }

    private function updateCart()
    {
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, strlen('amount')) != 'amount') {
                continue;
            }

            $tmp = explode('-', $k);

            if (!isset($tmp[2]) || (!isset($tmp[1]) || !is_numeric($tmp[1]))) {
                continue;
            }

            $productId = $tmp[1];
            $sizeHash = $tmp[2];

            // Value must be always at least 1
            $v = (int) $v;
            if ($v == 0) {
                $v = 1;
            }

            $value = $v;

            $this->cart->setProduct($productId, $value, $sizeHash);
        }

        $this->_verifyGiftCode();
    }

    private function removeProduct()
    {
        if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])
            && isset($_GET['product_hash']) && !empty($_GET['product_hash'])) {
            $this->cart->removeProduct((int) $_GET['product_id'], $_GET['product_hash'], true);
        }

        Redirect::to(substr(\Webvaloa\Webvaloa::translate('CART_LINK', 'Babypanda'), 1));
    }

    private function _verifyGiftCode()
    {
        if (isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
            $code = preg_replace('/[^A-Za-z0-9 ]/', '', $_POST['discount_code']);

            $this->cart->setDiscountCode(mb_strtoupper($code));
        } else {
            // Remove the code

            $this->cart->setDiscountCode('');
            $this->cart->setDiscountValue(0);
            $this->cart->setDiscount(0);
            $this->cart->updatePrice();
        }
    }

    private function verifyGiftCode()
    {
        $this->_verifyGiftCode();

        Redirect::to(substr(\Webvaloa\Webvaloa::translate('CART_LINK', 'Babypanda'), 1));
    }
}
