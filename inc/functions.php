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
	$ip = $_GET['ip'];
	$lsbresult   = array();
	$lsbcmd    = exec("ssh root@$ip 'lsb_release -as'",$lsbresult );
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
?>