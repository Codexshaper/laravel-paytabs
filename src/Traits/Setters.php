<?php

namespace Paytabs\Traits;

use Illuminate\Support\Str;
use Paytabs\Repositories\CustomerDetails;

trait Setters
{
    public function setRegion($region)
    {
        $this->baseUrl = self::BASE_URLS[$region]['baseUrl'];
        return $this;
    }

    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
        return $this;
    }

    public function setServerKey($serverKey)
    {
        $this->serverKey = $serverKey;
        return $this;
    }

    public function setTransactionType($tran_type)
    {
        $this->data['tran_type'] = $tran_type;
        return $this;
    }

    public function setTransactionClass($tran_class)
    {
        $this->data['tran_class'] = $tran_class;
        return $this;
    }

    public function setCartId($cart_id)
    {
        $this->data['cart_id'] = $cart_id;
        return $this;
    }

    public function setCartDescription($cart_description)
    {
        $this->data['cart_description'] = $cart_description;
        return $this;
    }

    public function setCartCurrency($cart_currency)
    {
        $this->data['cart_currency'] = $cart_currency;
        return $this;
    }

    public function setCartAmount($cart_amount)
    {
        $this->data['cart_amount'] = $cart_amount;
        return $this;
    }

    public function setReturnUrl($return_url)
    {
        $this->data['return'] = $return_url;
        return $this;
    }

    public function setCallbackUrl($callback_url)
    {
        $this->data['callback'] = $callback_url;
        return $this;
    }

    public function setRedirectUrl($redirect_url)
    {
        $this->data['redirect_url'] = $redirect_url;
        return $this;
    }
    public function setCustomerDetails($name, $email = '', $phone = '', $address = '', $city = '', $state = '', $country = '', $zip = '', $ip = '')
    {
        if (is_array($name)) {
            $this->data['customer_details'] = $name;
        }
        else {
            $this->data['customer_details'] = [
                'name' => $name, 
                'email' => $email, 
                'phone' => $phone, 
                'street1' => $address, 
                'city' => $city, 
                'state' => $state, 
                'country' => $country, 
                'zip' => $zip,
                'ip' => $ip
            ];
        }
        return $this;
    }
 
    public function setShippingDetails($name, $email = '', $phone = '', $address = '', $city = '', $state = '', $country = '', $zip = '', $ip = '')
    {
        if (is_array($name)) {
            $this->data['shipping_details'] = $name;
        }
        else if ($name == 'same_as_billing') {
            $this->data['shipping_details'] = $this->customer_details;
        }
        else {
            $this->data['shipping_details'] = [
                'name' => $name, 
                'email' => $email, 
                'phone' => $phone, 
                'street1' => $address, 
                'city' => $city, 
                'state' => $state, 
                'country' => $country, 
                'zip' => $zip,
                'ip' => $ip
            ];
        }
        
        return $this;
    }

    public function setCardDetails($number, $expiry_month = null, $expiry_year = null, $cvv = null)
    {
        if (is_array($number)) {
            $this->data['card_details'] = $number;
        }
        else {
            $this->data['card_details'] = [
                'pan' => $number, 
                'expiry_month' => $expiry_month, 
                'expiry_year' => $expiry_year, 
                'cvv' => $cvv
            ];
        }

        return $this;
    }

    public function setFramed($framed)
    {
        $this->data['framed'] = $framed;
        return $this;
    }

    public function setHideShipping($hide_shipping)
    {
        $this->data['hide_shipping'] = $hide_shipping;
        return $this;
    }

    public function __set($property, $value) {
        $this->data[$property] = $value;
        return $this;
    }
}