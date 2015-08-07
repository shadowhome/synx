<?php
include 'inc/upconfig.php';
include 'inc/functions.php';

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


//$OS = mysqli_real_escape_string($link, $_POST['OS']);
//$lsbresult   = array();
//$lsbcmd    = exec("ssh root@$ip 'lsb_release -as'",$lsbresult );
//print_r(exec("ssh root@$ip 'lsb_release -as'",$lsbresult ));
//$response = array();
//print_r($lsbresult);


list($OS, $version, $releasever) = getOS();
//if(!empty($lsbresult)) {
//	$OS        = $lsbresult[0];
//	$version  = $lsbresult[3];
//	$releasever = $lsbresult[2];
//}

//$version = mysqli_real_escape_string($link, $_POST['version']);

$description = mysqli_real_escape_string($link, $_POST['description']);

$sql = "INSERT INTO servers (servername,ip,company,OS,version,description,releasever) VALUES ('$servername','$ip','$company','$OS','$version','$description','$releasever')";

$serverid=0;

$who = getenv('USERNAME') ?: getenv('USER');

$home = getenv("HOME");

$sshkey =  $home . '/.ssh/id_rsa.pub';
//print_r($sshkey);

if (file_exists($sshkey)) {
	echo "The file $sshkey exists";
	//I dont know $sshpub = file_get_contents('$sshkey', false); would just not work for me
	$sshpub = exec("cat $sshkey");
	print_r($sshpub);

} else {
	echo "The file $sshkey does not exist";
	exec("ssh-keygen -t rsa -N \"\"");
	$sshpub = exec("cat $sshkey");
	print_r($sshpub);

}



if (mysqli_query($link, $sql)) {
	echo "New record created successfully";
	$serverid=mysqli_insert_id($link);
	//header( "Location: index.php" );
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($link);
}



if ($_POST['populate'] == 'yes') {
	$connection = ssh2_connect($ip, 22);
	ssh2_auth_password($connection, 'root', $pass);
	$cmd="id -u syad; if [ $? = 1 ];then useradd -d /home/sysad -p saqrX1N3h1MQ6 -m sysad;fi; if [ ! -d /home/sysad/manage ];then mkdir -p /home/sysad/manage/;fi ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh;/home/sysad/manage/packs.sh all ;su - sysad -c 'mkdir -p /home/sysad/.ssh; chmod 700 /home/sysad/.ssh; echo $sshpub > /home/sysad/.ssh/authorized_keys'";
	$stream = ssh2_exec($connection, $cmd);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	//$stream = ssh2_exec($connection, "id -u syad; if [ $? = 1 ];then useradd -d /home/sysad -p saqrX1N3h1MQ6 -m sysad;fi; if [ ! -d /home/sysad/manage ];then mkdir -p /home/sysad/manage/;fi ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh;/home/sysad/manage/packs.sh all & ;su - sysad -c 'ssh-keygen -t rsa -N \"\" -t rsa; echo $sshpub > /home/sysad/.ssh/authorized_keys'");
	print_r($stream);
	echo "Error: " . stream_get_contents($errorStream);
	echo "Output: " . stream_get_contents($stream);
	//exec("ssh root@$ip 'dpkg-query --show'",$packages);
	//exec("ssh root@$ip "'if [ ! -d /home/sysad/manage/packs.sh ];then mkdir /home/sysad/manage/packs.sh;fi' ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh"");
	//exec("ssh root@$ip \"'if [ ! -d /home/sysad/manage/packs.sh ];then mkdir /home/sysad/manage/packs.sh;fi' ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh \"");
	$response = array();
	//print_r($packages);
	//$out=array();
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