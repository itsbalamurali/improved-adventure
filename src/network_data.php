<?php



// include_once ('include_config.php');

// include_once (TPATH_CLASS . 'configuration.php');



// echo "<PRE>=====";

 //print_r($_REQUEST);exit;

if(!empty($_REQUEST) && !empty($_REQUEST['urlToVisit'])){

	$urlToVisit = $_REQUEST['urlToVisit'];

	unset($_REQUEST['urlToVisit']);



	$url_visit = $urlToVisit."?".http_build_query($_REQUEST);

	if($_REQUEST['test']){
		echo $url_visit;
		exit;
		
	}
//	echo file_get_contents($url_visit);
//
//	exit;


	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);

	$response = file_get_contents($url_visit, false, stream_context_create($arrContextOptions));
	echo $response;
	exit;

}



?>

