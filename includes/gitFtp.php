<?php
class gitFtp{
	var $conf = [];
	var $dir = "";
	var $ftp_conn = "";
	var $ftp_dir = "";
	var $io_server = "";

	function __construct($dir = "", $conf){
		$this->dir = $conf['dir_htdocs']."$dir/";
		$this->conf = $conf;
	}

	static function dir($dir, $conf){
		exec("cd ".$conf['dir_htdocs']."$dir/ && git status", $o);
		if(empty($o)) return false;
		return new gitFtp($dir, $conf);
	}

	static function nms($str){
		$str = preg_replace("/[\s]+/", " ", $str);
		$str = trim($str);
		return $str;
	}

	function exec($comand){
		exec("cd ".$this->dir." && ".$comand, $o);
		return $o;
	}

	function detail_commit($data, $code){
		if($data == "name") $o = $this->exec("git log -1 --pretty=format:%cn $code");
		if($data == "desc") $o = $this->exec("git log -1 --pretty=format:%s $code");
		return isset($o[0]) ? $o[0] : "";
	}

	function list_commit($key = ""){
		$o = $this->exec("git log");
		$list = [];
		$i = -1;
		foreach ($o as $k => $v) {
			if(strpos(" ".$v, "commit") == 1){
				$i++;
				$list[$i]['code'] = trim(str_replace("commit ","",$v));
				$list[$i]['shortcode'] = substr($list[$i]['code'], 0, 6);
			}elseif(strpos(" ".$v, "Author:") == 1){
				$list[$i]['author'] = trim(str_replace("Author: ","",$v));
			}elseif(strpos(" ".$v, "Date:") == 1){
				$list[$i]['date'] = trim(str_replace("Date: ","",$v));
			}else{
				if(isset($list[$i]['desc'])){
					$list[$i]['desc'] .= trim($v);
				}else{
					$list[$i]['desc'] = trim($v);
				}
			}
		}
		if(!empty($key)){
			$res = [];
			foreach ($list as $row) {
				if($key == "shortcode"){
					$res[] = substr($row['code'], 0, 6);
				}else{
					$res[] = $row[$key];
				}
			}
			return array_reverse($res);
		}
		return array_reverse($list);
	}

	function file_unstage($origin = 'status'){
		$file = [];

		$o = $this->exec("git diff --name-status");
		if($origin == 'status'){
			$file = ['add' => '', 'merge' => '', 'delete' => ''];
			foreach ($o as $k => $v) {
				$x = explode(" ",static::nms($v));
				if(trim($x[0]) == "A") $status = "add";
				if(trim($x[0]) == "M") $status = "merge";
				if(trim($x[0]) == "D") $status = "delete";
				$file[$status][$k] = trim($x[1]);
			}
			return $file;
		}
		if($origin == 'key'){
			foreach ($o as $k => $v) {
				$x = explode(" ",static::nms($v));
				if(trim($x[0]) == "A") $status = "add";
				if(trim($x[0]) == "M") $status = "merge";
				if(trim($x[0]) == "D") $status = "delete";
				$file[$x[1]] = $status;
			}
			return $file;
		}
		print_r($file);die;
		return $o;
	}

	function file_commit($code, $origin = 'status'){
		$file = [];

		$o = $this->exec("git diff --name-status $code");
		if($origin == 'status'){
			$file = ['add' => '', 'merge' => '', 'delete' => ''];
			foreach ($o as $k => $v) {
				$x = explode(" ",static::nms($v));
				if(trim($x[0]) == "A") $status = "add";
				if(trim($x[0]) == "M") $status = "merge";
				if(trim($x[0]) == "D") $status = "delete";
				$file[$status][$k] = trim($x[1]);
			}
			return $file;
		}
		if($origin == 'key'){
			foreach ($o as $k => $v) {
				$x = explode(" ",static::nms($v));
				if(trim($x[0]) == "A") $status = "add";
				if(trim($x[0]) == "M") $status = "merge";
				if(trim($x[0]) == "D") $status = "delete";
				$file[$x[1]] = $status;
			}
			return $file;
		}
		return $o;
	}

	function file_committo($code, $tocode, $origin = false){
		$file = [];$start = false;

		if($tocode){
			if(strlen($code) == 6){
				$list_commit = $this->list_commit('shortcode');
			}else{
				$list_commit = $this->list_commit('code');
			}
			foreach($list_commit as $commit) {
				if($commit == $code){
					$start = true;
					$file = $this->file_commit($commit, 'key');
				}elseif($commit == $tocode){
					$file = array_merge($file,$this->file_commit($commit, 'key'));
					$start = false;
				}elseif($start){
					$file = array_merge($file,$this->file_commit($commit, 'key'));
				}
			}
		}

		return $file;
	}

	// FTP Connection
	function socket_connect($server, $channel){
		$this->io_server = $server;
		$this->io_channel = $channel;
		return $this;
	}

	function socket_emit($event, $data){
		$field['channel'] = $this->io_channel;
		$field['event'] = $event;
		$field['data'] = $data;

		curl($this->io_server, $field);
	}

	// FTP Connection
	function ftp_connect($ftp_server, $ftp_username, $ftp_userpass, $ftp_dir){
		$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
		// turn passive mode on
		ftp_pasv($ftp_conn, true);
		$this->ftp_conn = $ftp_conn;
		$this->ftp_dir = $ftp_dir;
		return $this;
	}

	// Upload file with git repository
	function ftp_push($commit){
		$count = count($commit);
		$complete = 0;
		foreach ($commit as $file => $status) {
			// Explode part of remote file
			$parts = explode('/',trim($this->ftp_dir.$file, "/"));
			$last = count($parts) -1;
			$filename = $parts[$last];
			foreach ($this->conf['rm_dir_part'] as $v) {
				unset($parts[$v]);
			}
			unset($parts[$last]);
			$dir = "";
			foreach($parts as $part){
				$dir .= $part."/";
				if(empty(ftp_nlist($this->ftp_conn,$dir))){
					if(in_array($status, ['add','merge'])){
						ftp_mkdir($this->ftp_conn, $dir);
						//ftp_chmod($this->ftp_conn, 0777, $dir);
					}
				}
			}
			$file_part = $dir.$filename;
			// Add or Update
			if(in_array($status, ['add','merge'])){
				ftp_put($this->ftp_conn, $file_part, $this->dir.$file, FTP_ASCII);
			}
			// Deleted Files
			if(in_array($status, ['delete'])){
				ftp_delete($this->ftp_conn, $file_part);
			}
			$complete++;
			// Realtime with socket
			if($this->io_server){
				$data['percentage'] = ($complete / $count * 100)."%";
				$data['file'] = $status.": ".$file;
				$this->socket_emit('ftp_push', $data);
			}
		}
	}
}