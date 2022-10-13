var sidebarLoaderTimer=null;
var sidebarLoaderPeriod=(5 * 60 * 1000);
var sidebarTimer=[];

$(function() {
	$.ajaxSetup({cache: true});
  
    try {
        Notification.requestPermission(function(a,b) {
          //console.log("Notification Check Done");
        });
    } catch(e) {
        //console.log("Notification Error");
    }
	
	$("#header,#sidebarRight").delegate("a[href]","click",function(e) {
		e.preventDefault();
		
		ttl=$(this).text();
		href=$(this).attr('href');
		trgt=$(this).attr('target');
		
		if(href!=null && href.length>2 && href.substr(0,1)!="#") {
			if(trgt==null) {
				if(href.indexOf("http://")===0 || href.indexOf("https://")===0) {
                    openLinkFrame(ttl,href);
                } else {
                    openLinkFrame(ttl,_link(href));
                }
                
                $("#sidebarRight").removeClass("open");
        
                if(window.screen.width<window.screen.height && window.screen.width<767) {
                    $("#sidebarLeft").removeClass("open");
                    $("#page-wrapper").toggleClass("openSidebar");
                }
			} else if(trgt=="top") {
				window.top.location=href;
			} else if(trgt=="_blank") {
				window.open(href);
			} else if(trgt.substr(0,1)=="_") {
				window.open(href,trgt);
			} else {
				openLinkFrame(ttl,href);
			}
		}
	});
	
	$("#leftMenuOpen").click(function() {
			$("#sidebarRight").removeClass("open");
			$("#sidebarLeft").toggleClass("open");

			$("#page-wrapper").toggleClass("openSidebar");
    
      if($("#page-wrapper").hasClass("openSidebar")) {
        $.cookie("SIDEBAR-OPEN","open",{ path: '/' });
      } else {
        $.cookie("SIDEBAR-OPEN","closed",{ path: '/' });
      }
	});
	$("#rightMenuOpen").click(function() {
// 			$("#sidebarLeft").removeClass("open");
			$("#sidebarRight").toggleClass("open");

// 			$("#page-wrapper").removeClass("openSidebar");
	});

	$(".sidebarContainer .removeSidebars").click(function() {
		$(".sidebarContainer").removeClass("open");
	});
	
	$("#sidebarRight .nav.nav-tabs li").click(function(e) {
			if(typeof $.cookie == "function") {
				$.cookie("SIDEBAR_RIGHT_CURRENT",$(this).find("a").attr("href").substr(1));
			}
		});

	$("#searchQuery").keyup(function(e) {
		if(e.keyCode==13) {
			/*showLoader();
			lgksOverlayURL(_link("popup/search")+"&q="+$("#searchQuery").val(),"Searching ...",function(a) {
				hideLoader();
			});*/
			sURL=_link("modules/search")+"&q="+$("#searchQuery").val();
			openLinkFrame("Search",sURL,true,true);
		}
	});

	//$("#sidebarLeft").addClass("open");
	//$("#sidebarRight").addClass("open");
	if(window.screen.width>1024) {
		if($("#sidebarLeft").length>0) {
          if($.cookie("SIDEBAR-OPEN")=="closed") {
            
          } else {
            $("#sidebarLeft").toggleClass("open");
            $("#page-wrapper").toggleClass("openSidebar");
          }
	    }
	}

	// $("body").delegate(".nav.nav-tabs .dropdown .dropdown-toggle[aria-controls]","click",function() {
	// 	$("#"+$(this).attr("aria-controls")).toggle();
	// });
	
	sidebarLoaderTimer = setInterval(function() {
            loadSidebarItems();
        }, sidebarLoaderPeriod); 
	
    
	initUniversalEvents();
	
// 	loadSidebarItems();
});

function initUniversalEvents() {
	$("body").delegate("a.unilink[data-type]","click",function(e) {
	    e.preventDefault();
		type=$(this).data("type");
		id=$(this).data("hashid");

		link=type+"@"+id;
		showLoader();
		lgksOverlayURL(_link("popup/uniLink/"+link),"Viewer",function() {
			hideLoader();
		});
	});
	$("body").delegate("a.unilink[data-link]","click",function(e) {
	    e.preventDefault();
		link=$(this).data("link");

		showLoader();
		lgksOverlayURL(_link("popup/uniLink/"+link),"Viewer",function() {
			hideLoader();
		});
	});
  $("body").delegate("a.unilinkTop[data-type]","click",function(e) {
	    e.preventDefault();
		type=$(this).data("type");
		id=$(this).data("hashid");

		link=type+"@"+id;
		top.lgksOverlayFrame(_link("modules/uniLink/"+link),"Viewer",function() {
// 			hideLoader();
		});
	});
}

function loadSidebarItems() {
    // .sidebarNotifications
	//console.log("Sidebar Content Loaded every 5 mins ",new Date());
	if(Array.isArray(sidebarTimer)) {
		$.each(sidebarTimer, function(nx, funcName) {
			if(typeof funcName == "function") funcName(nx);
			else if(typeof window[funcName] == "function") window[funcName](nx);
		});
	}
}

function setUnilink(fieldArr,divId){
    $.each(fieldArr,function(k,v) {
        generateUNILink(divId, k, v);
    });
}
function generateUNILink(divID, name, dataLinkType) {
    //console.log(divID+" "+name+" "+dataLinkType+" "+($("td[name='"+name+"']",divID).length));
	$("td[name='"+name+"']",divID).each(function() {
		if($(this).data("refid")!=null) {
			$(this).html("<a href='#' class='unilink' data-type='"+dataLinkType+"' data-hashid='"+$(this).data("refid")+"'>"+$(this).html()+"</a>")
	    } else if($(this).data("rel")!=null) {
	        $(this).html("<a href='#' class='unilink' data-type='"+dataLinkType+"' data-hashid='"+$(this).data("rel")+"'>"+$(this).html()+"</a>")
		} else if($(this).closest("tr").data("refid")!=null) {
			$(this).html("<a href='#' class='unilink' data-type='"+dataLinkType+"' data-hashid='"+$(this).closest("tr").data("refid")+"'>"+$(this).html()+"</a>")
		} else {
			$(this).html("<a href='#' class='unilink' data-type='"+dataLinkType+"' data-hashid='"+$(this).closest("tr").attr("rel")+"'>"+$(this).html()+"</a>")
		}
	});
}

function showLoader() {
	$("body .loader_bg").detach();
	$("body").append("<div class='loader_bg'><div class='loader_wrapper'><div class='inner'><span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span></div></div></div>");
}
function hideLoader() {
	$("body .loader_bg").detach();
}

function toTitle(str) {
  return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
  });
}

//category, multiple, callBack
function browseDocuments(srcBtn,callBack) {
	if(srcBtn==null) {
		ref_src=0;
		ref_id=0;
		profile_id=0;
	    category="";
	    rule="";
	    multiple="false";
	} else {
		ref_src=$(srcBtn).data("ref_src");
		ref_id=$(srcBtn).data("ref_id");
		profile_id=$(srcBtn).data("profileid");
	    category=$(srcBtn).data("category");
	    rule=$(srcBtn).data("rule");
	    multiple=$(srcBtn).data("multiple");
	}
    
    if(category==null) category="*";
    if(rule==null) rule="";
    if(multiple==null || !multiple || multiple=="false") multiple="false";
	else multiple="true";
	
	if(callBack==null) callBack="";
	
	showLoader();
	lx="&ref_id="+ref_id+"&ref_src="+ref_src+"&profileid="+profile_id+"&multiple="+multiple+"&callback="+callBack;
	lgksOverlayURL(_link("popup/docman/browser/"+category+"/"+rule)+lx,"Browse Documents ...",function(a) {
				hideLoader();
			},{
				onEscape:false,
				className:"overlayBox browseFrame"
			});
}

function uploadDocument(srcBtn) {
    if(srcBtn==null) {
        ref_src=0;
    	ref_id=0;
    	profile_id=0;
    } else {
        ref_src=$(srcBtn).data("ref_src");
    	ref_id=$(srcBtn).data("ref_id");
    	profile_id=$(srcBtn).data("profileid");
    }
	
// 	showLoader();
	lgksOverlayFrame(_link("modules/docman/uploader")+"&ref_id="+ref_id+"&ref_src="+ref_src+"&profleid="+profile_id,"Upload Documents ...",function(a) {
	       // hideLoader();
    	},{
			onEscape:false,
			className:"overlayBox uploadFrame"
		});
}
function getSuggestedData(srcBtn) {
	refsrc=$(srcBtn).attr("refsrc");
	refid=$(srcBtn).attr("refid");
	reftype=$(srcBtn).attr("reftype");
	callBack=$(srcBtn).attr("callback");
	
	$(srcBtn).prepend("<i class='fa fa-spinner fa-spin'></i> ");
	
	lgksOverlayURL(_link("modules/suggester")+"&refsrc="+refsrc+"&reftype="+reftype+"&refid="+refid+"&callback="+callBack,"Suggest !!!",function(a) {
			$(srcBtn).find(".fa-spinner").detach();
		}, {
			onEscape:false,
			className:"overlayBox suggestFrame"
		});
}

function showNotification(msgBody, msgTitle) {
  if(msgTitle==null) msgTitle="Notification for you";
  
  if(Notification.permission.toLowerCase()=="granted") {
    new Notification(msgTitle, {
        body: msgBody,
        icon: window.location.origin+"/favicon.ico",
    });
  } else {
    top.lgksToast(msgBody);
  }
}