<?php

namespace Paytabs;

/**
 * API class which contacts PayTabs server's API
 */
class Client
{
    private $baseUrl = 'https://secure-global.paytabs.com/';
    private $profileId = null;
    private $serverKey = null;
    private static $instance = null;
    
    public static function getInstance($profileId, $serverKey, $baseUrl = null)
    {
        if (self::$instance == null) {
            self::$instance = new self($profileId, $serverKey, $region);
        }

        return self::$instance;
    }

    public function __construct($profileId = null, $serverKey = null, $baseUrl = null)
    {
        if ($baseUrl !== null) {
            $this->baseUrl = $baseUrl;
        }
        if ($profileId !== null) {
            $this->profileId = $profileId;
        }
        if ($serverKey !== null) {
            $this->serverKey = $serverKey;
        }
        
    }
    
    public function sendRequest($requestUrl, $data) {
        if (!is_array($data)) {
            $data = (array) $data;
        }
        return json_decode(
            $this->execute($requestUrl, $data)
        );
    }

    private function execute($endpoint, $data)
    {
        try {
            $headers = [
                'Content-Type: application/json',
                "Authorization: {$this->serverKey}"
            ];

            $data['profile_id'] = (int) $this->profileId;
            $fields = json_encode($data);

            $url = $this->baseUrl . $endpoint;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            // @curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $result = curl_exec($ch);

            $error_num = curl_errno($ch);

            if ($error_num) {
                $error_msg = curl_error($ch);
                PaytabsHelper::log("Paytabs Admin: Response [($error_num) $error_msg], [$result]", 3);

                $result = json_encode([
                    'message' => 'Sorry, unable to process your transaction, Contact the site Administrator'
                ]);
            }

            curl_close($ch);

            
        } catch (\Exception $ex) {
            $result = json_encode([
                'message' => $ex->getMessage()
            ]);
        }

        return $result;
        
    }
}