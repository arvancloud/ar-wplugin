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

    public static function changeCachingStatus($domain, $status) {
        return self::sendPatch("domains/{$domain}/caching", json_encode([
            'cache_status'  =>  $status
        ]));
    }

    public static function removeDomain($domain, $uuid) { // There too redirects for api
        return self::sendDelete("domains/{$domain}", json_encode([
            'id'    =>  $uuid
        ]));
    }

    public static function purge($url) {
        return self::sendDelete("domains/{$url}/caching", json_encode([
            "purge" => "individual",
            [$url]
        ]));
    }

    public static function purgeAll($domain) {
        return self::sendDelete("domains/{$domain}/caching", json_encode([
            "purge" => "all"
        ]));
    }

    public static function getDomains() {
        return self::sendGet("domains");
    }

    public static function getCachingSettings($domain) {
        return self::sendGet("domains/{$domain}/caching");
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

    private static function sendPatch($endPoint, $data) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://napi.arvancloud.com/cdn/4.0/{$endPoint}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "Authorization: " . self::getAPIKey(),
            "cache-control: no-cache",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if (!$err)
            return $response;
        return false;
    }

    private static function sendDelete($endPoint, $data) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://napi.arvancloud.com/cdn/4.0/{$endPoint}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "Authorization: " . self::getAPIKey(),
            "cache-control: no-cache",
            "content-type: application/json"
        ),
        ));
        curl_setopt( $curl, CURLOPT_MAXREDIRS, 100 );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err)
            return $response;
        return false;
    }

}
