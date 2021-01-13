<?php

class ArRequests {
    
    private static $greeting = 'Hello';
    private static $initialized = false;

    public function __construct() {

    }

    private static function initialize() {
        if (self::$initialized)
            return;

        self::$initialized = true;
    }

    public function changeCachingStatus() {

    }

    public function purge() {
        
    }

    public function purgeAll() {

    }

    public static function getDomains() {
        return self::sendGet("domains");
    }

    public function getCachingSettings() {

    }

    public function updateCachingSettings() {

    }

    public static function getAPIKey () : string { return get_option('ar_api_key'); }

    public static function setApiKey($key) : bool { return update_option('ar_api_key', $key); }

    public function checkConnection() {

    }

    private static function sendGet($endPoint) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://napi.arvancloud.com/cdn/4.0/{$endPoint}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: " . self::getAPIKey(),
            "cache-control: no-cache",
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if (!$err)
            return $response;
        return false;
    }
}
