<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("runNotification")) {

    function runNotification($alertID,$params = [],$attachments=[]) {
        if(is_numeric($alertID))
            $notifyData = _db()->_selectQ("sys_matrix_notify","*",["id"=>$alertID,"blocked"=>"false"])->_GET();
        else
            $notifyData = _db()->_selectQ("sys_matrix_notify","*",["topic"=>$alertID,"blocked"=>"false"])->_GET();
        
        if(!$notifyData || count($notifyData)<=0) {
            return false;
        }
        $notifyData = $notifyData[0];
        
        return runNotifyScript($notifyData, $params, $attachments);
    }
    
    function runAllNotification($category, $params = [], $attachments=[]) {
        $notifyData = _db()->_selectQ("sys_matrix_notify","*",["category"=>$category,"blocked"=>"false"])->_GET();
        
        if(!$notifyData || count($notifyData)<=0) {
            return false;
        }
        
        $a = [];
        
        foreach($notifyData as $row) {
            $a[] = runNotifyScript($row, $params, $attachments);
        }
        
        return $a;
    }
    
    function runNotifyScript($notifyData, $params = [],$attachments=[]) {
        $params['dated'] = date("Y-m-d H:i:s");
        if(is_array($params)) {
            foreach($params as $a=>$b) {
                $_REQUEST[$a]=$b;
            }
        }
        
        $to = $notifyData['notify_to'];
        $cc = $notifyData['notify_cc'];
        $bcc = $notifyData['notify_bcc'];
        $subject = $notifyData['subject'];
        $bodyOri = $notifyData['body_template'];
        
        if(strlen($bodyOri)<=0) {
            return false;
        }
        if(strlen($subject)<=0) {
            $subject = "New Notification EMail";
        }
        
        $body =  _replace($bodyOri,'%');
        
        $a = false;
        switch($notifyData['notify_type']) {
            case "email":
                $a = _msg("app")->send($to,$subject,$body,[
                        "cc"=>$cc,
                        "bcc"=>$bcc,
                        //"attachments"=>$attachments
                    ]);
                break;
            case "sms":
                $a = _msg("sms")->send($to,$subject,$body,[
                        "cc"=>$cc,
                        "bcc"=>$bcc,
                        //"attachments"=>$attachments
                    ]);
                break;
            case "gns":
                $a = _msg("gns")->send($to,$subject,$body,[
                        "cc"=>$cc,
                        "bcc"=>$bcc,
                        //"attachments"=>$attachments
                    ]);
                break;
            case "api":
                
                //break;
            case "module":
                //break;
            default:
                return false;
        }
        
        _db()->_insertQ1("log_notifications",[
                "guid"=>$_SESSION['SESS_GUID'],
                "groupuid"=>$_SESSION['SESS_GROUP_NAME'],
                "srcid"=>$notifyData['topic'],
                "category"=>$notifyData['category'],
                "type"=>$notifyData['notify_type'],
                "msg_to"=>$notifyData['notify_to'],
                "msg_cc"=>$notifyData['notify_cc'],
                "msg_bcc"=>$notifyData['notify_bcc'],
                "msg_subject"=>$notifyData['subject'],
                "msg_body"=>$body,
                "msg_xtras"=>$a,
                "created_by"=>$_SESSION['SESS_USER_ID'],
                "created_on"=>$params['dated'],
                "edited_by"=>$_SESSION['SESS_USER_ID'],
                "edited_on"=>$params['dated'],
            ])->_RUN();
        
        return true;
    }
}
?>
