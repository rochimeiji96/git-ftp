<?php

class EIO{
	var $websocket = "http://nodejs-rochimeiji.rhcloud.com/";
	var $app_user = "pub";
	var $app_secret = "12345";
	var $channel = "public";

	function __construct($app_user, $app_secret = ""){
		if($app_user) $this->app_user = $app_user;
		if($app_secret) $this->app_secret = $app_secret;
		$this->websocket .= $this->app_user;
	}

	function push($field = []){
		$field['channel'] = $this->channel;
		$field['secret'] = $this->app_secret;

	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, $this->websocket);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($field));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	    $data = curl_exec($ch);
	    curl_close($ch);

	    return $data;
	}

	static function app($app_user = "", $app_secret = ""){
		return new EIO($app_user, $app_secret);
	}

	function setChannel($channel){
		$this->channel = $channel;
		return $this;
	}

	function send($event, $data, $msv = false){
		$this->push(['event' => $event, 'data' => $data, 'action' => $msv == true ? "msvSave" : ""]);
	}

	function flush($event){
		$this->push(['event' => $event, 'action' => 'msvFlush']);
	}
}