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
$sshp = $_GET['sshp'];

if(isset($_REQUEST['Cron'])) {
	$ip=$_GET['ip'];
	$sshp=$_GET['sshp'];
	$cmd = "sudo /home/manage/packs.sh all"	;
	$output = trim(unattendedssh($cmd, $ip, $sshp));
	//exit;
	header( "Location: Servers.php?id=$id" );
}

@list($OS, $version, $releasever) = getOS();
$cmd = "echo 'SELECT cpua, cpu, cput, cpuc, cpuf, cpus FROM Packages;'|sqlite3 /home/sysad/manage/synx.db|head -1";
$output = trim(sshsysad($cmd, $ip, $sshp));
print_r($output);
$hwstat = explode("|", $output);
print_r($hwstat);
$sqlhw = "UPDATE servers SET CPUArch = '$hwstat[0]', CPUNo = '$hwstat[1]', CPUSockets = '$hwstat[5]', CPUThreads = '$hwstat[2]', CPUF = '$hwstat[4]', CPUC = '$hwstat[3]' WHERE id = $id";
print_r($sqlhw);
$sqlnew = "UPDATE servers SET OS = '$OS', version = '$version' , releasever = '$releasever' WHERE id = $id";
//print_r($sqlnew);

	exec("ssh sysad@$ip \"echo 'SELECT package , nversion, security, upgrade, date, md5, cversion, rc, ii, changelog FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $lines);
//print_r("ssh sysad@$ip \"echo 'SELECT package , nversion, security, upgrade, date, md5, cversion, rc, ii, changelog FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ");
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
	
	

	
//	mysqli_query($conn, 'SET @@global.max_allowed_packet = ' . (strlen( $sql ) + 1024 ));
	if (mysqli_query($conn, $sqlnew)&&mysqli_query($conn, $sql)&&mysqli_query($conn, $sqlhw)) {

		echo "New record created successfully";
	//	header( "Location: Servers.php?id=$id" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	

	
	mysqli_close($conn);