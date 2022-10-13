<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if (!function_exists("getBizDefaultData")) {
    include APPROOT . "/plugins/modules/companyConfig/api.php";
    
    //Provides the default array for all BizSuite tables
    function getBizDefaultData($insertArr=false) {
        $date=date("Y-m-d H:i:s");
        if($insertArr) {
            return [
                "guid"=>$_SESSION['SESS_GUID'],
                "created_by"=>$_SESSION['SESS_USER_ID'],
                "created_on"=>$date,
                "edited_by"=>$_SESSION['SESS_USER_ID'],
                "edited_on"=>$date,
            ];
        } else {
            return [
                "guid"=>$_SESSION['SESS_GUID'],
                "edited_by"=>$_SESSION['SESS_USER_ID'],
                "edited_on"=>$date,
            ];
        }
    }
    
    // ref_group :: will take care of the grouping logics for multi level data
    // ref_rule :: will take care of custom queries
    // ref_src, ref_id : will take care for src and id columns in target tables
    // ref_col, ref_id will take care with col=id format
    // ref_type : is for future needs
    function processRefRules($config) {
        $where=[];
        $subData=[];
        $subConfig=[];
        
        // if(isset($config['view'])) {
        //     $subConfig[]=processRefRules($config['view']);
        // }
        // if(isset($config['edit'])) {
        //     $subConfig[]=processRefRules($config['edit']);
        // }
        if(isset($config['view'])) {
            $config=$config['view'];
        }
        
        if(isset($config['ref_subquery'])) {
            if(!is_array($config['ref_subquery'])) {
                $config['ref_subquery']=[$config['ref_subquery']];
            }
            foreach($config['ref_subquery'] as $k=>$q) {
                if(is_array($q) && isset($q["table"]) && isset($q["where"]) && isset($q["cols"])) {
                    if(!isset($q["sortby"])) $q["sortby"]="edited_on DESC";
                    if(!isset($q["limit"])) $q["limit"]="100";
                    if(!is_array($q["where"])) {
                        $q["where"]=_replace($q["where"]);
                    } else {
                        foreach($q["where"] as $a=>$b) {
                            $q["where"][$a]=_replace($b);
                        }
                    }
                    $subData["SUBQUERY{$k}"]=_db()->_selectQ($q["table"],$q["cols"],$q["where"])->_orderBy($q["sortby"])->_limit($q["limit"])->_GET();
                } else {
                    $subData["SUBQUERY{$k}"]=_db()->_raw(_replace($q))->_GET();
                }
            }
            
        }
        if(count($subData)>0) {
            foreach($subData as $a=>$b) {
                if(!$b) continue;
                $rs=[];
                foreach($b as $c=>$d) {
                    $k=array_keys($d);
                    if(!is_numeric($d[$k[0]])) {
                        $rs[]="'{$d[$k[0]]}'";
                    } else {
                        $rs[]=$d[$k[0]];
                    }
                }
                if(count($rs)>0) {
                  $_REQUEST[$a]="(".implode(", ",$rs).")";
                } else {
                    $_REQUEST[$a]="()";
                }
            }
        }
        if(isset($config['ref_rule'])) {
            $where=$config['ref_rule'];
            
            if(is_string($where)) $where=_replace($where);
            elseif(is_array($where)) {
                foreach($where as $a=>$b) {
                    $where[$a]=_replace($b);
                }
            }
        } elseif(isset($config['ref_id'])) {
            $config['ref_id']=_replace($config['ref_id']);
            
            if(isset($config['ref_col'])) {
                $where=[
                        $config['ref_col']=>$config['ref_id'],
                        // "ref_src"=>$config['ref_src'],
                    ];
            } else {
                $where=[
                    "ref_id"=>$config['ref_id'],
                    // "ref_src"=>$config['ref_src'],
                ];
            }
            
            if(isset($config['ref_src'])) {
                $where['ref_src']=_replace($config['ref_src']);
            }
            
            if(isset($config['ref_type'])) {
                $where['ref_type']=_replace($config['ref_type']);
            }
        }
        // if($subConfig && count($subConfig)>0) {
        //     if(is_string($where) && strlen($where)>0) {
        //         $where=[$where=>"RAW"];
        //     }
        //     foreach($subConfig as $q) {
        //         if(is_array($q)) {
        //             $where[]=$q;
        //         } elseif(is_string($q)) {
        //             $where[]=[$q=>"RAW"];
        //         }
        //     }
        // }
        return $where;
    }
    
    function loadBizSettings() {
        if(!isset($_SESSION['COMP_ID'])) return;
        
        $settingsData=_db()->_selectQ("my_company_settings","*",["company_id"=>$_SESSION['COMP_ID']])->_GET();
        if(count($settingsData)>0) {
          foreach($settingsData as $f=>$v) {
            $_SESSION["PARAMS_".strtoupper($v['param_title'])]=$v['param_value'];
          }
          updateBizEnviroment();
        }
    }
    
    function updateBizEnviroment() {
	    foreach($_SESSION as $a=>$b) {
	        if(!is_array($b)) {
	            if(substr($a,0,7)=="PARAMS_") {
	                setConfig(substr($a,7),$b);
	            }
	        }
	    }
	    if(isset($_SESSION['COMP_CURRENCY'])) {
	        setConfig("CURRENCY",$_SESSION['COMP_CURRENCY']);
        }
        if(isset($_SESSION['COMP_DATE_FORMAT'])) {
    	    setConfig("DATE_FORMAT",$_SESSION['COMP_DATE_FORMAT']);
        }
        if(isset($_SESSION['COMP_TIME_FORMAT'])) {
    	    setConfig("TIME_FORMAT",$_SESSION['COMP_TIME_FORMAT']);
        }
        if(isset($_SESSION['COMP_TIMEZONE'])) {
    	    setConfig("TIMEZONE",$_SESSION['COMP_TIMEZONE']);
        }
        if(isset($_SESSION['COMP_TIMEZONE_UTC'])) {
    	    setConfig("TIMEZONE_UTC",$_SESSION['COMP_TIMEZONE_UTC']);
	    }
	    
	   // echo getConfig("DATE_FORMAT");
	   // printArray($_SESSION);
	   // exit();
	}
    
    function getGroupDropdown($autoSelect=true) {
		$html=[];
        $sql=_db(true)->_selectQ(_dbTable("users_group",true),"*");
		
		$sqlData=$sql->_GET();

		if($autoSelect) {
				foreach($sqlData as $p) {
						if($p['group_name']==$_SESSION['SESS_GROUP_NAME'])
										$html[]="<option value='{$p['group_name']}' selected>{$p['group_name']}</option>";
								else
										$html[]="<option value='{$p['group_name']}'>{$p['group_name']}</option>";
						}
		} else {
				foreach($sqlData as $p) {
						$html[]="<option value='{$p['group_name']}'>{$p['group_name']}</option>";
						}
		}

		return implode("",$html);
    }
    
    function getPrivilegeDropdown($id=false) {
        $list=getPrivilegeList();
        $html=[];
        if($id) {
            foreach($list as $p) {
                $html[]="<option value='{$p['id']}'>{$p['name']}</option>";
            }
        } else {
            foreach($list as $p) {
                $html[]="<option value='{$p['name']}'>{$p['name']}</option>";
            }
        }
        return implode("",$html);
    }
    
    function getPrivilegeQuery() {
        return "(access_rule='privilege' AND privilegeid>={$_SESSION['SESS_ACCESS_LEVEL']}) OR (access_rule='access' AND access_level>={$_SESSION['SESS_ACCESS_LEVEL']})";
    }
    
    function getMyAvatar() {
        if(isset($_SESSION['SESS_USER_AVATAR']) && strlen($_SESSION['SESS_USER_AVATAR'])>0) {
            return _service("avatar")."&avatar={$_SESSION['SESS_USER_AVATAR']}";
        }
        return loadMedia('images/user.png');
    }
    
	function getProfileID($idHash){
		$sql = _db()->_selectQ("profiletbl", "id", ["md5(id)" => $idHash])->_get();
		if (count($sql) > 0){
			return $sql[0]['id'];
		}
		return 0;
	}
	
	function setupUserEnvInfo() {
	    if(!isset($_SESSION['SESS_PRIVILEGE_ID'])) return false;
	    
	    $_SESSION['SESS_ACCESS_LEVEL']=$_SESSION['SESS_PRIVILEGE_ID'];
	    
	    if(!isset($_SESSION['SESS_GROUP_NAME']) || strlen($_SESSION['SESS_GROUP_NAME'])<=0) {
			$_SESSION['SESS_GROUP_NAME']="hq";
		}
		
		
// 		printArray($_SESSION);
    }
}

?>

 
