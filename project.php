<?php
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
	<link rel="stylesheet" href="assets/css/style.css">
	<script type="text/javascript" src="assets/js/socket.io.js"></script>
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/main.js"></script>
</head>
<body>
<div class="container" style="padding-top:20px;">
	<div class="form-group row">
		<a href="index.php" class="btn btn-default"><i class="glyphicon glyphicon-circle-arrow-left"></i> Back</a>
	</div>
	<input type="hidden" id="project-id" value="<?php echo $id;?>">
	<div class="form-group row">
		<label class="col-xs-2">Directory Project</label>
		<div class="col-xs-4">
			<input type="text" id="project" class="form-control" value="<?php echo $row['pj_dir'];?>">
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_project" class="btn btn-primary btn-block">Graph Commit</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="save_project" class="btn btn-success btn-block">Save</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_ftp_check" class="btn btn-warning btn-block">FTP Check</button>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xs-2">FTP</label>
		<div class="col-xs-3">
			<input type="text" id="ftp_server" class="form-control" placeholder="FTP Server" value="<?php echo $row['pj_ftp_server'];?>">
		</div>
		<div class="col-xs-2">
			<input type="text" id="ftp_user" class="form-control" placeholder="FTP User" value="<?php echo $row['pj_ftp_user'];?>">
		</div>
		<div class="col-xs-2">
			<input type="password" id="ftp_pass" class="form-control" placeholder="FTP Password" value="<?php echo $row['pj_ftp_pass'];?>">
		</div>
		<div class="col-xs-3">
			<input type="text" id="ftp_dir" class="form-control" placeholder="FTP Directory" value="<?php echo $row['pj_ftp_dir'];?>">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xs-2">Commit Data</label>
		<div class="col-xs-3">
			<select id="commit_from" class="form-control">
				<option>Commit From</option>
			</select>
		</div>
		<div class="col-xs-3">
			<select id="commit_to" class="form-control">
				<option>Commit To</option>
			</select>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_check" class="btn btn-info btn-block">Checkout</button>
		</div>
		<div class="col-xs-2">
			<button type="submit" id="submit_transfer" class="btn btn-primary btn-block">Deploy</button>
		</div>
	</div>
	<div>Result : <span class="result"></span></div>
	<div class="content_file">
		<div class="result_content">
		</div>
	</div>
</div>
</body>
</html>