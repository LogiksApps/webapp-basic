<?php
$jsLink=_service("resources","","raw")."&type=js&src=jquery,jquery.ui,bootstrap,chart,md5,moment,template&theme=".SITENAME;
$cssLinks=explode(",","reset,bootstrap,font-awesome,template");
foreach($cssLinks as $css) {
  echo "<link href='".SiteLocation."misc/themes/default/{$css}.css' rel='stylesheet' type='text/css' />";
}
?>
<script src='<?=$jsLink?>' type='text/javascript' language='javascript'></script>

<div class='toolbar hidden-print'>
  <button class='btn btn-default' style='position:fixed;right:10px;top:10px;' onclick="window.print()"><i class='fa fa-print'></i> Print</button>
</div>