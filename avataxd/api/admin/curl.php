<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Api{ 

    public static function curl($url, $method = "GET" ,$data = array()){
        $curl = curl_init();
        
        if(isset($data['apiKey'])){
           
            $header= array('Content-type: application/json',
            'Authorization: Basic '.$data['apiKey']);
           
        
        }else{
            $header= array('Content-type: application/json',
            'Authorization: Basic '.base64_encode(ACCOUNTNUMBER.':'.LICENSEKEY));
           
        }
        if(isset($data['env'])){
            if($data['env']=='sandbox'){
                curl_setopt($curl, CURLOPT_URL, 'https://sandbox-rest.avatax.com/'.$url);
                
            }else{
                curl_setopt($curl, CURLOPT_URL, 'https://rest.avatax.com/'.$url);
            }
        }else{
            curl_setopt($curl, CURLOPT_URL, AVATAXENDPOINT.$url);
        }
        

        //$header = self::getHeader();
        
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
                              'Authorization: Basic MjAwMDAwMzEwODozMTgyQTc2M0I0NTVFNDA2');

    }
}
?>