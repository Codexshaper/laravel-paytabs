<?php

namespace Paytabs\Traits;

use Illuminate\Support\Str;

trait Getters
{
    public function getRegion()
    {
        return $this->baseUrl;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }

    public function getServerKey()
    {
        return $this->serverKey;
    }

    public function getTransactionType()
    {
        return $this->data['tran_type'] ?? null;
    }

    public function getTransactionClass()
    {
        return $this->data['tran_class'] ?? null;
    }

    public function getCartId()
    {
        return $this->data['cart_id'] ?? null;
    }

    public function getCartDescription()
    {
        return $this->data['cart_description'] ?? null;
    }

    public function getCartCurrency()
    {
        return $this->data['cart_currency'] ?? null;
    }

    public function getCartAmount()
    {
        return $this->data['cart_amount'] ?? null;
    }

    public function getReturnUrl()
    {
        return $this->data['return'] ?? null;
    }

    public function getCallbackUrl()
    {
        return $this->data['callback'] ?? null;
    }

    public function getRedirectUrl()
    {
        return $this->data['redirect_url'] ?? null;
    }

    public function getCustomerDetails()
    {
        return $this->data['customer_details'] ?? null;
    }

    public function getShippingDetails()
    {
        return $this->data['shipping_details'] ?? null;
    }

    public function getCardDetails()
    {
        return $this->data['card_details'] ?? null;
    }

    public function getFramed()
    {
        return $this->data['framed'] ?? null;
    }

    public function getHideShipping()
    {
        return $this->data['hide_shipping'] ?? null;
    }

    public function __get($property) {
        return $this->data[$property] ?? null;
    }
}