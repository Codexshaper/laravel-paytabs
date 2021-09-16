<?php

namespace Paytabs\Contracts;

interface Paytabs
{
    public function createPayPage();

    public function verifyPayment($tran_id);

    public function requestFollowup($data);

    public function tokenQuery($token);

    public function tokenDelete($token);
}