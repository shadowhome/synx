<?php
include 'inc/functions.php';

$ip = $_GET['ip'];






list($installed) = getPACKS();


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