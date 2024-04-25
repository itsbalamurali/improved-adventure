<?php
require_once($tconfig['tsite_libraries_v'] . 'include_header.php');
if (!defined('ALLOWED_DOMAINS')) { exit; }
  
function getPathInfoBetweenLocations($requestDataArr){
	global $GOOGLE_SEVER_GCM_API_KEY, $MAPS_API_REPLACEMENT_STRATEGY, $tconfig;
	
	include_once("polyline.php");
	
	$url = "";
	if(strtoupper($MAPS_API_REPLACEMENT_STRATEGY) == "ADVANCE"){
		$url = GOOGLE_API_REPLACEMENT_URL."direction?source_latitude=".$requestDataArr['SOURCE_LATITUDE']."&source_longitude=".$requestDataArr['SOURCE_LONGITUDE']."&dest_latitude=".$requestDataArr['DEST_LATITUDE']."&dest_longitude=".$requestDataArr['DEST_LONGITUDE']."&language_code=".$requestDataArr['LANGUAGE_CODE']."&TSITE_DB=".TSITE_DB;
	}else{
		 $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $requestDataArr['SOURCE_LATITUDE'] . "," . $requestDataArr['SOURCE_LONGITUDE'] . "&destination=" . $requestDataArr['DEST_LATITUDE'] . "," . $requestDataArr['DEST_LONGITUDE'] . "&sensor=false&key=" . $GOOGLE_SEVER_GCM_API_KEY . "&language=" . $requestDataArr['LANGUAGE_CODE'];
	}
	
	try {
		$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response_contents = curl_exec($ch);
        curl_close($ch);

        $dataResponse = json_decode($response_contents, true);
    } catch (ErrorException $ex) {
        $returnArr['Action'] = "0";
        setDataResponse($returnArr);
    }
	
	$returnArr = array();
	
	if(strtoupper($MAPS_API_REPLACEMENT_STRATEGY) == "ADVANCE"){
		$returnArr['distance'] = $dataResponse['distance'];
		$returnArr['duration'] = $dataResponse['duration'];
		$returnArr['data'] = $dataResponse['data'];
	}else{
		if(strtoupper($dataResponse['status']) == "OK"){
			$legs = $dataResponse['routes'][0]['legs'][0];
			
			$returnArr['distance'] = strval($legs['distance']['value']);
			$returnArr['duration'] = strval($legs['duration']['value']);
			
			$returnArr['data'] = array();
			
			$position_count = 0;
			
			$steps = $legs['steps'];
			foreach ($steps as $steps_item) {
				$polyline_points = $steps_item['polyline']['points'];
				$points_arr = Polyline::pair(Polyline::decode($polyline_points));
				
				for($i=0; $i < count($points_arr); $i++){
					$returnArr['data'][$position_count]['latitude'] = $points_arr[$i][0];
					$returnArr['data'][$position_count]['longitude'] = $points_arr[$i][1];
					$position_count ++;
				}
			}
		}else{
			$returnArr['distance'] = "0";
			$returnArr['duration'] = "0";
			$returnArr['data'] = array();
		}
	}
	
	return $returnArr;
}
function getAddressFromGeoLocations($requestDataArr){
	global $GOOGLE_SEVER_GCM_API_KEY, $MAPS_API_REPLACEMENT_STRATEGY, $tconfig;
	
	include_once("polyline.php");
	
	$url = "";
	if(strtoupper($MAPS_API_REPLACEMENT_STRATEGY) == "ADVANCE"){
		$url = GOOGLE_API_REPLACEMENT_URL."reversegeocode?latitude=".$requestDataArr['LATITUDE']."&longitude=".$requestDataArr['LONGITUDE']."&language_code=".$requestDataArr['LANGUAGE_CODE']."&TSITE_DB=".TSITE_DB;
	} else {
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $requestDataArr['LATITUDE'] . "," . $requestDataArr['LONGITUDE'] . "&sensor=false&key=" . $GOOGLE_SEVER_GCM_API_KEY . "&language=" . $requestDataArr['LANGUAGE_CODE'];
	}
	
	try {
		$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response_contents = curl_exec($ch);
        curl_close($ch);
        
        $dataResponse = json_decode($response_contents, true);
    } catch (ErrorException $ex) {
        $returnArr['Action'] = "0";
        setDataResponse($returnArr);
    }
	
	$returnArr = array();
	
	if(strtoupper($MAPS_API_REPLACEMENT_STRATEGY) == "ADVANCE"){
		$returnArr['address'] = $dataResponse['address'];
	} else {
		if(strtoupper($dataResponse['status']) == "OK"){
			$returnArr['address'] = $dataResponse['results'][0]['formatted_address'];
		} else {
			$returnArr['address'] = "";
		}
	}
	
	return $returnArr;
}
?>