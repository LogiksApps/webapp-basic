<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug=_slug("a/b/c");

$commentUID="bizlogger".md5(time());
$_SESSION[$commentUID]=[
        "ref_src"=>$slug['b'],
        "ref_id"=>$slug['c'],
    ];
?>
<section id='<?=$commentUID?>' class="infoTableView infoTableGrid" data-page="0" data-limit="20" data-ui="grid" data-cmd='loadLocalLog' data-template='logger-template' >
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-pagination">
            <thead>
                <tr>
                    <th name="date" width=150px>On</th>
                    <th name="edited_on" width=100px>At</th>
                    <th name="category" width=200px>Medium/Via</th>
                    <th name="type" width=200px>Type</th>
                    <th name="msg">Action Message</th>
                    <th name="edited_by" width=200px>By</th>
                </tr>
                <tr>
                    <th class="filters hidden" colspan=100>
                    
                    </th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot class="info-form">
            </tfoot>
            <tfoot class="info-pagination">
            </tfoot>
        </table>
    </div>
</section>
<script id="logger-template" type="text/x-handlebars-template">
    {{#each Data}}
    <tr data-refid='{{id}}' data-title='{{{title}}}'>
        <td class='dated'>{{formatDate edited_on}}</td>
        <td class='edited_on'>{{formatTime edited_on}}</td>
        <td class='category'>{{{category}}}</td>
        <td class='type'>{{{type}}}</td>
        <td class='msg'>{{{msg}}}</td>
        <td class='edited_by'>{{edited_by}}</td>
    </tr>
    {{/each}}
</script>
<script>
function loadLocalLog(divBlock) {
    tmpl=$(divBlock).data("template");
    lx=_service("bizlogger","list-logs-local")+"&dcode=<?=$commentUID?>";
    processAJAXQuery(lx, function(data) {
        htmlData=Handlebars.compile($("#"+tmpl).html());
        htmlData=htmlData(data);
        $("table tbody",divBlock).html(htmlData)
    },"json");
}
</script>