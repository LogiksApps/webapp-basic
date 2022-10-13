<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("setupDGEnviroment")) {
	include_once __DIR__."/api.php";
	
	function checkEnviroment() {
		if(!isset($_SESSION['ENV_CHECK_DONE'])) {
			$reqModules=getConfig("REQUIRED_MODULES");
			if(strlen($reqModules)>0) {
				$reqModules=explode(",",$reqModules);
				$reqModules=array_flip(array_unique($reqModules));
				
				$arrError=[];
				foreach($reqModules as $m=>$s) {
					$s=checkModule($m);
					if(!$s) {
						$arrError[]=$m;
					}
					$reqModules[$m]=($s===false)?false:true;
				}
				if(count($arrError)>0) {
					echo "<h3>Error finding the below modules, please install them first.</h3><ul>";
					foreach($arrError as $m) {
						echo "<li>{$m}</li>";
					}
					echo "</ul>";
					exit();
				} else {
					$_SESSION['ENV_CHECK_DONE']=true;
				}
			} else {
				$_SESSION['ENV_CHECK_DONE']=true;
			}
		}
	}
	
	function setupDGEnviroment() {
		if(!defined("ADMIN_PRIVILEGE_RANGE")) define("ADMIN_PRIVILEGE_RANGE",5);
		
        if(defined("PAGE") && in_array(PAGE,["welcome","login","logout","logout.php"])) {
          return true;
        }
        if(defined("SERVICE_ROOT")) {
            if(!in_array($_REQUEST['scmd'],["auth"])) {
                setupUserEnvInfo();
            }
            return true;
        }
    
		checkEnviroment();
		
		if(isset($_SESSION['SESS_PRIVILEGE_ID'])) {
			$_SESSION['SESS_ACCESS_LEVEL']=$_SESSION['SESS_PRIVILEGE_ID'];
			
			if(!isset($_SESSION['SESS_GROUP_NAME']) || strlen($_SESSION['SESS_GROUP_NAME'])<=0) {
				$_SESSION['SESS_GROUP_NAME']="hq";
			}
			
			checkGenericPermissions();
			
			setupUserEnvInfo();
		}
		
		$_SESSION['SESS_CURRENT_YEAR'] = date("Y");
		$_SESSION['SESS_CURRENT_MONTH'] = date("m");
		$_SESSION['SESS_CURRENT_MONTH_NAME'] = date("M");
		$_SESSION['SESS_CURRENT_DATE'] = date("Y-m-d");
		$_SESSION['SESS_CURRENT_DAY'] = date("D");
	}
	
	function checkGenericPermissions() {
		if(isset($_SESSION["ROLEMODEL"])) unset($_SESSION["ROLEMODEL"]);
		return;
		if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_RANGE) {
			return true;
		}
			
		if(defined("SERVICE_ROOT")) {
			return true;
		} else {
			$slug=_slug("pg/mod/type/status");
			if(in_array($slug['pg'],["favicon.ico"])) {
				return true;
			}
			if(in_array($slug['pg'],["welcome"])) {
				return false;
			}
			if(!in_array($slug['pg'],["modules","popup","favicon.ico"])) {
				return true;
			}
			if(in_array($slug['mod'],["myProfile","mySettings"])) {//"myAccounts",
				return true;
			}
			if(strlen($slug['type'])>0) {
                $xtype=current(explode(".",$slug['type']));
                if(in_array($xtype,["my"])) {
                  return true;
                }
            }
			
// 			printArray($slug);echo $_SESSION['SESS_GUID'];
			$access=false;
			$errorMsg="Current Module/resource/URI";
			if(strlen($slug['type'])>0) {
				$typeArr=explode(".",$slug['type']);
				if(in_array($typeArr[0],["my","me"])) {
					return true;
				}
				
				$roleStr=$slug['type'];
				$errorMsg="{$typeArr[0]}>{$roleStr} {$slug['mod']}";
				
				if(in_array($slug['mod'],["reports"])) {
					// $roleStr="Allow viewing - {$slug['type']}";
					$access=checkUserRoles($typeArr[0],$roleStr,"ACCESS");
				} elseif(in_array($slug['mod'],["infoview"])) {
					// $roleStr="Allow detailing - {$slug['type']}";
					$access=checkUserRoles($typeArr[0],$roleStr,"DETAILS");
				} elseif(in_array($slug['mod'],["forms"])) {
					if($slug['status']=="new" || $slug['status']=="create") {
					// 	$roleStr="Allow creating - {$slug['type']}";
						$access=checkUserRoles($typeArr[0],$roleStr,"NEW");
					} else {
					// 	$roleStr="Allow updating - {$slug['type']}";
						$access=checkUserRoles($typeArr[0],$roleStr,"EDIT");
					}
				} else {
					$module=explode(".",$slug['mod']);
					if(count($module)==1) {
						$roleStr="{$slug['mod']}.MAIN";
					} else {
						$roleStr=$slug['mod'];
					}
					$typeArr[0]=$module[0];
					
					if(in_array(strtolower($slug['type']),["new","edit","delete","update"])) {
						$errorMsg="{$typeArr[0]}>$roleStr>{$slug['type']}";
						$access=checkUserRoles($typeArr[0],$roleStr,strtoupper($slug['type']));
					} elseif(strlen($slug['status'])>0 && strlen($slug['status'])<20) {
						$errorMsg="{$typeArr[0]}>$roleStr>{$slug['status']}";
						$access=checkUserRoles($typeArr[0],$roleStr,strtoupper($slug['status']));
					} else {
						$errorMsg="{$typeArr[0]}>$roleStr>access";
						$access=checkUserRoles($typeArr[0],$roleStr,"ACCESS");
					}
				}
			} else {
				$module=explode(".",$slug['mod']);
				if(count($module)==1) {
					if(strlen($slug['type'])>0) {
						$roleStr=$slug['type'];
					} else {
						$roleStr="MAIN";
					}
				} else {
					$roleStr=$slug['mod'];
				}
				$errorMsg="{$module[0]}>$roleStr";
				$access=checkUserRoles($module[0],$roleStr,"ACCESS");
			}
// 			printArray([$slug]);
			if($access) {
				return true;
			} else {
				trigger_logikserror("Sorry, <strong>{$errorMsg}</strong> is not accessible to you. Contact Admin!",E_LOGIKS_ERROR,401);
			}
		}
	}
	
	setupDGEnviroment();
	updateBizEnviroment();
}
?>
