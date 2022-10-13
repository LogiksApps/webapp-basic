<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

switch($_REQUEST["action"]) {
  case "savesettings":
    if(isset($_POST['value'])) {
      if(!isset($_POST['type'])) $_POST['type']="string";
      
      printArray($_POST);
    } else {
      printServiceMsg([]);
    }
    break;
  case "updatefield":
    if(isset($_POST['keyspace']) && count($_POST)>1) {
      $dbTable=getBizDBTable($_POST['keyspace']);
      unset($_POST['keyspace']);
      
      if($dbTable) {
        $postData=$_POST;
        //validate postData
        
        
        echo _db()->_insertQ1($dbTable,$postData)->_SQL();
        
        //generateActivity
      } else {
        printServiceMsg("Error in updating field, you may not have permission");
      }
    } else {
      printServiceMsg("Error in finding field");
    }
    break;
}
function getBizDBTable($keySpace) {
  //check permission
  //find form file
  //find dbTable
  //find fields
  //return dbTable;
  return $keySpace;
}
?>