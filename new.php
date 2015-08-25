<?php
include 'inc/upconfig.php';
include 'inc/functions.php';
//Include a generic header
include 'inc/html/header.php';

//ToDo: Change Classes to autoload
include __DIR__.'/classes/Server.php';
include __DIR__.'/classes/ServerController.php';

$sshp = mysqli_real_escape_string($link, $_REQUEST['sshp']);




//$OS = mysqli_real_escape_string($link, $_POST['OS']);
//$lsbresult   = array();
//$lsbcmd    = exec("ssh root@$ip 'lsb_release -as'",$lsbresult );
//print_r(exec("ssh root@$ip 'lsb_release -as'",$lsbresult ));
//$response = array();
//print_r($lsbresult);

$serverController = new ServerController();

//ToDo: verify server does not already exist
$server = new Server();
$server	->setName($_REQUEST['servername'])
		->setIP($_REQUEST['ip'])
		->setCompany($_REQUEST['company'])
		->setPassword($_REQUEST['pass'])
		->setDescription($_POST['description']);

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

$serverController->addServer($server);

$who = getenv('USERNAME') ?: getenv('USER');

$home = getenv("HOME");

$sshkey =  $home . '/.ssh/id_rsa.pub';

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

	

	sshiconn($cmd, $pass, $ip, $sshp);
	flush();
	echo "Getting bash script needed to populate database and setting permissions";
	$cmd="wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh";
	sshiconn($cmd, $pass, $ip, $sshp);
	flush();
	echo "Setting cronjobs and sudo access to perform upgrades when asked to";
	$cmd="su - sysad -c 'mkdir -p /home/sysad/.ssh; chmod 700 /home/sysad/.ssh; echo \"$sshpub\" >> /home/sysad/.ssh/authorized_keys';echo \"10 1 * * * root /home/sysad/manage/packs.sh all\" >> /etc/crontab;echo \"Cmnd_Alias SYNX = /usr/bin/apt-get, /home/sysad/manage/packs.sh, /usr/bin/sqlite3 \" >> /etc/sudoers;echo \"sysad   ALL=(root)      NOPASSWD: SYNX \" >> /etc/sudoers ";
	sshiconn($cmd, $pass, $ip, $sshp);
	flush();
	
	echo "Running populate which may take a while";
	$cmd="/home/sysad/manage/packs.sh all";
	sshiconn($cmd, $pass, $ip, $sshp);
	flush();
	echo "If the above completed we're going to retrieve some data";
	exec("ssh sysad@$ip \"echo 'SELECT package, cversion, nversion, md5, upgrade, security FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
	$sql="INSERT INTO packages(package,servers,version,nversion, md5, upgrade, security, servername) VALUES ";
	$sep = '';
	
	foreach ($packages as $md_s) {
		list($pack, $cver, $nver, $md5, $upgrade, $sec) = explode("|", $md_s);
		$sql .= $sep."(\"$pack\", $id, \"$cver\", \"$nver\", \"$md5\", \"$upgrade\", \"$sec\", \"$servername\")";
		$sep = ', ';
	}
	
	

}		

mysqli_close($link);

	//Include a generic footer
	include 'inc/html/footer.php';
?>
