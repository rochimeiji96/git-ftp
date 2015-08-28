<?php
ini_set('max_execution_time', 0);
require "conf.php";
require "includes/functions.php";
require "includes/EIO.php";
require "includes/gitFtp.php";
require "includes/dbar_class.inc.php";
$db = new DBAR;

// $EIO = EIO::app('pub');
// echo $EIO->send("asda",'asdasd');

// Update GIT-FTP
if(isset($_GET['update'])){
	exec("git pull", $o);
	echo "Update GIT-FTP Succesfull";
	die;
}

// Update GIT-FTP
if(isset($_POST['action']) && $_POST['action'] == "posix_kill"){
	$pid = $_POST['pid'];
	$project = $_POST['project'];
	kil_pid($pid, 15);

	$EIO = EIO::app('pub');
	$EIO->flush("ftp_push:".$project);
	echo "Update GIT-FTP Succesfull";
	die;
}

// Save Project
if(isset($_POST['action']) && $_POST['action'] == "save_project"){
	$id = $_POST['id'];
	if(empty($_POST['project'])){ echo "Please enter project directory!";die;}
	if(strlen($_POST['project']) <= 3){ echo "Project directory must be more than 3 character";die;}
	$data = [
		'pj_dir' => $_POST['project'],
		'pj_git_repo' => $_POST['git_repo'],
		'pj_git_user' => $_POST['git_user'],
		'pj_git_pass' => $_POST['git_pass'],
		'pj_git_ignore_dir' => $_POST['git_ignore_dir'],
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
	$gp = gitFtp::dir($_POST['project'], $conf);
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
}

// Checkout Files
if(isset($_POST['action']) && $_POST['action'] == "checkout"){
	$gp = gitFtp::dir($_POST['project'], $conf);
	// echo $_POST['git_ignore_dir'];die;
	$gp->git_ignore_dir($_POST['git_ignore_dir']);
	$o = $gp->file_committo($_POST['commit_from'], $_POST['commit_to']);
	if(empty($o)){
		$o = $gp->file_unstage('key');
	}
	echo json_encode($o);
}

// Git upload to FTP
if(isset($_POST['action']) && $_POST['action'] == "ftp_push"){
	$gp = gitFtp::dir($_POST['project'], $conf);
	$gp = $gp->ftp_connect($_POST['ftp_server'],$_POST['ftp_user'],$_POST['ftp_pass'],$_POST['ftp_dir']);
	$gp->git_ignore_dir($_POST['git_ignore_dir']);
	
	$o = $gp->file_committo($_POST['commit_from'], $_POST['commit_to']);
	if(empty($o)){
		$o = $gp->file_unstage('key');
		$gp->exec("git add -A");
	}else{
		$id = $_POST['id'];
		$last_comm_desc = $_POST['commit_to'].": ".$gp->detail_commit("desc", $_POST['commit_to']);

		$db->update_data("project", ['pj_last_push' => $last_comm_desc], ['pj_id' => $id]);
	}
	$gp->ftp_push($o, $_POST['project']);
	die;
}

// Git push repository
if(isset($_POST['action']) && $_POST['action'] == "git_push"){
	$gp = gitFtp::dir($_POST['project'], $conf);
	$project = $_POST['project'];
	$subject = $_POST['commit_subject'];
	$repo = $_POST['git_repo'];
	$user = $_POST['git_user'];
	$pass = $_POST['git_pass'];
	if(strpos($repo, "@")){
		$git_repo = str_replace("@", ":$pass@", $repo);
	}else{
		$git_repo = str_replace("//", "//$user:$pass@", $repo);
	}

	$gp->EIO->send('git_push:'.$project, ['action' => 'Git Add', 'result' => '', 'process' => 0]);
	$o = $gp->exec('git add -A', true);

	$gp->EIO->send('git_push:'.$project, ['action' => 'Git Commit '.$subject.'', 'result' => $o, 'process' => 25]);	
	$o = $gp->exec('git commit -m "'.$subject.'"', true);

	$gp->EIO->send('git_push:'.$project, ['action' => 'Git Pull '.$repo, 'result' => $o, 'process' => 50]);
	$o = $gp->exec('git pull '.$git_repo, true);

	$gp->EIO->send('git_push:'.$project, ['action' => 'Git Push '.$repo, 'result' => $o, 'process' => 75]);
	$o = $gp->exec('git push --force '.$git_repo, true);
	// Result
	$gp->EIO->send('git_push:'.$project, ['action' => 'Git Push Successfully', 'result' => $o, 'process' => 100]);
	$gp->EIO->flush('git_push:'.$project);
	die;
}
die;