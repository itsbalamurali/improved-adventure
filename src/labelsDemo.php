<?php
include_once('common.php');


// function startsWith($string, $startString) {
// 	$len = strlen($startString);
// 	return (substr($string, 0, $len) === $startString);
// }

// function endsWith($haystack, $needle) {
// 	$length = strlen($needle);
// 	if ($length == 0) {
// 		return true;
// 	}

// 	return (substr($haystack, -$length) === $needle);
// }

function lengthCountSort($a, $b) {
	return strlen($b) - strlen($a);
}

	
$dataLblArr=array();   

 
$sql = "SHOW TABLES";
$data = $obj->MySQLSelect($sql);

$language_labels_arr = array();

for($i=0;$i<count($data);$i++){
	foreach($data[$i] as $key => $value){
		
		if(startsWith($value, "language_label")){
			$language_labels_arr[] = $value;
		}
	}
}


for($i=0;$i<count($language_labels_arr);$i++){
	$sql = "SELECT * FROM `".$language_labels_arr[$i]."` WHERE vCode = 'EN'";
	$data = $obj->MySQLSelect($sql);

	for($j=0;$j<count($data);$j++){
	  echo "<br>";
	  echo "$".$language_labels_arr[$i]."Arr['".$data[$j]['vLabel']."']" . "='".addslashes($data[$j]['vValue'])."';";
	} 
}




?>