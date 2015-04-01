<?php
require "conf.php";
require "includes/functions.php";
require "includes/dbar_class.inc.php";
$db = new DBAR;
$id = isset($_GET['id']) ? $_GET['id'] : "";
if(!$id) redirect("index.php");

$row = $db->where(["pj_id" => $id])->limit(1)->get("project")->row_array();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0">
	<title>Git FTP</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/jquery.gritter.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<script type="text/javascript" src="assets/js/socket.io.js"></script>
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.gritter.js"></script>
	<script type="text/javascript">
	var $project = "<?php echo $row['pj_dir'];?>";
	var $conf = <?php echo json_encode($conf);?>;
	</script>
	<script type="text/javascript" src="assets/js/main.js"></script>
</head>
<body>
<div class="container" style="padding-top:20px;">
	<div class="form-group row">
		<a href="index.php" class="btn btn-default to_list"><i class="glyphicon glyphicon-circle-arrow-left"></i> Back</a>
	</div>
	<input type="hidden" id="posix_id" value="<?php echo getmypid();?>">
	<input type="hidden" id="project-id" value="<?php echo $id;?>">
	<div class="form-group row">
		<label class="col-xs-2">Directory Project</label>
		<div class="col-xs-4">
			<input type="text" id="project" class="form-control" value="<?php echo $row['pj_dir'];?>">
		</div>
		<div class="col-xs-2">
			<button type="submit" data-toggle="modal" data-target="#git_conf" class="btn btn-primary btn-block">Git Configuration</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" data-toggle="modal" data-target="#ftp_conf" class="btn btn-warning btn-block">FTP Configuration</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" class="btn btn-success btn-block save_project"><u>S</u>ave</button>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xs-2">Action</label>
		<div class="col-xs-2">
			<button data-toggle="modal" data-target="#graph_modal" class="btn btn-primary btn-block"><u>G</u>raph Commit</button>
		</div>
		<div class="col-xs-2">
			<button data-toggle="collapse" data-target="#coll_git_push" class="btn btn-primary btn-block">Git <u>P</u>ush</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_check" class="btn btn-info btn-block"><u>C</u>heckout</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_transfer" class="btn btn-primary btn-block">Depl<u>o</u>y</button>
		</div>
	</div>
	<div id="coll_git_push" class="form-group collapse row">
		<label class="col-xs-2">Git Subject</label>
		<div class="col-xs-4">
			<label for="commit_subject">Commit Subject</label>
			<textarea id="commit_subject" class="form-control" rows="3"></textarea>
		</div>
	</div>
	<div id="result_progress" class="form-group collapse row">
		<label class="col-xs-2">Result Progress</label>
		<div class="col-xs-10">
			<div class="progress progress-striped active">
			  <div class="progress-bar" style="width: 0%">
			    <span class="sr-only">45% Complete</span>
			  </div>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-xs-6">
			<label>Result File : <span class="result"></span></label>
			<div class="content_file">
				<div class="result_content">
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<label>Result Console : <span class="progress_title"></span></label>
			<div class="content_file">
				<div class="result_console">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal Git Configuration -->
<div class="modal fade" id="git_conf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Git Configuration</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="git_repo">Git Repository</label>
			<input type="text" id="git_repo" class="form-control" placeholder="Git Repository" value="<?php echo $row['pj_git_repo'];?>">
		</div>
		<div class="form-group">
			<label for="git_user">Git Username</label>
			<input type="text" id="git_user" class="form-control" placeholder="Git Username" value="<?php echo $row['pj_git_user'];?>">
		</div>
		<div class="form-group">
			<label for="git_pass">Git Password</label>
			<input type="password" id="git_pass" class="form-control" placeholder="Git Password" value="<?php echo $row['pj_git_pass'];?>">
		</div>
		<div class="form-group">
			<label for="git_ignore_dir">Git Ignore Directory</label>
			<input type="text" id="git_ignore_dir" class="form-control" placeholder="Git Ignore Directory" value="<?php echo $row['pj_git_ignore_dir'];?>">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="$('.save_project').trigger('click')">save</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal FTP Configuration -->
<div class="modal fade" id="ftp_conf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">FTP Configuration</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="ftp_server">FTP Server</label>
			<input type="text" id="ftp_server" class="form-control" placeholder="FTP Server" value="<?php echo $row['pj_ftp_server'];?>">
		</div>
		<div class="form-group">
			<label for="ftp_user">FTP User</label>
			<input type="text" id="ftp_user" class="form-control" placeholder="FTP User" value="<?php echo $row['pj_ftp_user'];?>">
		</div>
		<div class="form-group">
			<label for="ftp_pass">FTP Password</label>
			<input type="password" id="ftp_pass" class="form-control" placeholder="FTP Password" value="<?php echo $row['pj_ftp_pass'];?>">
		</div>
		<div class="form-group">
			<label for="ftp_dir">FTP Directory</label>
			<input type="text" id="ftp_dir" class="form-control" placeholder="FTP Directory" value="<?php echo $row['pj_ftp_dir'];?>">
		</div>
      </div>
      <div class="modal-footer">
		<button type="submit" id="submit_ftp_check" class="btn btn-warning">Check Connection</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="$('.save_project').trigger('click')">save</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Graph Commit -->
<div class="modal fade" id="graph_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Graph Commit</h4>
      </div>
      <div class="modal-body">
		<div class="form-group">
			<label for="submit_project">Action</label>
			<button type="submit" id="submit_project" class="btn btn-primary">Graph Commit</button>
		</div>
		<div class="form-group">
			<label for="commit_from">Commit From</label>
			<select id="commit_from" class="form-control">
				<option>Commit From</option>
			</select>
		</div>
		<div class="form-group">
			<label for="commit_to">Commit To</label>
			<select id="commit_to" class="form-control">
				<option>Commit To</option>
			</select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="$('#submit_check').trigger('click')">Checkout</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="$('#submit_transfer').trigger('click')">Deploy</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function(){
	var file_upload = [];
	app.on('ftp_push:'+$project, function(data){
		$("#result_progress").collapse("show");
		file_upload.push(data);
		var html = "";
		$.each(file_upload.reverse(), function(i, row){
			html += row.file+"<br>";
		});
		$("#result_progress").find(".progress-bar").width(data.percentage);
		$(".result").html(parseInt(data.percentage)+"%");
		$("#posix_id").html(data.pid);
		$(".result_content").html(html);
	}, true);

	app.on('git_push:'+$project, function(data){
		console.log(data);
		$("#result_progress").collapse("show");
		$("#result_progress").find(".progress-bar").width(data.process+"%");
		$(".progress_title").html(data.action);
		$(".result_console").html(data.result);
		if(data.process == 100) gAlert(data.action);
	}, true);
});
</script>
</body>
</html>