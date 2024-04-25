<?php 
$featuredArray = array(
   array('Age'=>23, 'name'=>'Joe', 'hobby'=>'Cycling'),
   array('Age'=>26, 'name'=>'Hannah', 'hobby'=>'Rowing'),
   array('Age'=>30, 'name'=>'Dev', 'hobby'=>'Cycling'),
   array('Age'=>30, 'name'=>'Deva', 'hobby'=>'Cycling')
);
$freeArray = array(
   array('Age'=>25, 'name'=>'Joe1', 'hobby'=>'Cycling1','eFree'=>'Yes'),
   array('Age'=>24, 'name'=>'Joe0', 'hobby'=>'Cycling2','eFree'=>'Yes'),
   array('Age'=>27, 'name'=>'Joe4', 'hobby'=>'Cycling4','eFree'=>'Yes'),
   array('Age'=>28, 'name'=>'Joe5', 'hobby'=>'Cycling5','eFree'=>'Yes'),
);


$featuredArray1 = array_chunk($featuredArray, 2);
$freeArray1 = array_chunk($freeArray, 2);

$newArr = array();
foreach ($featuredArray1 as $fkey => $fvalue) {
	foreach ($fvalue as $k1 => $val1) {
		$newArr[] = $val1;
	}

	if(isset($freeArray1[$fkey])) {
		foreach ($freeArray1[$fkey] as $k2 => $val2) {
			$newArr[] = $val2;
		}
	}

}
/*for ($i = 0; $i < count($featuredArray1); $i++) { 
	for($j = 0; $j < count($featuredArray1[$i]); $j++) {
		$newArr[] = $featuredArray1[$i][$j];
	}

	if(isset($freeArray1[$i])) {
		for($k = 0; $k < count($freeArray1[$i]); $k++) {
			$newArr[] = $freeArray1[$i][$k];
		}
	}
}*/
echo"<pre>";print_r($newArr);die;
?>