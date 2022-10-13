<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();

$verifyConfig=[];

if(defined("REQUIRED_MODULES")) {
	$verifyConfig['REQUIREDMODULES']=explode(",", REQUIRED_MODULES);
}

$verifyCfg= APPROOT."config/verify.json";
if(file_exists($verifyCfg)) {
	$verifyConfig=json_decode(file_get_contents($verifyCfg),true);
	if(!isset($verifyConfig['REQUIREDCOLS'])) {
		$verifyConfig['REQUIREDCOLS']=[];
	}
	if(!isset($verifyConfig['EXCEPTIONTABLEKEYS'])) {
		$verifyConfig['EXCEPTIONTABLEKEYS']=[];
	}
}

switch($_REQUEST["action"]) {
	case "verifydb":
		$ans=verifyDB($verifyConfig['REQUIREDCOLS'],$verifyConfig['EXCEPTIONTABLEKEYS']);
		if(count($ans)<=0) {
			printServiceMsg("All OK");
		} else {
			printServiceMsg($ans);
		}
	break;
	case "verifyfs":
		$ans=verifyFS();
		if(count($ans)<=0) {
			printServiceMsg("All OK");
		} else {
			printServiceMsg($ans);
		}
	break;
	case "compare":
		if(isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
			$ans=compareTables("app",$_REQUEST['src']);
			printServiceMsg($ans);
		} else {
			printServiceMsg("Source Not Defined");
		}
	break;
	case "comparelist":
		if(isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
			$ans=compareLists("app",$_REQUEST['src']);
			printServiceMsg($ans);
		} else {
			printServiceMsg("Source Not Defined");
		}//select groupid,count(*) from do_lists GROUP BY groupid
		break;
	default:
		printServiceMsg("No Action Needed");
}


function verifyDB($requiredColumns,$expectionKeys=[]) {
	$dataTables=_db()->get_tableList();
	$result=[];
	foreach($dataTables as $tbl) {
		$tblKey=current(explode("_", $tbl))."_";
		if(in_array($tblKey, $expectionKeys)) {
			continue;
		}

		$cols=_db()->get_columnList($tbl);
		foreach($requiredColumns as $colName) {
			if(!in_array($colName,$cols)) {
				$result[]="$tbl : $colName Missing";
			}
		}
	}
	return $result;
}
function verifyFS() {
	
}

function compareLists($dbKeySource,$dbKeyTarget) {
	$dataSource=_db($dbKeySource)->_selectQ("do_lists","groupid,count(*) as count")->_groupBy("groupid")->_GET();
	$dataTarget=_db($dbKeyTarget)->_selectQ("do_lists","groupid,count(*) as count")->_groupBy("groupid")->_GET();
	
	$keys=[];
	foreach($dataSource as $row) {
		$keys[$row['groupid']]=$row['count'];
	}
	$out=["new-group"=>[],"count-mismatch"=>[]];
	foreach($dataTarget as $row) {
		if(!isset($keys[$row['groupid']])) {
			$out['new-group'][]=$row;
		} elseif($keys[$row['groupid']]<$row['count']) {
			$row['old']=$keys[$row['groupid']];
			$out['count-mismatch'][]=$row;
		}
	}
	return $out;
}

function compareTables($dbKeySource,$dbKeyTarget) {
  //First DB
  $dataTablesSource=_db($dbKeySource)->get_tableList();
  $dataTablesSource=array_flip($dataTablesSource);
  
  foreach($dataTablesSource as $tbl=>$x) {
    $cols=_db($dbKeySource)->get_columnList($tbl);
    $dataTablesSource[$tbl]=$cols;
  }
  
  //Second DB
  $dataTablesTarget=_db($dbKeyTarget)->get_tableList();
  $dataTablesTarget=array_flip($dataTablesTarget);
  
  foreach($dataTablesTarget as $tbl=>$x) {
    $cols=_db($dbKeyTarget)->get_columnList($tbl);
    $dataTablesTarget[$tbl]=$cols;
  }
  
  $finalTable=[];
  foreach($dataTablesTarget as $tbl=>$cols) {
    if(isset($dataTablesSource[$tbl])) {
      $result = array_diff($dataTablesSource[$tbl], $cols);
      
      if(count($result)>0) {
        $finalTable['more-in-src'][$tbl]=$result;
      }
			
			$result = array_diff($cols, $dataTablesSource[$tbl]);
      
      if(count($result)>0) {
				$finalTable['not-in-src'][$tbl]=$result;
      }
    } else {
			$finalTable['table-missing'][$tbl]=$cols;
    }
  }
  return $finalTable;
//   printArray([$dataTablesSource,$dataTablesTarget]);
}
?>
