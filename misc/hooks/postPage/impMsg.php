<?php
if(PAGE=="home") {
  $sql=_db()->_selectQ("user_events","*",["blocked"=>"false","priority"=>"high"])
        ->_whereRAW("curdate()<=schedule AND curdate()>=DATE_SUB(schedule,INTERVAL flag_period DAY)");
  $sqlData=$sql->_GET();
  
  if($sqlData && count($sqlData)>0) {
    $html="<div class='table-responsive'><table class='table table-bordered table-hover infoTipTable' width=100% cellpadding=0 cellspacing=0 style='font-size: 14px;line-height: 100%;'>";
		$html.="<thead><tr><th width=100px>Dated</th><th>Event Message</th><th width=100px>BY</th></tr></thead>";
		foreach($sqlData as $a=>$b) {
			$b['event']=str_replace('"',"'",$b['event']);
			$html.="<tr class='{$b['priority']}'>";
			$html.="<th width=100px align=left>{$b['schedule']}</th>";
			$html.="<td>{$b['event']}</td>";
			$html.="<td width=100px>{$b['created_by']}</td>";
			$html.="</tr>";
		}
		$html.="</table></div>";
?>
<style>
.infoTipTable thead th {
  background: #d4664e;
  color: white;
  border: 1px solid #8a4131 !important;
  font-size: 11px !important;
  text-align: center !important;
  padding: 5px !important;
  height: 25px;
}
</style>
<script>
$(function() {
  showAlertInfo();
});
function showAlertInfo() {
  html="<?=$html?>";
  lgksAlert(html);
}
</script>
<?php 
  }
}
?>
