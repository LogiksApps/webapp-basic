<?php
if(!defined('ROOT')) exit('No direct script access allowed');

include_once __DIR__."/api.php";

switch($_REQUEST["action"]) {
	case "list":
		$data=_db()->_selectQ("sys_scripts","id,slug,name,category,family,tags,viewers,editors,blocked,created_by,created_on,edited_by,edited_on");
		$data=$data->_GET();
		
		$fData=["NoGroup"=>[]];
		foreach($data as $a=>$b) {
			if($b['category']==null || strlen($b['category'])<=0) $b['category']="NoGroup";
			if(!isset($fData[$b['category']])) $fData[$b['category']]=[];
			
			$b['title'] = "{$b['name']} [{$b['family']}]";
			
			$fData[$b['category']][$b['id']]=$b;
		}
		printServiceMSG($fData);
		break;
	case "fetch":case "fetchBody":
		if(isset($_POST['slug'])) {
			$data=getScript($_POST['slug']);
			
			if(isset($data)) echo $data["draft"];
			else echo "error: Script Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;		
	case "fetchParams":
		if(isset($_POST['slug'])) {
			$data=getScript($_POST['slug']);
			
			if(isset($data)) {
				$data=$data["draft_params"];
				$data=json_decode($data,true);
				if($data==null) echo "{}";
				else echo json_encode($data, true);
			} else echo "error: Script Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;	
	case "preview":
		if(isset($_POST['slug'])) {
			$data=getScript($_POST['slug']);
			if(isset($data)) {
				// if(strlen(strip_tags($data["script"]))<=2) {
				if($data["publish_status"]=="false") {
					echo "<br><h2 align=center class='metaTitle'>Not published yet</h2>";
					return;
				} else {
				// 	if($data['share_viewers']!="*") {
				// 		$data['share_viewers']=explode(",",$data['share_viewers']);
				// 		if(in_array($_SESSION['SESS_USER_ID'],$data['share_viewers']) || 
				// 				in_array($_SESSION['SESS_PRIVILEGE_NAME'],$data['share_viewers']) ||
				// 				in_array($_SESSION['SESS_GROUP_NAME'],$data['share_viewers'])) {
							
							echo "<span class='label label-success pull-right metaLabel'>{$data['created_on']}</span>";
							echo "<span class='label label-warning pull-right metaLabel'>{$data['created_by']}</span>";
							echo "<h1 class='metaTitle text-center'>{$data['name']}</h1><hr>";
							echo "<pre class='code'>".$data["script"]."</pre>";
							echo "<pre class='code'>".str_replace("%3B",";",$data["script_params"])."</pre>";
				// 		} else {
				// 			echo "error: Article Not Found or Is Unavailable To You";
				// 		}
				// 	} else {
				// 		echo "<span class='label label-success pull-right metaLabel'>{$data['published_on']}</span>";
				// 		echo "<span class='label label-warning pull-right metaLabel'>{$data['published_by']}</span>";
				// 		echo "<h1 class='metaTitle text-center'>{$data['title']}</h1><hr>";
				// 		echo str_replace("%3B",";",$data["script"]);
				// 	}
				}
			} else echo "error: script Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "properties":
		if(isset($_POST['slug'])) {
			$data=_db()->_selectQ("sys_scripts","id,slug,name,category,family,tags,blocked,viewers,editors,created_by,created_on,edited_by,edited_on,script_code")->_where(["slug"=>$_POST['slug']])->_GET();
			
			if(!isset($data[0])) echo "error: script Not Found";
			else {
				$data=$data[0];
				include __DIR__."/comps/properties.php";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "delete":
		if(checkUserRoles("setup","sysScripts","delete") || $_SESSION['SESS_PRIVILEGE_ID']<=getConfig("ADMIN_PRIVILEGE_RANGE")) {
			if(isset($_POST['slug']) && strlen($_POST['slug'])>0) {
				$slugs=explode(",",$_POST['slug']);
				$ans=_db()->_deleteQ("sys_scripts")->_whereIn("slug",$slugs)->_RUN();

				if($ans) {
						echo "Requested records deleted successfully.";
				} else {
					echo "error: Sorry, requested records could not be deleted.";
				}
			} else {
				echo "error: Reference Not Found";
			}
		} else {
				echo "error: Sorry, you do not have required privilege to delete this script.";
		}
		break;
	case "saveProps":
		if(isset($_POST['slug'])) {
			$slug=$_POST['slug'];
			unset($_POST['slug']);
			
// 			if($_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE")) {
				$dataArticle=_db()->_selectQ("sys_scripts","*")
					->_where(["slug"=>$slug,"created_by"=>$_SESSION['SESS_USER_ID']]);
				$dataArticle=$dataArticle->_GET();
				if(count($dataArticle)<=0) {
						echo "error: Only author can save/update this script";
						return;
				}
// 			}
			$dataArticle=_db()->_selectQ("sys_scripts","*")->_where(["slug"=>$slug]);
			$dataArticle=$dataArticle->_GET();
// 			if(count($dataArticle)>0) {
// 			    if($dataArticle[0]['publish_status'] =='true') {
// 				    echo "Script has been sent for approval, you can't edit in between.";
// 				    return;
// 			    }
// 			}
			
			if(isset($_POST['script_code'])) unset($_POST['script_code']);
			if(isset($_POST['script_params'])) unset($_POST['script_params']);
			
			if(isset($_POST['draft_code'])) unset($_POST['draft_code']);
			if(isset($_POST['draft_params'])) unset($_POST['draft_params']);
			$_POST['edited_by']=$_SESSION['SESS_USER_ID'];
			$_POST['edited_on']=date("Y-m-d H:i:s");
			$ans=_db()->_updateQ("sys_scripts",$_POST,["slug"=>$slug])->_RUN();
			
			if($ans) {
					echo "Successfully updated the properties.";
			} else echo "error: Update failed. Try again later.";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "saveContent":
		if(isset($_POST['slug']) && isset($_POST['text']) && isset($_POST['type'])) {
			$slug=$_POST['slug'];
			unset($_POST['slug']);
			
			$dataArticle=_db()->_selectQ("sys_scripts","*")
					->_where(["slug"=>$slug]);//,"created_by"=>$_SESSION['SESS_USER_ID']
			$dataArticle=$dataArticle->_GET();
			
			if(!isset($dataArticle[0])) {
				echo "error: script not found";
				return;
			}
			$dataArticle=$dataArticle[0];
			//printArray($dataArticle);
			
// 			if($_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE")) {
				$dataArticle['editors']=explode(",",$dataArticle['editors']);
				if(!in_array($_SESSION['SESS_USER_ID'],$dataArticle['editors'])) {
						echo "error: Only allowed editors can save/update this script";
						return;
				}
// 			}
// 			if($dataArticle['publish_status'] =='true' && $dataArticle['approve_status'] =='') {
// 				    echo "Script has been sent for approval, you can't edit in between.";
// 				    return;
// 			}
			
			//$_POST['text']=stripslashes(str_replace("\\r\\n","",$_POST['text']));
			//$_POST['text']=stripslashes(str_replace("&amp%3B","&amp;",$_POST['text']));
			//$_POST['text']=str_replace("%3B",";",$_POST['text']);
			
			switch(strtoupper($_POST['type'])) {
				case "BODY":
					$data['draft_code']=base64_encode($_POST['text']);
					break;
				case "PARAMS":
					$jsonData=json_encode($_POST['text']);
					if($jsonData==null) {
						echo "error: Params should be formatted JSON data";
						return;
					} else {
					    $data['draft_params']=$_POST['text'];
					}
					break;
				default:
					echo "error: Mode/Type not supported. Try again later";
					return;
			}
			$data['edited_by']=$_SESSION['SESS_USER_ID'];
			$data['edited_on']=date("Y-m-d H:i:s");
			$ans=_db()->_updateQ("sys_scripts",$data,["slug"=>$slug])->_RUN();
			
			if($ans) {
				echo "Successfully updated the script";
			} else {
			    echo "error: Update failed. Try again later";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "publishScript":
		if(isset($_POST['slug'])) {
			if(!checkUserRoles("setup","sysScripts","publish") && $_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE"))  {
				echo "error: Sorry, you do not have required privilege to create script.";
				return;
			}

			$slug=$_POST['slug'];
			unset($_POST['slug']);
			
			$dataArticle=_db()->_selectQ("sys_scripts","*")->_where(["slug"=>$slug]);
			$dataArticle=$dataArticle->_GET();
			if(!isset($dataArticle[0])) {
				echo "error: script not found";
				return;
			}
// 			if($_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE")) {
				if($_SESSION['SESS_USER_ID'] != $dataArticle[0]['created_by']) {
						echo "error: Only owner can publish this script";
						return;
				}
// 			}
			$script_code=trim($dataArticle[0]['script_code']);
			$draft_code=trim($dataArticle[0]['draft_code']);
			
			
			$script_params=trim($dataArticle[0]['script_params']);
			$draft_params=trim($dataArticle[0]['draft_params']);
			
			
			if($draft_code == '') {
				echo "Please add Scipt Code and Then try to publish";
				return;
			}
			
			if($draft_code == $script_code && $script_params == $draft_params) {
				echo "No changes found. Maintaining the last state of the script.";
				return;
			}

			$data['script_code']=$draft_code;
			$data['script_params']=$draft_params;
			
			$ans=_db()->_updateQ("sys_scripts",$data,["slug"=>$slug])->_RUN();
			if($ans) {
			    echo "Successfully Publish the script";
			} else echo "error: Publish failed. Try again later";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "create":
		if(isset($_POST['slug'])) {
			if(!checkUserRoles("setup","sysScripts","create") && $_SESSION['SESS_PRIVILEGE_ID']>getConfig("ADMIN_PRIVILEGE_RANGE"))  {
				echo "error: Sorry, you do not have required privilege to create script.";
				return;
			}
			
			$_POST['slug']=strtolower(preg_replace('/[^a-zA-Z0-9-_\.\/]/','_', $_POST['slug']));
			
			$category="";
			$slugArr=explode(".",$_POST['slug']);
			if(count($slugArr)>1) {
				$category=$slugArr[0];
			} else {
				$slugArr=explode("/",$_POST['slug']);
				if(count($slugArr)>1) {
					$category=$slugArr[0];
				}
			}
			
			$slug=_slugify($_POST['slug']);
			
			$data=_db()->_selectQ("sys_scripts","count(*) as cnt")
							->_where(["slug"=>$_POST['slug']])->_GET();
			
			if(isset($data[0]) && $data[0]['cnt']>0) {
				echo "error: The defined Code already exists.";
				return;
			}
			
			$_POST['slug']=$slug;
			$_POST['name']=toTitle(str_replace("_"," ",$_POST['slug']));
			$_POST['category']=$category;
			$_POST['tags']="";
			$_POST['script_code']="";
			$_POST['script_params']="";
			$_POST['draft_code']="";
			$_POST['draft_params']="";
			$_POST['viewers']="*";
			$_POST['editors']=$_SESSION['SESS_USER_ID'];
			$_POST['blocked']="false";
			$_POST['created_by']=$_SESSION['SESS_USER_ID'];
			$_POST['created_on']=date("Y-m-d H:i:s");
			$_POST['edited_by']=$_SESSION['SESS_USER_ID'];
			$_POST['edited_on']=date("Y-m-d H:i:s");
			$ans=_db()->_insertQ1("sys_scripts",$_POST)->_RUN();
			if($ans) {
			    echo $slug;
			} else echo "error: name. Try again later.";
		} else {
			echo "error: Reference Not Found";
		}
		break;
}
?>