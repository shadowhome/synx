<?php
error_reporting(E_ALL);

function getBetween($content,$start,$end){
	echo $content;
	return substr($content, strlen($start) -1, strlen($content)-strlen($start)-strlen($end));
}
function _dbList($stringArray){
	array_walk($stringArray, function(&$field){$field = '"'.$field.'"';});
	return implode(', ',$stringArray);
}

function getOS($pass=false){
	$ip = $_REQUEST['ip'];
	$sshp = $_REQUEST['sshp'];
	if(!isset($_REQUEST['sshp'])) {
		$sshp = '22';
	}
	$lsbresult1   = array();
	if (isset($pass) && $pass){
		$connection = ssh2_connect($ip, $sshp);
		echo ssh2_auth_password($connection, 'root', $pass)?'success':'fail';
		$cmd="lsb_release -as";
		$stream = ssh2_exec($connection, $cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$lsbresult1 = stream_get_contents($stream);
	}
	else {
		$lsbresult1 = array();
		$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
		ssh2_auth_pubkey_file($connection, 'sysad','~/.ssh/id_rsa.pub', '~/.ssh/id_rsa');
		$stream = ssh2_exec($connection, $cmd);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		stream_set_blocking($stream, true);
		$lsbresult1 = stream_get_contents($stream);
		
	}
	
		print_r($lsbresult1);
		$lsbresult = explode("\n", $lsbresult1);
		if(!empty($lsbresult)) {
		$OS        = $lsbresult[0];
		$version  = $lsbresult[3];
		$releasever = $lsbresult[2];
		}
		else {
			echo "No values present";
			die();
}
	return array($OS, $version, $releasever);
}
function getPACKS(){
	$ip = $_GET['ip'];

	$installed = array();
	exec("ssh root@$ip '/home/sysad/manage/packs.sh inst'",$installed);

	return array($installed);

}
function sshiconn($cmd, $pass, $ip, $sshp=22){
	
	$ip = $_REQUEST['ip'];
	$pass = $_REQUEST['pass'];
	$sshp = $_REQUEST['sshp'];
	if(!isset($_REQUEST['sshp'])) {
		$sshp = '22';
	}

	$connection = ssh2_connect($ip, $sshp);
	ssh2_auth_password($connection, 'root', $pass);
	$stream = ssh2_exec($connection, $cmd);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	print_r($cmd);

	
	$output = stream_get_contents($stream);
	fclose($stream);
	fclose($errorStream);
	ssh2_exec($connection, 'exit');
	unset($connection);
	return $output;

}
function sshsysad($cmd, $ip, $sshp=22){
	print_r($sshp);
	$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
	ssh2_auth_pubkey_file($connection, 'sysad','~/.ssh/id_rsa.pub', '~/.ssh/id_rsa');
	$stream = ssh2_exec($connection, $cmd, true);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	$output = stream_get_contents($stream);
	fclose($stream);
	fclose($errorStream);
	ssh2_exec($connection, 'exit');
	unset($connection);
	return $output;
}

?>