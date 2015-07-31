<?php
include 'inc/upconfig.php';
include 'inc/functions.php';
ini_set('post_max_size','20M');
ini_set('upload_max_filesize','2M');
ini_set('max_execution_time', '3600');


// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$id=$_GET['id'];
$ip=$_GET['ip'];

$aptup=array();
	//$aptch = exec("ssh root@$ip apt-get update");
	//exec("ssh root@$ip apt-get --just-print upgrade 2>&1 | perl -ne 'if (/Inst\s([\w,\-,\d,\.,~,:,\+]+)\s\[([\w,\-,\d,\.,~,:,\+]+)\]\s\(([\w,\-,\d,\.,~,:,\+]+)\)? /i) {print \"PROGRAM: $1 INSTALLED: $2 AVAILABLE: $3\n\"}'",$aptup);
	exec("ssh root@$ip '/home/sysadmin/bin/packs.sh check; apt-get update ; cat /home/sysad/manage/apacks.txt'",$aptup);
	$response = array();
	$condition = '/(PROGRAM:\s*)(.*)(INSTALLED:\s*)(.*)(AVAILABLE:\s*)(.*)/';
	foreach($aptup as $a){
		if(false === strpos($a, 'INSTALLED')){
			continue;
		}
		preg_match_all($condition, $a, $response[]);
	}
	$packages = array();
	foreach($response as $r){
		if(!empty($r)) {
			$package = $r[2][0];
			$cver = $r[4][0];
			$uver = $r[6][0];
			$packages[] = $package;
		}
	}
	
	$sql="UPDATE packages SET upgrade = 1 where servers = $id AND package IN ("._dbList($packages).")";
	
	//$sec = exec("ssh root@$ip apt-get upgrade -s|grep Debian-Security|grep ^Inst",$response1);
	$aptspack = array();
	$sec = exec("ssh root@$ip '/home/sysadmin/bin/packs.sh security ; cat /home/sysad/manage/spacks.txt'",$aptspack);
	//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh security ; cat /home/sysad/manage/spacks.txt'");

	$secupdat = array();
	$response1 = array();
	$condition1 = '/(name:\s*)(.*)(Current:\s*)(.*)(New:\s*)(.*)/';
	foreach ($aptspack as $b ) {
		preg_match_all($condition1, $b, $response1[]);
		//$blah=explode(' ', $supdates);
		//$secupdat[] = '"'.$blah[1].'"';
	}
	foreach($response1 as $rs){
	if(!empty($rs)) {
		$package = $rs[2][0];
		$cver = $rs[4][0];
		$uver = $rs[6][0];
		$secupdat[] = $package;
	}
}

$changelog_long = array();
$changelogs = array();
exec("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\"'", $changelog_long);
//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\"'");
//exec("ssh root@$ip 'cat /home/sysad/manage/changelog.*'", $changelogs);

//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\";cat /home/sysad/manage/changelog.*'");
//print_r($changelogs);
//$content = $changelogs;
$start = "START";
$end = "ENDED";

$current_log = array();
for($i=0;$i<sizeof($changelog_long);$i++){
	if($changelog_long[$i] === $start){
		$current_log = array();
	}
	elseif($changelog_long[$i] === $end){
		$changelogs[] = $current_log;	
	}else{
		$current_log[] = $changelog_long[$i];
	}
}

$changed_packages = array();
$changelog_sql = array();
foreach ($changelogs as $content) {
	if($content && isset($content[0])){
		$p = substr($content[0], 0, strpos($content[0], ' '));
		if(isset($changed_packages[$p])){
			continue;
		}
		
		$changed_packages[$p] = implode(PHP_EOL,$content);

		$changelog_sql[]="UPDATE packages SET changelog = '".mysqli_escape_string($conn, $changed_packages[$p])."' where servers = $id AND (package = '$p' OR package like CONCAT('$p','-%') OR package like CONCAT('$p','_%'))";
		
	}
}



$sql2="UPDATE packages SET security = 1, upgrade = 1 where servers = $id AND package IN ("._dbList($secupdat).")";


if (mysqli_query($conn, $sql)&&!empty($secupdat)&&mysqli_query($conn, $sql2)) {
	foreach($changelog_sql as $sql_cl){
		//echo $sql_cl;echo '<br/><br/>';
		mysqli_query($conn, $sql_cl);
	}
	echo "New record created successfully";
	header( "Location: Servers.php" );
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

	//print_r("ssh root@$ip 'cd /var/cache/apt/archives/ ;apt-get download '".implode(" ",$packages));
	//$run = exec("ssh root@$ip 'cd /var/cache/apt/archives/ ;apt-get download '".implode(" ",$packages), $cmd);
	//print_r($packages);
	//print_r($cmd);
	//print_r($run);

	mysqli_close($conn);