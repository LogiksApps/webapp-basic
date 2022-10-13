<?php
function getAddMenu() {
  $data=_db()->_selectQ(_dbTable("links"),"*",["blocked"=>"false","menuid"=>"createmenu"])->_orderby("weight")->_GET();

  return $data;
}
function getToolsMenu() {
  $data=_db()->_selectQ(_dbTable("links"),"*",["blocked"=>"false","menuid"=>"toolsmenu"])->_orderby("weight")->_limit(15)->_GET();

  return $data;
}
?>
