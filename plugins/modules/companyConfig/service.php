<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession(false);

switch($_REQUEST['action']){
    case "save":
        if(isset($_POST) && count($_POST)>0) {
            $dateTime=date("Y-m-d H:i:s");
            
            $a=_db()->_updateQ("my_company_settings",[
                'param_value'=>$_POST['keyvalue'],
            
                'edited_by'=>$_SESSION['SESS_USER_ID'],
                'edited_on'=>$dateTime,
                ],[
                    "md5(id)"=>$_POST['keyhash'],
                    "guid"=>$_SESSION['SESS_GUID'],
                    "company_id"=>$_SESSION['COMP_ID'],
                    "param_title"=>$_POST['keyname'],
                    ])->_RUN();
            if($a) {
                $_SESSION["PARAMS_".strtoupper($_POST['keyname'])]=$_POST['keyvalue'];
                echo toTitle(_ling($_POST['keyname']))." Updated";
            } else {
                echo "Error in updating company settings";
            }
        } else {
            echo "Error in submiting company settings";
        }
    break;
}
?>