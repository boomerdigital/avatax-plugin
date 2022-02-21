<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Api{ 

    public static function curl($url, $method = "GET" ,$data = array()){
        $curl = curl_init();
        $header = self::getHeader();
        curl_setopt($curl, CURLOPT_URL, AVATAXENDPOINT.$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        if($method == "POST"){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);    
        }
        
        $output = curl_exec($curl); 
            
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);
        
        if (isset($error_msg)) {
            ErrorLog::errorLogs($error_msg);
        }

        return $output;
        
    }

    public static function getHeader(){
        return array('Content-type: application/json',
                              'Authorization: Basic MjAwMTU4MjU1MDowM0NGRjMwNTVDNUNGMzA2');

    }
}
?>