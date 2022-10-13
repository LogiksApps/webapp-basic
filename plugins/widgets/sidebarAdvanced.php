<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModuleLib("navigator","api");

$availableMenus = [
        "suites"=>["icon"=>"fa fa-vcard-o fa-fw"],
        "reports"=>["icon"=>"fa fa-line-chart fa-fw"],
        "bookmarks"=>["icon"=>"fa fa-link fa-fw"],
    ];

$menuType = getCompanySettings("Left_SideBar_Items","suites,reports,test");
$menuType = explode(",",$menuType);
$finalSidebars = array_intersect(array_keys($availableMenus), $menuType);

function getMenuTree($menuid, $menuFolder=false) {
  if($menuid=="suites") $menuid="default";
  
  $menuTree1=generateNavigationFromDB($menuid,"links","app");
  
  if($menuFolder) {
    $menuTree2=generateNavigationFromDir(APPROOT."misc/menus/","app");
  } else {
    $menuTree2 = [];
  }

  $menuTree=array_merge_recursive($menuTree1,$menuTree2);
  
  foreach ($menuTree as $category=>$menuSet) {
    foreach ($menuSet as $key => $menu) {
      if($menu['category']!=null && strlen($menu['category'])>0) {
        unset($menuTree[$category][$key]);
        $menuTree[$category][$menu['category']][$key]=$menu;
      }
    }
  }
  return $menuTree;
}

function getSubmenuTree($menuCategory) {
  $menuTree = [];
  
  return $menuTree;
}

function getMenuContent($menuSrc,$barType=false) {
    if($menuSrc=="suites") $menuSrc="default";
  
    if(!isset($_SESION['MENUIDS'])) {
      $_SESION['MENUIDS']=["default","reports"];
      
      $dataMenus=_db()->_selectQ(_dbTable("links"),"menuid as title,count(*) as max",["menuid"=>[",","notlike"],"blocked"=>"false"])->_groupBy("menuid")->_GET();
      array_walk($dataMenus, function (&$v, $k) { $v = strtolower($v['title']); }); 
      $_SESION['MENUIDS']=$dataMenus;
    }
    if(!isset($_SESION['MENUGRPS'])) {
      $_SESION['MENUGRPS']=["inventory"];
      
      $dataMenus=_db()->_selectQ(_dbTable("links"),"category as title,count(*) as max",["blocked"=>"false"])->_whereRAW("length(category)>0 AND category IS NOT NULL AND category<>'#'")->_groupBy("category")->_GET();
      array_walk($dataMenus, function (&$v, $k) { $v = strtolower($v['title']); }); 
      $_SESION['MENUGRPS']=$dataMenus;
    }
    
    if($barType===false) {
      //Auto detect the Menubar type
      if(in_array($menuSrc,["bookmarks"])) $barType="bookmarks";
      elseif(in_array($menuSrc,$_SESION['MENUIDS'])) $barType="menugroup";
      elseif(in_array($menuSrc,$_SESION['MENUGRPS'])) $barType="submenugroup";
      else return "";
    }

    switch(strtolower($barType)) {
        case "bookmarks":
            $bookmarkMenu = _db()->_selectQ("user_bookmarks","id,title,category,remarks,link_uri,shared_with,created_by,created_on,edited_by,edited_on",["blocked"=>"false"])
                    ->_whereRAW("(shared_with ='' OR shared_with IS NULL OR shared_with ='*' OR shared_with like '%#SESS_USER_ID#%' OR shared_with like '%#SESS_PRIVILEGE_NAME#,%')")
                    ->_GET();
            $bookmarkFinal = [];
            foreach($bookmarkMenu as $menu) {
                if(!$menu['category']) $menu['category'] = "General";
              
                $category = explode("/",$menu['category']);
                if(count($category)>1) {
                  $category1 = trim($category[0]);
                  array_shift($category);
                  $category = trim(implode("/",$category));
                  if(!isset($bookmarkFinal[$category1])) $bookmarkFinal[$category1] = [];
                  $bookmarkFinal[$category1][$category][] = $menu;
                } else {
                  $bookmarkFinal[$menu['category']][] = $menu;
                }
            }
            
            ksort($bookmarkFinal);
            
            foreach ($bookmarkFinal as $category=>$menuSet) {
                $category = _ling($category);
                $html="<li class='menuGroup'>";
                $html.="<a href='#' aria-expanded='false'>$category <span class='fa arrow'></span></a>";
                $html.="<ul aria-expanded='false' class='secondary collapse'>";
                foreach($menuSet as $key=>$menu) {
                    if(is_numeric($key)) {
                      $menu['title'] = _ling(urldecode($menu['title']));
                      $html.="<li title='{$menu['remarks']}'><a href='{$menu['link_uri']}' target=_blank>{$menu['title']}</a></li>";
                    } else {
                      $html.="<li class='menuGroup'>";
                      $html.="<a href='#' aria-expanded='false'>$key <span class='fa arrow'></span></a>";
                      $html.="<ul aria-expanded='false' class='tertiary collapse'>";
                      foreach($menu as $key1=>$menu1) {
                        $menu1['title'] = _ling(urldecode($menu1['title']));
                        $html.="<li title='{$menu1['remarks']}'><a href='{$menu1['link_uri']}' target=_blank>{$menu1['title']}</a></li>";
                      }
                      $html.="</ul>";
                      $html.="</li>";
                    }
                }
                $html.="</ul>";
                $html.="</li>";
                echo $html;  
            }
        break;
        case "submenugroup":
            $reportsTree = getSubmenuTree($menuSrc);
            $htmlLast="";
            $html="";
            foreach ($reportsTree as $category=>$menuSet) {
              if(count($menuSet)<=0 || strlen($category)<=0) continue;
            
              $hash=md5($category);
        
              if(is_numeric($category)) {
                    $menu1=$menuSet;
                    $more=[];
                    if($menu1['target']!=null && strlen($menu1['target'])>0) {
                      $more[]="target='{$menu1['target']}'";
                    }
                    if($menu1['class']!=null && strlen($menu1['class'])>0) {
                      $more[]="class='menuItem {$menu1['class']}'";
                    } else {
                      $more[]="class='menuItem'";
                    }
                    if($menu1['category']!=null && strlen($menu1['category'])>0) {
                      $more[]="data-category='{$menu1['category']}'";
                    }
                    if($menu1['tips']!=null && strlen($menu1['tips'])>0) {
                      $more[]="title='{$menu1['tips']}'";
                    }
        
                    if($menu1['iconpath']!=null && strlen($menu1['iconpath'])>0) {
                      $htmlLast.="<li><a href='{$menu1['link']}' ".implode(" ", $more)."><i class='menuIcon {$menu1['iconpath']}'></i>&nbsp; {$menu1['title']}</a></li>";
                    } else {
                      $htmlLast.="<li><a href='{$menu1['link']}' ".implode(" ", $more).">{$menu1['title']}</a></li>";
                    }
              } else {
                  $html="<li class='menuGroup'>";
                  $html.="<a href='#' aria-expanded='false'>$category<span class='fa arrow'></span></a>";
                  $html.="<ul aria-expanded='false' class='secondary collapse'>";
                  $html1="";
                  foreach ($menuSet as $key => $menu) {
                    if(is_numeric($key)) {
                      $menu['title']=_ling($menu['title']);    
                      $more=[];
                      if($menu['target']!=null && strlen($menu['target'])>0) {
                        $more[]="target='{$menu['target']}'";
                      }
                      if($menu['class']!=null && strlen($menu['class'])>0) {
                        $more[]="class='menuItem {$menu['class']}'";
                      } else {
                        $more[]="class='menuItem'";
                      }
                      if($menu['category']!=null && strlen($menu['category'])>0) {
                        $more[]="data-category='{$menu['category']}'";
                      }
                      if($menu['tips']!=null && strlen($menu['tips'])>0) {
                        $more[]="title='{$menu['tips']}'";
                      }
            
                      if($menu['iconpath']!=null && strlen($menu['iconpath'])>0) {
                        $html1.="<li><a href='{$menu['link']}' ".implode(" ", $more)."><i class='menuIcon {$menu['iconpath']}'></i>&nbsp; {$menu['title']}</a></li>";
                      } else {
                        $html1.="<li><a href='{$menu['link']}' ".implode(" ", $more).">{$menu['title']}</a></li>";
                      }
                    } else {
                      $keyS=toTitle($key);
                      $html.="<li class='menuGroup'>";
                      $html.="<a href='#' aria-expanded='false'>$keyS <span class='fa arrow'></span></a>";
                      $html.="<ul aria-expanded='false' class='secondary collapse'>";
            
                      foreach ($menu as $key1 => $menu1) {
                        $menu1['title']=_ling($menu1['title']);
                        $more=[];
                        if($menu1['target']!=null && strlen($menu1['target'])>0) {
                          $more[]="target='{$menu1['target']}'";
                        }
                        if($menu1['class']!=null && strlen($menu1['class'])>0) {
                          $more[]="class='menuItem {$menu1['class']}'";
                        } else {
                          $more[]="class='menuItem'";
                        }
                        if($menu1['category']!=null && strlen($menu1['category'])>0) {
                          $more[]="data-category='{$menu1['category']}'";
                        }
                        if($menu1['tips']!=null && strlen($menu1['tips'])>0) {
                          $more[]="title='{$menu1['tips']}'";
                        }
            
                        if($menu1['iconpath']!=null && strlen($menu1['iconpath'])>0) {
                          $html.="<li><a href='{$menu1['link']}' ".implode(" ", $more)."><i class='menuIcon {$menu1['iconpath']}'></i>&nbsp; {$menu1['title']}</a></li>";
                        } else {
                          $html.="<li><a href='{$menu1['link']}' ".implode(" ", $more).">{$menu1['title']}</a></li>";
                        }
                      }
            
                      $html.="</ul>";
                      $html.="</li>";
                    }
                  }
                  $html.=$html1;
                  $html.="</ul>";
                  $html.="</li>";
              }
        
              echo $html;
            }
            echo $htmlLast;
        break;
        case "menugroup":
        default:
            $menuTree = getMenuTree($menuSrc);
            foreach ($menuTree as $category=>$menuSet) {
              if(count($menuSet)<=0 || strlen($category)<=0) continue;
              $hash=md5($category);
              $category = _ling($category);
              $html="<li class='menuGroup'>";
              $html.="<a href='#' aria-expanded='false'>$category <span class='fa arrow'></span></a>";
              $html.="<ul aria-expanded='false' class='secondary collapse'>";
        
              $html1="";
              foreach ($menuSet as $key => $menu) {
                //$menu['title']=_ling($menu['title']);
                if(is_numeric($key)) {
                  $menu['title']=_ling($menu['title']);
                  $menu['tips']=_ling($menu['tips']);
                  $more=[];
                  if($menu['target']!=null && strlen($menu['target'])>0) {
                    $more[]="target='{$menu['target']}'";
                  }
                  if($menu['class']!=null && strlen($menu['class'])>0) {
                    $more[]="class='menuItem {$menu['class']}'";
                  } else {
                    $more[]="class='menuItem'";
                  }
                  if($menu['category']!=null && strlen($menu['category'])>0) {
                    $more[]="data-category='{$menu['category']}'";
                  }
                  if($menu['tips']!=null && strlen($menu['tips'])>0) {
                    $more[]="title='{$menu['tips']}'";
                  }
        
                  if($menu['iconpath']!=null && strlen($menu['iconpath'])>0) {
                    $html1.="<li><a href='{$menu['link']}' ".implode(" ", $more)."><i class='menuIcon {$menu['iconpath']}'></i>&nbsp; {$menu['title']}</a></li>";
                  } else {
                    $html1.="<li><a href='{$menu['link']}' ".implode(" ", $more).">{$menu['title']}</a></li>";
                  }
                } else {
                  $keyS=toTitle($key);
                  $keyS=_ling($keyS);
                  $html.="<li class='menuGroup'>";
                  $html.="<a href='#' aria-expanded='false'>$keyS <span class='fa arrow'></span></a>";
                  $html.="<ul aria-expanded='false' class='secondary collapse'>";
        
                  foreach ($menu as $key1 => $menu1) {
                    $menu1['title']=_ling($menu1['title']);
                    $more=[];
                    if($menu1['target']!=null && strlen($menu1['target'])>0) {
                      $more[]="target='{$menu1['target']}'";
                    }
                    if($menu1['class']!=null && strlen($menu1['class'])>0) {
                      $more[]="class='menuItem {$menu1['class']}'";
                    } else {
                      $more[]="class='menuItem'";
                    }
                    if($menu1['category']!=null && strlen($menu1['category'])>0) {
                      $more[]="data-category='{$menu1['category']}'";
                    }
                    if($menu1['tips']!=null && strlen($menu1['tips'])>0) {
                      $more[]="title='{$menu1['tips']}'";
                    }
        
                    if($menu1['iconpath']!=null && strlen($menu1['iconpath'])>0) {
                      $html.="<li><a href='{$menu1['link']}' ".implode(" ", $more)."><i class='menuIcon {$menu1['iconpath']}'></i>&nbsp; {$menu1['title']}</a></li>";
                    } else {
                      $html.="<li><a href='{$menu1['link']}' ".implode(" ", $more).">{$menu1['title']}</a></li>";
                    }
                  }
        
                  $html.="</ul>";
                  $html.="</li>";
                }
              }
              $html.=$html1;
              $html.="</ul>";
              $html.="</li>";
        
              echo $html;
            }
        break;
    }
}

?>
<ul id="sidebarTabLeft" class="nav nav-tabs nav-justified" data-tabs="tabs">
    <?php
        if(count($finalSidebars)>2) {
            foreach($finalSidebars as $k=>$bar) {
                $barTitle = ucwords(_ling($bar));
                $barHash = md5($bar);
                $claz = ($k==0)?"active":"";
                $barParam = $availableMenus[$bar];
                ?>
                <li role="presentation" class="<?=$claz?>"><a href="#sidebar<?=$barHash?>" data-toggle="tab" title="<?=$barTitle?>"><i class="<?=$barParam['icon']?>"></i>&nbsp;</a></li>
                <?php
            }
        } else {
            foreach($finalSidebars as $k=>$bar) {
                $barTitle = ucwords(_ling($bar));
                $barHash = md5($bar);
                $claz = ($k==0)?"active":"";
                $barParam = $availableMenus[$bar];
                ?>
                <li role="presentation" class="<?=$claz?>"><a href="#sidebar<?=$barHash?>" data-toggle="tab" title="<?=$barTitle?>"><i class="<?=$barParam['icon']?>"></i>&nbsp;<?=$barTitle?></a></li>
                <?php
            }
        }
    ?>
</ul>
<div id="sidebarPaneLeft" class="tab-content">
    <?php
    foreach($finalSidebars as $k=>$bar) {
        $barHash = md5($bar);
        $barParam = $availableMenus[$bar];
        $claz = ($k==0)?"active":"";
        ?>
        <div id='sidebar<?=$barHash?>' class='tab-pane <?=$claz?>'>
            <ul class="metismenu" id="metismenu<?=$k?>">
            <?php
                getMenuContent($bar);
            ?>
            </ul>
        </div>
        <?php
    }
    ?>
</div>

<script type="text/javascript">
$(function() {
//    $('#metismenu').metisMenu();
// 	$('#metismenu1').metisMenu();
// 	$('#metismenu2').metisMenu();
  
    $('.metismenu').each(function() {
      $(this).metisMenu();
    });
	
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
