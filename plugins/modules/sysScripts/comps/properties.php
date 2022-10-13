<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$tableData=["publish_status","created_by","created_on","edited_by","edited_on"];
$noShow=["id","slug","script_code","script_params","tags","blocked"];

$paramsText=$data['script_code'];

$paramArr=scriptParseToParams($paramsText);
?>
<div style='margin: auto;margin-top:10px;margin: 20px;margin-top: 10px;'>   
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Properties</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Other Infos</a></li>
	<li role="presentation"><a href="#params" aria-controls="params" role="tab" data-toggle="tab">Computed</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
   
    <div role="tabpanel" class="tab-pane active" id="home">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="slug" class="col-sm-3 control-label">Script Code</label>
					<div class="col-sm-9">
						<p class="form-control-static"><?=$_POST['slug']?></p>
					</div>
				</div>
				<?php
					foreach($data as $key=>$val) {
						if(in_array($key,$tableData)) continue;
						if(in_array($key,$noShow)) continue;
				?>
					<div class="form-group">
						<label for="<?=$key?>" class="col-sm-3 control-label"><?=toTitle($key)?></label>
						<div class="col-sm-9">
							<?php
								if($key=="platform" || $key=="device" ) {
								    $productTypeDropdown=createDataSelector($key);
								    ?>
								    <select class="form-control" name="<?=$key?>" id="<?=$key?>"  data-value='<?=$val;?>'>
								        <option selected ><?=$val;?></option>
									    <?=$productTypeDropdown?>
									</select>
									<?php
									echo "</select>";
								} elseif($key=="members") {
									echo "<textarea class='form-control' name='{$key}' placeholder='".toTitle($key)."'>{$val}</textarea>";
								} else {
									echo "<input type='text' class='form-control' name='{$key}' placeholder='".toTitle($key)."' value='{$val}' />";
								}
							?>
						</div>
					</div>
				<?php
					}
				?>
				
				<!--<div class="form-group">-->
				<!--	<label for="blocked" class="col-sm-3 control-label">Debug</label>-->
				<!--	<div class="col-sm-9">-->
				<!--		<select class="form-control" name="debug">-->
							<?php
								// if($data['debug']=="true") {
								// 	echo "<option value='false'>False</option><option value='true' selected>True</option>";
								// } else {
								// 	echo "<option value='false' selected>False</option><option value='true'>True</option>";
								// }
							?>
				<!--		</select>-->
				<!--	</div>-->
				<!--</div>-->
				<!--<div class="form-group">-->
				<!--	<label for="publish_status" class="col-sm-3 control-label">Published</label>-->
				<!--	<div class="col-sm-9">-->
				<!--		<select class="form-control" name="publish_status">-->
				<!--			< ?php-->
				<!--				if($data['publish_status']=="true") {-->
				<!--					echo "<option value='false'>False</option><option value='true' selected>True</option>";-->
				<!--				} else {-->
				<!--					echo "<option value='false' selected>False</option><option value='true'>True</option>";-->
				<!--				}-->
				<!--			? >-->
				<!--		</select>-->
				<!--	</div>-->
				<!--</div>-->
				<!--<div class="form-group">-->
				<!--	<label for="blocked" class="col-sm-3 control-label">Blocked</label>-->
				<!--	<div class="col-sm-9">-->
				<!--		<select class="form-control" name="blocked">-->
							<?php
								// if($data['blocked']=="true") {
								// 	echo "<option value='false'>False</option><option value='true' selected>True</option>";
								// } else {
								// 	echo "<option value='false' selected>False</option><option value='true'>True</option>";
								// }
							?>
				<!--		</select>-->
				<!--	</div>-->
				<!--</div>-->
				<!--<br>-->
				<div class="form-group">
					<div class="col-sm-12">
						<button onclick='saveProperties(this)' type="button" class="btn btn-default btn-success pull-right">Submit</button>
					</div>
				</div>
			</form>
		</div>
	 <div role="tabpanel" class="tab-pane" id="profile">
		<div class="table-responsive">
			<table class="table table-bordered table-hover">
				<tbody>
				<?php
				$tableData=array_diff($tableData, ['blocked',"script_code"] );
				foreach($data as $key=>$val) {
					if(!in_array($key,$tableData)) continue;
				?>
					<tr>
						<th>#</th>
						<th><?=toTitle($key)?></th>
						<td><?=$val?></td>
					</tr>
				<?php
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="params">
		<div class="well">
			Below are the parameters that are used in the script draft.
			<br><br>
			<ol class="" style="list-style-type: decimal;margin: -11px;margin-left: 30px;">
				<?php
					foreach($paramArr as $a) {
						echo "<li><a href='#'>{$a}</a></li>";
					}
				?>
			</ol>
		</div>
	</div>	
		
  </div>
</div>