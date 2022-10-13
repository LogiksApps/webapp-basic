<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!isset($_ENV['INFOVIEW']['config'])) {
    echo "<p>Error in configuration</p>";
    return;
}

if(!isset($_ENV['INFOVIEW']['vmode'])) {
	$_ENV['INFOVIEW']['vmode']="view";
}

$config=$_ENV['INFOVIEW']['config'];
// printArray($config);
$where=processRefRules($config);

$commentUID="commentsBox".md5(time());
$_SESSION[$commentUID]=[
        "query"=>$where,
    ];

if(isset($config['ref_src']) && isset($config['ref_id'])) {
    $_SESSION[$commentUID]['create']=processRefRules(["ref_src"=>$config['ref_src'],"ref_id"=>$config['ref_id']]);
}

echo _css("userComments");

// $shareArr=[
//   "@{$_SESSION['SESS_GROUP_NAME']}"=>"My Team",
//   "#{$_SESSION['SESS_PRIVILEGE_NAME']}"=>"All in {$_SESSION['SESS_PRIVILEGE_NAME']}",
// ];

// $staffData=_db()->_selectQ("profiletbl","loginid,full_name",["blocked"=>'false',"type"=>"employee","status"=>"active"])->_GET();
// if(!$staffData) $staffData=[];

// $teamData=_db(true)->_selectQ(_dbTable("users_group",true),"*",["guid"=>[["global",$_SESSION['SESS_GUID']],"IN"]])->_GET();
// if(!$teamData) $teamData=[];
?>
<section id='<?=$commentUID?>' class="infoTableView infoTableGrid detailBox" data-page="0" data-limit="20" data-ui="grid" data-cmd='loadUserComments' data-template='comments-template' >
    <div class="commentBox clearfix">
			<?php if($_ENV['INFOVIEW']['vmode']!="view") { ?>
        <form class="form-inline row" role="form">
            <div class="form-group col-md-10">
                <textarea id='commentTextArea' class="form-control" type="text" placeholder="Your comments. You can use @user, #team and !privilegeGroup. SHIFT+ENTER for submiting" style="width: 100%;" autocomplete=off ></textarea>
            </div>
            <div class="form-group col-md-2">
                <button type='button' class="btn btn-default" style="width: 100%;" onclick='newComment(this)'><i class='fa fa-play'></i> POST</button>
            </div>
        </form>
        <citie style='font-size: 10px;font-weight: bold;'>You can assign using @user, #team and !privilegeGroup. SHIFT+ENTER for submiting</citie>
      <?php } ?>
    </div>  
    <div class="actionBox">
		<ul class="commentList">
		</ul>
    </div>
</section>
<script id="comments-template" type="text/x-handlebars-template">
    {{#each Data}}
    <li>
       {{#if useravatar}}
        <div class="commenterImage">
          <img src="{{useravatar}}">
        </div>
        {{else}}
         <div class="commenterImage">
          <img src="<?=loadMedia("images/user.png")?>">
        </div>
         {{/if}}
        <div class="commentText">
            <p class="commentTitle">{{{userprofile created_by}}} -> {{{userprofile shared_with}}} </p> 
            <p>{{{msg}}}</p>
            <div class="date sub-text text-right" title='{{edited_on}}'>{{humanDate edited_on}}</div>
        </div>
    </li>
    {{/each}}
</script>
<script>
$(function() {
  $("#commentTextArea").keydown(function(e) {
    if(e.keyCode==13 && e.shiftKey) {
      e.preventDefault();
      newComment(this);
    }
  });
});
function loadUserComments(divBlock) {
    lx=_service("userComments","list-comments")+"&dcode=<?=$commentUID?>";
    processAJAXQuery(lx, function(data) {
        console.log(data);
        tmpl=$(divBlock).data("template");
        htmlData=Handlebars.compile($("#"+tmpl).html());
        htmlData=htmlData(data);
        $(".commentList",divBlock).html(htmlData)
			
		if($(".commentList",divBlock).children().length<=0) {
			$(".commentList",divBlock).html("<h3 class='text-center'>No comments found ...</h3>");
		}
    },"json");
}
function newComment(btn) {
    var  msg= $("#commentTextArea").val();
   var  msg_encode=encodeURIComponent(msg);
    //alert(msg_encode);
    form=$(btn).closest("form");
    q="msg="+msg_encode+"&shared_with="+$("#shareWith",form).val()+"&dcode=<?=$commentUID?>";
   // alert(q);
    processAJAXPostQuery(_service("userComments","create-comment"),q,function(data) {
        lgksToast(data.Data);
        $("#commentTextArea").val("");
        loadUserComments("#<?=$commentUID?>");
    },"json");
}
function resetComment() {
    $("#commentTextArea").val("");
}
Handlebars.registerHelper('if', function(conditional, options) {
        if (conditional) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
</script>