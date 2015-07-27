<?php
include 'inc/upconfig.php';


// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$id=$_GET['id'];
$ip=$_GET['ip'];

$aptup=array();
	$aptch = exec("ssh root@$ip apt-get update");
	exec("ssh root@$ip apt-get --just-print upgrade 2>&1 | perl -ne 'if (/Inst\s([\w,\-,\d,\.,~,:,\+]+)\s\[([\w,\-,\d,\.,~,:,\+]+)\]\s\(([\w,\-,\d,\.,~,:,\+]+)\)? /i) {print \"PROGRAM: $1 INSTALLED: $2 AVAILABLE: $3\n\"}'",$aptup);
	$response = array();
	$condition = '/(PROGRAM:\s*)(.*)(INSTALLED:\s*)(.*)(AVAILABLE:\s*)(.*)/';
	foreach($aptup as $a){
		preg_match_all($condition, $a, $response[]);
	}
	//print_r($aptup);exit;
	//print_r($aptch);
	//print_r($ip);
	//print_r($_GET);
	//print_r($response);
	
	
$sec = exec("ssh root@$ip apt-get upgrade -s|grep Debian-Security|grep ^Inst",$response1);
//print_r("ssh root@$ip apt-get upgrade -s|grep Debian-Security|grep ^Inst");
//print_r($response1);
$secupdat = array();
foreach ($response1 as $supdates ) {
$blah=explode(' ', $supdates);
//print_r($blah[1]);
$secupdat[] = '"'.$blah[1].'"';
}
foreach($response as $r){
if(!empty($r)) {
	$package = $r[2][0];
	$cver = $r[4][0];
	$uver = $r[6][0];
}

$sql="UPDATE packages SET upgrade = 1 where servers = $id AND package = '$package'";
$sql2="UPDATE packages SET security = 1, upgrade = 1 where servers = $id AND package IN (".implode(',',$secupdat).")";
if (mysqli_query($conn, $sql)&&!empty($secupdat)&&mysqli_query($conn, $sql2)) {
	echo "New record created successfully";
	header( "Location: Servers.php?id=$id" );
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
}
	//print_r($package);
	//print_r($cver);
	//print_r($uver);
	//print_r($response);
	//print_r($aptup);

	mysqli_close($conn);