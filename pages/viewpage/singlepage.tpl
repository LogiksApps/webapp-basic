<div id="content">
    {module src=$MODULESRC}
</div>

<script>
$(function() {
    // $("#myTab").delegate(".closeTab","click",function(e) {
    //     var tabContentId = $(this).parent().attr("href");
    //     $(this).parent().parent().remove(); //remove li of tab
    //     $('#myTab a:last').tab('show'); // Select first tab
    //     $(tabContentId).remove(); //remove respective tab content
    // });
    // $('#myTab a').click(function (e) {
    //       e.preventDefault();
    //       $(this).tab('show');
    //     });
});
function openLink(tabName,tabLink,closable,replaceable) {
    tabLink = tabLink.replace('/modules/','/singlepage/');
    window.location = tabLink;
}
function openLinkFrame(tabName,tabLink,closable,replaceable) {
    tabLink = tabLink.replace('/modules/','/singlepage/');
    window.location = tabLink;
}
</script>
