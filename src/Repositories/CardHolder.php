<?php

namespace Paytabs\Repositories;

class CardHolder
{
    protected $pan;
    protected $expiry_month;
    protected $expiry_year;
    protected $cvv;

    public function build()
    {
        return [
            'pan' => $this->pan, 
            'expiry_month' => $this->expiry_month, 
            'expiry_year' => $this->expiry_year, 
            'cvv' => $this->cvv
        ];
    }

    public function setNumber($number)
    {
        $this->pan = $number;
        return $this;
    }

    public function setMonth($number)
    {
        $this->expiry_month = $number;
        return $this;
    }

    public function setYear($number)
    {
        $this->expiry_year = $number;
        return $this;
    }

    public function setCvv($number)
    {
        $this->cvv = $number;
        return $this;
    }

    public function getNumber()
    {
        return $this->pan ?? null;
    }

    public function getMonth()
    {
        return $this->expiry_month ?? null;
    }

    public function getYear()
    {
        return $this->expiry_year ?? null;
    }

    public function getCvv()
    {
       return $this->cvv ?? null;
    }

    public function __get($property)
    {
        return $this->{$property};
    }

    public function __set($property, $value)
    {
        $this->{$property} = $value;
    }

    public function __call($method, $args = []) {
        $prefix = 'get_';

        if (!empty($args)) {
            $prefix = 'set_';
        }

        if (!method_exists($this, $method)) {
            $method = Str::camel("{$prefix}{$method}");
        }

        return $this->{$method}(...$args);
    }

}