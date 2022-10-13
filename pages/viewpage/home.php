<?php 

$uiType = getConfig("UI_TYPE");
header("Location:"._link($uiType));
?>