<?php
include 'inc/autoloader.php';
include 'inc/functions.php';
//Include a generic header
include 'inc/html/header.php';

use Synx\Controller\ServerController;
use Synx\Controller\CompanyController;
use Synx\Controller\OperatingSystemController;
use Synx\Controller\OperatingSystemVersionController;

use Synx\Exception\EmptyResultException;

use Synx\Model\Company;

$serverController = new ServerController();
$companyController = new CompanyController();
$operatingSystemController = new OperatingSystemController();
$operatingSystemVersionController = new OperatingSystemVersionController();

$error_msg = array();

$company = null;

try {
	try {
		$company = $companyController->getCompanyByName($_REQUEST['company']);
	} catch (EmptyResultException $e){
		$company = new Company();
		$company->setName($_REQUEST['company']);
		$companyController->addCompany($company);
	}
}catch (PDOException $e){
	$error_msg[] = 'An Error occurred whilst interacting the database.';
}catch (InvalidArgumentException $e){
	$error_msg[] = 'Please enter a valid company name';
}catch (Exception $e){
	$error_msg[] = 'Something went wrong checking the company';
}

$server = null;
try {
	$server = new \Synx\Model\Server();
	$server
		->setName($_REQUEST['servername'])
		->setIp($_REQUEST['ip'])
		->setCompanyId($company->getId())
		->setDescription($_REQUEST['description']);
	if(isset($_REQUEST['sshp']) && $_REQUEST['sshp']){
		$server->setPort($_REQUEST['sshp']);
	}
	if(isset($_REQUEST['pass']) && $_REQUEST['pass']){
		$server->setPassword($_REQUEST['pass']);
	}
	$serverController->checkOperatingSystem($server);
	$serverController->addServer($server);
}catch (PDOException $e){
	$error_msg[] = 'An Error occurred whilst interacting the database.';
}catch (InvalidArgumentException $e){
	$error_msg[] = $e->getMessage();
}catch (Exception $e){
	$error_msg[] = "Something went wrong checking the company:\n".$e->getMessage();
}

print_r($error_msg);
exit;

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
