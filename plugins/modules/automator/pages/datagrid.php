<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<div class='col-xs-12 col-md-12 col-lg-12'>
	<div class='row'>
		<?php
			printDataGrid($report,$form,$form,["slug"=>"subtype/type/refid","glink"=>_link("modules/automator"),"add_record"=>"Add Job","add_class"=>'btn btn-info'],"core");
		?>
	</div>
</div>
<style>
.pageCompContainer {
    margin-top: 0px;
}
.control-toolbar .fa.fa-2x {
    margin-top: 5px;
    color: black;
}
#scriptList li {
    cursor: pointer;
}
</style>
<script>
$(function() {
    $("#scriptList").delegate("li>.fa.fa-pencil","click",function(a) {
        f1 = $(this).closest("li").data("file");
        if(f1!=null && f1.length>0) {
            window.location = _link("modules/automator/scriptfile/"+f1);
        } else {
            lgksToast("File source missing");
        }
    });
    listScripts();
});
function listScripts() {
    $("#scriptList").html("<i class='fa fa-spin fa-spinner'></i>");
    lx = _service("automator","scriptlist");
    processAJAXQuery(lx, function(ans) {
        $("#scriptList").html("");
        $.each(ans.Data, function(b,a) {
            $("#scriptList").append("<li class='list-group-item' data-file='"+a+"'>"+a+" <i class='fa fa-pencil pull-right'></i></li>");
        });
    },"json");
}
function editScript(row) {
    window.location = _link("modules/automator/editscript/"+$(row).data("hash"));
}
function createScript() {
    lgksPrompt("New Script Name (No Space Allowed)","New Script",function(newName) {
			if(newName!=null && newName.length>0) {
				newName = newName.replace(/[^\w\s]/gi, '').replace(/ /g,'_').replace('.php','').replace(/__/g,'_');
				window.location = _link("modules/automator/createscript/"+newName);
			}
		});
}
function showPCronHelpInfo() {
    lgksAlert("To Configure the PCron System, please follow below procedure.<br>"+
        "<ul style='padding: 20px;list-style: initial;'>"+
        "<li>Configure a schedulled job on your system using cron/windows tasks to <br>ping the below address at certain interval like every 1hr</li><br>"+
        "<li><a href='<?=current(explode("?",_service("pcron")))."?site={$_REQUEST['REFSITE']}"?>' target=_blank><?=current(explode("?",_service("pcron")))."?site={$_REQUEST['REFSITE']}"?></a></li><br>"+
        "<li>You can use linux CRON tab. <a href='https://linuxconfig.org/linux-crontab-reference-guide' target=_blank>Crontab</a>, <a href='https://crontab.guru/#*_*/1_*_*_*' target=_blank>CRON Calculator</a></li>"+
        "<li>You can use windows task scheduller. <a href='https://docs.microsoft.com/en-us/windows/desktop/taskschd/task-scheduler-start-page' target=_blank>Task Scheduller</a></li>"+
        "<li>You can also use node-scheduller to schedule a job. Checkout <a href='https://github.com/bismay4u/automaton' target=_blank>Github Source</a></li>"+
        "</ul>");
}
function viewJobLogs() {
    window.location = _link("modules/automator/viewlogs");
}
</script>