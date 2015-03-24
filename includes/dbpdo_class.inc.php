<?php
class DBPDO {
	var $_username = "";
	var $_password = "";
	var $_hostname = "";
	var $_database = "";
	var $_db     = "";
	var $_sth     = "";
	var $_conn_id  = FALSE;
	var $_sql      = "";
	var $_clear_value = true;
	
	function __construct() {
		global $dbconfig;
	    
		$this->_username = "root";
		$this->_password = "";
		$this->_hostname = "localhost";
		$this->_database = "gitFtp";
		
		$this->_connect();
    }
	
	function _connect(){
		try {
			$this->_db = new PDO('mysql:host=' . $this->_hostname . ';dbname=' . $this->_database , $this->_username, $this->_password, array(PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8"));
			//$this->_db->exec("SET CHARACTER SET utf8");
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			$error = explode("]",$e->getMessage());
			echo "[ERROR] : ".end($error);
			die;
		}
	}

	function query($sql){
		$sth = $this->_db->prepare($sql);
		$sth->execute();
		$this->_sth = $sth;
		return $this;
	}
	function query_data($sql,$data){
		$sth = $this->_db->prepare($sql);
		$sth->execute($data);
		$this->_sth = $sth;
		return $this;
	}
	
	function row(){
		$sth = $this->_sth->fetch(PDO::FETCH_OBJ);
		$this->_sth->closeCursor();
		return $sth;
	}

	function result(){		
		$sth = $this->_sth->fetchAll(PDO::FETCH_OBJ);
		$this->_sth->closeCursor();
		return $sth;
	}
	function row_array(){
		$sth = $this->_sth->fetch(PDO::FETCH_ASSOC);
		$sth = $this->clear_value($sth);
		$this->_sth->closeCursor();
		return $sth;
	}

	function result_array(){		
		$sth = $this->_sth->fetchAll(PDO::FETCH_ASSOC);
		$sth = $this->clear_value($sth);
		$this->_sth->closeCursor();
		return $sth;
	}
	function last_insert_id(){		
		$sth = $this->_db->lastInsertId();
		$this->_sth->closeCursor();
		return $sth;
	}
	
	function clear_value($data){
		if($this->_clear_value == true){
			$data = $this->unstrip_array($data);
		}
		return $data;
	}
	
	function unstrip_array($array){
		if(is_array($array)){
			foreach($array as &$val){
				if(is_array($val)){
					$val = $this->unstrip_array($val);
				}else{
					$val = stripslashes($val);
				}
			}
		}
		return $array;
	}

	function _disconnect(){
		$this->_db = null;
	}
}

?>