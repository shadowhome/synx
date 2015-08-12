<?php

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
	$lsbresult1   = array();
	if (isset($pass) && $pass){
		$connection = ssh2_connect($ip, 22);
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
		exec("ssh sysad@$ip 'lsb_release -as'",$lsbresult1 );
	}
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
	//get some dates
	$installed = array();
	exec("ssh root@$ip '/home/sysad/manage/packs.sh inst'",$installed);
	//print_r("ssh root@$ip '/home/sysadmin/bin/packs.sh inst'");
	//print_r($installed);
	return array($installed);

}
function sshiconn($cmd, $pass, $ip, $sshp=22){
	
	$ip = $_REQUEST['ip'];
	$pass = $_REQUEST['pass'];
	$sshp = $_REQUEST['sshp'];
	$connection = ssh2_connect($ip, $sshp);
	ssh2_auth_password($connection, 'root', $pass);
	$stream = ssh2_exec($connection, $cmd);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
//	$response = '';
//	while($buffer = fread($stream, 4096)) {
//	$response .= $buffer;
//	}
	
	return stream_get_contents($stream);
	fclose($stream);
	fclose($errorStream);
	ssh2_exec($connection, 'exit');
	unset($connection);
//	echo $response;
}

?>