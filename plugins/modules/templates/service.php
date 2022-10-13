<?php
if(!defined('ROOT')) exit('No direct script access allowed');
include_once __DIR__."/api.php";

switch($_REQUEST["action"]) {
	case "list":
		$data=_db()->_selectQ("do_templates","id,slug,title,category,tags,subject,viewers,editors,blocked,created_by,created_on,edited_by,edited_on");//published,published_on,published_by,
		$data=$data->_GET();
		//printArray($data);
		$fData=["NoGroup"=>[]];
		foreach($data as $a=>$b) {
			if($b['category']==null || strlen($b['category'])<=0) $b['category']="NoGroup";
			if(!isset($fData[$b['category']])) $fData[$b['category']]=[];
			$fData[$b['category']][$b['id']]=$b;
		}
		printServiceMSG($fData);
		break;
	case "fetch":case "fetchBody":
		if(isset($_POST['slug'])) {
			$data=getTemplate($_POST['slug']);
			//	printArray($data);
			
			if(isset($data)) echo $data["txt"];
			else echo "error: Template Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "fetchStyle":
		if(isset($_POST['slug'])) {
			$data=getTemplate($_POST['slug']);
			
			if(isset($data)) echo $data["style"];
			else echo "error: Template Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "fetchParams":
		if(isset($_POST['slug'])) {
			$data=getTemplate($_POST['slug']);
			
			if(isset($data)) {
				$data=$data["params"];
				$data=json_decode($data,true);
				if($data==null) echo "{}";
				else echo json_encode($data, true);
			} else echo "error: Template Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "fetchSQL":
		if(isset($_POST['slug'])) {
			$data=getTemplate($_POST['slug']);
			
			if(isset($data)) echo $data["sqlquery"];
			else echo "error: Template Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "preview":
		if(isset($_POST['slug'])) {
			$data=getTemplate($_POST['slug']);
			
			if(isset($data)) {
				if(strlen(strip_tags($data["txt"]))<=2) {
					echo "<br><h2 align=center class='metaTitle'>Not template content yet</h2>";
				} else {
					echo "<h1 class='metaTitle'>{$data['title']}</h1><hr>";
					echo $data["txt"];
				}
			} else echo "error: Template Not Found";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "properties":
		if(isset($_POST['slug'])) {
			$data=_db()->_selectQ("do_templates","id,slug,title,category,subject,tags,blocked,debug,viewers,editors,created_by,created_on,edited_by,edited_on,body")
								->_where(["slug"=>$_POST['slug']])->_GET();
			
			if(!isset($data[0])) echo "error: Template Not Found";
			else {
				$data=$data[0];
				include __DIR__."/comps/properties.php";
			}
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "rename":
		if(isset($_POST['slug'])) {
			
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "delete":
		if(checkUserRoles("setup","templates","delete") || $_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_RANGE) {
			if(isset($_POST['slug']) && strlen($_POST['slug'])>0) {
				$slugs=explode(",",$_POST['slug']);
				$ans=_db()->_deleteQ("do_templates")->_whereIn("slug",$slugs)->_RUN();

				if($ans) {
				    loadModules(["bizflow","bizlogger"]);
						echo "Requested records deleted successfully.";
				} else {
					echo "error: Sorry, requested records could not be deleted.";
				}
			} else {
				echo "error: Reference Not Found";
			}
		} else {
				echo "error: Sorry, you do not have required privilege to delete this template.";
		}
		break;
	case "saveProps":
		if(isset($_POST['slug'])) {
			$slug=$_POST['slug'];
			unset($_POST['slug']);
			
			if($_SESSION['SESS_PRIVILEGE_ID']>ADMIN_PRIVILEGE_RANGE) {
				$dataArticle=_db()->_selectQ("do_templates","*")
					->_where(["slug"=>$slug,"created_by"=>$_SESSION['SESS_USER_ID']]);
				$dataArticle=$dataArticle->_GET();
				if(count($dataArticle)<=0) {
						echo "error: Only author can save/update this template";
						return;
				}
			}
			
			if(isset($_POST['body'])) unset($_POST['body']);
			if(isset($_POST['style'])) unset($_POST['style']);
			if(isset($_POST['sqlquery'])) unset($_POST['sqlquery']);
			if(isset($_POST['params'])) unset($_POST['params']);
			if(isset($_POST['xtras'])) unset($_POST['xtras']);
			
			$_POST['edited_by']=$_SESSION['SESS_USER_ID'];
			$_POST['edited_on']=date("Y-m-d H:i:s");
			$ans=_db()->_updateQ("do_templates",$_POST,["slug"=>$slug])->_RUN();
			
			if($ans) {
			    //loadModules(["bizflow","bizlogger"]);
			    loadModules(["bizlogger"]);
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
			
			$dataArticle=_db()->_selectQ("do_templates","*")
					->_where(["slug"=>$slug]);//,"created_by"=>$_SESSION['SESS_USER_ID']
			$dataArticle=$dataArticle->_GET();
			
			if(!isset($dataArticle[0])) {
				echo "error: Template not found";
				return;
			}
			$dataArticle=$dataArticle[0];
			
			if($_SESSION['SESS_PRIVILEGE_ID']>ADMIN_PRIVILEGE_RANGE) {
				$dataArticle['editors']=explode(",",$dataArticle['editors']);
				if(!in_array($_SESSION['SESS_USER_ID'],$dataArticle['editors'])) {
						echo "error: Only allowed editors can save/update this template";
						return;
				}
			}
			
			//$_POST['text']=stripslashes(str_replace("\\r\\n","",$_POST['text']));
			//$_POST['text']=stripslashes(str_replace("&amp%3B","&amp;",$_POST['text']));
			//$_POST['text']=str_replace("%3B",";",$_POST['text']);
			
			switch(strtoupper($_POST['type'])) {
				case "BODY":
					$data['body']=base64_encode($_POST['text']);
					break;
				case "STYLE":
					$data['style']=base64_encode($_POST['text']);
					break;
				case "SQLQUERY":
					$data['sqlquery']=$_POST['text'];
					break;
				case "PARAMS":
					$data['params']=$_POST['text'];
					$jsonData=json_decode($data['params'],true);
					if($jsonData==null) {
						echo "error: Params should be formatted JSON data";
						return;
					}
					break;
				case "XTRAS":
					$data['xtras']=$_POST['text'];
					break;
				default:
					echo "error: Mode/Type not supported. Try again later";
					return;
			}
			
			if(isset($_POST['subject'])) {
				$data['subject']=$_POST['subject'];
			}
			
			$data['edited_by']=$_SESSION['SESS_USER_ID'];
			$data['edited_on']=date("Y-m-d H:i:s");
			$ans=_db()->_updateQ("do_templates",$data,["slug"=>$slug])->_RUN();
			
			if($ans) {
					$templID=md5($dataArticle['id'].SITENAME);
					$templFile = _dirTemp("template")."{$templID}.tpl";
					if(file_exists($templFile)) unlink($templFile);
					
			    loadModules(["bizflow","bizlogger"]);
					echo "Successfully updated the template";
			} else echo "error: Update failed. Try again later";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "create":
		if(isset($_POST['slug'])) {
			if(!checkUserRoles("setup","templates","create"))  {
				echo "error: Sorry, you do not have required privilege to create template.";
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
			
			$data=_db()->_selectQ("do_templates","count(*) as cnt")
							->_where(["slug"=>$_POST['slug']])->_GET();
			
			if(isset($data[0]) && $data[0]['cnt']>0) {
				echo "error: The defined Code already exists.";
				return;
			}
			
			$_POST['slug']=$slug;
			$_POST['title']=toTitle(str_replace("_"," ",$_POST['slug']));
			$_POST['category']=$category;
			$_POST['tags']="";
			$_POST['subject']=$_POST['title'];
			$_POST['body']="";
			$_POST['sqlquery']="";
			$_POST['params']="";
			$_POST['xtras']="";
			
			$_POST['viewers']="*";
			$_POST['editors']=$_SESSION['SESS_USER_ID'];
			
			$_POST['blocked']="false";
			$_POST['created_by']=$_SESSION['SESS_USER_ID'];
			$_POST['created_on']=date("Y-m-d H:i:s");
			$_POST['edited_by']=$_SESSION['SESS_USER_ID'];
			$_POST['edited_on']=date("Y-m-d H:i:s");
		    $ans=_db()->_insertQ1("do_templates",$_POST)->_RUN();
			
			if($ans) {
			    loadModules(["bizflow","bizlogger"]);
			    echo $slug;
			} else echo "error: Create failed. Try again later.";
		} else {
			echo "error: Reference Not Found";
		}
		break;
	case "demoMail":
		if(isset($_REQUEST['slug'])) {
			
			$data=getTemplate($_REQUEST['slug']);
			
			if(isset($data)) {
				$tmpl=$data['txt'];
				if(strlen($tmpl)>0) {
				    $tmpl=preg_replace("/#[a-zA-Z0-9-_]+#/", 'DUMMY', $tmpl);
				    $templateData=_templateData($tmpl);
				    // echo $templateData;
				    $a=_msg()->send($_SESSION['SESS_USER_EMAIL'],"Demo Mail By {$_SESSION['SESS_USER_EMAIL']}",$templateData);
				    if($a) {
				        loadModules(["bizflow","bizlogger"]);
				        echo "Demo Mail Sent Successfully @ {$_SESSION['SESS_USER_EMAIL']}";
				    } else {
				        echo "Error Sending Demo Mail";
				    }
				} else {
				    echo "Template Not Published Yet";
				}
			} else {
			    echo "Template Not Found";
			}
	    } else {
				echo "Reference Not Found";
			}
		break;
	case "printpreview":
		if(isset($_REQUEST['slug'])) {
// 			setConfig("TEMPLATE_ALLOW_PHP",true);
// 			include_once __DIR__."/toolbar.php";
			if(isset($_REQUEST['ref_id']) && is_numeric($_REQUEST['ref_id'])) {
				$_REQUEST['ref_id']=md5($_REQUEST['ref_id']);
			}
  		echo getProcessedTemplate($_REQUEST['slug']);
		} else {
			echo "Reference Not Found";
		}
		break;
		
	case "print":
		if(isset($_REQUEST['slug']) && isset($_REQUEST['ref_id']) && isset($_REQUEST['ref_src'])) {
// 			setConfig("TEMPLATE_ALLOW_PHP",true);
			include_once __DIR__."/toolbar.php";
  		echo getProcessedTemplate($_REQUEST['slug']);
		} else {
			echo "Reference Not Found";
		}
		break;
}
?>