<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$logContent = "No Log Content";
        
$logDir=ROOT.LOG_FOLDER.$_REQUEST['REFSITE']."/pcron/";
$logList = scandir($logDir,SCANDIR_SORT_DESCENDING);
$logList = array_slice($logList,0,count($logList)-2);

if(!$_ENV['LOGFILE'] && count($logList)>0) {
    $_ENV['LOGFILE'] = $logList[0];
}
if($_ENV['LOGFILE']) {
    $contentFile = $logDir.$_ENV['LOGFILE'];
    if(file_exists($contentFile)) {
        $logContent = file_get_contents($contentFile);
    }
}
?>
<table class='table table-hover table-stripped'>
<thead>
	<tr>
		<th width=150px>Date/Time</th>
		<th width=100px>Type/Level</th>
		<th>Message</th>
		<th width=100px></th>
	</tr>
</thead>
<tbody>
<?php
    if(strlen($logContent)>1) {
        $logContent = explode("\n",$logContent);
        foreach($logContent as $line) {
            $line = explode(">",$line);
            if(count($line)<=1) continue;
            $line1 = explode("] ",$line[0]);
            if(isset($line1[1])) {
                switch(strtoupper(trim($line1[1]))) {
                    case "PCRON.ERROR":
                        echo "<tr class='alert-danger'>";
                        break;
                    case "PCRON.WARNING":
                        echo "<tr class='alert-warning'>";
                        break;
                    default:
                        echo "<tr>";
                        break;
                }
            } else {
                echo "<tr>";
            }
            
            echo "<td>".substr($line1[0],1)."</td>";
            if(isset($line1[1]))
                echo "<td>".trim(str_replace("pcron.","",$line1[1]))."</td>";
            else
                echo "<td>-</td>";
            echo "<td>{$line[2]}</td>";
            echo "<td>";
            echo "<i class='glyphicon glyphicon-info-sign' data-json='".json_encode(json_decode($line[1]))."' style='margin-right: 5px;float: right;'></i>";
            echo "<i class='glyphicon glyphicon-screenshot' data-json='".json_encode(json_decode($line[3]))."' style='margin-right: 5px;float: right;'></i>";
            //printArray($line);
            echo "</td>";
            echo "</tr>";
        }
    }
?>
</tbody>
</table>
<script>
$(function() {
    $("#pgtoolbar .nav.navbar-right").append(`<select class='form-control select' onchange='loadLogList(this)'>
                    <?php
                        foreach($logList as $a=>$b) {
                            if($b==$_ENV['LOGFILE']) {
                                echo "<option value='{$b}' selected>{$b}</option>";
                            } else {
                                echo "<option value='{$b}'>{$b}</option>";
                            }
                        }
                    ?>
                    </select>`);
});
function goBackToList() {
    window.location = _link("modules/automator");
}
function loadLogList(src) {
    window.location = _link("modules/automator/viewlogs/"+$(src).val());
}
</script>
