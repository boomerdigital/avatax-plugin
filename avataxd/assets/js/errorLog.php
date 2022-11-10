<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class ErrorLog{
    public static function errorLogs($message){
        $currentDate = date("Y-m-d h:i:sa");
        $message = $currentDate.'------'.$message;
        if(DEBUG=="yes"){
        file_put_contents(AVATAXPLUGINPATH.'logs/error.log', print_r("==================\n\n",true),FILE_APPEND);
        file_put_contents(AVATAXPLUGINPATH.'logs/error.log', print_r($message,true),FILE_APPEND);
        file_put_contents(AVATAXPLUGINPATH.'logs/error.log', print_r("-----------------\n\n",true),FILE_APPEND);
    }
    }
    public static function sysLogs($message){
        $currentDate = date("Y-m-d h:i:sa");
        $message = $currentDate.'------'.$message;
            if(DEBUG=="yes"){
            file_put_contents(AVATAXPLUGINPATH.'logs/sysLogs/error.log', print_r("==================\n\n",true),FILE_APPEND);
            file_put_contents(AVATAXPLUGINPATH.'logs/sysLogs/error.log', print_r($message,true),FILE_APPEND);
            file_put_contents(AVATAXPLUGINPATH.'logs/sysLogs/error.log', print_r("-----------------\n\n",true),FILE_APPEND);
        }
    }
} 