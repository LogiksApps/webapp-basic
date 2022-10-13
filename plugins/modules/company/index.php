<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$companyData=_db()->_selectQ("my_company","*")->_GET();
if(count($companyData)<=0) {
  $companyInfo=[
        "name"=>"My Company",
        "branch_type"=>"HQ",
        "logo"=>loadMedia("logs/logo.png"),
        "created_by"=>$_SESSION['SESS_USER_ID'],
        "created_on"=>date("Y-m-d H:i:s"),
        "edited_by"=>$_SESSION['SESS_USER_ID'],
        "edited_on"=>date("Y-m-d H:i:s"),
      ];
  _db()->_insertQ1("my_company",$companyInfo)->_RUN();
} else {
  $companyInfo=$companyData[0];
}
if(!isset($companyInfo['logo']) || strlen($companyInfo['logo'])<=0) {
  $companyInfo['logo']=loadMedia("images/noimage.png");
} elseif(strpos("http://",$companyInfo['logo'])===0 || strpos("https://",$companyInfo['logo'])===0) {
  
} else {
  $companyInfo['logo']=loadMedia($companyInfo['logo']);
}
// printArray($companyInfo);
?>
<div class="col-xs-12">
	<div class="row">
		<div class="col-sm-12">
			<h1 class="pull-left">
				<i class="fa fa-user"></i>
				<span><?=$companyInfo['name']?></span>
			</h1>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-sm-2 col-lg-2">
          <div class="box">
            <div class="box-content img-content">
              <img class="img-responsive" src="<?=$companyInfo['logo']?>">
            </div>
          </div>
        </div>
        <div class="col-sm-10 col-lg-10">
        	<?php
				loadModuleLib("forms","api");
			    $formConfig=findForm(__DIR__."/form.json");
            	printForm('update',$formConfig,'app',["md5(id)"=>md5($companyInfo['id'])]);
        	?>
        </div>
    </div>
</div>