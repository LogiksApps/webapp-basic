<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$tableData=["blocked","debug","created_by","created_on","edited_by","edited_on","body","sqlquery","params","xtras"];
$noShow=["id","slug"];

$paramsText=$data['body'];

$paramArr=templateParseToParams($paramsText);
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
					<label for="slug" class="col-sm-3 control-label">Template Code</label>
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
								if($key=="share") {
									echo "<select class='form-control' name='share'>";
									foreach(['public','private','members'] as $s) {
										if($s==$val) {
											echo "<option value='{$s}' selected>{$s}</option>";
										} else {
											echo "<option value='{$s}'>{$s}</option>";
										}
									}
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
				
				<div class="form-group">
					<label for="blocked" class="col-sm-3 control-label">Debug</label>
					<div class="col-sm-9">
						<select class="form-control" name="debug">
							<?php
								if($data['debug']=="true") {
									echo "<option value='false'>False</option><option value='true' selected>True</option>";
								} else {
									echo "<option value='false' selected>False</option><option value='true'>True</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="blocked" class="col-sm-3 control-label">Blocked</label>
					<div class="col-sm-9">
						<select class="form-control" name="blocked">
							<?php
								if($data['blocked']=="true") {
									echo "<option value='false'>False</option><option value='true' selected>True</option>";
								} else {
									echo "<option value='false' selected>False</option><option value='true'>True</option>";
								}
							?>
						</select>
					</div>
				</div>
				<br>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
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
					$tableData=array_diff($tableData, ['blocked',"body"] );
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
				Below are the parameters that are used in the template draft.
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