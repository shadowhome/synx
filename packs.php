<?php
include 'inc/upconfig.php';
include 'inc/functions.php';
ini_set('post_max_size','20M');
ini_set('upload_max_filesize','2M');
ini_set('max_execution_time', '3600');
//Include a generic header
include 'inc/html/header.php';

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

@list($OS, $version, $releasever) = getOS();

$sqlnew = "UPDATE servers SET OS = '$OS', version = '$version' , releasever = '$releasever' WHERE id = $id";

	exec("ssh sysad@$ip \"echo 'SELECT package , nversion, security, upgrade, date, md5, cversion, rc, ii, changelog FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $lines);

	$sql="REPLACE INTO packages (package, version, security, upgrade, servers, servername, date, md5, nversion, rc, ii, changelog) VALUES";	
	$sep = '';
	
	$package = null;
	$packages = array();
	$changelogs = array();
	
	foreach($lines as $line){
		if(strpos($line, '|')!==false){
			$p = explode('|', $line);
			
			if(sizeof($p) < 10){
				$changelogs[] = $line;
				continue;
			}
			else{
				if(isset($package)){
					$package[9] = implode(PHP_EOL,$changelogs);
					$packages[] = $package;
					$changelogs = array();
				}
				$changelogs[] = $p[9];
			}
			$package = $p;
		}else{
			$changelogs[] = $line;
		}
		
		//if()
	}
	if(isset($package)){
		$package[9] = implode(PHP_EOL,$changelogs);
		$packages[] = $package;
		$changelogs = array();
	}
	
	
	foreach ($packages as $md_s) {
		//if(strpos($md_s,'|')){
			//print_r($md_s);
			//echo '<br/><br/>';
			
		list($pack, $nver,$security, $upgrade, $date, $md5, $cver, $rc, $ii, $cl) = $md_s;//explode("|", $md_s);
		
		$sql .= $sep."(\"$pack\", \"$cver\", \"$security\", \"$upgrade\", $id, \"$servername\" , \"$date\",  \"$md5\", \"$nver\", \"$rc\", \"$ii\", \"".mysqli_escape_string($conn, $cl)."\")";
		$sep = ', ';
		//}
	}
	

	if (mysqli_query($conn, $sqlnew)&&mysqli_query($conn, $sql)) {

		echo "New record created successfully";
		header( "Location: Servers.php?id=$id" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);