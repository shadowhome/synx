<?php
include 'inc/functions.php';

$ip = $_REQUEST['ip'];






exec("ssh sysad@$ip \"echo 'SELECT package, cversion, oversion, md5, upgrade, security FROM Packages;'|sqlite3 /home/sysad/manage/synx.db \" ", $packages);
//print_r($packages);
foreach ($packages as $md_s) {
	list($pack, $cver, $over, $md5, $upgrade, $sec) = explode("|", $md_s);
//	print_r($pack);echo '<br/>';echo '<br/>';
//	print_r($cver);echo '<br/>';
	$sql .= $sep."(\"$pack\", $id, 1, \"$cver\", \"$over\", \"$md5\", \"$upgrade\", \"$sec\")";

//	$sep = ', ';
}

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