<?php
include 'inc/functions.php';

$ip = '192.168.2.1';



$sshp = '22';

$lsbresult1 = array();
//		exec("ssh -p $sshp sysad@$ip 'lsb_release -as'",$lsbresult1 );
//		print_r("ssh -P $sshp sysad@$ip 'lsb_release -as'");
$cmd="lsb_release -as";
$connection = ssh2_connect($ip, $sshp, array('hostkey', 'ssh-rsa'));
ssh2_auth_pubkey_file($connection, 'sysad','~/.ssh/id_rsa.pub', '~/.ssh/id_rsa');
$stream = ssh2_exec($connection, $cmd);
$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
stream_set_blocking($errorStream, false);
stream_set_blocking($stream, false);
$lsbresult1 = stream_get_contents($stream);
print_r($lsbresult1);

@list($OS, $version, $releasever) = getOS();



//exec("ssh sysad@$ip \"echo 'SELECT package, cversion, oversion, md5, upgrade, security FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
//print_r($packages);
//foreach ($packages as $md_s) {
//	list($pack, $cver, $over, $md5, $upgrade, $sec) = explode("|", $md_s);
//	print_r($pack);echo '<br/>';echo '<br/>';
//	print_r($cver);echo '<br/>';
//	$sql .= $sep."(\"$pack\", $id, 1, \"$cver\", \"$over\", \"$md5\", \"$upgrade\", \"$sec\")";

//	$sep = ', ';
//}

//explode("|", $packages);
//print_r($pack);


//$md5_check = array();

//exec("ssh root@$ip '/home/sysadmin/bin/packs.sh md5'", $md5_check);

//foreach ($md5_check as $md_s) {
//print_r($md5_check);
//list($packm, $md5val, $filename) = explode(" ", $md_s);
//print_r($packm);
//echo '<br/>';
//print_r($md5val);
//echo '<br/>';
//echo '<br/>';
//}
//foreach ($md5_check as $md5_p) {
//$md5_p = array();
//$md5_s=preg_match_all(' ', $md5_p);
//print_r($md5_p);
//}
//print_r($md5_check);
//$md5_p = explode(' ', $md5_check,2);
//foreach ($md5_check as $md5_s) {
//	$md5_s = array();
//	explode(' ', $md5_s,2);
//	print_r($md5_s); echo '<br/>';
//}