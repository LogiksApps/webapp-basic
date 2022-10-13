<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if($_SESSION['SESS_PRIVILEGE_ID']<=ADMIN_PRIVILEGE_RANGE) {
  $settingsData=_db()->_selectQ("my_company_settings","*",["guid"=>$_SESSION['SESS_GUID'],"company_id"=>$_SESSION['COMP_ID']])->_GET();
} else {
  $settingsData=_db()->_selectQ("my_company_settings","*",["guid"=>$_SESSION['SESS_GUID'],"company_id"=>$_SESSION['COMP_ID'],"param_module"=>["system","neq"]])->_GET();
}

if(!$settingsData) $settingsData=[];

// echo getCompanySettings("AWS_KEYS","","AWS");
// echo getCompanySettings("AWS_SECRET","","AWS");
// echo getCompanySettings("APIKEY","","GOOGLE");

$finalData=[];

foreach($settingsData as $row) {
    $finalData[$row['param_module']][]=$row;
}
$settingsData=$finalData;
// printArray($settingsData);

function renderField($fieldValue, $fieldName, $fieldHash) {
    if($fieldValue=="true") {
        echo "<div class='material-switch pull-right'>
                <input id='{$fieldHash}' name='{$fieldHash}' data-name='{$fieldName}' type='checkbox' checked />
                <label for='{$fieldHash}' class='label-info'></label>
            </div>";
    } elseif($fieldValue=="false") {
        echo "<div class='material-switch pull-right'>
                <input id='{$fieldHash}' name='{$fieldHash}' data-name='{$fieldName}' type='checkbox' />
                <label for='{$fieldHash}' class='label-info'></label>
            </div>";
    } elseif($fieldName=="select") {
        echo "<div class='form-group pull-right'><select class='form-control' id='{$fieldHash}' name='{$fieldHash}' data-name='{$fieldName}' value='{$fieldValue}'>";
        echo "<option></option>";
        echo "</select></div>";
    } elseif(is_numeric($fieldValue)) {
        echo "<div class='form-group pull-right'><input class='form-control' id='{$fieldHash}' name='{$fieldHash}' data-name='{$fieldName}' value='{$fieldValue}' placeholder='".strtoupper(toTitle($fieldName))."' type='number' /></div>";
    }
    else {
        $fieldValue=str_replace("%3B",";",$fieldValue);
        echo "<div class='form-group pull-right'><input class='form-control' id='{$fieldHash}' name='{$fieldHash}' data-name='{$fieldName}' value='{$fieldValue}' placeholder='".strtoupper(toTitle($fieldName))."' type='text' /></div>";
    }
}

echo _css("companyConfig");
?>
<section class="settingsSection">
 	<div class="container-fluid">
 		<div class="row settingsMain">
 			<div class="col-md-8 col-md-offset-2">
 				<div class="settingsBlock">
 				    <?php
                if(count($settingsData)<=0) {
                    echo "<h1 align=center>Nothing to setup for now! Try later ...</h1>";
                }
 				        foreach($settingsData as $key=>$blocks) {
 				            $blockTitle=toTitle(_ling($key));
 				            $slug=md5($key);
 				    ?>
 				    <p class="settingsBlockTitle"><strong><?=$blockTitle?></strong></p>
 				    <div class="settingsSubblock">
 				        <?php
 				            foreach($blocks as $nx=>$blk) {
 				        ?>
 				        <div class="settingsDivition">
 							<p class="settingNames"><?=toTitle(_ling($blk['param_title']))?></p>
 							<?php
 							    renderField($blk['param_value'],$blk['param_title'],md5($blk['id']));
 							?>
 						</div>
 				        <?php
 				            }
 				        ?>
 			        </div>
 				    <?php
 				        }
 				    ?>
 				</div>
 			</div>
 		</div>
 	</div>
</section>
<script>
$(function() {
    $("input[name],select[name],textarea[name]",".settingsSection").change(function() {
        q="keyname="+$(this).data("name")+"&keyvalue="+$(this).val()+"&keyhash="+$(this).attr("name");
        processAJAXPostQuery(_service("companyConfig","save"),q, function(ans) {
            lgksToast(ans);
        });
    });
});
</script>