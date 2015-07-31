<?php
include 'inc/upconfig.php';

// Create connection
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$link) {
	die("Connection failed: " . mysqli_connect_error());
}

$servername = mysqli_real_escape_string($link, $_POST['servername']);

$ip = mysqli_real_escape_string($link, $_POST['ip']);

$company = mysqli_real_escape_string($link, $_POST['company']);

//$OS = mysqli_real_escape_string($link, $_POST['OS']);
$lsbresult   = array();
$lsbcmd    = exec("ssh root@$servername 'lsb_release -as'",$lsbresult );
$response = array();
//print_r($lsbresult);

if(!empty($lsbresult)) {
	$OS        = $lsbresult[0];
	$version  = $lsbresult[3];
	$releasever = $lsbresult[2];
}

//$version = mysqli_real_escape_string($link, $_POST['version']);

$description = mysqli_real_escape_string($link, $_POST['description']);

$sql = "INSERT INTO servers (servername,ip,company,OS,version,description,releasever) VALUES ('$servername','$ip','$company','$OS','$version','$description','$releasever')";

$serverid=0;

if (mysqli_query($link, $sql)) {
	echo "New record created successfully";
	$serverid=mysqli_insert_id($link);
	//header( "Location: index.php" );
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($link);
}



if ($_POST['populate'] == 'yes') {
	exec("ssh root@$ip 'dpkg-query --show'",$packages);
	exec("ssh root@$ip 'if [ ! -d /home/sysad/manage/packs.sh ];then mkdir /home/sysad/manage/packs.sh' ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh ");
	$response = array();
	//print_r($packages);
	$out=array();
	$sqlval=array();
	$sql='INSERT INTO packages(package,version,OS,servername,servers) VALUES ';
	foreach ($packages as $package) {
		$condition = '/(\S+)(\s)(\S+)/';
		preg_match_all($condition, $package, $response);
		$name = $response[1][0];
		$version = $response[3][0];
		$sqlval[]='("'.$name.'" , "'.$version.'", "'.$OS.'", "'.$servername.'", "'.$serverid.'")';
	}
	$sql.=implode(',' , $sqlval);
	
	$condition = '/(\S+)(\s)(\S+)/';
	
	//print_r($sql);
	if (mysqli_query($link, $sql)) {
		echo "New record created successfully";
		//header( "Location: index.php" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($link);
	}
		
}
mysqli_close($link);
?>