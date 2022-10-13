<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(defined("CMS_SITENAME")) $_REQUEST['REFSITE']=CMS_SITENAME;
else $_REQUEST['REFSITE']=SITENAME;

switch($_REQUEST['action']) {
    case "scriptlist":
        $dir = ROOT."apps/{$_REQUEST['REFSITE']}/".PCRON_FOLDER;
        $list = [];
        if(is_dir($dir)) {
            $ds = scandir($dir);
            array_shift($ds);array_shift($ds);
            
            $list = $ds;
        }
        foreach($list as $a=>$b) {
            $list[$a] = str_replace(".php","",$b);
        }
        printServiceMsg($list);
    break;
    case "savescript":
        if(isset($_POST['scriptcode']) && $_POST['file']) {
            $scriptFile = ROOT."apps/{$_REQUEST['REFSITE']}/".PCRON_FOLDER."{$_POST['file']}.php";
            $a = file_put_contents($scriptFile,$_POST['scriptcode']);
            if($a>0) printServiceMsg("success");
            else printServiceMsg("Error saving script file");
        } else {
            printServiceMsg("Wrong Data Format");
        }
    break;
}
?>