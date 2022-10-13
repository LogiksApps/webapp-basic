<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadVendor("ace");

$rid = "content".rand(10000,99999999);
$content = file_get_contents($_ENV['SCRIPTFILE']);
?>
<div id='componentSpace' class='componentSpace'>
    <pre id='<?=$rid?>' style='width:100%;height:100%;border:0px;'></pre>
</div>
<script>
var editor = null;
$(function() {
    loadAceEditor("<?=$rid?>","php");
    editor.session.setValue(`<?=$content?>`);
});
function saveScript() {
    content = editor.session.getValue();
    processAJAXPostQuery(_service("automator","savescript"),"scriptcode="+encodeURIComponent(content)+"&file=<?=$_ENV['SCRIPTFILENAME']?>", function(ans) {
        if(ans.Data=="success") lgksToast("Script Saved Successfully");
        else lgksToast("Error saving script");
    },"json");
}
function showScriptInfo() {
    
}
function goBackToList() {
    window.location = _link("modules/automator");
}
function loadAceEditor(rid, format) {
	if(format==null) format="html";
	editor = ace.edit(rid);
	editor.setTheme("ace/theme/twilight");
	editor.session.setMode("ace/mode/"+format);
	
	editor.commands.addCommand({
			name: 'saveScript',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function(editor) {
				saveScript();
			},
			readOnly: true // false if this command should not apply in readOnly mode
	});
}
</script>
