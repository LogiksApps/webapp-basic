<?php
if (!defined('ROOT')) exit('No direct script access allowed');

$error_msg="";
$success_msg="";
$token_error="";
$token="";
$id = "";
$date = date('Y-m-d H:i:s');
$slug= _slug("page/token");    

//printArray($slug);
$token=$slug['token'];
$secure_code='';

//if (session_check() ) header("Location:"._link('home'));



// if(isset($_SESSION['SUCCESS_MSG']))
// {
//     $success_msg = $_SESSION['SUCCESS_MSG'];
//     unset($_SESSION['SUCCESS_MSG']);
// }
// else
// {
//     if(isset($_SESSION['ERROR_MSG']))
//     {
//         $error_msg = $_SESSION['ERROR_MSG'];
//         unset($_SESSION['ERROR_MSG']);   
//     }
//     $slug= _slug("page/token");    
//     $token = $slug['token'];
//     if($token=="")
//     {
//         $error_msg = "invalid token";
//     }
//     else
//     {
//         $res = _db(true)->_selectQ("lgks_users","*",['blocked'=>'false','vcode'=>$token])->_GET();
//     }
// }





if(isset($_SESSION['SERVICE_MSG'])){
    
    if(strpos($_SESSION['SERVICE_MSG'],'successfully') !== false){
        $success_msg = $_SESSION['SERVICE_MSG'];
        
        
    }else{
        
        $error_msg=$_SESSION['SERVICE_MSG'];
    }
        
   
    unset($_SESSION['SERVICE_MSG']);
}
if(empty($slug['token'])){
    
   //$token_error="Token missing.";
        $error_msg="Token missing.";
 }
         $token=$slug['token'];
           $res = _db(true)->_selectQ("lgks_users","*",['blocked'=>'false','vcode'=>$token])->_GET();
        // echo  var_dump($res);
           $secure_code=$res[0]['mauth'];
           //$vtoken=$res[0]['vcode'];
          //echo $token."<br>";
          //echo $secure_code;
           
            if(count($res)>0){
                // $edited_on=$res[0]['edited_on'];
                // // 	//echo date("Y-m-d H:i:s",time()+60*60);exit;
                // $exp_time= date($edited_on,time()+60*60);
                // // $exp_time=$edited_on+ 60*60;
               
                // if($exp_time<$date){
                  
                // $error_msg ="Token already expired.";
                
                // }else{
               
                $id = md5($res[0]['id']);
                $token = $res[0]['vcode'];    
               
           }else{
               
               $token_error="Token mismatch.";
           }





 //printArray($token) ;
//echo "hghh";
   
//$id = md5(5);
//$token = $_GET['token'];
_pageVar("ID",$id);
_pageVar("ERRORMSG",$error_msg);
_pageVar("SUCCESSMSG",$success_msg);
_pageVar("ERROR_MSG",$token_error);
_pageVar("TOKEN_VAL",$token);
_pageVar("CODE_VAL",$secure_code);



?>