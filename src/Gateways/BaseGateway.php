<?php

namespace Paytabs\Gateways;

use Paytabs\Contracts\Paytabs;

class BaseGateway
{
    protected $code = '';
    protected $title = '';
    protected $description = '';
    protected $icon = null;
    //
    protected $paytabs;



    public function __construct(Paytabs $paytabs)
    {
        $this->paytabs = $paytabs;
    }

    /**
     * We're processing the payments here
     **/
    public function payment()
    {
       
    }


    public function scheduled()
    {}

    public function refund()
    {}
}