<?php
/* Function curl
* Function: CURL file get content.
*/
function curl($url, $field = [], $type = "get"){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($type == "json") curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    if($field) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($field));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}


function is_connected($domain, $default = ""){
    $connected = @fsockopen($domain, 80); //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    if($default){
        return $is_conn ? $domain : $default;
    }
    return $is_conn;

}

function redirect($url){
    echo "<script>window.location='$url'</script>";
}

function kil_pid($pid){ 
    return stripos(php_uname('s'), 'win')>-1 ? exec("taskkill /F /PID $pid") : exec("kill -9 $pid");
};

function app_emit($channel, $event, $data){
	$field['channel'] = $channel;
	$field['event'] = $event;
	$field['data'] = $data;

	curl("http://zonareplika.com:2000/pub", $field);
}