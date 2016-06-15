<?php

namespace ValoaApplication\Controllers\Cart;

use Libvaloa\Debug;
use Webvaloa\Helpers\PriceFormat;
use Webvaloa\Helpers\BabypandaCart;
use Webvaloa\Helpers\BabypandaDiscountcodes as DiscountHelper;
use Webvaloa\Helpers\BabypandaSmartpost as SmartpostHelper;
use Webvaloa\Helpers\Article as ArticleHelper;
use Webvaloa\Security;
use Webvaloa\Controller\Request\Response;
use stdClass;

class CustomerinfoController extends \Webvaloa\Application
{
    private $cart;
    private $form;

    private $defaultDeliveryPrice;
    private $freeDeliveryPrice;
    private $returnNumItems;

    public function __construct()
    {
        $this->cart = new BabypandaCart();
        $this->view->token = Security::getToken();

        $this->ui->setMainTemplate('checkout');

        $this->defaultDeliveryPrice = $this->cart->getDefaultDeliveryPrice();
        $this->freeDeliveryPrice = $this->cart->getFreeDeliveryPrice();
        $this->returnNumItems = $this->cart->getReturnNumItems();

        // Initialize form data object
       	if (!isset($_SESSION['orderForm'])) {
       		$_SESSION['orderForm'] = new stdClass;
       	}
       	
       	$this->form = & $_SESSION['orderForm'];

       	// Delivery method
       	if (!isset($this->form->deliveryMethod)) {
       		$this->form->deliveryMethod = 'default';
       	}

       	$this->view->form = $this->form;

       	Debug::__print('Order data in session:');
       	Debug::__print($this->view->form);
    }

    public function index()
    {
        $products = $this->cart->getCart();

        if (!isset($products->products)) {
            $this->view->cartEmpty = true;

            return;
        }

        $this->loadcartcontent();
    }

    public function smartpost($i = false)
    {
        $this->ui->setMainTemplate('empty');

        $this->view->zip = $i;

        $this->view->smartpost = new stdClass;
        $this->view->smartpost->success = 0;
        $this->view->smartpost->errorMessage = "";
        $this->view->smartpost->items = "";

        if (!preg_match('/^[0-9]+$/', $i)) {
            $this->view->smartpost->errorMessage = \Webvaloa\Webvaloa::translate('ZIP_NOT_VALID', 'Babypanda');
            unset($this->view->smartpost->items);

            Debug::__print('Valid zip not found - skipping API query');
            return;
        }

        try {
            $smartpost = new SmartpostHelper($i);
            $tmp = $smartpost->getNearestLocations();

            if (!isset($_SESSION['_smartpost_selected'])) {
                $_SESSION['_smartpost_selected'] = array();
            }

            if (!isset($_SESSION['_smartpost_selected'][$i])) {
                $_SESSION['_smartpost_selected'][$i] = 0;
            }

            Debug::__print('Currently selected smartpost:');
            Debug::__print($_SESSION['_smartpost_selected'][$i]);

            $i = 0;

            if (!is_object($tmp) && !is_array($tmp)) {
                $this->view->smartpost->errorMessage = \Webvaloa\Webvaloa::translate('NO_LOCATIONS_FOUND', 'Babypanda');

                return false;
            }
            foreach ($tmp as $k => $v) {
                if ( (isset($_SESSION['_smartpost_selected'][$i])) && $i == 0 && $_SESSION['_smartpost_selected'][$i] == 0) {
                    $v->selected = 1;
                } elseif (isset($_SESSION['_smartpost_selected'][$i]) && $v->FetchLocationSequenceCode == $_SESSION['_smartpost_selected'][$i]) {
                    $v->selected = 1;
                }

                $sorted[$k] = $v;
                $i++;
            }

            if (isset($sorted)) {
                $this->view->smartpost->items = $sorted;
                $this->view->smartpost->success = 1;
            }
        } catch(Exception $e) {
            $this->view->smartpost->errorMessage = $e->getMessage();
        }

        Debug::__print($this->view->smartpost);
    }

    public function saveform()
    {

    }

    public function loadcartcontent() 
    {
    	if ($this->request->isAjax()) {
        	$this->ui->setMainTemplate('empty');
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
    }

    public function loadtotals()
    {
    	if ($this->request->isAjax()) {
        	$this->ui->setMainTemplate('empty');
    	}

    	$this->loadcartcontent();
    }

    public function setDeliveryMethod($p)
    {
    	// Delivery methods
    	$methods = array(
    		'default' => 3.90,		// Default delivery
    		'smartpost' => 5.90,	// Smartpost
    		'package_delivery' => 6.90, 	// Post package,
    		'fast_delivery' => 6.90
    	);

    	foreach ($methods as $method => $price) {
    		Debug::__print('Delivery method:');
    		Debug::__print($method);

    		if ($p != $method) {
    			continue;
    		}

    		if ($p == $method) {
    			$this->cart->setDeliveryPrice($methods[$method]);
    			$this->form->deliveryMethod = $method;
    		}
    	}

    	Response::JSON(new stdClass());
    }

}
