var editor = null;
var currentContent = null;
var currentFileMode = null;

$(function() {
	$("#componentSpace").css("height","99%");
	$("#componentSpace").css("overflow","hidden");
	$("#pgtoolbar .nav.navbar-nav.navbar-left").css("width",$(".pageCompContainer.withSidebar .pageCompSidebar").width());
	//"<a id='toolbtn_editTemplate' title='Edit Template' data-cmd='editTemplate' href='#' class='onOpenEditor hidden'><i class='fa fa-pencil'></i> </a>"+
	$("<a id='toolbtn_saveTemplate' title='Save Template' data-cmd='saveTemplate' href='#' class='onOpenEditor hidden'><i class='fa fa-save'></i> </a>"+
			"<label id='titleContent' class='titleContent'></label>").
			insertAfter($("#pgtoolbar .nav.navbar-nav.navbar-left"));
	
	//$("<label id='titleContent' class='titleContent'></label>").insertAfter($("#pgtoolbar .nav.navbar-nav.navbar-left"));
	
	$('#componentTree').delegate(".list-group-item.list-file a","click",function() {
		file=$(this).closest(".list-group-item");
		
		title=$(this).text();
		slug=$(file).data("slug");
		vers=$(file).data("vers");
		
		openContent(title,slug);
	});
	listContent();
});
function listContent() {
	//closeContentFile();
	$("#componentTree").html("<div class='ajaxloading5'></div>");
	
	processAJAXQuery(_service("templates","list"),function(txt) {
		fs=txt.Data;
		if(fs==null || fs.length<=0) {
			$("#componentTree").html("<p align=center><br>No Content Found.</p>");
			return;
		}
		html="";html1="";
		$.each(fs,function(k,v) {
			if(v.length<=0) return;
			kx=md5(k);
			
			html1+="<div class='list-group-item list-folder'><a href='#item-"+kx+"' data-toggle='collapse'><i class='glyphicon glyphicon-folder-close'></i>"+toTitle(k)+"</a></div>";
			html1+="<div class='list-group-folder collapse' id='item-"+kx+"'>";
			$.each(v,function(m,n) {
				//data-schema='"+k+"/"+n+"' 
				html1+="<div class='list-group-item list-file' title='"+n.title+"' data-id='"+n.id+"' data-slug='"+n.slug+"'>";
				html1+="<a href='#'><i class='fa fa-file'></i><span class='text'>"+n.title+"</span></a>";
				html1+="<input type='checkbox' name='selectFile' class='pull-right' data-slug='"+n.slug+"' data-title='"+n.title+"' /></div>";
			});
			html1+="</div>";
		});
		$("#componentTree").html(html+html1);

		if($('#componentTree .list-group-item[data-slug="'+currentContent+'"]').length>0) {
			$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').closest(".list-group-folder.collapse").addClass("in");
			$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').addClass("active");
			
			tag=$('#componentTree .list-group-item[data-slug="'+currentContent+'"]');
			title=$(tag).text();
			vers=$(tag).data("vers");
			
			$("#pgtoolbar .titleContent").html(title);
		} else {
			$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
		}
	},"json");
}

function openContent(title,slug) {
	currentContent=slug;
	$("#pgtoolbar .titleContent").html(title);
	
	$('#componentTree .list-group-item.active').removeClass("active");
	$('#componentTree .list-group-item[data-slug="'+currentContent+'"]').addClass("active");
	
	loadPreviewComponent();
}

function loadTempateEditor(slug) {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	
	currentFileMode = "body";
	
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadTempateEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templates","fetch"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="content"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<pre id='"+rid+"' style='width:100%;height:100%;border:0px;'></pre>");
			$("#"+rid).html(txt);
			
			loadAceEditor(rid,"html");
			editor.session.setValue(txt);
		}
	},"RAW");
}
function loadStyleEditor($slug) {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	
	currentFileMode = "style";
	
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadStyleEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templates","fetchStyle"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="content"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<pre id='"+rid+"' style='width:100%;height:100%;border:0px;'></pre>");
			$("#"+rid).html(txt);
			
			loadAceEditor(rid,"css");
		}
	},"RAW");
}
function loadQueryEditor(slug) {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	
	currentFileMode = "sqlquery";
	
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadQueryEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templates","fetchSQL"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="content"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<pre id='"+rid+"' style='width:100%;height:100%;border:0px;'></pre>");
			$("#"+rid).html(txt);
			
			loadAceEditor(rid,"sql");
		}
	},"RAW");
}
function loadParamsEditor(slug) {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	
	currentFileMode = "params";
	
	$("#pgtoolbar .onOpenEditor").removeClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadParamsEditor").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templates","fetchParams"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			rid="content"+Math.ceil(Math.random()*1000000);
			$("#componentSpace").html("<pre id='"+rid+"' style='width:100%;height:100%;border:0px;'></pre>");
			$("#"+rid).html(txt);
			
			loadAceEditor(rid,"json");
		}
	},"RAW");
}

function loadInfoComponent() {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	$("#pgtoolbar .onOpenEditor").addClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadInfoComponent").closest("li").addClass("active");
	
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	processAJAXPostQuery(_service("templates","properties"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			$("#componentSpace").html(txt);
		}
	},"RAW");
}
function loadPreviewComponent() {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	$("#pgtoolbar .onOpenEditor").addClass("hidden");
	$("#pgtoolbar .nav.navbar-right li.active").removeClass('active');
	$("#toolbtn_loadPreviewComponent").closest("li").addClass("active");
	$("#componentSpace").html("<h2 class='ajaxloading5'></h2>");
	
	processAJAXPostQuery(_service("templates","preview"),"slug="+currentContent,function(txt) {
		err=txt.trim().split(":");
		if(err[0]=="error") {
			$("#componentSpace").html("<h2 class='errorMsg'>"+err[1]+"</h2>");
		} else {
			$("#componentSpace").html("<div class='contentPreview contentTemplate'>"+txt+"</div>")
		}
	},"RAW");
}

function createContent() {
	lgksPrompt("New Template REFCODE! [FORMAT - category.templatecode]","New Template",function(newName) {
			if(newName!=null && newName.length>0) {
				processAJAXPostQuery(_service("templates","create"),"slug="+newName,function(ans) {
						err=ans.trim().split(":");
						if(err[0]=="error") {
							lgksToast(err[1]);
						} else {
							openContent(newName,ans)
							listContent();
						}
					},"RAW");
			}
		});
}
function deleteContent() {
	q=[];q1=[];
	$("#componentTree input[type=checkbox]:checked").each(function() {
		q.push($(this).data("slug"));
		q1.push("<li>"+$(this).data("title")+"</li>");
	});
	htmlMsg="Are you sure about deleting the following templates?<br><ul style='margin-top: 10px;list-style-type: decimal;margin-left: 20px;'>";
	htmlMsg+=q1.join("");
	htmlMsg+="</ul>";
	lgksConfirm(htmlMsg,"Delete Templates",function(ans) {
		if(ans) {
			processAJAXPostQuery(_service("templates","delete"),"slug="+q.join(","),function(ans) {
						err=ans.trim().split(":");
						if(err[0]=="error") {
							lgksToast(err[1]);
						} else {
							lgksToast(ans);
							listContent();
						}
					},"RAW");
		}
	});
}
function saveTemplate() {
	if(currentContent==null) {
		lgksToast("Please load an template to edit its content");
		return;
	}
	if(currentFileMode==null) {
		lgksToast("Please load an template to edit its content (2)");
		return;
	}
	
	switch(currentFileMode.toUpperCase()) {
		case "BODY":
			txt=editor.getValue();//editor.nicInstances[0].getContent();
			processAJAXPostQuery(_service("templates","saveContent"),"slug="+currentContent+"&type="+currentFileMode.toUpperCase()+"&text="+encodeURIComponent(txt),function(ans) {
				err=ans.trim().split(":");
				if(err[0]=="error") {
					lgksToast(err[1]);
				} else {
					lgksToast(ans);
				}
			},"RAW");
			break;
		case "STYLE":
			txt=editor.getValue();//$("#componentSpace textarea").val();
			processAJAXPostQuery(_service("templates","saveContent"),"slug="+currentContent+"&type="+currentFileMode.toUpperCase()+"&text="+encodeURIComponent(txt),function(ans) {
				err=ans.trim().split(":");
				if(err[0]=="error") {
					lgksToast(err[1]);
				} else {
					lgksToast(ans);
				}
			},"RAW");
			break;
		case "SQLQUERY":
			txt=editor.getValue();//$("#componentSpace textarea").val();
			processAJAXPostQuery(_service("templates","saveContent"),"slug="+currentContent+"&type="+currentFileMode.toUpperCase()+"&text="+encodeURIComponent(txt),function(ans) {
				err=ans.trim().split(":");
				if(err[0]=="error") {
					lgksToast(err[1]);
				} else {
					lgksToast(ans);
				}
			},"RAW");
			break;
		case "PARAMS":
			txt=editor.getValue();//$("#componentSpace textarea").val();
			processAJAXPostQuery(_service("templates","saveContent"),"slug="+currentContent+"&type="+currentFileMode.toUpperCase()+"&text="+encodeURIComponent(txt),function(ans) {
				err=ans.trim().split(":");
				if(err[0]=="error") {
					lgksToast(err[1]);
				} else {
					lgksToast(ans);
				}
			},"RAW");
			break;
		case "XTRAS":
			txt=editor.getValue();//$("#componentSpace textarea").val();
			processAJAXPostQuery(_service("templates","saveContent"),"slug="+currentContent+"&type="+currentFileMode.toUpperCase()+"&text="+encodeURIComponent(txt),function(ans) {
				err=ans.trim().split(":");
				if(err[0]=="error") {
					lgksToast(err[1]);
				} else {
					lgksToast(ans);
				}
			},"RAW");
			break;
	}
	//console.log(currentContent, currentFileMode);
}
function saveProperties(btn) {
	if(currentContent==null) {
		lgksToast("Please load an article to edit its content");
		return;
	}
	frm=$(btn).closest("form");
	
	q=[];
	q.push("slug="+currentContent);
	$(frm).find("input[name],select[name]").each(function() {
		q.push($(this).attr("name")+"="+$(this).val());
	});
	processAJAXPostQuery(_service("templates","saveProps"),q.join("&"),function(ans) {
		err=ans.trim().split(":");
		if(err[0]=="error") {
			lgksToast(err[1]);
		} else {
			lgksToast(ans);
			listContent();
		}
	},"RAW");
}

function closeContentFile() {
	currentContent=null;
	$("#pgtoolbar .titleContent").html("");
	$("#componentSpace").html("<h2 align=center>Please load an template to edit its content</h2>");
}

function loadAceEditor(rid, format) {
	if(format==null) format="html";
	editor = ace.edit(rid);
	editor.setTheme("ace/theme/twilight");
	editor.session.setMode("ace/mode/"+format);
	
	editor.commands.addCommand({
			name: 'saveTemplate',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function(editor) {
					saveTemplate();
			},
			readOnly: true // false if this command should not apply in readOnly mode
	});
}

function sendTemplateMail() {
    if(currentContent==null || currentContent.length<=0) {
        lgksToast("No template selected for demo mail.");
        return;
    }
		
		lgksConfirm("Do you want to send demo mail to your EMail?","Demo EMail", function(a) {
				if(a) {
					processAJAXQuery(_service("templates","demoMail")+"&slug="+currentContent,function(data) {
											lgksAlert(data);
									});
				}
			});
}

function showPrintPreview() {
    if(currentContent==null || currentContent.length<=0) {
        lgksToast("No template selected for print preview.");
        return;
    }
		lgksPrompt("Reference ID#","Print Preview Input", function(txt) {
			if(txt!=null) {
				lgksOverlayFrame(_service("templates","printpreview")+"&slug="+currentContent+"&ref_id="+txt,"Print Preview");
			}
		});	
}

function toTitle(s) {
    if(s==null || s.length<=0) return "";
    return s.charAt(0).toUpperCase()+s.substr(1);
}
