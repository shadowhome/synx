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
$servername = $_GET['servername'];
$company = $_GET['company'];

list($OS, $version, $releasever) = getOS();

//$sqlnew = "REPLACE INTO servers (OS,version,releasever,id) VALUES ('$OS','$version','$releasever',$id)";
$sqlnew = "UPDATE servers SET OS = '$OS', version = '$version' , releasever = '$releasever' WHERE id = $id";
//print_r($sqlnew);

//list($installed) = getPACKS();
//$sqlinst="REPLACE INTO packages (package, date, servers) VALUES";
//$sep = '';

//foreach ($installed as $inst) {
//	print_r($inst);echo '<br/><br/>';
//	list($insta, $date) = explode(" ", $inst);
//	$sqlinst .= $sep."(\"$insta\", \"".substr($date, 0, 8)."\", $id)";
	
//	$sep = ', ';
//	print_r($insta);
//	print_r($date);
//	print_r($time);
//}
//print_r($sqlinst);

//$aptup=array();

	//exec("ssh root@$ip '/home/sysadmin/bin/packs.sh check; apt-get update ; cat /home/sysad/manage/apacks.txt'",$aptup);
	exec("ssh root@$ip 'apt-get update ;/home/sysadmin/bin/packs.sh all");
	exec("ssh sysad@$ip \"echo 'SELECT package , oversion, security, upgrade, changelog, date, md5, cversion FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
	//$response = array();
	//$condition = '/(PROGRAM:\s*)(.*)(INSTALLED:\s*)(.*)(AVAILABLE:\s*)(.*)/';
	//foreach($aptup as $a){
	//	if(false === strpos($a, 'INSTALLED')){
	//		continue;
	//	}
	//	preg_match_all($condition, $a, $response[]);
	//}
	//$packages = array();
	//foreach($response as $r){
	//	if(!empty($r)) {
	//		$package = trim($r[2][0]);
	//		$cver = $r[4][0];
	//		$uver = $r[6][0];
	//		$packages[] = $package;
	//	}
	//}
	
	$sql="REPLACE INTO packages (package, version, security, upgrade, servers, servername, changelog, date, md5, nversion) VALUES";	
	$sep = '';
	foreach ($packages as $md_s) {
		list($pack, $over,$security, $upgrade, $changelog, $date, $md5, $cver) = explode("|", $md_s);
		//	print_r($pack);echo '<br/>';echo '<br/>';
		//	print_r($cver);echo '<br/>';
		$sql .= $sep."(\"$pack\", \"$over\", \"$security\", \"$upgrade\", $id, \"$servername\", \"$changelog\" , \"$date\",  \"$md5\", \"$cver\")";
		$sep = ', ';
	}
//	print_r($sql);
	//$md5_check = array();
	
	//exec("ssh root@$ip '/home/sysadmin/bin/packs.sh md5'", $md5_check);

	//$sql="REPLACE INTO packages (package, servers, upgrade, md5, date, changelog) VALUES";
	//$sep = '';
	
	//foreach ($md5_check as $md_s) {
	//	list($packm, $md5val, $filename) = explode(" ", $md_s);
	//	$sql .= $sep."(\"$packm\", $id, 1, \"$md5val\")";
	//	$sep = ', ';
	//}
	
	
	//$aptspack = array();
	//$sec = exec("ssh root@$ip '/home/sysadmin/bin/packs.sh security ; cat /home/sysad/manage/spacks.txt'",$aptspack);

	//$secupdat = array();
	//$response1 = array();
	//$condition1 = '/(name:\s*)(.*)(Current:\s*)(.*)(New:\s*)(.*)/';
	//foreach ($aptspack as $b ) {
	//	preg_match_all($condition1, $b, $response1[]);

	//}
	//foreach($response1 as $rs){
	//if(!empty($rs)) {
	//	$package = $rs[2][0];
	//	$cver = $rs[4][0];
	//	$uver = $rs[6][0];
	//	$secupdat[] = $package;
	//}
//}

//$hist = "INSERT INTO packagesHist (package, OS, version, security, upgrade, servers, servername, changelog) SELECT package, OS, version, security, upgrade, servers, servername, changelog FROM packages WHERE servers = $id";


//$changelog_long = array();
//$changelogs = array();
//exec("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\"'", $changelog_long);
//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\"'");
//exec("ssh root@$ip 'cat /home/sysad/manage/changelog.*'", $changelogs);

//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh changelog \"".implode(' ',$packages)."\";cat /home/sysad/manage/changelog.*'");
//print_r($changelogs);
//$content = $changelogs;
//$start = "START";
//$end = "ENDED";



//$current_log = array();
//for($i=0;$i<sizeof($changelog_long);$i++){
//	if($changelog_long[$i] === $start){
//		$current_log = array();
//	}
//	elseif($changelog_long[$i] === $end){
//		$changelogs[] = $current_log;	
//	}else{
//		$current_log[] = $changelog_long[$i];
//	}
//}

//$changed_packages = array();
//$changelog_sql = array();
//foreach ($changelogs as $content) {
//	if($content && isset($content[0])){
//		$p = substr($content[0], 0, strpos($content[0], ' '));
//		if(isset($changed_packages[$p])){
//			continue;
//		}
//		$changed_packages[$p] = implode(PHP_EOL,$content);
//		$changelog_sql[]="UPDATE packages SET changelog = '".mysqli_escape_string($conn, $changed_packages[$p])."' where servers = $id AND (package = '$p' OR package like CONCAT('$p','-%') OR package like CONCAT('$p','_%'))";
//	}
//}

//$sql2="UPDATE packages SET security = 1, upgrade = 1 where servers = $id AND package IN ("._dbList($secupdat).")";

//if (mysqli_query($conn, $sqlinst)&&mysqli_query($conn, $sql)&&!empty($secupdat)&&mysqli_query($conn, $sql2)) {
//	foreach($changelog_sql as $sql_cl){
		//echo $sql_cl;echo '<br/><br/>';
//		mysqli_query($conn, $sql_cl);
//	}
//	echo "New record created successfully";
//	header( "Location: Servers.php" );
//} else {
//	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
//}

	if (mysqli_query($conn, $sqlnew)&&mysqli_query($conn, $sql)) {
//		foreach($changelog_sql as $sql_cl){
//	echo $sql_cl;echo '<br/><br/>';
//			mysqli_query($conn, $sql_cl);
//		}
		echo "New record created successfully";
		header( "Location: Servers.php" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);