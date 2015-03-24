<?php
$ftp_server="31.170.160.87"; 
$ftp_user_name="a3303664"; 
$ftp_user_pass="ASgoperty0T"; 
$file = "gitFtp.php";//tobe uploaded 
$remote_file = "/gitFtp.php"; 

// set up basic connection 
$conn_id = ftp_connect($ftp_server); 

// login with username and password 
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

// turn passive mode on
ftp_pasv($conn_id, true);

// upload a file 
if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) { 
    echo "successfully uploaded $file\n"; 
    exit; 
} else { 
    echo "There was a problem while uploading $file\n"; 
    exit; 
} 
// close the connection 
ftp_close($conn_id); 