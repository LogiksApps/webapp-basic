<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(defined("CMS_SITENAME")) $_REQUEST['REFSITE']=CMS_SITENAME;
else $_REQUEST['REFSITE']=SITENAME;

include_once __DIR__."/index.php";
?>