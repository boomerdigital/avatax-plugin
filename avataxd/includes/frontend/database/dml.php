<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class DmlFront{
    public function __construct(){
        
    }
    
    public static function insertAtAddress($response){
        global $wpdb;
      
        try{
            $wpdb->insert('at_address',array( 
            'address' => serialize(array($response->address)), 
            'validatedAddresses' => serialize($response->validatedAddresses), 
            'coordinates' => serialize(array($response->coordinates)), 
            'resolutionQuality' => $response->resolutionQuality,
            'taxAuthorities' => serialize(array($response->taxAuthorities))      
            ), 
            array('%s','%s','%s','%s','%s') 
            );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }

    } 
}