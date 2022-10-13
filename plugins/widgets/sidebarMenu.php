<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$arr = [
        
    ];
?>
<style>
.sidebar-nav ul a {
    padding: 5px !important;
    text-shadow: none;
    border: 0px;
}
</style>
<ul class="list-group">
    <li class="list-group-item"><a class='menuItem' href='<?=_link("modules/newDev")?>'>New Dev</a></li> 
    <hr>
    <li class="list-group-item"><a class="menuItem" href="<?=_link("modules/reports/my.applications")?>">Applications</a></li>
    <hr>
    <li class="list-group-item"><a class='menuItem' href='<?=_link("modules/uiDemo")?>'>UI Demo</a></li>
</ul>
<script type="text/javascript">
$(function() {
    $("#sidebarLeft").delegate("a.menuItem[href]","click",function(e) {
        e.preventDefault();

        ttl=$(this).text();
        href=$(this).attr("href");
        target=$(this).attr("target");

        if(target==null || $(this).attr("target").length<=0) {
          if(href.indexOf("http://")===0 || href.indexOf("https://")===0) {
            openLinkFrame(ttl,href);
          } else {
            openLinkFrame(ttl,_link(href));
          }

          if(window.screen.width<window.screen.height && window.screen.width<767) {
            $("#sidebarLeft").removeClass("open");
            $("#page-wrapper").toggleClass("openSidebar");
          }
        } else if(target=="top") {
          window.top.location=href;
        } else if(target=="_blank") {
          window.open(href);
        } else if(target.substr(0,1)=="_") {
          window.open(href,target);
        } else {
          openLinkFrame(ttl,href);
        }
    });

// $("#sidebarLeft").addClass("open");
});
</script>
