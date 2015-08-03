<?php

function getBetween($content,$start,$end){
	echo $content;
	return substr($content, strlen($start) -1, strlen($content)-strlen($start)-strlen($end));
}
function _dbList($stringArray){
	array_walk($stringArray, function(&$field){$field = '"'.$field.'"';});
	return implode(', ',$stringArray);
}
?>