<?php

namespace Webvaloa\Helpers;

use stdClass;

class Price
{
    private $price;
    private $isIntegerVat;
    private $amount;

    public function __construct()
    {
        $this->price = new stdClass();
        $this->price->isDiscountedPrice = 0;
        $this->price->vatCountable = $this->price->vat = PriceFormat::formatCountablePrice(0);
        $this->price->vatAmount = PriceFormat::formatCountablePrice(0);
        $this->price->priceNoVAT = PriceFormat::formatCountablePrice(0);
        $this->price->price = PriceFormat::formatCountablePrice(0);
        $this->price->originalPrice = $this->price->price;
        $this->price->discountPercent = 0;
        $this->setIntegerVat();

        $this->amount = 1;
    }

    public function setAmount($v)
    {
        $this->amount = (int) $v;
    }

    public function isIntegerVat()
    {
        return (bool) $this->isIntegerVat;
    }

    public function setIntegerVat($b = true)
    {
        $this->isIntegerVat = (bool) $b;
    }

    public function setIsDiscountedPrice($b = true)
    {
        $this->price->isDiscountedPrice = (bool) $b;
        $this->price->isDiscountedPrice = (int) $this->price->isDiscountedPrice;
        $this->price->originalPrice = $this->price->price;
    }

    public function setVat($v)
    {
        // Little hack for fieldValues fields.
        // This is always assumed to be in format 'VAT_<amount>'
        $pos = strpos($v, 'VAT_');
        if ($pos !== false) {
            $v = str_replace('VAT_', '', $v);
        }

        $this->price->vat = PriceFormat::formatCountablePrice($v);

        if ($this->isIntegerVat()) {
            $this->price->vat = (int) $this->price->vat;
        }

        $this->price->vatCountable = PriceFormat::formatCountablePrice((($this->price->vat + 100) / 100));
    }

    public function setPriceNoVAT($v)
    {
        $this->price->priceNoVAT = PriceFormat::formatCountablePrice($v);
        $this->price->price = PriceFormat::formatCountablePrice(($this->price->priceNoVAT * $this->price->vatCountable));
        $this->price->vatAmount = PriceFormat::formatCountablePrice($this->price->price - $this->price->priceNoVAT);
    }

    public function setPrice($v)
    {
        $this->price->price = PriceFormat::formatCountablePrice($v);
        $this->price->priceNoVAT = PriceFormat::formatCountablePrice(($this->price->price / $this->price->vatCountable));
        $this->price->vatAmount = PriceFormat::formatCountablePrice($this->price->price - $this->price->priceNoVAT);
    }

    public function getObject()
    {
        $this->price->amount = $this->amount;

        // Prices with amount calculated in
        $this->price->withAmount = new stdClass();
        $this->price->withAmount->priceView = PriceFormat::formatPrice(($this->amount * $this->price->price));
        $this->price->withAmount->priceNoVATView = PriceFormat::formatPrice(($this->amount * $this->price->priceNoVAT));
        $this->price->withAmount->vatAmountView = PriceFormat::formatPrice(($this->amount * $this->price->vatAmount));
        $this->price->withAmount->originalPriceView = PriceFormat::formatPrice(($this->amount * $this->price->originalPrice));

        // Amounts for a single item
        $this->price->priceView = PriceFormat::formatPrice($this->price->price);
        $this->price->priceNoVATView = PriceFormat::formatPrice($this->price->priceNoVAT);
        $this->price->vatAmountView = PriceFormat::formatPrice($this->price->vatAmount);
        $this->price->originalPriceView = PriceFormat::formatPrice($this->price->originalPrice);

        // Discount percent
        if ($this->price->isDiscountedPrice == 1
            && $this->price->originalPrice > 0
            && $this->price->price) {
            $this->price->discountPercent = 100 - (int) PriceFormat::formatCountablePrice(($this->price->price * 100) / $this->price->originalPrice);
        }

        return $this->price;
    }
}
