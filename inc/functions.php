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

function updateOsVersion(\Synx\Model\Server &$server)
{
	try {
		$output = array();
		if ($server->isPasswordSet()) {
			$connection = ssh2_connect($server->getIp(), $server->getPort());
			echo ssh2_auth_password($connection, 'root', $server->getPassword()) ? 'success' : 'fail';
			$cmd = "lsb_release -as";
			$stream = ssh2_exec($connection, $cmd);
			$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
			stream_set_blocking($errorStream, true);
			stream_set_blocking($stream, true);
			$output = stream_get_contents($stream);
		} else {
			exec("ssh sysad@" . $server->getPassword() . " 'lsb_release -as'", $output);
		}

		if (!empty($output)) {
			$output = explode(' ', $output);
			$server
				->setOsName($output[0])
				->setOsVersionCode($output[2])
				->setOsVersionName($output[3]);
		}
	} catch (Exception $e) {
		print_r($e->getMessage());
	}
}

function getOS($pass=false){
	$ip = $_REQUEST['ip'];
	$sshp = $_REQUEST['sshp'];
	if(!isset($_REQUEST['sshp'])) {
		$sshp = '22';
	}
	$lsbresult1   = array();

	$methods = array('hostkey', 'ssh-rsa');
	if(isset($pass) && $pass){
		$methods = array();
	}
	
	$connection = ssh2_connect($ip, $sshp, $methods);
	if(!($connection)){
		throw new Exception("fail: unable to establish connection, please Check IP or if server is on and connected");
	}
	$pass_success = false;
	
	if($methods){
		$rsa_pub = realpath($_SERVER['HOME'].'/.ssh/id_rsa.pub');
		$rsa = realpath($_SERVER['HOME'].'/.ssh/id_rsa');
		$pass_success = ssh2_auth_pubkey_file($connection, 'sysad',$rsa_pub, $rsa);
	}else{
		$pass_success = ssh2_auth_password($connection, 'root', $pass);
	}
	
	if(!($pass_success)){
		throw new Exception("fail: unable to establish connection\nPlease Check your password"); 
	}
	$cmd="lsb_release -as";
	$stream = ssh2_exec($connection, $cmd);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	$lsbresult1 = stream_get_contents($stream);
		
	
	stream_set_blocking($errorStream, false);
	stream_set_blocking($stream, false);
	flush();
	fclose($errorStream);
	fclose($stream);
	fclose($rsa_pub);
	fclose($rsa);
	unset($connection);
	
		print_r($lsbresult1);
		$lsbresult = explode("\n", $lsbresult1);
		if(!empty($lsbresult)) {
		$OS        = $lsbresult[0];
		$version  = $lsbresult[3];
		$releasever = $lsbresult[2];
}

ssh2_exec($connection, 'exit');
	fclose($stream);
	fclose($errorStream);
	flush();
	unset($connection);
	fclose($connection);
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
	if(!($connection)){
		throw new Exception("fail: unable to establish connection\nPlease IP or if server is on and connected");
	}
	$pass_success = ssh2_auth_password($connection, 'root', $pass);
	if(!($pass_success)){
		throw new Exception("fail: unable to establish connection\nPlease Check your password");
	}
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
//	echo 'CMD:';print_r($cmd);
//	echo ' IP:';print_r($ip);
	$rsa_pub = realpath($_SERVER['HOME'].'/.ssh/id_rsa.pub');
	$rsa = realpath($_SERVER['HOME'].'/.ssh/id_rsa');
	
	$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
	if(!($connection)){
		throw new Exception("fail: unable to establish connection\nPlease IP or if server is on and connected");
	}
	$pass_success = ssh2_auth_pubkey_file($connection, 'sysad',$rsa_pub, $rsa);
	
	if(!($pass_success)){
		throw new Exception("fail: unable to establish connection\nPlease Check your password");
	}

	$stream = ssh2_exec($connection, $cmd, true);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	stream_set_blocking($stream, true);
	$output = stream_get_contents($stream);
	fclose($stream);
	fclose($errorStream);
	ssh2_exec($connection, 'exit');
	fclose($rsa_pub);
	fclose($rsa);
	unset($connection);
	return $output;
}


function unattendedssh($cmd, $ip, $sshp=22){
	$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
	$rsa_pub = realpath($_SERVER['HOME'].'/.ssh/id_rsa.pub');
	$rsa = realpath($_SERVER['HOME'].'/.ssh/id_rsa');
	
	if(!($connection)){
		throw new Exception("fail: unable to establish connection\nPlease IP or if server is on and connected");
	}
	
	$pass_success = ssh2_auth_pubkey_file($connection, 'sysad',$rsa_pub, $rsa);
	if(!($pass_success)){
		throw new Exception("fail: unable to establish connection\nPlease Check your password");
	}
	$stream = ssh2_exec($connection, $cmd, true);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, false);
	stream_set_blocking($stream, false);
	fclose($stream);
	fclose($errorStream);
	fclose($rsa_pub);
	fclose($rsa);
	unset($connection);
}
//function user_exec($shell,$cmdu) {
//	fwrite($shell,$cmdu . "\n");
//	$output = "";
//	$start = false;
//	$start_time = time();
//	$max_time = 2; //time in seconds
//	while(((time()-$start_time) < $max_time)) {
//		$line = fgets($shell);
//		if(!strstr($line,$cmdu)) {
//			if(preg_match('/\[start\]/',$line)) {
//				$start = true;
//			}elseif(preg_match('/\[end\]/',$line)) {
//				return $output;
//			}elseif($start){
//				$output[] = $line;
//			}
//		}
//	}
//}
//function unattendedssh($cmd, $ip, $sshp=22){
//	$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
//	$rsa_pub = realpath($_SERVER['HOME'].'/.ssh/id_rsa.pub');
//	$rsa = realpath($_SERVER['HOME'].'/.ssh/id_rsa');
//	if(!($connection)){
//		throw new Exception("fail: unable to establish connection\nPlease IP or if server is on and connected");
//	}
//	$pass_success = ssh2_auth_pubkey_file($connection, 'sysad',$rsa_pub, $rsa);
//	if(!($pass_success)){
//		throw new Exception("fail: unable to establish connection\nPlease Check your password");
//	}
//	$shell = ssh2_shell($connection,"bash");
//	$cmdu = "echo '[start]';$cmd;echo '[end]'";
//	$output = user_exec($shell,$cmdu);
//	fclose($shell);
//	return $output;
//}
?>
