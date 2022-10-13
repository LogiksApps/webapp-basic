<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("runBizLogger")) {
    function runAutoLogger() {
			$title="";
	    $src="";
	    $srcid="";
	    $msg="";
	    $category="MODULE";
	    $type="modify";
	    
	    if(isset($_REQUEST['scmd'])) {
	        $title=$_REQUEST['scmd'];
	        if(isset($_REQUEST['action'])) {
	            $src=$_REQUEST['scmd'].".".$_REQUEST['action'];
	            $type=$_REQUEST['action'];
	        } else {
	            $src=$_REQUEST['scmd'];
	        }
	    }
	    
	    if(isset($_REQUEST['hashid'])) {
	        $srcid=$_REQUEST['hashid'];
	    } elseif(isset($_REQUEST['refid'])) {
	        $srcid=$_REQUEST['refid'];
	    } elseif(isset($_REQUEST['srcid'])) {
	        $srcid=$_REQUEST['srcid'];
	    } elseif(isset($_REQUEST['slug'])) {
	        $srcid=$_REQUEST['slug'];
	    } elseif(isset($_REQUEST['id'])) {
	        $srcid=$_REQUEST['id'];
	    }
	    if(is_numeric($srcid)) $srcid=md5($srcid);
	    
	    if(isset($_REQUEST['groupid'])) {
	        $srcid=$_REQUEST['groupid']."#".$srcid;
	    }
	    //printArray([$src,$srcid,$title,$type,$category]);exit("III");
	    createActivityLog($src,$srcid,$title,$type,$category);
    }
	function runBizLogger() {
		if(isset($_REQUEST['formid'])) {
			$formKey=$_REQUEST['formid'];
			if(!isset($_SESSION['FORM'][$formKey])) {
				return;
			}
			$formConfig=$_SESSION['FORM'][$formKey];
			
			$srcKey=$formConfig['srckey'];
			$srcid=0;
			$sourcefile=$formConfig['sourcefile'];
			$mode=$formConfig['mode'];
			$modeString=$mode;
			
			if($mode=="edit" || $mode=="update") {
				$mode="Update";
				$modeString="updated";
				if(isset($formConfig['source']) && isset($formConfig['source']['where_auto']) && isset($formConfig['source']['where_auto']['md5(id)'])) {
					$srcid=$formConfig['source']['where_auto']['md5(id)'];
				}
			} elseif($mode=="new" || $mode=="create") {
				$mode="Create";
				$modeString="created";
				$srcid=_db()->get_insertID();
			}
			
			$src=explode(".",$srcKey);
			if(count($src)<=1) {
				$src[1]=$src[0];
			}
			$srcS=implode("/",$src);
      if(strpos($srcS,"modules")>0) {
        $a=strpos($srcS,"modules");
        $b=strpos($srcS,"/",$a);
        $c=strpos($srcS,"/",$b+1);
        $src[0]=substr($srcS,$b+1,$c-$b-1);
        $srcS=$src[0];
      } elseif(strpos($srcS,"popup")>0) {
        $a=strpos($srcS,"popup");
        $b=strpos($srcS,"/",$a);
        $c=strpos($srcS,"/",$b+1);
        $src[0]=substr($srcS,$b+1,$c-$b-1);
        $srcS=$src[0];
      }
			
			createActivityLog($srcKey,$srcid,$src[0],$mode,"Forms",$_SESSION["SESS_USER_ID"]." {$modeString} in {$srcS}");
		} elseif(isset($_REQUEST['gridid'])) {
			$gridKey=$_REQUEST['gridid'];
		    if(!isset($_SESSION['REPORT'][$gridKey])) {
				return;
			}
			$reportConfig=$_SESSION['REPORT'][$gridKey];
		    
		  $srcKey=$reportConfig['srckey'];
			$srcid=$_REQUEST['dataHash'];
			$sourcefile=$reportConfig['sourcefile'];
			$mode="Update";
			$modeString="updated";
		    
      $src=explode(".",$srcKey);
			if(count($src)<=1) {
				$src[1]=$src[0];
			}
			$srcS=implode("/",$src);
      if(strpos($srcS,"modules")>0) {
        $a=strpos($srcS,"modules");
        $b=strpos($srcS,"/",$a);
        $c=strpos($srcS,"/",$b+1);
        $src[0]=substr($srcS,$b+1,$c-$b-1);
        $srcS=$src[0];
      } elseif(strpos($srcS,"popup")>0) {
        $a=strpos($srcS,"popup");
        $b=strpos($srcS,"/",$a);
        $c=strpos($srcS,"/",$b+1);
        $src[0]=substr($srcS,$b+1,$c-$b-1);
        $srcS=$src[0];
      }
			
			createActivityLog($srcKey,$srcid,$src[0],$mode,"Reports",$_SESSION["SESS_USER_ID"]." {$modeString} {$srcS}");
		} elseif(isset($_REQUEST['dtuid'])) {
			if(isset($_SESSION['INFOVIEWTABLE']) && isset($_SESSION['INFOVIEWTABLE'][$_REQUEST['dtuid']])) {
				$infoConfig=$_SESSION['INFOVIEWTABLE'][$_REQUEST['dtuid']];
				
				$srcKey=$infoConfig['srckey'];
				$srcid=$infoConfig['refhash'];
				$title=$infoConfig['table'];
				
				switch(strtolower($_REQUEST['slugpath'])) {
					case "update-record":
						$mode="Update";
						$modeString="updated";
						break;
					case "create-record":
						$mode="create";
						$modeString="created";
						break;
					case "delete-record":
						$mode="delete";
						$modeString="deleted";
						break;
					default:
						$mode="Update";
						$modeString="updated";
						break;
				}
				$srcS=" {$infoConfig['table']}";
				if(isset($_REQUEST['refid'])) {
					createActivityLog($srcKey,$srcid,$title,$mode,"Infoview",$_SESSION["SESS_USER_ID"]." {$modeString} {$srcS}","{$srcKey}@{$srcid}!{$infoConfig['table']}@{$_REQUEST['refid']}");
				} else {
					createActivityLog($srcKey,$srcid,$title,$mode,"Infoview",$_SESSION["SESS_USER_ID"]." {$modeString} {$srcS}","{$srcKey}@{$srcid}");
				}
			} else {
				runAutoLogger();
			}
		} else {
		    runAutoLogger();
		}
	}
	function createActivityLog($src,$srcid,$title,$type="modify",$category="System",$msg=false,$unilink='') {
		if(is_numeric($srcid)) $srcid=md5($srcid);
		$type=strtolower($type);
		
		if($msg===false) {
			$msg="";
			$modeString="modified";
			
			$typeS=current(explode("-",$type));
			
			if(in_array($typeS,["save","update","block"])) {
          $type="update";
          $modeString="updated";
      } elseif(in_array($typeS,["new","create"])) {
          $type="create";
          $modeString="created";
      } elseif(in_array($typeS,["delete","remove","rm"])) {
          $type="delete";
          $modeString="deleted";
      } elseif(in_array($typeS,["copy","cp"])) {
          $type="copy";
          $modeString="copied";
      } elseif(in_array($typeS,["publish"])) {
          $type="publish";
          $modeString="published";
      } elseif(in_array($typeS,["merge"])) {
          $type="merge";
          $modeString="merged";
      } elseif(in_array($typeS,["mail","send","share","shared"])) {
          $type="share";
          $modeString="shared";
      }

      $srcX=explode(".",$src);
      if(count($srcX)<=1) {
        $srcX[1]=$srcX[0];
      }
      $srcS=implode("/",$srcX);
			
			$msg=$_SESSION["SESS_USER_ID"]." {$modeString} {$srcS}";
		}
		$dbKey=getConfig("LOG_DBKEY");
		if(!$dbKey) $dbKey="app";
		
		_db($dbKey)->_insertQ1("log_activities",[
					"ref_src"=>$src,
					"ref_id"=>$srcid,
					"date"=>date("Y-m-d"),
					"title"=>$title,
					"category"=>$category,
					"type"=>$type,
					"msg"=>$msg,
					"unilink"=>$unilink,
          "guid"=>$_SESSION["SESS_GUID"],
          "groupuid"=>$_SESSION["SESS_GROUP_NAME"],
					"privilegeid"=>$_SESSION["SESS_PRIVILEGE_ID"],
    	"access_level"=>$_SESSION["SESS_ACCESS_LEVEL"],
					"post_data"=>json_encode($_POST),
					"created_on"=>date("Y-m-d H:i:s"),
					"edited_on"=>date("Y-m-d H:i:s"),
					"created_by"=>$_SESSION["SESS_USER_ID"],
					"edited_by"=>$_SESSION["SESS_USER_ID"],
				])->_RUN();
	}
}
?>

