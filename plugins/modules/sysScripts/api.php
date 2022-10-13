<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("runSysScript")) {
    
    function runAllSysScripts($callReference, $category, $params = [], $consecutive=false) {
        $scriptList = getScriptList($category);
        if($consecutive) {
            $out = $params;
            
            if($scriptList) {
                foreach($scriptList as $script) {
                    $a = runSysScript($callReference,$script['slug'],$out);
                    if($a) $out = $a;
                }
            }
        } else {
            $out = [];
            if($scriptList) {
                foreach($scriptList as $script) {
                    $a = runSysScript($callReference,$script['slug'],$params);
                    $out[$script['slug']] = $a;
                }
            }
        }
        return $out;
    }
    
    function runSysScript($callReference, $slugID, $params = []) {
        $scriptData = getScript($slugID);
        
        if($scriptData) {
            if(strlen($scriptData['script'])>0) {
                if(!is_array($params)) $params = [];
                if(strlen($scriptData['script_params'])>0) {
                    $scriptData['script_params'] = json_decode($scriptData['script_params'],true);
                }
                if($scriptData['script_params']==null) $scriptData['script_params'] =[];
                
                //Final Params
                $params = array_merge($scriptData['script_params'],$params);
                if($params && is_array($params)) {
                    foreach($params as $a=>$b) {
                        $_REQUEST[$a] = _replace($b);
                    }
                }
                
                //Final Script
                $script =  _replace($scriptData['script'],'%');
                
                //Write the code here to run the script
                $a = eval($script);
                
                $alog = $a;
                if(is_array($alog)) {
                    $alog = json_encode($alog);
                }
                
                $dated = date("Y-m-d H:i:s");
                _db()->_insertQ1("log_scripts",[
                        "guid"=>$_SESSION['SESS_GUID'],
                        "groupuid"=>$_SESSION['SESS_GROUP_NAME'],
                        "reference"=>$callReference,
                        "script_refid"=>$slugID,
                        "output_log"=>$alog,
                        "params"=>json_encode($params),
                        "created_by"=>$_SESSION['SESS_USER_ID'],
                        "created_on"=>$dated,
                        "edited_by"=>$_SESSION['SESS_USER_ID'],
                        "edited_on"=>$dated,
                    ])->_RUN();
                
                return $a;
            } else {
                return false;
            }
        }
        
		return false;
  	}
  	
  	function getScriptParams($slugID) {
  	    $data=_db()->_selectQ("sys_scripts","id,slug,script_params,draft_params")->_where(["slug"=>$slugID,"blocked"=>"false"])->_GET();
		
		if($data[0]) {
		    if(strlen($scriptData['script_params'])>0) {
		        return json_decode($data[0]['script_params'],true);
		    } else {
		        return [];
		    }
		} else {
		    return [];
		}
  	}
    
	/**
	 * @author : snehlata@smartinfologiks.com
	 * Used to list all Script data  according to category.
	 * @input : category
	 * @return boolean
	 */
	function getScriptList($category) {
		$data=_db()->_selectQ("sys_scripts","id,slug,name,category,tags,blocked,created_by,created_on,edited_by,edited_on")
							->_where(["category"=>$category,"blocked"=>"false"])->_GET();
		return $data;
	}
	/**
	 * @author : snehlata@smartinfologiks.com
	 * Used to list all Script data  according to slug or (slug and category).
	 * @input : slug or (slug and category).
	 * @return boolean
	 */	
  	function getScript($slugID,$category=null) {
  	    if(is_numeric($slugID)) {
		    $where = ["id"=>$slugID];
		} else {
		    $where = ["slug"=>$slugID];
		}
		
		if($category!=null && strlen($category)>1) {
			$where['category'] = $category;
		}
		
		$data=_db()->_selectQ("sys_scripts","id,slug,name,category,tags,blocked,created_by,created_on,edited_by,edited_on,script_code as txt,draft_code,script_params,draft_params")
							->_where($where)->_GET();
		
        if(isset($data[0])) {
			$data[0]['script']=decodeScript($data[0]['txt']);
			$data[0]['script_params']=str_replace("%3B",";",$data[0]['script_params']);
			$data[0]['draft']=decodeScript($data[0]['draft_code']);
			$data[0]['draft_params']=str_replace("%3B",";",$data[0]['draft_params']);
			
			$data[0]['created_on']=$data[0]['created_on'];
			$data[0]['created_by']=$data[0]['created_by'];
			$data[0]['publish_status']="true";
			return $data[0];
		}
        else return false;
  	}

	/**
	 * @author : snehlata@smartinfologiks.com
	 * Used to Script Parse To Params
	 * @input : data
	 * @return boolean
	 */		
	function scriptParseToParams($text) {
		preg_match_all("/#[a-zA-Z0-9-_]+#/", $text, $matches);
// 		printArray($matches);
		if(isset($matches[0])) return $matches[0];
		return [];
	}
	/**
	 * @author : snehlata@smartinfologiks.com
	 * Used to decode Script with base64_decode
	 * @input : data
	 * @return boolean
	 */		
	function decodeScript($data) {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
           return base64_decode($data);
        } else {
           return $data;
        }
	}
	/**
	 * @author : snehlata@smartinfologiks.com
	 * Used to encode Script with base64_encode
	 * @input : data
	 * @return boolean
	 */		
	function encodeScript($data) {
		return base64_encode($data);
	}
}
?>