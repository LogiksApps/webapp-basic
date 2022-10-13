<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getTemplateList")) {
	function getTemplateList($category) {
		$data=_db()->_selectQ("do_templates","id,slug,title,category,tags,blocked,created_by,created_on,edited_by,edited_on,subject,debug")
							->_where(["category"=>$category,"blocked"=>"false"])->_GET();
		return $data;
	}
  function getTemplate($slugID,$category=null) {
		if($category==null) {
			$data=_db()->_selectQ("do_templates","id,slug,title,category,tags,blocked,created_by,created_on,edited_by,edited_on,debug,subject,body as txt,style,sqlquery,params,xtras")
							->_where(["slug"=>$slugID])->_GET();
		} else {
			$data=_db()->_selectQ("do_templates","id,slug,title,category,tags,blocked,created_by,created_on,edited_by,edited_on,debug,subject,body as txt,style,sqlquery,params,xtras")
							->_where(["slug"=>$slugID,"category"=>$category])->_GET();
		}
		
    if(isset($data[0])) {
			$data[0]['txt']=decodeTemplate($data[0]['txt']);
			$data[0]['style']=decodeTemplate($data[0]['style']);
			
			$data[0]['sqlquery']=str_replace("%3B",";",$data[0]['sqlquery']);
			$data[0]['params']=str_replace("%3B",";",$data[0]['params']);
			$data[0]['xtras']=str_replace("%3B",";",$data[0]['xtras']);
			return $data[0];
		}
    else return false;
  }
  
  function getProcessedTemplate($slugID,$category=null) {
		$html=false;
		$data=getTemplate($slugID,$category);
		
		if(isset($data)) {
			$html="";
			
			$tmplSubject=$data['subject'];
			$tmplStyle=$data['style'];
			$tmplBody=$data['txt'];
			$tmplSQL=$data['sqlquery'];
			$tmplParams=$data['params'];
			
			//exit($tmplBody);
			
			setConfig("TEMPLATE_ALLOW_PHP",true);
			$engine=LogiksTemplate::getEngineForExtension(".tpl");
			$lt=new LogiksTemplate($engine);
			
			$_ENV['TENGINE']=$lt;

			//$tmplBody=urldecode($tmplBody);

			$tmplSQL=_replace($tmplSQL);
			if(strlen($tmplSQL)>0) $lt->loadSQL($tmplSQL);

			$templID=md5($data['id'].SITENAME);
			$templFile = _dirTemp("template")."{$templID}.tpl";

			if(!file_exists($templFile)) {
				if(!is_dir(dirname($templFile))) mkdir(dirname($templFile),0777,true);
				file_put_contents($templFile,$tmplBody);
			} elseif((strtotime($data['edited_on'])-filemtime($templFile))>0) {
				file_put_contents($templFile,$tmplBody);
			}
			//file_put_contents($templFile,$tmplBody);
			//exit($templFile);

			if(strlen($tmplParams)>0) {
				$tmplParams=json_decode($tmplParams,true);
				if($tmplParams==null) $tmplParams=[];
			} else {
				$tmplParams=[];
			}
			
			ob_start();
			if($data['debug']=="true") {
				file_put_contents($templFile,$tmplBody);
				printArray($data);
				echo "<hr>";
				printArray($lt->getEngine());
				echo "<hr>";
			}
			
			echo "<div class='wrapper'>";
			echo "<style>{$tmplStyle}</style>";
			$lt->printTemplate($templFile,$tmplParams);
			echo "</div>";
			
			$html=ob_get_contents();
			ob_clean();
		}
		
		return $html;
  }
	
	function templateParseToParams($text) {
		preg_match_all("/#[a-zA-Z0-9-_]+#/", $text, $matches);
// 		printArray($matches);
		if(isset($matches[0])) return $matches[0];
		return [];
	}
	
	function decodeTemplate($data) {
    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
       return base64_decode($data);
    } else {
       return $data;
    }
	}
	function encodeTemplate($data) {
		return base64_encode($data);
	}
}
?>