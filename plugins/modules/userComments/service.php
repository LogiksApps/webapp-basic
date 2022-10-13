<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST['dcode']) && strlen($_REQUEST['dcode'])>0) {
    printServiceMsg([]);
    return;
}

$dcode=$_REQUEST['dcode'];

$commentCols = "user_comments.id,user_comments.category,user_comments.flags,user_comments.msg,user_comments.shared_with,profiletbl.avatar as useravatar,user_comments.created_by,user_comments.edited_by,user_comments.created_on,user_comments.edited_on";

switch($_REQUEST["action"]) {
    case "list-comments":
        if(isset($_SESSION[$dcode]) && isset($_SESSION[$dcode]['query'])) {
            $commentWhere = $_SESSION[$dcode]['query'];
            
            if(is_array($commentWhere)) {
              $commentWhere['user_comments.blocked']='false';
              $sql=_db()->_selectQ("user_comments,profiletbl",$commentCols,$commentWhere)
                    ->_whereRAW("user_comments.created_by=profiletbl.loginid")
                    ->_orderBy("user_comments.edited_on DESC");
            } else {
              $sql=_db()->_selectQ("user_comments,profiletbl",$commentCols)
                    ->_whereRAW($commentWhere)
                    ->_whereRAW("user_comments.blocked='false' and user_comments.created_by=profiletbl.loginid")
                    ->_orderBy("user_comments.edited_on DESC");
            }
            
            if($_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE")) {
                $sql->_where([
                        "user_comments.shared_with"=>[["*","@{$_SESSION['SESS_GROUP_NAME']}","#{$_SESSION['SESS_PRIVILEGE_NAME']}","{$_SESSION['SESS_USER_ID']}"],"IN"],
                        "user_comments.created_by"=>$_SESSION['SESS_USER_ID'],
                    ],"AND","OR");
            }
            // echo $sql->_SQL();exit();
            $data=$sql->_limit(100)->_GET();
            if($data) {
                $defaultUserImage = loadMedia("images/user.png");
                foreach($data as $a=>$b) {
                    $data[$a]['msg']=str_replace("\\n","<br>",$b['msg']);
                    $data[$a]['msg']=str_replace(" ",'+',$b['msg']);
                    $data[$a]['msg']=str_replace('\"','"',$b['msg']);
                    $data[$a]['msg']=str_replace("%3B",";",$b['msg']);
                    $data[$a]['msg']=str_replace("\'","'",$b['msg']);
                   // $data[$a]['msg']=str_replace("\\","\",$b['msg']);
                   
                    $data[$a]['msg'] = str_replace("\\n","<br>",$data[$a]['msg']);
                   
                    $data[$a]['username']=toTitle(current(explode("@",$b['created_by'])));
                    
                    if($data[$a]['shared_with']=="*") $data[$a]['shared_with'] = "ALL";
                    
                    if(strlen($data[$a]['useravatar'])<1 || $data[$a]['useravatar']=="photoid::") {
                        $data[$a]['useravatar'] = $defaultUserImage;
                    } elseif(substr($data[$a]['useravatar'],0,7)=="http://" || substr($data[$a]['useravatar'],0,8)=="https://") {
                        
                    } else {
                        $data[$a]['useravatar'] = WEBAPPROOT.$data[$a]['useravatar'];
                    }
                }
            }
            
            printServiceMsg($data);
        } else {
            printServiceMsg([]);
        }
    break;
    case "create-comment":
        if(isset($_POST['msg']) && strlen($_POST['msg'])>0 && isset($_SESSION[$dcode]) && isset($_SESSION[$dcode]['create'])) {
            $dcode=$_REQUEST['dcode'];
            if(isset($_POST['dcode'])) unset($_POST['dcode']);
           
            $commentData=array_merge(getBizDefaultData(),$_SESSION[$dcode]['create']);
            $commentData=array_merge($commentData,$_POST);
            
            $commentData['created_by']=$_SESSION['SESS_USER_ID'];
            $commentData['created_on']=date("Y-m-d H:i:s");
            
            if(!isset($commentData['category'])) $commentData['category']="user";
          
            $msgText = $commentData['msg'];
            $users = [];
            
            preg_match_all("/@[a-zA-Z0-9-_.]+/",$msgText,$userArr, PREG_PATTERN_ORDER);
            if(isset($userArr[0]) && count($userArr[0])>0) {
                $users[] = implode(",",$userArr[0]);
            }
            
            preg_match_all("/#[a-zA-Z0-9-_.]+/",$msgText,$teamArr, PREG_PATTERN_ORDER);
            if(isset($teamArr[0]) && count($teamArr[0])>0) {
                $users[] = implode(",",$teamArr[0]);
            }
            
            preg_match_all('/![a-zA-Z0-9-_.]+/',$msgText,$previlegeArr, PREG_PATTERN_ORDER);
            if(isset($previlegeArr[0]) && count($previlegeArr[0])>0) {
                $users[] = implode(",",$previlegeArr[0]);
            }
            $users = implode(",",$users);
            
            if(strlen($users)<=1) {
                $commentData['shared_with']="*";
            } else {
                $commentData['shared_with']= $users;
            }
            
            $a=_db()->_insertQ1("user_comments",$commentData)->_get();
            
            if($a)
                printServiceMsg("Comment posted");
            else
                printServiceMsg("Comment creation failed. Try again");//._db()->get_error()
        } else {
            printServiceMsg("No Message Body found");
        }
    break;
}
?>