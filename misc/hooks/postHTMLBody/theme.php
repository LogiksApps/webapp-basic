<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$theme=getCompanySettings("THEME");
if($theme!=null && strlen($theme)>0) {
  echo "<style>{$theme}</style>";
}
?>