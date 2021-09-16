<?php

namespace Paytabs\Traits;

trait DetailsGetters
{
    public function getName()
    {
        return $this->name ?? '';
    }

    public function getEmail()
    {
        return $this->email ?? '';
    }

    public function getPhone()
    {
        return $this->phone ?? '';
    }

    public function getAddress()
    {
        return $this->address ?? '';
    }

    public function getCity()
    {
        return $this->city ?? '';
    }

    public function getState()
    {
        return $this->state ?? '';
    }

    public function getCountry()
    {
        return $this->country ?? '';
    }

    public function getZip()
    {
        return $this->zip ?? '';
    }


    public function getIP()
    {
        return $this->ip ?? '';
    }

    public function __get($property) {
        return $this->{$property};
    }
}