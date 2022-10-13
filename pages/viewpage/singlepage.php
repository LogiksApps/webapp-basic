<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug=_slug();

if(isset($slug["module"]) && strlen($slug["module"])>0 && $slug["module"]!="dashboard") {
    if(checkModule($slug["module"])) {
      _pageVar("MODULESRC",$slug["module"]);
	} else {
		//trigger_logikserror("Sorry, Module '{$slug["module"]}' not found.");
	}
} else {
	_pageVar("MODULESRC",getConfig("PAGE_DASHBOARD"));
}
?>