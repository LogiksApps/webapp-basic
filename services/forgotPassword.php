<?php
if (!defined('ROOT')) exit('No direct script access allowed');

switch ($_REQUEST['action']) {	

	case 'generateUserToken':
		$result=generateUserToken($_POST);
	
	break;
	case 'resetPassword':
		$result=resetPassword($_POST);
	
	break;
	
}




function generateUserToken($data){
    
    $uinfo = trim($data['email']);
    	
	if(isValidEmail($uinfo)){
	    
		$reason = "Forgot Password";
		//$exp_time = (int)getConfig('PASSWORD_TOKEN_EXPIRY');
		if(isset($_REQUEST['reason'])) {
		    
			$reason = trim($_REQUEST['reason']);
		}
		
		$check = getUserDetailsByEmail($uinfo);
	   //printArray($check);exit;

		
		if(isset($check[0]['userid'])) {
		    
			$link ="";
			
			$token = random_chars(32);
			$mauth=random_characters(8);
			//printArray($mauth);exit;
			$now=time();
			$reset_page= "reset-password/reset/".$token;
			$link = _link($reset_page);
			$fields = array(
				'userid' => $check[0]['userid'],
				'vcode' => $token,
				'mauth' => $mauth,
				'blocked' => 'false',
				'created_on' => date('Y-m-d H:i:s',$now),
				'edited_on' => date('Y-m-d H:i:s',$now)
			);
			
			
		   $sql = _db(true)->_updateQ(_dbTable('users',true),$fields,['userid' =>$uinfo])->_GET();
			$res = _dbQuery($sql);
			
			if($sql) {
		
				$fields = array(
					'userid' => $check[0]['name'],
					'link' => $link,
					'code' => $token,
					'securitycode'=>$mauth
				);
			    $body=_templateFetch('password_recover',$fields);
				$from=getConfig("DEFAULT_FROM_MAIL");
				$params=array("from"=>$from);
                
				$mail=_message()->send($check[0]['userid'],"Password Reset",$body);			
				
				
				if($mail) {
				    
				    $msg="Password request has been processed successfully. We have sent you an email with the link to reset your password. Please click on the link to update your account password.";
				     doRedirect('forgot-password',$msg);
					
				} else {
				    
				    $msg="An error occurred when trying to send the mail.";
				    doRedirect('forgot-password',$msg);
				
				}
			} else {
			    $msg="Sorry, an error occurred for implementing the password token.";
			    doRedirect('forgot-password',$msg);
			}
		} else {
		    $msg="You are not registered with us.";
		    doRedirect('forgot-password',$msg);
		
		}
    }else{
        
        $msg="Not a valid mail.";
        doRedirect('forgot-password',$msg);
	
	}
 }

 
function resetPassword($data){
   // $data['token'] = $_GET['token'];
   //printArray($data);
    loadHelpers("pwdhash");
    $token=$data['token'];
    if(!empty($data['token'])) {
        //echo $data['token'];exit;
        $new = trim($data['password']);
        $cnf = trim($data['password_confirm']);
        $security_code=$data['security_code'];
        //echo $security_code;
        if(!empty($security_code)) {
            $sec_code=$data['secure'];
            //echo $sec_code;exit;
        
            if(!empty($new) && !empty($cnf)){
                if($security_code == $sec_code) {      
                
                    if($new == $cnf) {        
                        
                        $newPWD = getPWDHash($new);
                        
                        if(is_array($newPWD)) {
                            
                            $pwdSalt=$newPWD['salt'];
                            $pwdHash=$newPWD['hash'];
                            
                        } else {
                            $pwdSalt="";
                            $pwdHash=$newPWD;
                        }
                        
                        $id=$data['sub'];
                      
                        $sql = _db(true)->_updateQ(_dbTable('users',true),array('pwd' => $pwdHash, 'pwd_salt'=>$pwdSalt),['md5(id)' =>$id])->_GET();
                        
                        
                            if($sql) {
                                //echo "hghkjhjkjhkj";
                               
                                $msg="Your password has been successfully updated.";
                        
                                
    				             doRedirect('reset-password/reset/'.$token,$msg);
    				             
                            } else {
                                
                                $msg="An error occurred when trying to update the token.";
                                //$_SESSION['SUCCESS_MSG'] = "swapnil";
        				        doRedirect('reset-password/reset/'.$token,$msg);
                            }
                            
                          
                         } else {
                        
                        $msg="The passwords do not match.";
                		doRedirect('reset-password/reset/'.$token,$msg);
                    }
                }else{
                     $msg="Plese enter correct security code.";
                		doRedirect('reset-password/reset/'.$token,$msg);
                }
            }else{
                     
                $msg="The passwords cannot be blank";
        		doRedirect('reset-password/reset/'.$token,$msg);
            }
        }else{
             $msg="The security code cannot be blank";
        		doRedirect('reset-password/reset/'.$token,$msg);    
        }
    } else {
        $msg="Invalid token.";
         doRedirect('reset-password/reset/'.$token,$msg);
    }

}

function getUserDetailsByEmail($uinfo){
    
    $data=[];
     $rec=_db(true)->_selectQ("lgks_users",'*',['email'=>$uinfo])->_GET();
     
     if(count($rec)>0){
         
       $data= $rec;  
       
     }
     
     return $data;
    
}


function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function doRedirect($link,$msg){
        $_SESSION['SERVICE_MSG']=$msg;
        header("Location: "._link($link));
        
}

function random_chars($length = 8) {
    
    if($length <= 0) {
        
        return "";
    }
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $char_len = strlen($charset);
    $letters = "";
    if($length > $char_len) {
        
        $factor = ceil($length / $char_len);
        
        for($i=0;$i<$factor;$i++) {
            
            $letters .= $charset;
        }
    } else {
        
        $letters = $charset;
    }

    $shuffle = str_shuffle($letters);
    $letters = substr($shuffle,0,$length);
    
    return $letters;
}


function random_characters($length = 8) {
    
    if($length <= 0) {
        
        return "";
    }
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $char_len = strlen($charset);
    $letters = "";
    if($length > $char_len) {
        
        $factor = ceil($length / $char_len);
        
        for($i=0;$i<$factor;$i++) {
            
            $letters .= $charset;
        }
    } else {
        
        $letters = $charset;
    }

    $shuffle = str_shuffle($letters);
    $letters = substr($shuffle,0,$length);
    
    return $letters;
}




?>
