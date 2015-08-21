<?php
include __DIR__.'/inc/functions.php';

//ToDo: Change Classes to autoload
include __DIR__.'/classes/Server.php';
include __DIR__.'/classes/ServerController.php';

use Synx\Model\Server;
use Synx\Controller\ServerController;

$serverController = new ServerController();

//ToDo: verify server does not already exist
$server = new Server();
$server	->setName($_REQUEST['servername'])
		->setIP($_REQUEST['ip'])
		->setCompany($_REQUEST['company'])
		->setPassword($_REQUEST['pass'])
		->setDescription($_POST['description']);

updateOsVersion($server);

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
	echo "Running populate";
	$connection = ssh2_connect($server->getIp(), $server->getPort());
	ssh2_auth_password($connection, 'root', $server->getPassword());
	$cmd="id -u syad; if [ $? = 1 ];then useradd -d /home/sysad -p saqrX1N3h1MQ6 -m sysad;fi; if [ ! -d /home/sysad/manage ];then mkdir -p /home/sysad/manage/;fi ;wget https://raw.githubusercontent.com/shadowhome/synx/master/packs.sh -O /home/sysad/manage/packs.sh; chmod 700 /home/sysad/manage/packs.sh;/home/sysad/manage/packs.sh all ; su - sysad -c 'mkdir -p /home/sysad/.ssh; chmod 700 /home/sysad/.ssh; echo \"$sshpub\" > /home/sysad/.ssh/authorized_keys'; echo \"10 1 * * * root /home/sysad/manage/packs.sh all\" >> /etc/crontab;echo \"sysad   ALL=(root)      NOPASSWD: /usr/bin/apt-get\" >> /etc/sudoers ";
	$stream = ssh2_exec($connection, $cmd);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	exec("ssh sysad@".$server->getIp()." \"echo 'SELECT package, cversion, oversion, md5, upgrade, security FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
	$sql="INSERT INTO packages(package,servers,version,nversion, md5, upgrade, security, servername) VALUES ";
	$sep = '';
	
	foreach ($packages as $md_s) {
		list($pack, $cver, $over, $md5, $upgrade, $sec) = explode("|", $md_s);
		$sql .= $sep . "(\"$pack\", " . $server->getId() . ", \"$cver\", \"$over\", \"$md5\", \"$upgrade\", \"$sec\", \"" . $server->getName() . "\")";
		$sep = ', ';
	}
}