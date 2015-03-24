<?php
ini_set('max_execution_time', 0);
require "includes/functions.php";
require "includes/gitFtp.php";
require "includes/dbar_class.inc.php";
$db = new DBAR;

// Save Project
if(isset($_POST['action']) && $_POST['action'] == "save_project"){
	$id = $_POST['id'];
	if(empty($_POST['project'])){ echo "Please enter project directory!";die;}
	if(strlen($_POST['project']) <= 3){ echo "Project directory must be more than 3 character";die;}
	$data = [
		'pj_dir' => $_POST['project'],
		'pj_ftp_server' => $_POST['ftp_server'],
		'pj_ftp_user' => $_POST['ftp_user'],
		'pj_ftp_pass' => $_POST['ftp_pass'],
		'pj_ftp_dir' => $_POST['ftp_dir'],
	];

	$row = $db->where(["pj_id" => $id])->limit(1)->get("project")->row_array();
	if($row){
		$db->update_data('project', $data, ['pj_id' => $id]);
	}else{
		$data['pj_id'] = $id;
		$db->insert_data('project', $data);
	}
	echo "Project Saved!";
	die;
}

// Delete Project
if(isset($_GET['delete'])){
	$id = $_GET['delete'];
	$db->delete_data("project", ["pj_id" => $id]);
	redirect("index.php");
	die;
}

// FTP Check Connection
if(isset($_POST['action']) && $_POST['action'] == "ftp_check"){
	$ftp_conn = ftp_connect($_POST['ftp_server']) or die("Could not connect to $_POST[ftp_server]");
	ftp_login($ftp_conn, $_POST['ftp_user'], $_POST['ftp_pass']);
	echo is_array(ftp_nlist($ftp_conn, "")) ? 'Connected!' : 'Not Connected!';
	die;
}

// Graph Commit Data
if(isset($_POST['action']) && $_POST['action'] == "list_commit"){
	$gp = gitFtp::dir($_POST['project']);
	// If no git
	if(!$gp){
		echo "error";die;
	}

	// Graph all commit
	$res = array_reverse($gp->list_commit());
	$select = "";
	foreach($res as $commit){
		$select .= '<option value="'.$commit['shortcode'].'">'.$commit['shortcode'].' ('.substr($commit['desc'],0,20).')</option>';
	}
	echo $select;die;

	$gp = gitFtp::dir("ut-develop");

	$o = $gp->file_committo('db17f0', '96b9d3');
	// $gp->ftp_push($o);die;
	print_r($o);die;
	if($gp){
		$res = $gp->list_commit('shortcode');
		print_r($res);
	}
}

// Checkout Files
if(isset($_POST['action']) && $_POST['action'] == "checkout"){
	$gp = gitFtp::dir($_POST['project']);
	$o = $gp->file_committo($_POST['commit_from'], $_POST['commit_to']);
	if(empty($o)){
		$o = $gp->file_unstage('key');
	}
	echo json_encode($o);
}

// Git upload to FTP
if(isset($_POST['action']) && $_POST['action'] == "ftp_push"){
	$gp = gitFtp::dir($_POST['project']);
	$gp = $gp->ftp_connect($_POST['ftp_server'],$_POST['ftp_user'],$_POST['ftp_pass'],$_POST['ftp_dir']);
	$gp = $gp->socket_connect('http://zonareplika.com:2000/pub','gitFtp');
	
	$o = $gp->file_committo($_POST['commit_from'], $_POST['commit_to']);
	if(empty($o)){
		$o = $gp->file_unstage('key');
		$gp->exec("git add -A");
	}
	$gp->ftp_push($o);

	$id = $_POST['id'];
	$last_comm_desc = $_POST['commit_to'].": ".$gp->detail_commit("desc", $_POST['commit_to']);

	$db->update_data('project', $data, ['pj_id' => $id]);
	$db->update_data("project", ['pj_last_push' => $last_comm_desc], ['pj_id' => $id]);
	die;
}
die;