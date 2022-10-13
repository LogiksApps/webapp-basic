<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageSidebar() {
	// <form role='search'>
	//     <div class='form-group'>
	//       <input type='text' class='form-control' placeholder='Search'>
	//     </div>
	// </form>
	return "<div id='componentTree' class='componentTree list-group list-group-root well'></div>";
}
function pageContentArea() {
	return "<div id='componentSpace' class='componentSpace'><h2 align=center>Please load a template.</h2></div>
<script>
FORSITE='".SITENAME."';
</script>
	";
}

// $webPath=dirname(getWebPath(__FILE__))."/";
// echo '<script src="'.$webPath.'nicedit/nicEdit.js"></script>';
// echo "<script>var modulePath='{$webPath}';</script>";

loadVendor("ace");

echo _css("templates");
echo _js(["templates"]);

$a=checkUserRoles("setup","templates","create");
$b=checkUserRoles("setup","templates","edit");
$c=checkUserRoles("setup","templates","delete");

$toolBar=[
// 			"showPrintPreview"=>["title"=>"Print Preview","align"=>"right"],
//       "sendTemplateMail"=>["title"=>"Demo Mail","align"=>"right"],
      "loadPreviewComponent"=>["title"=>"Template","align"=>"right"],
      "loadInfoComponent"=>["title"=>"About","align"=>"right"],
      "loadTempateEditor"=>["title"=>"Editor","align"=>"right"],
	  "loadStyleEditor"=>["title"=>"Style","align"=>"right"],
	  "loadQueryEditor"=>["title"=>"Query","align"=>"right"],
      "loadParamsEditor"=>["title"=>"Params","align"=>"right"],
	

      // ["title"=>"Search Site","type"=>"search","align"=>"left"]
      "listContent"=>["icon"=>"<i class='fa fa-refresh'></i>"],
      "createContent"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
//       ['type'=>"bar"],
      "rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
      "deleteContent"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
			['type'=>"bar"],
			"sendTemplateMail"=>["icon"=>"<i class='fa fa-envelope'></i>","tips"=>"Demo Mail"],
			"showPrintPreview"=>["icon"=>"<i class='fa fa-print'></i>","tips"=>"Preview Template"],
    ];

if(!$a) {
  unset($toolBar['createContent']);
}
if(!$c) {
  unset($toolBar['deleteContent']);
}
if(!$b) {
  unset($toolBar['rename']);
}

printPageComponent(false,[
    "toolbar"=>$toolBar,
    "sidebar"=>"pageSidebar",
    "contentArea"=>"pageContentArea"
  ]);

?>