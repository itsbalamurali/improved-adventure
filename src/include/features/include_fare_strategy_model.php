<?php

##################  Added By HJ On 29-09-2020 For Fare Time and Distance Base On Fare Strategy Model ##################
function getFixedFareStrategyDetails($tripId,$vCurrencyDriver){
    global $obj,$tripDetailsArr;
    //ini_set('display_errors', 1);
    //error_reporting(E_ALL);
    $returnArr = array();
    if(isset($tripDetailsArr['trips_'.$tripId])){
        $trip_start_data_arr = $tripDetailsArr['trips_'.$tripId];
    } else {
        $trip_start_data_arr = $obj->MySQLSelect("SELECT *,fRatio_" . $vCurrencyDriver . " as fRatioDriver FROM trips WHERE iTripId='".$tripId."'");
        $tripDetailsArr['trips_'.$tripId] = $trip_start_data_arr;
    }
    if(count($trip_start_data_arr) > 0){
        $vehicleTypeID = $trip_start_data_arr[0]['iVehicleTypeId'];
        $eHailTrip = $trip_start_data_arr[0]['eHailTrip'];
        $getVehicleTypeData = $obj->MySQLSelect("SELECT eFareCalcModel FROM vehicle_type WHERE iVehicleTypeId='".$vehicleTypeID."' AND eType='Ride' AND eFareCalcModel='Fixed'");
        if(isset($_REQUEST['test'])){
            //echo "<pre>";print_r($getVehicleTypeData);die;
        }
        //echo "<pre>";print_r($getVehicleTypeData);die;
        if(count($getVehicleTypeData) > 0){
            if(strtoupper($eHailTrip) == "YES"){
                $tTotalDuration = $trip_start_data_arr[0]['tTotalDuration'];
                $tTotalDistance = $trip_start_data_arr[0]['tTotalDistance'];

                $returnArr['time'] = $tTotalDuration;
                $returnArr['distance'] = $tTotalDistance;
            }else{
                $iCabRequestId = $trip_start_data_arr[0]['iCabRequestId'];
                $tableName_req = "cab_request_now";
                $tableName_key = "iCabRequestId";
                if(empty($iCabRequestId)){
                    //for cab booking later
                    $iCabRequestId = $trip_start_data_arr[0]['iCabBookingId'];
                    $tableName_req = "cab_booking";
                    $tableName_key = "iCabBookingId";
                }
                $tp_reqData = $obj->MySQLSelect("SELECT tTotalDuration,tTotalDistance FROM `".$tableName_req."` WHERE ".$tableName_key." = '" . $iCabRequestId . "'");
                //echo "<pre>";print_r($tp_reqData);die;
                if(count($tp_reqData) > 0){
                    $tTotalDuration = $tp_reqData[0]['tTotalDuration'];
                    $tTotalDistance = $tp_reqData[0]['tTotalDistance'];

                    $returnArr['time'] = $tTotalDuration;
                    $returnArr['distance'] = $tTotalDistance;
                }
            }
            
        }
    }
    return $returnArr;
}
##################  Added By HJ On 29-09-2020 For Fare Time and Distance Base On Fare Strategy Model ##################
?>