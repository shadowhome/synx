<?php
include 'inc/upconfig.php';
include 'inc/functions.php';
//Include a generic header
include 'inc/html/header.php';

// Create connection
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$link) {
	die("Connection failed: " . mysqli_connect_error());
}

$servername = mysqli_real_escape_string($link, $_REQUEST['servername']);

$ip = mysqli_real_escape_string($link, $_REQUEST['ip']);

$company = mysqli_real_escape_string($link, $_REQUEST['company']);

$pass = mysqli_real_escape_string($link, $_REQUEST['pass']);

$sshp = mysqli_real_escape_string($link, $_REQUEST['sshp']);

//$OS = mysqli_real_escape_string($link, $_POST['OS']);
//$lsbresult   = array();
//$lsbcmd    = exec("ssh root@$ip 'lsb_release -as'",$lsbresult );
//print_r(exec("ssh root@$ip 'lsb_release -as'",$lsbresult ));
//$response = array();
//print_r($lsbresult);


@list($OS, $version, $releasever) = getOS($pass);
//if(!empty($lsbresult)) {
//	$OS        = $lsbresult[0];
//	$version  = $lsbresult[3];
//	$releasever = $lsbresult[2];
//}

//$version = mysqli_real_escape_string($link, $_POST['version']);

$description = mysqli_real_escape_string($link, $_POST['description']);

$sql = "INSERT INTO servers (servername,ip,company,OS,version,description,releasever,sshp) VALUES ('$servername','$ip','$company','$OS','$version','$description','$releasever','$sshp')";

	if (mysqli_query($link, $sql)) {
		echo "New Server created successfully";
		//header( "Location: index.php" );
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($link);
		die("Server already exists");
	}

// $resultp=mysqli_query($link, $sql);
$serverid=0;

$who = getenv('USERNAME') ?: getenv('USER');

$home = getenv("HOME");

$sshkey =  $home . '/.ssh/id_rsa.pub';

$getid = "SELECT id FROM servers where ip = '$ip'";
$resultp=mysqli_query($link, $getid);
$row=mysqli_fetch_assoc($resultp);
$id=$row['id'];

if (file_exists($sshkey)) {

	$sshpub = exec("cat $sshkey");

} else {
//	echo "The file $sshkey does not exist";
	exec("ssh-keygen -t rsa -N \"\"");
	$sshpub = exec("cat $sshkey");

}



if ($_REQUEST['populate'] == 'yes') {

	echo "Setting up non-privelged ssh user \"sysad\"";
	$cmd="id -u syad; if [ $? = 1 ];then useradd -d /home/sysad -p saqrX1N3h1MQ6 -m sysad;fi; if [ ! -d /home/sysad/manage ];then mkdir -p /home/sysad/manage/;fi ";
	echo "Getting bash script needed to populate database and setting permissions";
	$cmd2="wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh";
	echo "Setting cronjobs and sudo access to perform upgrades when asked to";
	$cmd3="su - sysad -c 'mkdir -p /home/sysad/.ssh; chmod 700 /home/sysad/.ssh; echo \"$sshpub\" > /home/sysad/.ssh/authorized_keys';echo \"10 1 * * * root /home/sysad/manage/packs.sh all\" >> /etc/crontab;echo \"sysad   ALL=(root)      NOPASSWD: /usr/bin/apt-get\" >> /etc/sudoers ";	
	echo "Running populate which may take a while";
	$cmd4="/home/sysad/manage/packs.sh all";
	sshiconn($cmd, $pass, $ip);
	flush();
	sshiconn($cmd2, $pass, $ip);
	flush();
	sshiconn($cmd3, $pass, $ip);
	flush();
	sshiconn($cmd4, $pass, $ip);
	flush();
	echo "If the above completed we're going to retrieve some data";
	exec("ssh sysad@$ip \"echo 'SELECT package, cversion, oversion, md5, upgrade, security FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
	$sql="INSERT INTO packages(package,servers,version,nversion, md5, upgrade, security, servername) VALUES ";
	$sep = '';
	
	foreach ($packages as $md_s) {
		list($pack, $cver, $over, $md5, $upgrade, $sec) = explode("|", $md_s);
		$sql .= $sep."(\"$pack\", $id, \"$cver\", \"$over\", \"$md5\", \"$upgrade\", \"$sec\", \"$servername\")";
		$sep = ', ';
	}
	
	

}		

mysqli_close($link);

	//Include a generic footer
	include 'inc/html/footer.php';
?>