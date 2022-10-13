<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$settingsData=_db()->_selectQ("my_company_settings","*",["guid"=>$_SESSION['SESS_GUID'],"company_id"=>$_SESSION['COMP_ID'],"param_title"=>"theme"])->_GET();

if(!isset($settingsData[0])) {
  $theme=getCompanySettings("THEME");
  
  $settingsData=_db()->_selectQ("my_company_settings","*",["guid"=>$_SESSION['SESS_GUID'],"company_id"=>$_SESSION['COMP_ID'],"param_title"=>"theme"])->_GET();
}

$settingsData[0]['param_value']=str_replace("%3B",";",$settingsData[0]['param_value']);
//printArray($settingsData[0]);
?>
<section class="settingsSection">
 	<div class="container-fluid">
 		<div class="row settingsMain">
 			<div class="col-md-8 col-md-offset-2">
 				<div class="settingsBlock">
            <div class='text-right'>
              <button class='btn btn-success' onclick='saveThemeData()'>
                <i class='fa fa-save'></i> Save Theme
              </button>
            </div>
            <br>
 				    <textarea id='themeData' class="form-control" placeholder='Pleases place your styling here in CSS3' style="height: 50%;" data-hash='<?=md5($settingsData[0]['param_value'])?>'><?=$settingsData[0]['param_value']?></textarea>
            <br>
            <button class="btn btn-primary pull-right" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
              <i class='fa fa-question-circle'></i> Some examples
            </button>
          
            <citie>* Please place new rules in new lines</citie><br>
            <citie>* Theme changes will impact the system only after reloading the entire page</citie><br>
            <citie>* Please ask a certified CSS3 designer to alter the UI/UX/Theme of your application</citie><br>
            
            <div class="collapse" id="collapseExample">
              <div class="well">
                <pre>
                  <strong>For changing the header color</strong>
                  #header .navbar {
                    background-color:red;
                  }
                </pre>
              </div>
            </div>
          
 				</div>
 			</div>
 		</div>
 	</div>
</section>
<script>
function saveThemeData() {
  v1=$("#themeData").val();
  hashData=$("#themeData").data("hash");
  hashNew=md5(v1);
  if(hashNew==hashData) {
    lgksToast("No changes found");
    return;
  }
  q="keyname=THEME&keyvalue="+encodeURIComponent(v1)+"&keyhash=<?=md5($settingsData[0]['id'])?>";
  processAJAXPostQuery(_service("companyConfig","save"),q, function(ans) {
      lgksToast(ans);
  });
}
</script>