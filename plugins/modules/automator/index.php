<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(defined("CMS_SITENAME")) $_REQUEST['REFSITE']=CMS_SITENAME;
else $_REQUEST['REFSITE']=SITENAME;

loadModule("pages");

$slugs = _slug("a/b/c/d/e");

$scriptFile = false;

$pageOptions=[
    		"toolbar"=>false,
    		"contentArea"=>"pageContentArea"
    	];

function getPCronScriptList() {
    $html = [];
    $dir = ["Global Jobs"=>ROOT.PCRON_FOLDER,"App Jobs"=>APPROOT.PCRON_FOLDER];
    foreach($dir as $grp=>$d) {
        if(is_dir($d)) {
            $ds = scandir($d);
            array_shift($ds);array_shift($ds);
            $dir[$grp] = $ds;
        }
    }
    foreach($dir as $grp=>$ds) {
        $html[] = "<optgroup label='{$grp}'>";
        foreach($ds as $d1) {
            $dt = str_replace(".php","",$d1);
            $html[] = "<option value='{$dt}'>{$dt}</option>";
        }
        $html[] = "</optgroup>";
    }
    return implode("",$html);//"<option value=''>NOTHING</option>";
}

echo _css("automator");
echo _js(["automator"]);


//Finally Page Request Routing
if($slugs['b']=="editscript" || $slugs['b']=="createscript" || $slugs['b']=="scriptfile") {
    if(strlen($slugs['c'])<=0) {
        echo "<h1 align=center>Script Source Missing</h1><div align=center><a href='"._link("modules/automator")."'>Go Back To Listing Page</a></div>";
        return;
    }
    if($slugs['b']=="editscript") {
        $tbl=_dbTable("system_cronjobs",true);
        $data=_db(true)->_selectQ($tbl,"*")
            ->_where(["md5(id)"=>$slugs['c']])
            ->_GET();
        if(!isset($data[0])) {
            echo "<h1 align=center>PCron Job Not Found</h1><div align=center><a href='"._link("modules/automator")."'>Go Back To Listing Page</a></div>";
            return;
        }
        if(strlen(($data[0]['scriptpath']))<=0) {
            echo "<h1 align=center>PCron Job Script Not Found</h1><div align=center><a href='"._link("modules/automator")."'>Go Back To Listing Page</a></div>";
            return;
        }
        $slugs['c'] = $data[0]['scriptpath'];
    } elseif($slugs['b']=="scriptfile") {
        // $slugs['c'] = $data[0]['scriptpath'];
        $slugs['c'] = str_replace(".php","",$slugs['c']);
    }
    
    if(strlen($slugs['c'])>0) {
        $scriptFile = ROOT."apps/{$_REQUEST['REFSITE']}/".PCRON_FOLDER._slugify($slugs['c']).".php";
        if(!file_exists($scriptFile)) {
            file_put_contents($scriptFile,"<?php\nif(!defined('ROOT')) exit('No direct script access allowed');\n\n\nreturn [\"status\"=>true];\n?>");
        }
    }
    
    $pageOptions['toolbar'] = [
            "goBackToList"=>["icon"=>"<i class='fa fa-arrow-left'></i>"],
            "saveScript"=>["icon"=>"<i class='fa fa-save'></i>"],
            
            "showScriptInfo"=>["title"=>$slugs['c'],"align"=>"right","class"=>"active"],
        ];
    
    $_ENV['SCRIPTFILE'] = $scriptFile;
    $_ENV['SCRIPTFILENAME'] = $slugs['c'];
    
    function pageContentArea() {
        ob_start();
        include_once __DIR__."/pages/editor.php";
        $html = ob_get_contents();
        ob_clean();
        
        return $html;
    }
    
    printPageComponent(false,$pageOptions);
} elseif($slugs['b']=="viewlogs") {
    $pageOptions['toolbar'] = [
            "goBackToList"=>["icon"=>"<i class='fa fa-arrow-left'></i>"],
            //"saveScript"=>["icon"=>"<i class='fa fa-save'></i>"],
            
            //"showScriptInfo"=>["title"=>$slugs['c'],"align"=>"right","class"=>"active"],
        ];
        
    if(strlen($slugs['c'])>0) {
        $_ENV['LOGFILE'] = $slugs['c'];
    } else {
        $_ENV['LOGFILE'] = false;
    }
    
    function pageContentArea() {
        ob_start();
        include_once __DIR__."/pages/logviewer.php";
        $html = ob_get_contents();
        ob_clean();
        
        return $html;
    }
    
    printPageComponent(false,$pageOptions);
} else {
    if($slugs['b']=="new" || $slugs['b']=="edit") {
        
    } else {
        $pageOptions['sidebar'] = "getSidebarArea";
    }
    
    function getSidebarArea() {
        return "<div class='subheader'>Available Script List ".
                "<i class='fa fa-refresh fa-2x pull-right' style='margin-top: -2px;cursor:pointer;' onclick='listScripts()' ></i>".
                "<i class='fa fa-plus fa-2x pull-right' style='margin-top: -2px;cursor:pointer;' onclick='createScript()' ></i>".
                "</div><ul id='scriptList' class='list-group'></ul>";
    }
    function pageContentArea() {
        //return "<h3 align=center>Coming Soon</h3>";
        $basePath = __DIR__."/panels/";
        $report=$basePath."report.json";
        $form=$basePath."form.json";
        
        loadModule("datagrid");
    
        ob_start();
        include_once __DIR__."/pages/datagrid.php";
        $html = ob_get_contents();
        ob_clean();
        
        return $html;
    }
    
    printPageComponent(false,$pageOptions);
}
?>