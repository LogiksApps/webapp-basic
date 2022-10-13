<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$config=$_ENV['INFOVIEW']['config'];
//printArray($config);

if(!isset($config['ref_src'])) {
  return;
}

loadModuleLib("templates","api");

$templates=getTemplateList($config['ref_src']);

$xtraAttribute=[];
foreach($config as $a=>$b) {
  $xtraAttribute[]="data-{$a}='"._replace($b)."'";
}
$xtraAttribute=implode(" ",$xtraAttribute);
?>
<style>
  .printButtons .btn {
    margin:10px;
  }
</style>
<div class='container printButtons'>
<?php
  if(count($templates)>0) {
    foreach($templates as $tmpl) {
    //   echo "<li class='list-group-item'>{$tmpl['title']}</li>";
      echo "<button type='button' class='btn btn-default' data-slug='{$tmpl['slug']}' {$xtraAttribute} onclick='showPrintableTemplate(this)' >{$tmpl['title']}</button>";
    }  
  } else {
    echo "<h2 class='text-center'>No printable templates found</h2>";
  }
?>
</div>
<script>
function showPrintableTemplate(src) {
  q=[];
  qD=$(src).data();
  $.each(qD, function(a,b) {
    q.push(a+"="+b);
  });
  lx=_service("templates","print")+"&"+q.join("&");
  top.lgksOverlayFrame(lx,"Template Preview");
}
</script>