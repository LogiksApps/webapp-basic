#cssLinks#
<div class='container error'>
	<h1 style='font-size: 150px;text-align: center;'><i class='fa fa-exclamation-triangle color_red' style="margin-right: 10px;"></i>#code#</h1>
	<div class="panel panel-default">
  <div class="panel-body">
    {if $APPS_STATUS eq 'prod'}
    	<p>#msg#</p>
		{elseif $APPS_STATUS eq 'staging'}
			<p>#msg#</p>
			<p style="border-top: 1px dotted #AAA;width: 100%;display: block;padding-top: 5px;">#file# <span>line</span> #line#</p>
		{else}
			<p>#msg#</p>
			<p style="border-top: 1px dotted #AAA;width: 100%;display: block;padding-top: 5px;">#file# <span>line</span> #line#</p>
		{/if}
  </div>
</div>
</div>
#jsLinks#
