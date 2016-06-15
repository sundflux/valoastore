<?php

namespace Webvaloa\Helpers;

use stdClass;
use UnexpectedValueException;
use Webvaloa\Article;
use Webvaloa\Helpers\Article as ArticleHelper;
use Webvaloa\Helpers\Price as PriceHelper;

class Cart
{
    public $cart;
    private $locale;
    private $discountTypes;

    const DEFAULT_DELIVERY_PRICE = 3.90;
    const FREE_DELIVERY_PRICE = 100.00;
    const RETURN_NUM_ITEMS = 1;

    public function __construct()
    {
        $this->locale = \Webvaloa\Webvaloa::getLocale();

        $this->discountTypes = array(
            'euro',
            'percent',
        );

        $this->initCart();
    }

    public function getDefaultDeliveryPrice()
    {
        return self::DEFAULT_DELIVERY_PRICE;
    }

    public function getFreeDeliveryPrice()
    {
        return self::FREE_DELIVERY_PRICE;
    }

    public function getReturnNumItems()
    {
        return self::RETURN_NUM_ITEMS;
    }

    public function destroyCart()
    {
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }

    private function initCart()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        if (!isset($_SESSION['cart'][$this->locale])) {
            // Initialize new cart for this locale

            $_SESSION['cart'][$this->locale] = new stdClass();
            $this->cart = &$_SESSION['cart'][$this->locale];

            // Number of products in cart
            $this->cart->numproducts = 0;

            // Cart price
            $this->cart->price = PriceFormat::formatCountablePrice(0);
            $this->cart->priceView = PriceFormat::formatPrice(0);

            $this->cart->priceBeforeDiscount = PriceFormat::formatCountablePrice(0);
            $this->cart->priceBeforeDiscountView = PriceFormat::formatPrice(0);

            // Cart TOTAL price. That's after adding delivery
            // price and reducing discounts.
            $this->cart->totalPrice = PriceFormat::formatCountablePrice(0);
            $this->cart->totalPriceView = PriceFormat::formatPrice(0);

            // DeliveryPrice
            $this->cart->deliveryPrice = PriceFormat::formatCountablePrice(0);
            $this->cart->deliveryPriceView = PriceFormat::formatPrice(0);

            // Discount
            $this->cart->discount = PriceFormat::formatCountablePrice(0);
            $this->cart->discountView = PriceFormat::formatPrice(0);

            // Discount code
            $this->cart->discountValue = '';
            $this->cart->discountCode = '';
            $this->cart->discountType = 'euro';

            $this->cart->products = array();
        } else {
            // Load existing cart

            $this->cart = &$_SESSION['cart'][$this->locale];
        }
    }

    public function setDeliveryPrice($v)
    {
        $this->cart->deliveryPrice = PriceFormat::formatCountablePrice($v);
        $this->cart->deliveryPriceView = PriceFormat::formatPrice($this->cart->deliveryPrice);

        $this->updatePrice();
    }

    public function setDiscountCode($code = '')
    {
        $this->cart->discountCode = $code;
    }

    public function setDiscountValue($value = '')
    {
        $this->cart->discountValue = $value;
    }

    public function setDiscountType($type = 'euro')
    {
        if (!in_array($type, $this->discountTypes)) {
            throw new UnexpectedValueException('type does not exist');
        }

        $this->cart->discountType = $type;
    }

    public function setDiscount($v)
    {
        $this->cart->discount = PriceFormat::formatCountablePrice($v);
        $this->cart->discountView = PriceFormat::formatPrice($this->cart->discount);
    }

    public function getDeliveryPrice()
    {
        return $this->cart->deliveryPriceView;
    }

    public function getDiscount()
    {
        return $this->cart->discountView;
    }

    public function getDiscountCode()
    {
        return $this->cart->discountCode;
    }

    public function getPrice()
    {
        if (!isset($this->cart->priceView)) {
            return PriceFormat::formatPrice(PriceFormat::formatCountablePrice(0));
        }

        return $this->cart->priceView;
    }

    public function getPriceBeforeDiscount()
    {
        if (!isset($this->cart->priceBeforeDiscountView)) {
            return PriceFormat::formatPrice(PriceFormat::formatCountablePrice(0));
        }

        return $this->cart->priceBeforeDiscount;
    }

    public function getTotalPrice()
    {
        if (!isset($this->cart->totalPriceView)) {
            return PriceFormat::formatPrice(PriceFormat::formatCountablePrice(0));
        }

        return $this->cart->totalPriceView;
    }

    // Number of unqiue products in cart
    public function getNumProducts()
    {
        if (!isset($this->cart->products)) {
            return 0;
        }

        $amount = 0;
        foreach ($this->cart->products as $id => $product) {
            foreach ($product->size as $size) {
                $amount++;
            }
        }

        return $amount;
    }

    // Number of items in cart
    public function getNumItems()
    {
        if (!isset($this->cart->products)) {
            return 0;
        }

        $amount = 0;
        foreach ($this->cart->products as $id => $product) {
            foreach ($product->size as $size) {
                $amount += $size->amount;
            }
        }

        return $amount;
    }

    // Update product amount in cart (not additive)
    public function setProduct($id, $amount = 1, $size = false, $updateAmount = false)
    {
        $this->addProduct($id, $amount, $size, true, true);
    }

    // Add product to cart
    public function addProduct($id, $amount = 1, $size = false, $updateAmount = false, $sizeAsHash = false, $url = false)
    {
        // No size, use -1 as dummy hash key
        if (!$size) {
            $size = -1;
        }

        // Alternatively, size can be supplied as hash
        if (!$sizeAsHash) {
            $hash = md5($size);
        } else {
            $hash = $size;
        }

        // Make sure amount is integer
        $amount = (int) $amount;

        // Initialize product
        if (!isset($this->cart->products[$id])) {
            $this->cart->products[$id] = new stdClass();
            $this->cart->products[$id]->id = (int) $id;
        }

        // Initialize size property with amount
        if (!isset($this->cart->products[$id]->size[$hash])) {
            $this->cart->products[$id]->size[$hash] = new stdClass();
            $this->cart->products[$id]->size[$hash]->amount = 0;
            $this->cart->products[$id]->size[$hash]->size = $size;
            $this->cart->products[$id]->size[$hash]->hash = $hash;
        }

        // Save reference url if given. Since products can be in multiple
        // categories, we cannot really "know" the origin of the product otherwise.
        if ($url) {
            $this->cart->products[$id]->url = $url;
        }

        // Add amount to cart;
        $this->cart->products[$id]->size[$hash]->amount += $amount;

        // Check if this product/size has max stock value
        $article = $this->_getArticle($id);
        if (isset($article->fieldValues->product_stock_title) && isset($article->fieldValues->product_stock_single)) {
            foreach ($article->fieldValues->product_stock_title as $k => $v) {
                if ($v == $size) {
                    $sizeKey = $k;
                    break;
                }
            }

            // Save maxvalue to cart
            if (isset($sizeKey) && isset($article->fieldValues->product_stock_single[$sizeKey])) {
                $this->cart->products[$id]->size[$hash]->max_amount = $article->fieldValues->product_stock_single[$sizeKey];
            }
        }

        if ($updateAmount) {
            // Don't add more than maximum amount, if possible
            if (isset($this->cart->products[$id]->size[$hash]->max_amount) && $amount > $this->cart->products[$id]->size[$hash]->max_amount) {
                $amount = $this->cart->products[$id]->size[$hash]->max_amount;
            }

            // REPLACE the amount
            $this->cart->products[$id]->size[$hash]->amount = $amount;
        }

        $this->updatePrice();
    }

    public function removeProduct($id, $size = false, $sizeAsHash = false)
    {
        // No size, use -1 as dummy hash key
        if (!$size) {
            $size = -1;
        }

        // Alternatively, size can be supplied as hash
        if (!$sizeAsHash) {
            $hash = md5($size);
        } else {
            $hash = $size;
        }

        if (isset($this->cart->products[$id])) {
            if (isset($this->cart->products[$id]->size[$hash])) {
                unset($this->cart->products[$id]->size[$hash]);
            }

            // No sizes left, remove product completely
            if (empty($this->cart->products[$id]->size)) {
                unset($this->cart->products[$id]);
            }
        }

        $this->updatePrice();
    }

    private function _getArticle($id)
    {
        // Try loading associated article
        $association = new ArticleAssociation($id);
        $association->setLocale(\Webvaloa\Webvaloa::getLocale());
        if ($associatedID = $association->getAssociatedId()) {
            $id = $associatedID;
        }

        $articleHelper = new ArticleHelper($id);
        $article = $articleHelper->article;

        return $article;
    }

    public function updatePrice()
    {
        if ($this->getNumproducts() == 0 && isset($_SESSION['cart'][$this->locale])) {
            unset($_SESSION['cart'][$this->locale]);

            return;
        }

        // Reset cart price
        $this->cart->price = PriceFormat::formatCountablePrice(0);

        // Recalculate prices
        foreach ($this->cart->products as $id => $product) {
            if (!is_numeric($id)) {
                continue;
            }

            $article = $this->_getArticle($id);

            $amount = 0;
            foreach ($product->size as $size) {
                $amount += $size->amount;
            }

            // Prices
            $priceHelper = new PriceHelper();
            $priceHelper->setAmount($amount);

            if (isset($article->fieldValues->vat[0]) && !empty($article->fieldValues->vat[0])) {
                $priceHelper->setVat($article->fieldValues->vat[0]);
            }

            // Price without vat
            if (isset($article->fieldValues->price_novat[0]) && $article->fieldValues->price_novat[0] > 0) {
                $priceHelper->setPriceNoVAT($article->fieldValues->price_novat[0]);
            }

            // Price with vat
            if (isset($article->fieldValues->price_vat[0]) && $article->fieldValues->price_vat[0] > 0) {
                $priceHelper->setPrice($article->fieldValues->price_vat[0]);
            }

            // Discount price. This is always assumed to be with VAT
            if (isset($article->fieldValues->discount_price[0])
                && $article->fieldValues->discount_price[0] > 0
                && $article->fieldValues->discount_price[0] != '0,00') {
                $priceHelper->setIsDiscountedPrice();
                $priceHelper->setPrice($article->fieldValues->discount_price[0]);
            }

            // Counted price object
            $price = $priceHelper->getObject();

            $this->cart->products[$id]->price = $price;
            $this->cart->price += PriceFormat::formatCountablePrice($price->withAmount->priceView);
        }

        $this->cart->priceView = PriceFormat::formatCountablePrice($this->cart->price);

        $this->cart->priceBeforeDiscount = $this->cart->price;
        $this->cart->priceBeforeDiscountView = PriceFormat::formatPrice($this->cart->totalPrice);

        // Add delivery price
        $this->cart->totalPrice = PriceFormat::formatCountablePrice($this->cart->price) + PriceFormat::formatCountablePrice($this->cart->deliveryPrice);

        // Handle discount

        // Percent discount
        if (!empty($this->cart->discountCode) && $this->cart->discountType == 'percent' && $this->cart->discountValue > 0) {
            $this->setDiscount(PriceFormat::formatCountablePrice((PriceFormat::formatCountablePrice($this->cart->discountValue) / 100) * $this->cart->totalPrice));
        }

        // Make sure discount is not more than the total price
        if (PriceFormat::formatCountablePrice($this->cart->discount) > PriceFormat::formatCountablePrice($this->cart->totalPrice)) {
            $this->cart->discount = $this->cart->totalPrice;
        }

        // Reduce discounts
        $this->cart->totalPrice = $this->cart->totalPrice - PriceFormat::formatCountablePrice($this->cart->discount);

        // Format for view
        $this->cart->totalPriceView = PriceFormat::formatPrice($this->cart->totalPrice);
    }

    public function getCart()
    {
        $this->updatePrice();

        return $this->cart;
    }
}
