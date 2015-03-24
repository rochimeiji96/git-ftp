<?php
require "includes/dbar_class.inc.php";
$db = new DBAR;
$data = $db->get("project")->result_array();
$new = $db->order_by("pj_id", "DESC")->limit(1)->get("project")->row_array();
$new_id = $new['pj_id'] + 1;
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
<div class="container" style="padding-top:50px;">
	<div class="row">
		<div class="col-xs-12">
			<a href="project.php?id=<?php echo $new_id;?>" class="btn btn-success">Add New</a>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-striped">
		      <thead>
		        <tr>
		          <th>No</th>
		          <th>Project Directory</th>
		          <th>FTP Server</th>
		          <th>Last Push</th>
		          <th>Action</th>
		        </tr>
		      </thead>
		      <tbody>
		      <?php
		      $no = 1;
		      foreach ($data as $key => $row) { ?>
		        <tr>
		          <td><?php echo $no;?></td>
		          <td><a href="project.php?id=<?php echo $row['pj_id'];?>"><?php echo $row['pj_dir'];?></a></td>
		          <td><?php echo $row['pj_ftp_server'];?></td>
		          <td><?php echo $row['pj_last_push'] ? $row['pj_last_push'] : "-";?></td>
		          <td><a href="action.php?delete=<?php echo $row['pj_id'];?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></a></td>
		        </tr>
		      <?php $no++; } ?>
		      </tbody>
		    </table>
		</div>
	</div>
</div>
</body>
</html>