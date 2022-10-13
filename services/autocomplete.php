<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_REQUEST['refid'])) {
  printServiceMsg([]);
  return;
}

switch($_REQUEST["action"]) {
  case "tasksForStaff":
    $cols="id,name";
    $data=_db()->_selectQ("task_tbl",$cols,["blocked"=>"false","profile_id"=>$_REQUEST['refid']])->_get();
    $fData=["Select Task"=>""];
    foreach ($data as $key => $row) {
      $fData[$row['name']]=$row['id'];
    }
    printServiceMsg($fData);
	break;
}
?>
