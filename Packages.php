<?php
include 'inc/upconfig.php';




// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

function getPackages($server) {
	$aptch = exec("ssh root@$servername apt-get update");
	$aptup = exec("ssh root@$servername apt-get --just-print upgrade 2>&1 | perl -ne 'if (/Inst\s([\w,\-,\d,\.,~,:,\+]+)\s\[([\w,\-,\d,\.,~,:,\+]+)\]\s\(([\w,\-,\d,\.,~,:,\+]+)\)? /i) {print \"PROGRAM: $1 INSTALLED: $2 AVAILABLE: $3\n\"}'");
	$response = array();
	$condition = '/(PROGRAM:\s*)(.*)(INSTALLED:\s*)(.*)(AVAILABLE:\s*)(.*)(?=\s)/';
	preg_match_all($condition, $aptup, $response);
	
	print_r($response);
}
			