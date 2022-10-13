<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if (!function_exists("getCompanySettings")) {
    
    function getCompanySettings($paramKey, $defaultValue="", $module="system") {
        if(!isset($_SESSION['SESS_GUID']) || !isset($_SESSION['COMP_ID'])) return $defaultValue;
        if(array_key_exists("PARAMS_".strtoupper($paramKey),$_SESSION)) {
            return $_SESSION["PARAMS_".strtoupper($paramKey)];
        } else {
            $dateTime=date("Y-m-d H:i:s");
            _db()->_insertQ1("my_company_settings",[
                    'guid'=>$_SESSION['SESS_GUID'],
                    'groupuid'=>$_SESSION['SESS_GROUP_NAME'],
                    
                    "company_id"=>$_SESSION['COMP_ID'],
                    "param_title"=>strtoupper($paramKey),
                    "param_value"=>$defaultValue,
                    "param_module"=>$module,
                    
                    'created_by'=>$_SESSION['SESS_USER_ID'],
                    'edited_by'=>$_SESSION['SESS_USER_ID'],
                    'created_on'=>$dateTime,
                    'edited_on'=>$dateTime,
                ])->_RUN();
            
            $_SESSION["PARAMS_".strtoupper($paramKey)]=$defaultValue;
            
            return $defaultValue;
        }
    }
}
?>