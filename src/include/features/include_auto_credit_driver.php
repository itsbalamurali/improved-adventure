
<?php

######################  Auto Credit Driver Wallet start by PM ########################

function AutoCreditWalletDriver($data, $checktype = 'ProcessEndTrip', $iServiceId = "0") {
    global $obj,$tripDetailsArr, $WALLET_OBJ;
    $iUserId = $data['iUserId'];
    $iTripId = $data['iTripId'];
    if ($checktype == 'ChargePassengerOutstandingAmount' || $checktype == 'cancelTrip') {
        ## Update passenger outstanding amount by giving refund to previous drivers for card trips ##	
        $sql = "SELECT iTripOutstandId,fDriverPendingAmount,vTripAdjusmentId,iDriverId,fWalletDebit,iTripId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No'";
        $tripoutstandingdata = $obj->MySQLSelect($sql);
        if (count($tripoutstandingdata) > 0) {
            //Added By HJ On 30-06-2020 For Optimize trips Table Query Start
            $tripIdArr =$tripDataArr= array();
            for($h=0;$h<count($tripoutstandingdata);$h++){
                $tripIdArr[] = $tripoutstandingdata[$h]['iTripId'];
            }
            if(count($tripIdArr) > 0){
                $implodeId = implode(",",$tripIdArr);
                $tripridedata = $obj->MySQLSelect("SELECT vRideNo,iTripId FROM trips WHERE iTripId IN ($implodeId)");
                for($g=0;$g<count($tripridedata);$g++){
                    $tripDataArr[$tripridedata[$g]['iTripId']] = $tripridedata[$g]['vRideNo']; 
                }
            }
            //Added By HJ On 30-06-2020 For Optimize trips Table Query ENd
            for ($i = 0; $i < count($tripoutstandingdata); $i++) {
                $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
                $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] - $tripoutstandingdata[$i]['fWalletDebit'];
                $iTripRefundId = $tripoutstandingdata[$i]['iTripId'];
                $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
                //$sql = "SELECT vRideNo FROM trips WHERE iTripId = '" . $iTripRefundId . "'";
                //$tripridedata = $obj->MySQLSelect($sql);
                $vRideNo = "";
                if(isset($tripDataArr[$iTripRefundId])){
                    $vRideNo = $tripDataArr[$iTripRefundId];
                }
                //$vRideNo = $tripridedata[0]['vRideNo'];
                $iBalance = $fDriverPendingAmount;
                $eFor = "Booking";
                $eType = "Credit";
                $ePaymentStatus = "Settelled";
                $dDate = Date('Y-m-d H:i:s');
                $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
                if($iBalance > 0){
                    $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
                }
                $where = " iTripId = '" . $iTripRefundId . "'";
                $data['ePaymentDriverStatus'] = "Paid";
                $idd = $obj->MySQLQueryPerform("payments", $data, 'update', $where);

                $WhereCard = " iTripId = '$iTripRefundId'";
                $Data_update_driver_paymentstatus_Card = array();
                $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
                $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
                ## Add Trip Earning Amount Into Driver Wallet ##
                $obj->sql_query("UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes' WHERE iTripOutstandId = " . $TripOutstandId);
            }
        }
        if ($checktype == 'cancelTrip' && !empty($iTripId)) {
            $tripoutstandingdata = $obj->MySQLSelect("SELECT vTripPaymentMode FROM trips WHERE iTripId = '" . $iTripId . "'");
            $vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
            if ($vTripPaymentMode == "Card") {
                $updateQuery2 = "UPDATE trips set fOutStandingAmount = 0 WHERE iTripId = '" . $iTripId . "'";
                $obj->sql_query($updateQuery2);
            }
        }
        ## Update passenger outstanding amount by giving refund to previous drivers for card trips ##

        $updateQuery = "UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId;
        $obj->sql_query($updateQuery);
    } elseif ($checktype == 'CollectPayment') {

        $isCollectCash = $data['isCollectCash'];
        $dDate = Date('Y-m-d H:i:s');
        //Added By HJ On 30-06-2020 For Optimize trips Table Query Start
        /*if(isset($tripDetailsArr['trips_'.$iTripId])){
            $tripData = $tripDetailsArr['trips_'.$iTripId];
        }else{
            $tripData = $obj->MySQLSelect("SELECT * FROM trips WHERE iTripId='$iTripId'");
            $tripDetailsArr['trips_'.$iTripId] = $tripData;
        }*/
        $tripData = $obj->MySQLSelect("SELECT * FROM trips WHERE iTripId='$iTripId'"); // For SOlved Updated Data Issue By HJ On 17-09-2020
        //Added By HJ On 30-06-2020 For Optimize trips Table Query End
        //$sql = "SELECT vTripPaymentMode,iUserId,iDriverId,iFare,vRideNo,fWalletDebit,fTripGenerateFare,fDiscount,fCommision,fTollPrice,eHailTrip,fOutStandingAmount,fHotelCommision,eBookingFrom, ePaymentCollect_Delivery,fTax1,fTax2, eType FROM trips WHERE iTripId='$iTripId'";
        ///$tripData = $obj->MySQLSelect($sql);
        $eType = $tripData[0]['eType'];
        $vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
        $ePaymentCollect_Delivery = $tripData[0]['ePaymentCollect_Delivery'];
        $vRideNo = $tripData[0]['vRideNo'];
        $iDriverId = $tripData[0]['iDriverId'];
        $totalTax = $tripData[0]['fTax1'] + $tripData[0]['fTax2'];
        //echo $ePaymentCollect_Delivery."====".$isCollectCash;die;
        if(isset($_REQUEST['test'])){
            //echo $vTripPaymentMode."====".$isCollectCash."===".$eType."====".$ePaymentCollect_Delivery;die;
        }
        if ($vTripPaymentMode == "Cash" && $isCollectCash == "" && ($eType != "Multi-Delivery" || ($ePaymentCollect_Delivery == "No" && $eType == "Multi-Delivery"))) {
            if(isset($_REQUEST['test'])){
                //echo "!";die;
            }
            $sql = "SELECT iTripOutstandId,fDriverPendingAmount,vTripAdjusmentId,iDriverId,fWalletDebit,iTripId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No'";
            $tripoutstandingdata = $obj->MySQLSelect($sql);
            //echo "<pre>";print_r($tripoutstandingdata);die;
            if (count($tripoutstandingdata) > 0) {
                //Added By HJ On 30-06-2020 For Optimize trips Table Query Start
                $tripIdArr =$tripDataArr= array();
                for($h=0;$h<count($tripoutstandingdata);$h++){
                    $tripIdArr[] = $tripoutstandingdata[$h]['iTripId'];
                }
                if(count($tripIdArr) > 0){
                    $implodeId = implode(",",$tripIdArr);
                    $tripridedata = $obj->MySQLSelect("SELECT vRideNo,iTripId FROM trips WHERE iTripId IN ($implodeId)");
                    for($g=0;$g<count($tripridedata);$g++){
                        $tripDataArr[$tripridedata[$g]['iTripId']] = $tripridedata[$g]['vRideNo']; 
                    }
                }
                //Added By HJ On 30-06-2020 For Optimize trips Table Query ENd
                for ($i = 0; $i < count($tripoutstandingdata); $i++) {
                    $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
                    $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] - $tripoutstandingdata[$i]['fWalletDebit'];
                    $iTripRefundId = $tripoutstandingdata[$i]['iTripId'];
                    $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
                    //$sql = "SELECT vRideNo FROM trips WHERE iTripId = '" . $iTripRefundId . "'";
                    //$tripridedata = $obj->MySQLSelect($sql);
                    $vRideNo = "";
                    if(isset($tripDataArr[$iTripRefundId])){
                        $vRideNo = $tripDataArr[$iTripRefundId];
                    }
                    //$vRideNo = $tripridedata[0]['vRideNo'];
                    $iBalance = $fDriverPendingAmount;
                    if(isset($_REQUEST['test'])){
                        echo $iBalance;die;
                    }
                    $eFor = "Booking";
                    $eType = "Credit";
                    $ePaymentStatus = "Settelled";
                    $dDate = Date('Y-m-d H:i:s');
                    $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
                    if($iBalance > 0){
                        $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
                    }
                    $updateQuery = "UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes', ePaidByPassenger = 'Yes' WHERE iTripOutstandId = " . $TripOutstandId;
                    $obj->sql_query($updateQuery);

                    $WhereCard = " iTripId = '$iTripRefundId'";
                    $Data_update_driver_paymentstatus_Card = array();
                    $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
                    $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
                    ## Add Trip Earning Amount Into Driver Wallet ##
                }
                $updateQuery = "UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId;
                $obj->sql_query($updateQuery);
            }
        }
        if (($vTripPaymentMode == "Card" || $vTripPaymentMode == "Organization") && $isCollectCash == "" && ( $eType != "Multi-Delivery" || ($ePaymentCollect_Delivery == "No" && $eType == "Multi-Delivery"))) {
            ## Update passenger outstanding amount by giving refund to previous drivers for card trips ##	
            $fOutStandingAmount = $tripData[0]['fOutStandingAmount'];

	    $sql = "SELECT iTripOutstandId,fDriverPendingAmount,vTripAdjusmentId,iDriverId,fWalletDebit,iTripId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No' AND iTripId !='".$iTripId."'";
            $tripoutstandingdata = $obj->MySQLSelect($sql);
            if (count($tripoutstandingdata) > 0) {
                for ($i = 0; $i < count($tripoutstandingdata); $i++) {
                    $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
                    $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] - $tripoutstandingdata[$i]['fWalletDebit'];
                    $iTripRefundId = $tripoutstandingdata[$i]['iTripId'];
                    $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
                    $sql = "SELECT vRideNo FROM trips WHERE iTripId = '" . $iTripRefundId . "'";
                    $tripridedata = $obj->MySQLSelect($sql);
                    $vRideNo = $tripridedata[0]['vRideNo'];
                    $iBalance = $fDriverPendingAmount;
                    $eFor = "Booking";
                    $eType = "Credit";
                    $ePaymentStatus = "Settelled";
                    $dDate = Date('Y-m-d H:i:s');
                    $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
                    if($iBalance > 0){
                        $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
                    }
                    $updateQuery = "UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes', ePaidByPassenger = 'Yes' WHERE iTripOutstandId = " . $TripOutstandId;
                    $obj->sql_query($updateQuery);
                }
                $updateQuery = "UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId;
                $obj->sql_query($updateQuery);
            }
            ## Update passenger outstanding amount by giving refund to previous drivers for card trips ##	
            // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
            $getPaymentStatus = $obj->MySQLSelect("SELECT eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iTripId='" . $iTripId . "'");
            $walletArray = array();
            for ($h = 0; $h < count($getPaymentStatus); $h++) {
                $walletArray[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['ePaymentStatus']] = $getPaymentStatus[$h]['eType'];
            }
            // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
            ## Add Trip Earning Amount Into Driver Wallet ##
            $vRideNo = $tripData[0]['vRideNo'];
            $iBalanceCard = $tripData[0]['fTripGenerateFare'] - ( $tripData[0]['fCommision'] + $tripData[0]['fHotelCommision'] + $tripData[0]['fOutStandingAmount'] + $totalTax );
            $eForCard = "Deposit";
            $eTypeCard = "Credit";
            $tDescriptionCard = '#LBL_CREDITED_TRIP_EARNING_PROVIDER#' . $vRideNo;
            $ePaymentStatusCard = 'Settelled';
            $dDateCard = Date('Y-m-d H:i:s');
            if(isset($_REQUEST['test'])){
                //echo $iBalanceCard;die;
            }
            if($iBalanceCard > 0){
                $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalanceCard, $eTypeCard, $iTripId, $eForCard, $tDescriptionCard, $ePaymentStatusCard, $dDateCard);
            }
            $WhereCard = " iTripId = '$iTripId'";
            $Data_update_driver_paymentstatus_Card = array();
            $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
            $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
            ## Add Trip Earning Amount Into Driver Wallet ##
        }
    } else if ($checktype == 'TripCollectTip') {

        $price = $data['price'];
        $TripsData = get_value('trips', 'iDriverId,vRideNo', 'iTripId', $iTripId);
        $vRideNo = $TripsData[0]['vRideNo'];
        $iDriverId = $TripsData[0]['iDriverId'];
        $datad_wallet['iUserId'] = $iDriverId;
        $datad_wallet['eUserType'] = "Driver";
        $datad_wallet['iBalance'] = $price;
        $datad_wallet['eType'] = "Credit";
        $datad_wallet['dDate'] = date("Y-m-d H:i:s");
        $datad_wallet['iTripId'] = $iTripId;
        $datad_wallet['eFor'] = "Deposit";
        $datad_wallet['ePaymentStatus'] = "Unsettelled";
        $datad_wallet['tDescription'] = '#LBL_CREDITED_TIP_AMOUNT_TXT#' . " - " . $vRideNo; //Debited for Tip of Trip
        if($datad_wallet['iBalance'] > 0){
            $WALLET_OBJ->PerformWalletTransaction($datad_wallet['iUserId'], $datad_wallet['eUserType'], $datad_wallet['iBalance'], $datad_wallet['eType'], $datad_wallet['iTripId'], $datad_wallet['eFor'], $datad_wallet['tDescription'], $datad_wallet['ePaymentStatus'], $datad_wallet['dDate']);
        }
    } elseif ($checktype == 'UpdateOrderStatusDriver') {
        $vOrderNo = $data['vOrderNo'];
        $ePaymentOption = $data['ePaymentOption'];
        $iUserId = $data['iUserId'];
        $iOrderId = $data['iOrderId'];
        $fDeliveryCharge = $data['fDeliveryCharge'];

        $sql = "SELECT vTripPaymentMode,iUserId,iDriverId,iTripId,vRideNo FROM trips WHERE iOrderId = '" . $iOrderId . "'";
        $tripData = $obj->MySQLSelect($sql);
        $eType = $tripData[0]['eType'];
        $vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
        $vRideNo = $tripData[0]['vRideNo'];
        $iDriverId = $tripData[0]['iDriverId'];
        $iTripId = $tripData[0]['iTripId'];
        $totalTax = $tripData[0]['fTax1'] + $tripData[0]['fTax2'];
        
        $sql = "SELECT iTripOutstandId,fDriverPendingAmount,vTripAdjusmentId,iDriverId,fWalletDebit,iTripId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No'";
        $tripoutstandingdata = $obj->MySQLSelect($sql);
        if (count($tripoutstandingdata) > 0) {
            for ($i = 0; $i < count($tripoutstandingdata); $i++) {
                $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
                $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] - $tripoutstandingdata[$i]['fWalletDebit'];
                $iTripRefundId = $tripoutstandingdata[$i]['iTripId'];
                $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
                $sql = "SELECT vRideNo FROM trips WHERE iTripId = '" . $iTripRefundId . "'";
                $tripridedata = $obj->MySQLSelect($sql);
                $vRideNo = $tripridedata[0]['vRideNo'];
                $iBalance = $fDriverPendingAmount;
                $eFor = "Booking";
                $eType = "Credit";
                $ePaymentStatus = "Settelled";
                $dDate = Date('Y-m-d H:i:s');
                $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
                if($iBalance > 0){
                    $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
                }
                $updateQuery = "UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes', ePaidByPassenger = 'Yes',vOrderAdjusmentId = '" . $vOrderNo . "'  WHERE iTripOutstandId = " . $TripOutstandId;
                $obj->sql_query($updateQuery);
            }
            $updateQuery = "UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId;
            $obj->sql_query($updateQuery);
        }
        $eForCard = "Deposit";
        $eTypeCard = "Credit";
        $iTripId = $iTripId;
        $tDescriptionCard = '#LBL_CREDITED_TRIP_EARNING_PROVIDER#' . $vOrderNo;
        $ePaymentStatusCard = 'Settelled';
        $dDateCard = Date('Y-m-d H:i:s');
        if($fDeliveryCharge > 0){
            $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $fDeliveryCharge, $eTypeCard, $iTripId, $eForCard, $tDescriptionCard, $ePaymentStatusCard, $dDateCard);
        }
        $WhereCard = " iTripId = '$iTripId'";
        $Data_update_driver_paymentstatus_Card = array();
        $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
        $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
    }
}

######################  Auto Credit Driver Wallet End by PM ########################
//Added By HJ On 22-10-2020 For Auto Credit Driver Earning Amount Start
function autoCreditDriverEarning($data, $checktype = 'ProcessEndTrip'){
    global $obj, $WALLET_OBJ;
    $driverTipAmt =$iUserId = $iTripId =$vOrderNo=$fDeliveryCharge= 0;
    $isCollectCash = isset($data["isCollectCash"]) ? $data["isCollectCash"] : '';
    if(isset($data['iUserId']) && $data['iUserId'] > 0){
        $iUserId = $data['iUserId'];
    }
    if(isset($data['iTripId']) && $data['iTripId'] > 0){
        $iTripId = $data['iTripId'];
    }
    if(isset($data['price']) && $data['price'] > 0){
        $driverTipAmt = $data['price'];
    }
    if(isset($data['vOrderNo']) && $data['vOrderNo'] > 0){
        $vOrderNo = $data['vOrderNo'];
    }
    if(isset($data['fDeliveryCharge']) && $data['fDeliveryCharge'] > 0){
        $fDeliveryCharge = $data['fDeliveryCharge'];
    }
    if(isset($data['tOutStandingIds']) && !empty($data['tOutStandingIds'])){
        $tOutStandingIds = $data['tOutStandingIds'];
    }
    $tripoutstandingdata =$walletArr =$tripIdArr =$tripDataArr= array();
    if($checktype != 'TripCollectTip'){
        $tripoutstandingdata = $obj->MySQLSelect("SELECT iTripOutstandId,fDriverPendingAmount,vTripAdjusmentId,iDriverId,fWalletDebit,iTripId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No'");
    }

    //echo "<pre>";print_r($tripoutstandingdata);die;
    $tripData = $obj->MySQLSelect("SELECT * FROM trips WHERE iTripId='".$iTripId."'");
    $eType = $tripData[0]['eType'];
    $vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
    $ePaymentCollect_Delivery = $tripData[0]['ePaymentCollect_Delivery'];
    $vRideNo = $tripData[0]['vRideNo'];
    $iDriverId = $tripData[0]['iDriverId'];
    $totalTax = $tripData[0]['fTax1'] + $tripData[0]['fTax2'];
    // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
    $getPaymentStatus = $obj->MySQLSelect("SELECT iTripId,eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iTripId='" . $iTripId . "'");
    for ($h = 0;$h < count($getPaymentStatus);$h++) {
        $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iTripId']] = $getPaymentStatus[$h]['eType'];
    }
    //echo "<pre>";print_r($walletArr);die;
    // Added By HJ Orn 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
    if (count($tripoutstandingdata) > 0) {
        //Added By HJ On 30-06-2020 For Optimize trips Table Query Start
        for($h=0;$h<count($tripoutstandingdata);$h++){
            $tripIdArr[] = $tripoutstandingdata[$h]['iTripId'];
        }
        if(count($tripIdArr) > 0){
            $implodeId = implode(",",$tripIdArr);
            $tripricedata = $obj->MySQLSelect("SELECT vRideNo,iTripId FROM trips WHERE iTripId IN ($implodeId)");
            for($g=0;$g<count($tripricedata);$g++){
                $tripDataArr[$tripricedata[$g]['iTripId']] = $tripricedata[$g]['vRideNo']; 
            }
        }
        //echo "<pre>";print_r($tripDataArr);die;
        //Added By HJ On 30-06-2020 For Optimize trips Table Query End
        for ($i = 0; $i < count($tripoutstandingdata); $i++) {
            $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
            $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] - $tripoutstandingdata[$i]['fWalletDebit'];
            $iTripRefundId = $tripoutstandingdata[$i]['iTripId'];
            $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
            $vRideNo = "";
            if(isset($tripDataArr[$iTripRefundId])){
                $vRideNo = $tripDataArr[$iTripRefundId];
            }
            $iBalance = $fDriverPendingAmount;
            $eFor = "Booking";
            $eType = "Credit";
            $ePaymentStatus = "Settelled";
            $dDate = Date('Y-m-d H:i:s');
            $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
            if($iBalance > 0){
                $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
            }
            if($checktype == 'ChargePassengerOutstandingAmount' || $checktype == 'cancelTrip'){
                $paymentData = array();
                $where = " iTripId = '" . $iTripRefundId . "'";
                $paymentData['ePaymentDriverStatus'] = "Paid";
                $idd = $obj->MySQLQueryPerform("payments", $paymentData, 'update', $where);
            }
            $WhereCard = " iTripId = '$iTripRefundId'";
            $Data_update_driver_paymentstatus_Card = array();
            $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
            $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
            if($checktype == 'UpdateOrderStatusDriver'){
                $tOutStandingIdsArr = array();
                if(!empty($tOutStandingIds)) {
                    $tOutStandingIdsArr = explode(",", $tOutStandingIds);
                    if(!in_array($TripOutstandId, $tOutStandingIdsArr)) {
                        continue;
                    }
                }

                $obj->sql_query("UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes', ePaidByPassenger = 'Yes',vOrderAdjusmentId = '" . $vOrderNo . "'  WHERE iTripOutstandId = " . $TripOutstandId);
            }else{
                $obj->sql_query("UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes' WHERE iTripOutstandId = " . $TripOutstandId);
            }
        }
        $obj->sql_query("UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId);
    }
    if ($checktype == 'cancelTrip' && !empty($iTripId) && (strtoupper($vTripPaymentMode) == "CARD" || strtoupper($vTripPaymentMode) == "WALLET")) {
        $obj->sql_query("UPDATE trips set fOutStandingAmount = 0 WHERE iTripId = '" . $iTripId . "'");
    }
    if($checktype == 'CollectPayment'){
        if ((strtoupper($vTripPaymentMode) == "CARD" || strtoupper($vTripPaymentMode) == "ORGANIZATION" || strtoupper($vTripPaymentMode) == "WALLET") && $isCollectCash == "" && ( $eType != "Multi-Delivery" || (strtoupper($ePaymentCollect_Delivery) == "NO" && $eType == "Multi-Delivery"))) {
            ## Add Trip Earning Amount Into Driver Wallet ##
            $vRideNo = $tripData[0]['vRideNo'];
            $iBalanceCard = $tripData[0]['fTripGenerateFare'] - ( $tripData[0]['fCommision'] + $tripData[0]['fHotelCommision'] + $tripData[0]['fOutStandingAmount'] + $totalTax );
            if(isset($_REQUEST['test'])){
                //echo "<pre>";print_r($walletArr);die;
            }
            $eForCard = "Deposit";
            $eTypeCard = "Credit";
            $tDescriptionCard = '#LBL_CREDITED_TRIP_EARNING_PROVIDER#' . $vRideNo;
            $ePaymentStatusCard = 'Settelled';
            $dDateCard = Date('Y-m-d H:i:s');
            if (!isset($walletArr[$eTypeCard]['Driver'][$iTripId]) && $iBalanceCard > 0) {
                $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalanceCard, $eTypeCard, $iTripId, $eForCard, $tDescriptionCard, $ePaymentStatusCard, $dDateCard);
            }
            $WhereCard = " iTripId = '".$iTripId."'";
            $Data_update_driver_paymentstatus_Card = array();
            $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
            $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
        }
    }else if($checktype == 'TripCollectTip' && $driverTipAmt > 0){
        // $datad_wallet['ePaymentStatus'] = "Unsettelled";
        // $dDate = date("Y-m-d H:i:s");
        // $tDescription = '#LBL_CREDITED_TIP_AMOUNT_TXT#' . " - " . $vRideNo; //Debited for Tip of Trip
        // $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $driverTipAmt, "Credit", $iTripId, "Deposit", $tDescription, "Unsettelled", $dDate);

        $iDriverId = isset($data['iDriverId']) ? $data['iDriverId'] : $tripData[0]['iDriverId'];
        $iTripId = $data['iTripId'];
        $iOrderId = $data['iOrderId'];
        $vOrderNo = $data['vOrderNo']; 

        if(!empty($data['vOrderNo']) && $vOrderNo > 0) {
            $wallet_desc =  '#LBL_CREDITED_ORDER_TIP_AMOUNT_TXT#' ." - ". $vOrderNo;   
        }
        else {
            $vRideNo = $tripData[0]['vRideNo'];
            $wallet_desc =  '#LBL_CREDITED_TIP_AMOUNT_TXT#' ." - ". $vRideNo;   
        }

        $datad_wallet['iUserId'] = $iDriverId;
        $datad_wallet['eUserType'] = "Driver";
        $datad_wallet['iBalance'] = $driverTipAmt;
        $datad_wallet['eType'] = "Credit";
        $datad_wallet['dDate'] = date("Y-m-d H:i:s");
        $datad_wallet['iTripId'] = $iTripId;
        $datad_wallet['iOrderId'] = $iOrderId;
        $datad_wallet['eFor'] = "Tip"; //Deposit
        $datad_wallet['ePaymentStatus'] = "Settelled";
        $datad_wallet['tDescription'] = $wallet_desc; //Debited for Tip for order
        
        $WALLET_OBJ->PerformWalletTransaction($datad_wallet['iUserId'], $datad_wallet['eUserType'], $datad_wallet['iBalance'], $datad_wallet['eType'], $datad_wallet['iTripId'], $datad_wallet['eFor'], $datad_wallet['tDescription'], $datad_wallet['ePaymentStatus'], $datad_wallet['dDate'],$iOrderId);
    }else if($checktype == 'UpdateOrderStatusDriver' && $fDeliveryCharge > 0){
        $iOrderId = $data['iOrderId'];
        $iDriverId = $data['iDriverId'];
        $iTripId = $data['iTripId'];
        $eForCard = "Deposit";
        $eTypeCard = "Credit";
        $tDescriptionCard = '#LBL_ADJUSTMENT_EARNING_ORDER#' . $vOrderNo;
        $ePaymentStatusCard = 'Settelled';
        $dDateCard = Date('Y-m-d H:i:s');
        $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $fDeliveryCharge, $eTypeCard, $iTripId, $eForCard, $tDescriptionCard, $ePaymentStatusCard, $dDateCard);
        $WhereCard = " iTripId = '$iTripId'";
        $Data_update_driver_paymentstatus_Card = array();
        $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
        $Update_Payment_Card_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
    }
}
//Added By HJ On 22-10-2020 For Auto Credit Driver Earning Amount End
function autoCreditDriverEarningBidding($data, $checktype = 'CollectPayment')
{
    global $obj, $WALLET_OBJ,$BIDDING_OBJ;
    $driverTipAmt = $iUserId = $iTripId = $vOrderNo = $fDeliveryCharge = 0;
    $isCollectCash = isset($data["isCollectCash"]) ? $data["isCollectCash"] : '';
    if(isset($data['iUserId']) && $data['iUserId'] > 0){
        $iUserId = $data['iUserId'];
    }
    if(isset($data['iBiddingPostId']) && $data['iBiddingPostId'] > 0){
        $iBiddingPostId = $data['iBiddingPostId'];
    }
    if(isset($data['vBiddingPostNo']) && $data['vBiddingPostNo'] > 0){
        $vBiddingPostNo = $data['vBiddingPostNo'];
    }
    $tripoutstandingdata = $walletArr = $biddingIdArr = $tripDataArr= array();
    $tripoutstandingdata = $obj->MySQLSelect("SELECT iTripOutstandId, fDriverPendingAmount, vTripAdjusmentId, iDriverId, fWalletDebit, iBiddingPostId FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND ePaidToDriver = 'No'");
    if(isset($_REQUEST['test'])){
        //echo "<pre>";print_r($tripoutstandingdata);die;
    }
    $BiddingData = $obj->MySQLSelect("SELECT * FROM bidding_post WHERE iBiddingPostId='".$iBiddingPostId."'");
    $vTripPaymentMode = $BiddingData[0]['ePaymentOption'];
    $vBiddingPostNo = $BiddingData[0]['vBiddingPostNo'];
    $iDriverId = $BiddingData[0]['iDriverId'];
    $totalTax = $BiddingData[0]['fTax1'] + $BiddingData[0]['fTax2'];
    $fCommission = $BiddingData[0]['fCommission'];
    // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
    $getPaymentStatus = $obj->MySQLSelect("SELECT iBiddingPostId,eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iBiddingPostId='" . $iBiddingPostId . "' AND eUserType ='Driver'");
    for ($h = 0;$h < count($getPaymentStatus);$h++) {
        $walletArr[$getPaymentStatus[$h]['eType']]['Driver'][$getPaymentStatus[$h]['iBiddingPostId']] = $getPaymentStatus[$h]['eType'];
    }
    // Added By HJ Orn 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
    if (count($tripoutstandingdata) > 0) {
        //Added By HJ On 30-06-2020 For Optimize trips Table Query Start
        for($h=0;$h<count($tripoutstandingdata);$h++){
            $biddingIdArr[] = $tripoutstandingdata[$h]['iBiddingPostId'];
        }
        if(count($biddingIdArr) > 0){
            $implodeId = implode(",",$biddingIdArr);
            $tripricedata = $obj->MySQLSelect("SELECT vBiddingPostNo,iBiddingPostId FROM bidding_post WHERE iBiddingPostId IN ($implodeId)");
            for($g=0;$g<count($tripricedata);$g++){
                $tripDataArr[$tripricedata[$g]['iBiddingPostId']] = $tripricedata[$g]['vBiddingPostNo']; 
            }
        }
        //echo "<pre>";print_r($tripDataArr);die;
        //Added By HJ On 30-06-2020 For Optimize trips Table Query End
        for ($i = 0; $i < count($tripoutstandingdata); $i++) {
            $TripOutstandId = $tripoutstandingdata[$i]['iTripOutstandId'];
            $fDriverPendingAmount = $tripoutstandingdata[$i]['fDriverPendingAmount'] ;
            $iTripRefundId = $tripoutstandingdata[$i]['iBiddingPostId'];
            $iDriverRefundId = $tripoutstandingdata[$i]['iDriverId'];
            $vRideNo = "";
            if(isset($biddingIdArr[$iTripRefundId])){
                $vRideNo = $biddingIdArr[$iTripRefundId];
            }
            $iBalance = $fDriverPendingAmount;
            $eFor = "Booking";
            $eType = "Credit";
            $ePaymentStatus = "Settelled";
            $dDate = Date('Y-m-d H:i:s');
            $tDescription_credit = '#LBL_AMOUNT_CANCELTRIP_CREDIT# ' . $vRideNo;
            if($iBalance > 0){
                $WALLET_OBJ->PerformWalletTransaction($iDriverRefundId, "Driver", $iBalance, $eType, $iTripRefundId, $eFor, $tDescription_credit, $ePaymentStatus, $dDate);
            }
            $WhereCard = " iBiddingPostId = '$iBiddingPostId'";
            $Data_update_driver_paymentstatus_Card = array();
            $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
            $Update_Payment_Card_Id = $obj->MySQLQueryPerform("bidding_post", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
            $obj->sql_query("UPDATE trip_outstanding_amount set ePaidToDriver = 'Yes' WHERE iTripOutstandId = " . $TripOutstandId);
        }
        $obj->sql_query("UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = " . $iUserId);
    }
    if($checktype == 'CollectPayment'){
        if ((strtoupper($vTripPaymentMode) == "CARD" ||  strtoupper($vTripPaymentMode) == "WALLET") && $isCollectCash == "") {
            ## Add Trip Earning Amount Into Driver Wallet ##
            $getbiddingFinalAmount = $BIDDING_OBJ->getbiddingFinalAmount($iBiddingPostId);
            $total_fare = $getbiddingFinalAmount + $fOutStandingAmount;
            $vBiddingPostNo = $BiddingData[0]['vBiddingPostNo'];
            $fCommissiondebit = round(($total_fare * $fCommission) / 100, 2);
           // $iBalanceCard = $fCommissiondebit + $fOutStandingAmount;
            $iBalanceCard = $total_fare - $fCommissiondebit + $fOutStandingAmount;
            $eForCard = "Deposit";
            $eTypeCard = "Credit";
            $tDescriptionCard = '#LBL_CREDITED_TRIP_EARNING_PROVIDER#' . $vBiddingPostNo;
            $ePaymentStatusCard = 'Settelled';
            $dDateCard = Date('Y-m-d H:i:s');
            if (!isset($walletArr[$eTypeCard]['Driver'][$iBiddingPostId]) && $iBalanceCard > 0) {
                $wallet_id =  $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalanceCard, $eTypeCard, $iBiddingPostId, $eForCard, $tDescriptionCard, $ePaymentStatusCard, $dDateCard);
                 $obj->sql_query("UPDATE user_wallet SET iTripId = 0, iBiddingPostId = '" . $iBiddingPostId . "' WHERE iUserWalletId = '$wallet_id'");
            }
            $WhereCard = " iBiddingPostId = '".$iBiddingPostId."'";
            $Data_update_driver_paymentstatus_Card = array();
            $Data_update_driver_paymentstatus_Card['eDriverPaymentStatus'] = "Settelled";
            $Update_Payment_Card_Id = $obj->MySQLQueryPerform("bidding_post", $Data_update_driver_paymentstatus_Card, 'update', $WhereCard);
        }
    }
}
?>