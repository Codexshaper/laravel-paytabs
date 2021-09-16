<?php

namespace Paytabs\Repositories;

use Paytabs\Traits\DetailsGetters;
use Paytabs\Traits\DetailsSetters;

class BaseDetails
{
    use DetailsGetters, DetailsSetters;
    
    protected $name = '';
    protected $email = '';
    protected $phone = '';
    protected $address = '';
    protected $city = '';
    protected $state = ''; 
    protected $country = '';
    protected $zip = ''; 
    protected $ip = '';
    protected $data = [];

    protected static $instance = null;

    public static function getInstance()
    {
        if (static::$instance == null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function build()
    {
        return [
            'name' => $this->name, 
            'email' => $this->email, 
            'phone' => $this->phone, 
            'street1' => $this->address, 
            'city' => $this->city, 
            'state' => $this->state, 
            'country' => $this->country, 
            'zip' => $this->zip,
            'ip' => $this->ip
        ];
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