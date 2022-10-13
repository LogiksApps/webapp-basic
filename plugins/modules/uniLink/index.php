<?php
if(!defined("ROOT")) exit("Direct Access To This Script Not Allowed");

$slug=_slug("moduleName/refData/refHash");
// printArray($slug);exit();
$pageName=current(explode("/",PAGE));

if($pageName=="modules") $pageName="modules";
else $pageName="popup";

$mSlug=explode("!",$slug['refData']);

if(isset($mSlug[1]) && strlen($mSlug[1])>0) $tabSlug=$mSlug[1];
else $tabSlug=false;

$slug['refData']=$mSlug[0];

$subSlug=explode("@",$slug['refData']);
if(count($subSlug)>1) {
    $slug['refHash']=$subSlug[1];
    $slug['refData']=$subSlug[0];
}

if(is_numeric($slug['refHash'])) $slug['refHash']=md5($slug['refHash']);

$infoview=str_replace(".","/",$slug['refData']);
//$infoFile=;


$infoFiles = [
        APPROOT."misc/forms/{$infoview}.json",
    ];

if(explode("/",$infoview)>1) {
    $infoArr = explode("/",$infoview);
    $infoFiles[]=APPROOT."plugins/modules/{$infoArr[0]}/forms/{$infoArr[1]}.json";
    $infoFiles[]=APPROOT."pluginsDev/modules/{$infoArr[0]}/forms/{$infoArr[1]}.json";
}

foreach($infoFiles as $infoFile) {
    if(file_exists($infoFile)) {
      if(is_numeric($slug['refHash'])) {
          $slug['refHash']=md5($slug['refHash']);
      }
      $url= _link("{$pageName}/infoview/{$slug['refData']}/{$slug['refHash']}");
      
      header("Location:{$url}");
      exit();
    }
}

if(checkModule($infoview)) {
    $pgArr = explode("@",PAGE);
    if(isset($pgArr[1])) {
        $url= _link("{$pageName}/{$infoview}/{$pgArr[1]}");
    } else {
        $url= _link("{$pageName}");
    }
    $getParams = $_GET;
    if(isset($getParams['site'])) unset($getParams['site']);
    $getParams = http_build_query($getParams);
    
    if(strlen($getParams)>1) {
        if(strpos($url,"?")>0) {
            $url.='&'.$getParams;
        } else {
            $url.='?'.$getParams;
        }
        $url = str_replace("&&","&",$url);
    }
    header("Location:{$url}");
} else {
  _log("UNILINK:MISSING:{$infoFile}","activity");
  echo "Required Module Not Found";
}
?>