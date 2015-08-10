<?php

function getBetween($content,$start,$end){
	echo $content;
	return substr($content, strlen($start) -1, strlen($content)-strlen($start)-strlen($end));
}
function _dbList($stringArray){
	array_walk($stringArray, function(&$field){$field = '"'.$field.'"';});
	return implode(', ',$stringArray);
}

function getOS(){
	$ip = $_REQUEST['ip'];
	$lsbresult   = array();
	exec("ssh sysad@$ip 'lsb_release -as'",$lsbresult );
	//print_r(exec("ssh root@$ip 'lsb_release -as'",$lsbresult ));
	$response = array();
	//print_r($lsbresult);

		if(!empty($lsbresult)) {
		$OS        = $lsbresult[0];
		$version  = $lsbresult[3];
		$releasever = $lsbresult[2];
}
	return array($OS, $version, $releasever);
}
function getPACKS(){
	$ip = $_GET['ip'];
	//get some dates
	$installed = array();
	exec("ssh root@$ip '/home/sysadmin/bin/packs.sh inst'",$installed);
	//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh inst'");
	//print_r($installed);
	return array($installed);

}
function sshiconn(){
	$connection = ssh2_connect('$ip', 22);
	ssh2_auth_password($connection, 'root', '$pass');
	
	$stream = ssh2_exec($connection, '/usr/local/bin/php -i');
}

?>