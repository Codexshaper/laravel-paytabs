<?php

namespace Paytabs\Traits;

trait DetailsSetters
{
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    public function setZip($zip)
    {
        $this->zip = $zip;
        return $this;
    }


    public function setIP($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function __set($property, $value) {
        return $this->{$property} = $value;
    }
}