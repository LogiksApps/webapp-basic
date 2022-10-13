<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slugs = _slug("a/b/c/d/e");

if($slugs["b"]=="logs") {
    $basePath = __DIR__."/panel/";
    $report=$basePath."logs.json";
    
    loadModuleLib("reports","api");
    ?>
    <div class='col-xs-12 col-md-12 col-lg-12'>
    	<div class='row'>
    		<?php
    			//printDataGrid($report,$form,$form,["slug"=>"subtype/type/refid","glink"=>_link("modules/notifyMatrix"),"add_record"=>"Add Notifier"],"app");
    			printReport($report);
    		?>
    	</div>
    </div>
    <script>
    function viewMatrix() {
        window.location = _link("modules/serviceCmd");
    }
    </script>  
    <?php
} elseif($slugs["b"]=="view") {
    $basePath = __DIR__."/panel/";
    $form=$basePath."form.json";
    
    loadModuleLib("infoview","api");
    
    $_ENV['INFOVIEW-REFHASH'] = $slugs["c"];
    ?>
    <div class='col-xs-12 col-md-12 col-lg-12'>
    	<div class='row'>
    		<?php
    			printInfoView(findInfoView($form),"app",["md5(id)"=>$slugs["c"]]);
    		?>
    	</div>
    </div>
    <?php
} else {
    $basePath = __DIR__."/panel/";
    $report=$basePath."report.json";
    $form=$basePath."form.json";
    
    loadModule("datagrid");
    ?>
    <div class='col-xs-12 col-md-12 col-lg-12'>
    	<div class='row'>
    		<?php
    			printDataGrid($report,$form,$form,["slug"=>"subtype/type/refid","glink"=>_link("modules/serviceCmd"),"add_record"=>"Add Service"],"app");
    		?>
    	</div>
    </div>
    <script>
    function viewInfo(src) {
        lgksOverlayFrame(_link("modules/serviceCmd/view/"+$(src).data("hash")),"Info");
    }
    function viewLogs() {
        window.location = _link("modules/serviceCmd/logs");
    }
    </script>  
    <?php
}
?>