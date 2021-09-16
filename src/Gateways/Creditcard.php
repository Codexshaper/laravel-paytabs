<?php

namespace Paytabs\Gateways;

class Creditcard extends BaseGateway
{
    protected $code = 'creditcard';
    protected $title = 'PayTabs - CreditCard';
    protected $description = 'PayTabs - CreditCard payment method';

    protected $icon = "creditcard.svg";
}