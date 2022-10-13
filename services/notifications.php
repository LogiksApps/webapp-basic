<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

switch($_REQUEST["action"]) {
  case "memos":
    $data=_db()->_selectQ("user_memos","md5(id) as hashid,date,title,category,type,msg,unilink,edited_on,edited_by",[//ref_src,ref_id,priority
        "blocked"=>'false'
      ])->_orderBy("edited_on DESC")->_limit(50,0);
    //$data=$data->_whereRAW("((for_userid='*') OR FIND_IN_SET('{$_SESSION['SESS_USER_ID']}',for_userid) OR FIND_IN_SET('{$_SESSION['SESS_PRIVILEGE_NAME']}',for_userid) OR '#SESS_PRIVILEGE_ID#' <= '#ADMIN_PRIVILEGE_RANGE#')");
    
    //->_whereRAW("(priority='high' OR priority='medium')")->_GET();
    
    $data=$data->_GET();
    printServiceMsg(["timestamp"=>time(),"notifications"=>$data]);
    break;
    
  case "alerts":
    $data=_db()->_selectQ("log_alerts","md5(id) as hashid,ref_src,ref_id,date,title,category,icon,msg,rule,for_userid,edited_on,edited_by",[
        //"date(created_on)"=>date("Y-m-d"),
        "blocked"=>'false'
      ])
      ->_whereRAW("(for_userid='*' OR FIND_IN_SET('{$_SESSION['SESS_USER_ID']}',for_userid) OR FIND_IN_SET('{$_SESSION['SESS_PRIVILEGE_NAME']}',for_userid) OR '#SESS_PRIVILEGE_ID#' <= '#ADMIN_PRIVILEGE_RANGE#')")
      ->_orderBy("edited_on DESC")->_limit(50,0);
    
    $data=$data->_GET();
    printServiceMsg(["timestamp"=>time(),"notifications"=>$data]);
    break;
  
  case "events":
    printServiceMsg(["timestamp"=>time(),"notifications"=>[]]);
    break;
  
  case "today-events":
    $data=_db()->_selectQ("user_events","schedule,flag_period,ref_src,ref_id,priority,event,descs,edited_on,edited_by",[
        "blocked"=>"false","schedule"=>date("Y-m-d"),
      ])->_whereRAW("(priority='high' OR priority='medium')")->_orderBy("schedule DESC,schedule DESC,edited_on DESC")->_GET();
    printServiceMsg($data);
    break;
  
  case "activity":
    $data=_db()->_selectQ("log_activities","md5(id) as hashid,ref_src,ref_id,date,title,category,type,msg,unilink,edited_on,edited_by",[
        "blocked"=>'false',
        "date(created_on)"=>date("Y-m-d"),
      ])->_orderBy("edited_on DESC")->_limit(50,0);
    
    //->_whereRAW("(priority='high' OR priority='medium')")->_GET();
    
    $data=$data->_GET();
    printServiceMsg(["timestamp"=>time(),"notifications"=>$data]);
    break;
  
  case "comments":
    $where=["blocked"=>"false"];
    $whereRAW="";
    
    if($_SESSION['SESS_PRIVILEGE_ID']>ADMIN_PRIVILEGE_RANGE) {
      $whereRAW="(shared_with NOT IN ('*','private') AND (shared_with='@{$_SESSION['SESS_GROUP_NAME']}' OR shared_with='@{$_SESSION['SESS_PRIVILEGE_NAME']}' OR shared_with='{$_SESSION['SESS_USER_ID']}' OR shared_with='@'))";
    }
    
    $sql=_db()->_selectQ("user_comments","*",$where)->_orderBy("edited_on DESC")->_limit(50,0);
    
    if(strlen($whereRAW)>0) {
      $sql->_whereRAW($whereRAW);
    }
    //exit($sql->_SQL());
    
    $data=$sql->_GET();
    if($data) {
      foreach($data as $a=>$b) {
          $data[$a]['msg']=str_replace("\\n"," ",$b['msg']);
          $data[$a]['username']=toTitle(current(explode("@",$b['edited_by'])));
          
          if($data[$a]['shared_with']=="*" || strtolower($data[$a]['shared_with'])=="private") {
            $data[$a]['isprivate']=true;
          } else {
            $data[$a]['isprivate']=false;
          }
          
      }
    } else {
      $data=[];
    }
    
    printServiceMsg(["timestamp"=>time(),"notifications"=>$data]);
    break;
    
  default:
    printServiceMsg([]);
}
?>