<?php
if(!defined('ROOT')) exit('No direct script access allowed');

switch($_REQUEST["action"]) {
    case "list-logs-local":
        if(isset($_REQUEST['dcode']) && strlen($_REQUEST['dcode'])>0 && isset($_SESSION[$_REQUEST['dcode']])) {
            $dcode=$_REQUEST['dcode'];
            $_SESSION[$dcode]['blocked']='false';
            
            $data=_db()->_selectQ("log_activities","id,ref_src,ref_id,date,title,category,type,msg,edited_by,edited_on as edited_on",$_SESSION[$dcode])->_orderBy("edited_on DESC")->_GET();
            //post_data
            
            printServiceMsg($data);
        } else {
            printServiceMsg([]);
        }
    break;
}
?>