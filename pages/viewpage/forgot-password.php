
<?php
if (!defined('ROOT')) exit('No direct script access allowed');
echo "huihih";exit;

$success_msg = '';
$error_msg = '';
 //if (session_check() ) header("Location:"._link('home'));   
if(isset($_SESSION['SERVICE_MSG']) && !empty($_SESSION['SERVICE_MSG'])){
    
    if(strpos($_SESSION['SERVICE_MSG'],'successfully')!==false || strpos($_SESSION['SERVICE_MSG'],'Thank you')!==false){
        
    $success_msg = $_SESSION['SERVICE_MSG'];
    
    }else{
        
        $error_msg = $_SESSION['SERVICE_MSG'];
    }
    
    unset($_SESSION['SERVICE_MSG']);
}

_pageVar("ERROR",$error_msg);
_pageVar("SUCCESS",$success_msg);

?>