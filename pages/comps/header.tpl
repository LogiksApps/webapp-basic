<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <button id='leftMenuOpen' class='pull-left'><i class='glyphicon glyphicon-menu-hamburger'></i></button>
    <a href='#' class='pull-left navbar-brand'>{_ling($APPS_COMPANY)}</a>

    <ul class="nav pull-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle strgtMidle"><i class='glyphicon glyphicon-user'></i><span class="postionName">{_ling('#SESS_USER_NAME#')} </span> <b class="caret"></b></a>
            <ul class="dropdown-menu profile-menu">
                <li><a href="{_link('modules/myProfile')}">{_ling('My Profile')}</a></li>
                <li><a href="{_link('modules/reports/my.applications')}">{_ling('My Applications')}</a></li>
                <li class="divider"></li>
                <!--
                <li><a href="{_link("modules/mySettings")}">App Settings</a></li>
                -->
                <li><a href="{getConfig('SUPPORT_LINK')}" target="_blank">{_ling('Get Support')}</a></li>
                <li><a href="{getConfig('HELPCENTER_LINK')}" target="_blank">{_ling('Help Documentation')}</a></li>
                <li class="divider"></li>
                <li><a href="{_link('logout.php')}" target='top'>{_ling('Logout')}</a></li>
            </ul>
        </li>
    </ul>
   
    <div id="addonToolBar">
        <!--<button id='rightMenuOpen' class='pull-right'><i class='glyphicon glyphicon-bullhorn'></i></button>-->
        <!--<button id='' class='pull-right openDropdown'><i class='fa fa-th'></i></button>-->
    </div>
    
    <!--<div class="search-form hidden-xs">-->
    <!--    <div class="search-input-area">-->
    <!--        <input id='searchQuery' class="search-query" type="text" placeholder="Search ...">-->
    <!--        <i class="fa fa-search"></i>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="tool-buttons hidden-xs">
      <div class="btn-group">
        {assign var="MENUARR" value=getToolsMenu()}
        {foreach from=$MENUARR item=menu}
          <a href="{_link($menu.link)}" class="btn btn-primary {$menu.class}" title="{$menu.tips}" target="{$menu.target}"><i class='{$menu.iconpath}'></i><span class='hidden'>{$menu.title}</span></a>
        {/foreach}
      </div>
    </div>

</nav>

<script>
      $('input.search-query').focus(function() {
            $("body").addClass('position');
      }).
      blur(function() {
            $("body").removeClass('position');
      });
</script>
