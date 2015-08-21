<?php

function getBetween($content,$start,$end){
	echo $content;
	return substr($content, strlen($start) -1, strlen($content)-strlen($start)-strlen($end));
}
function _dbList($stringArray){
	array_walk($stringArray, function(&$field){$field = '"'.$field.'"';});
	return implode(', ',$stringArray);
}

function updateOsVersion(\Synx\Model\Server &$server){
	try{
		$output = array();
		if ($server->isPasswordSet()){
			$connection = ssh2_connect($server->getIp(), 22);
			echo ssh2_auth_password($connection, 'root', $server->getPassword())?'success':'fail';
			$cmd="lsb_release -as";
			$stream = ssh2_exec($connection, $cmd);
			$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
			stream_set_blocking($errorStream, true);
			stream_set_blocking($stream, true);
			$output = stream_get_contents($stream);
		}
		else {
			exec("ssh sysad@".$server->getPassword()." 'lsb_release -as'", $output);
		}

		if(!empty($output)) {
			$output = explode(' ',$output);
			$server
				->setOsName($output[0])
				->setOsVersionCode($output[2])
				->setOsVersionName($output[3]);
		}
	}catch(Exception $e){
		print_r($e->getMessage());
	}
}
function getPACKS(){
	$ip = $_GET['ip'];
	//get some dates
	$installed = array();
	exec("ssh root@$ip '/home/sysad/manage/packs.sh inst'",$installed);
	//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh inst'");
	//print_r($installed);
	return array($installed);

}
function sshiconn(){
	$connection = ssh2_connect('$ip', 22);
	ssh2_auth_password($connection, 'root', '$pass');
	
	$stream = ssh2_exec($connection, '/usr/local/bin/php -i');
}