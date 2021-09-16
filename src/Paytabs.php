<?php

namespace Paytabs;

use Paytabs\Client;
use Illuminate\Support\Str;
use Paytabs\Traits\Getters;
use Paytabs\Traits\Setters;
use Paytabs\Contracts\Paytabs as PaytabsContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

/**
 * API class which contacts PayTabs server's API
 */
class Paytabs implements PaytabsContract
{
    use Getters, Setters;
    
    const PAYMENT_TYPES = [
        ['name' => 'all', 'title' => 'PayTabs - All', 'currencies' => null],
        ['name' => 'stcpay', 'title' => 'PayTabs - StcPay', 'currencies' => ['SAR']],
        ['name' => 'stcpayqr', 'title' => 'PayTabs - StcPay(QR)', 'currencies' => ['SAR']],
        ['name' => 'applepay', 'title' => 'PayTabs - ApplePay', 'currencies' => ['AED', 'SAR']],
        ['name' => 'omannet', 'title' => 'PayTabs - OmanNet', 'currencies' => ['OMR']],
        ['name' => 'mada', 'title' => 'PayTabs - Mada', 'currencies' => ['SAR']],
        ['name' => 'creditcard', 'title' => 'PayTabs - CreditCard', 'currencies' => null],
        ['name' => 'sadad', 'title' => 'PayTabs - Sadad', 'currencies' => ['SAR']],
        ['name' => 'atfawry', 'title' => 'PayTabs - @Fawry', 'currencies' => ['EGP']],
        ['name' => 'knet', 'title' => 'PayTabs - KnPay', 'currencies' => ['KWD']],
        ['name' => 'amex', 'title' => 'PayTabs - Amex', 'currencies' => ['AED', 'SAR']],
        ['name' => 'valu', 'title' => 'PayTabs - valU', 'currencies' => ['EGP']],
    ];
    const BASE_URLS = [
        'ARE' => [
            'title' => 'United Arab Emirates',
            'baseUrl' => 'https://secure.paytabs.com/'
        ],
        'SAU' => [
            'title' => 'Saudi Arabia',
            'baseUrl' => 'https://secure.paytabs.sa/'
        ],
        'OMN' => [
            'title' => 'Oman',
            'baseUrl' => 'https://secure-oman.paytabs.com/'
        ],
        'JOR' => [
            'title' => 'Jordan',
            'baseUrl' => 'https://secure-jordan.paytabs.com/'
        ],
        'EGY' => [
            'title' => 'Egypt',
            'baseUrl' => 'https://secure-egypt.paytabs.com/'
        ],
        'GLOBAL' => [
            'title' => 'Global',
            'baseUrl' => 'https://secure-global.paytabs.com/'
        ],
    ];

    const REQUEST_ENDPOINT = 'payment/request';
    const QUERY_ENDPOINT   = 'payment/query';
    const TOKEN_QUERY_ENDPOINT  = 'payment/token';
    const TOKEN_DELETE_ENDPOINT = 'payment/token/delete';

    private $client = null;
    private $data = [];
    private $customerDetails = [];
    private $shippingDetails = [];

    public function __construct($profileId, $serverKey, $region = 'GLOBAL')
    {  $baseUrl = self::BASE_URLS[$region]['baseUrl'];
        $this->client = new Client($profileId, $serverKey, $baseUrl);
    }

    public static function getBaseUrls()
    {
        $baseUrls = [];
        foreach (self::BASE_URLS as $key => $value) {
            $baseUrls[$key] = $value['title'];
        }
        return $baseUrls;
    }


    /** start: API calls */

    public function createPayPage()
    {

        if (empty($this->data)) {
            return [
                'success' => false,
                'message' => 'You must fill required data first.'
            ];
        }

        $isTokenize = array_key_exists('token', $this->data);
        
        try {
            $response = $this->client->sendRequest(self::REQUEST_ENDPOINT, $this->data);
        } catch (\ERxception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
        
        $paypage = $isTokenize ? $this->enhanceTokenization($response) : $this->enhance($response);

        if ($paypage->success) {
            $redirectUrl = $paypage->redirect_url;
            return isset($this->data['framed']) &&  $this->data['framed'] == true ? $redirectUrl : Redirect::to($redirectUrl);
        }
        Log::channel('paytabs')->info(json_encode($paypage));
    }

    public function secure3DAuth($data)
    {
        return $this->client->sendRequest(self::QUERY_ENDPOINT, $data);
    }

    public function verifyPayment($tran_id)
    {
        $result = $this->client->sendRequest(self::QUERY_ENDPOINT, ['tran_ref' => $tran_id]);
        $verify = json_decode($this->enhanceVerify($result));

        return $verify;
    }

    public function requestFollowup($data)
    {
        $result = $this->client->sendRequest(self::REQUEST_ENDPOINT, $data);
        $refund = $this->enhanceRefund($result);

        return $refund;
    }

    public function tokenQuery($token)
    {
        return $this->client->sendRequest(self::TOKEN_QUERY_ENDPOINT, ['token' => $token]);
    }

    public function tokenDelete($token)
    {
        return  $this->client->sendRequest(self::TOKEN_DELETE_ENDPOINT, ['token' => $token]);
    }

    //

    public function isValidRedirect($data)
    {
        
        if (is_object($data)) {
            $data = (array) $data;
        }

        $serverKey = $this->serverKey;

        // Request body include a signature post Form URL encoded field
        // 'signature' (hexadecimal encoding for hmac of sorted post form fields)
        $requestSignature = $data["signature"];
        unset($data["signature"]);
        $fields = array_filter($data);

        // Sort form fields
        ksort($fields);

        // Generate URL-encoded query string of Post fields except signature field.
        $query = http_build_query($fields);

        return $this->verifySignature($query, $requestSignature, $serverKey);
    }


    public function isValidIpn($data, $signature, $serverkey = false)
    {
        $serverKey = $serverKey ?? $this->serverKey;

        return $this->verifySignature($data, $signature, $serverKey);
    }


    private function verifySignature($data, $requestSignature, $serverKey)
    {
        $signature = hash_hmac('sha256', $data, $serverKey);

        if (hash_equals($signature, $requestSignature) === TRUE) {
            // VALID Redirect
            return true;
        } else {
            // INVALID Redirect
            return false;
        }
    }

    /** end: API calls */


    /** start: Local calls */

    /**
     *
     */
    private function enhance($paypage)
    {
        if (!$paypage || !is_object($paypage)) {
            return (object) [
                'success' => false,
                'message' => 'Create paytabs payment failed.'
            ];
        }

        $paypage->success = isset($paypage->tran_ref, $paypage->redirect_url) && !empty($paypage->redirect_url);
        $paypage->payment_url = $paypage->redirect_url ?? null;

        return $paypage;
    }

    private function enhanceVerify($verify)
    {
        if (!$verify || !is_object($verify)) {
            return (object) [
                'success' => false,
                'message' => 'Verifying paytabs payment failed.'
            ];
        }

        $verify->success = true;

        if (isset($verify->code, $verify->message) || !isset($verify->payment_result)) {
            $verify->success = false;
        } 

        if ($verify->success) {
            $verify->success = $verify->payment_result->response_status == "A";
            $verify->message = $verify->payment_result->response_message;
        }

        $verify->reference_no = $verify->cart_id;
        $verify->transaction_id = $verify->tran_ref;

        return $verify;
    }

    private function enhanceRefund($refund)
    {
        if (!$refund || !is_object($refund)) {
            return (object) [
                'success' => false,
                'message' => 'Verifying paytabs Refund failed.'
            ];
        }

        $refund->success = false;

        if (isset($refund->payment_result)) {
            $refund->success = $refund->payment_result->response_status == "A";
            $refund->message = $refund->payment_result->response_message;
        }
        $refund->pending_success = false;

        return $refund;
    }

    private function enhanceTokenization($paypage)
    {

        if (!$paypage || !is_object($paypage)) {
            return (object) [
                'success' => false,
                'message' => 'Create paytabs tokenization payment failed.'
            ];
        }

        $isRedirect = isset($paypage->tran_ref, $paypage->redirect_url) && !empty($paypage->redirect_url);
        $isCompleted = isset($paypage->payment_result);

        if ($isRedirect) {
            $paypage->success = true;
            $paypage->payment_url = $paypage->redirect_url;
        } else if ($isCompleted) {
            $paypage = $this->enhanceVerify($paypage);
        } else {
            $paypage = $this->enhance($paypage);
        }

        $paypage->is_redirect = $isRedirect;
        $paypage->is_completed = $isCompleted;

        return $paypage;
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