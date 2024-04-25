<?php



namespace Kesk\Web\Common;

class RiderReward
{
    public function __construct()
    {
        global $obj;
    }

    public function GiveRiderReward($iUserId, $iTripId)
    {
        global $obj, $USER_REWARD_AMOUNT_FOR_COINS, $USER_REWARD_COINS_FOR_DISTANCE, $WALLET_OBJ, $DEFAULT_DISTANCE_UNIT, $COMM_MEDIA_OBJ;
        $yes_to_reaward = [];
        $TripDetails = $obj->MySQLSelect('SELECT * FROM `trips` WHERE `iTripId` = '.$iTripId);
        $fDistance = $TripDetails[0]['fDistance'];
        if ('Miles' === $DEFAULT_DISTANCE_UNIT) {
            $tripDistanceDisplay = $fDistance * 0.621_371;
        } else {
            $tripDistanceDisplay = $fDistance;
        }
        $tripDistanceDisplayNumber = floor($tripDistanceDisplay);
        $UserRewardscoins = $tripDistanceDisplayNumber * $USER_REWARD_COINS_FOR_DISTANCE;
        if ($UserRewardscoins > 0) {
            $Userrewardamounts = $UserRewardscoins * $USER_REWARD_AMOUNT_FOR_COINS;
            $update_sql = "UPDATE trips set fUserRewardsCoins = '".$UserRewardscoins."' WHERE iTripId ='".$iTripId."'";
            $result = $obj->sql_query($update_sql);
            $data_wallet['iUserId'] = $iUserId;
            $data_wallet['eUserType'] = 'Rider';
            $data_wallet['iBalance'] = $Userrewardamounts;
            $data_wallet['eType'] = 'Credit';
            $data_wallet['dDate'] = date('Y-m-d H:i:s');
            $data_wallet['iTripId'] = $iTripId;
            $data_wallet['eFor'] = 'Deposit';
            $data_wallet['ePaymentStatus'] = 'Settelled';
            $data_wallet['tDescription'] = '#LBL_REWARD_AMOUNT_CREDITED_USER#';
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username, vCurrencyPassenger from register_user where iUserId='".$iUserId."'";
            $db_user_data = $obj->MySQLSelect($sql12);
            $vCurrencyPassenger = $db_user_data[0]['vCurrencyPassenger'];
            $userCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='".$vCurrencyPassenger."'");
            $userCurrencySymbol = $userCurrencyData[0]['vSymbol'];
            $userCurrencyRatio = $userCurrencyData[0]['Ratio'];
            $Userrewardamounts = round($Userrewardamounts * $userCurrencyRatio, 2);
            $maildatadeliverd['vEmail'] = $db_user_data[0]['vEmail'];
            $maildatadeliverd['UserName'] = $db_user_data[0]['username'];
            $maildatadeliverd['vRideNo'] = $TripDetails[0]['vRideNo'];
            $maildatadeliverd['amount'] = formateNumAsPerCurrency($Userrewardamounts, $vCurrencyPassenger);
            $mailResponse = $COMM_MEDIA_OBJ->SendMailToMember('REWARD_AMOUNT_CREDIT_TO_USER', $maildatadeliverd);
            $yes_to_reaward['status'] = 1;
        } else {
            $yes_to_reaward['status'] = 0;
        }

        return $yes_to_reaward;
    }
}
