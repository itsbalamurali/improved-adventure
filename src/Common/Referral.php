<?php



namespace Kesk\Web\Common;

class Referral
{
    public function __construct() {}

    public function ValidateReferralCode($id)
    {
        global $obj;
        $str = '';
        $sql = "SELECT iUserId,vRefCode FROM register_user WHERE vRefCode = '".$id."' AND (eStatus = 'Active' || eStatus = 'Inactive')";
        $db_user = $obj->MySQLSelect($sql);
        if (\count($db_user) > 0) {
            $eRefType = 'Rider';
            $str .= $db_user[0]['iUserId'].'|'.$eRefType;
        } else {
            $sql = "SELECT iDriverId,vRefCode FROM register_driver WHERE vRefCode = '".$id."'AND (eStatus = 'Active' || eStatus = 'Inactive')";
            $db_driver = $obj->MySQLSelect($sql);
            if (\count($db_driver) > 0) {
                $eRefType = 'Driver';
                $str .= $db_driver[0]['iDriverId'].'|'.$eRefType;
            } else {
                $str .= 0;
            }
        }

        return $str;
    }

    public function GenerateReferralCode($ereftype)
    {
        global $obj;
        $str = '';
        $milliseconds = time();
        $shareCode = $this->randomAlphaNum(5);
        $timeDigitsStr = (string) $milliseconds;
        $shareCode .= substr($timeDigitsStr, \strlen($timeDigitsStr) - 4, \strlen($timeDigitsStr) - 1);
        if ('Rider' === $ereftype) {
            $newstring = $shareCode;
            $str .= 'pr'.$newstring;
        } elseif ('Driver' === $ereftype) {
            $newstring = $shareCode;
            $str .= 'dr'.$newstring;
        }
        $sql_chk_user = "SELECT ru.vRefCode as pRefCode FROM register_user as ru WHERE ru.vRefCode = '".$str."'";
        $sql_chk_driver = "SELECT rd.vRefCode as dRefCode FROM register_driver as rd WHERE rd.vRefCode = '".$str."'";
        $result_chk_user = $obj->MySQLSelect($sql_chk_user);
        $result_chk_driver = $obj->MySQLSelect($sql_chk_driver);
        if ((\count($result_chk_user) > 0 && !empty($result_chk_user)) || (\count($result_chk_driver) > 0 && !empty($result_chk_driver))) {
            $str = $this->GenerateReferralCode($ereftype);
        }

        return $str;
    }

    public function randomAlphaNum($length)
    {
        $rangeMin = 36 ** ($length - 1);
        $rangeMax = 36 ** $length - 1;
        $base10Rand = random_int($rangeMin, $rangeMax);

        return base_convert($base10Rand, 10, 36);
    }

    public function CreditReferralAmountSingle($iTripId)
    {
        global $obj, $COMPANY_NAME, $REFERRAL_AMOUNT, $tripDetailsArr, $userDetailsArr, $WALLET_OBJ, $COMM_MEDIA_OBJ, $CONFIG_OBJ;
        if (isset($tripDetailsArr['trips_'.$iTripId])) {
            $db_result = $tripDetailsArr['trips_'.$iTripId];
        } else {
            $db_result = $obj->MySQLSelect('SELECT * from trips where iTripId='.$iTripId);
            $tripDetailsArr['trips_'.$iTripId] = $db_result;
        }
        $getPaymentStatus = $obj->MySQLSelect("SELECT iTripId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iTripId='".$iTripId."'");
        $walletArr = [];
        for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
            $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iTripId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
        }
        $count_rider = \count($db_result);
        if ($count_rider > 0) {
            if (isset($userDetailsArr['register_user_'.$db_result[0]['iUserId']])) {
                $db_rider_user = $userDetailsArr['register_user_'.$db_result[0]['iUserId']];
                if (\count($db_rider_user) > 0) {
                    $db_rider_user[0]['currency'] = $db_rider_user[0]['vCurrencyPassenger'];
                    $db_rider_user[0]['tripusername'] = $db_rider_user[0]['vName'].' '.$db_rider_user[0]['vLastName'];
                }
            } else {
                $db_rider_user = $obj->MySQLSelect("SELECT vCurrencyPassenger AS currency,iUserId,iRefUserId ,eRefType, CONCAT(vName,' ',vLastName) as tripusername from register_user where iUserId=".$db_result[0]['iUserId']);
            }
            $count_rider_user = \count($db_rider_user);
            if ($count_rider_user > 0) {
                $iRefUserId = $db_rider_user[0]['iRefUserId'];
                $iUserId = $db_rider_user[0]['iUserId'];
                $eRefType = $db_rider_user[0]['eRefType'];
                if (0 !== $iRefUserId) {
                    $sql1 = "SELECT iUserId,vRideNo from trips where iUserId='".$iUserId."' AND iActive = 'Finished'";
                    $db_trips_user = $obj->MySQLSelect($sql1);
                    $count_rider = \count($db_trips_user);
                    $bsql1 = "SELECT iUserId,vBiddingPostNo,iBiddingPostId from bidding_post where iUserId = '".$iUserId."' AND eStatus = 'Completed'";
                    $db_bidding_task_user = $obj->MySQLSelect($bsql1);
                    $count_rider_bidding = \count($db_bidding_task_user);
                    if (1 === $count_rider && 0 === $count_rider_bidding) {
                        $eFor = 'Referrer';
                        $tDescription = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                        $dDate = date('Y-m-d H:i:s');
                        $ePaymentStatus = 'Unsettelled';
                        if (!isset($walletArr['Credit'][$eRefType][$iTripId][$eFor])) {
                            $WALLET_OBJ->PerformWalletTransaction($iRefUserId, $eRefType, $REFERRAL_AMOUNT, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                        }
                        if ('Driver' === $eRefType) {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_driver where iDriverId='".$iRefUserId."'";
                        } else {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_user where iUserId='".$iRefUserId."'";
                        }
                        $db_user_refer = $obj->MySQLSelect($sql12);
                        $vEmailrider = $db_user_refer[0]['vEmail'];
                    }
                }
            }
            if (isset($userDetailsArr['register_driver_'.$db_result[0]['iDriverId']])) {
                $db_driver_user = $userDetailsArr['register_driver_'.$db_result[0]['iDriverId']];
                if (\count($db_driver_user) > 0) {
                    $db_driver_user[0]['currency'] = $db_driver_user[0]['vCurrencyDriver'];
                    $db_driver_user[0]['tripusername'] = $db_driver_user[0]['vName'].' '.$db_driver_user[0]['vLastName'];
                }
            } else {
                $db_driver_user = $obj->MySQLSelect("SELECT iRefUserId,iDriverId,eRefType, CONCAT(vName,' ',vLastName) as tripusername,vCurrencyDriver As currency from register_driver where iDriverId=".$db_result[0]['iDriverId']);
            }
            $count_driver_user = \count($db_driver_user);
            if ($count_driver_user > 0) {
                $iRefUserId = $db_driver_user[0]['iRefUserId'];
                $iDriverId = $db_driver_user[0]['iDriverId'];
                $eRefType = $db_driver_user[0]['eRefType'];
                if (0 !== $iRefUserId) {
                    $sql1 = "SELECT iDriverId,vRideNo from trips where iDriverId='".$iDriverId."' AND iActive = 'Finished'";
                    $db_trips_driver = $obj->MySQLSelect($sql1);
                    $count_driver = \count($db_trips_driver);
                    $bsql1 = "SELECT iDriverId,vBiddingPostNo,iBiddingPostId from bidding_post where iDriverId='".$iDriverId."' AND eStatus = 'Completed'";
                    $db_bidding_task_driver = $obj->MySQLSelect($bsql1);
                    $count_driver_bidding = \count($db_bidding_task_driver);
                    if (1 === $count_driver && 0 === $count_driver_bidding) {
                        $eFor_Driver = 'Referrer';
                        $tDescription_Driver = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                        $dDate_Driver = date('Y-m-d H:i:s');
                        $ePaymentStatus_Driver = 'Unsettelled';
                        if (!isset($walletArr['Credit'][$eRefType][$iTripId][$eFor_Driver])) {
                            $WALLET_OBJ->PerformWalletTransaction($iRefUserId, $eRefType, $REFERRAL_AMOUNT, 'Credit', $iTripId, $eFor_Driver, $tDescription_Driver, $ePaymentStatus_Driver, $dDate_Driver);
                        }
                        if ('Driver' === $eRefType) {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_driver where iDriverId='".$iRefUserId."'";
                        } else {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_user where iUserId='".$iRefUserId."'";
                        }
                        $db_driver_refer = $obj->MySQLSelect($sql12);
                        $vEmaildriver = $db_driver_refer[0]['vEmail'];
                    }
                }
            }
        }
        $vEmail = '';
        $currency = '';
        if (!empty($vEmaildriver)) {
            $vEmail = $vEmaildriver;
            $userName = $db_driver_refer[0]['username'];
            $tripusername = $db_driver_user[0]['tripusername'];
            $currency = $db_driver_user[0]['currency'];
        } elseif (!empty($vEmailrider)) {
            $vEmail = $vEmailrider;
            $userName = $db_user_refer[0]['username'];
            $tripusername = $db_rider_user[0]['tripusername'];
            $currency = $db_rider_user[0]['currency'];
        }
        if (!empty($vEmail)) {
            $REFERRAL_AMOUNT = $WALLET_OBJ->MemberCurrencyWalletBalance(0, $REFERRAL_AMOUNT, $currency);
            $maildatadeliverd['vEmail'] = $vEmail;
            $maildatadeliverd['UserName'] = $userName;
            $maildatadeliverd['TripUserName'] = $tripusername;
            if (empty($COMPANY_NAME)) {
                $COMPANY_NAME = $CONFIG_OBJ->getConfigurations('configurations', 'COMPANY_NAME');
                $COMPANY_NAME = $COMPANY_NAME[0]['vValue'];
            }
            $maildatadeliverd['CompanyName'] = $COMPANY_NAME;
            $maildatadeliverd['amount'] = $REFERRAL_AMOUNT;
            $mailResponse = $COMM_MEDIA_OBJ->SendMailToMember('REFERRAL_AMOUNT_CREDIT_TO_USER', $maildatadeliverd);
        }

        return $count_rider;
    }

    public function CreditReferralAmount($iTripId)
    {
        global $obj, $COMPANY_NAME, $REFERRAL_AMOUNT_EARN_STRATEGY, $REFERRAL_SCHEME_ENABLE, $MODULES_OBJ, $REFERRAL_LEVEL, $WALLET_OBJ;
        if (!$MODULES_OBJ->isEnableMultiLevelReferralSystem()) {
            if ('YES' === strtoupper($REFERRAL_SCHEME_ENABLE)) {
                $this->CreditReferralAmountSingle($iTripId);
            }
        } else {
            $sql = 'SELECT iUserId,iDriverId,iFare,eSystem,iOrderId,fWalletDebit from trips where iTripId='.$iTripId;
            $db_result = $obj->MySQLSelect($sql);
            $count_rider = \count($db_result);
            $getPaymentStatus = $obj->MySQLSelect("SELECT iTripId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iTripId='".$iTripId."'");
            $walletArr = [];
            for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
                $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iTripId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
            }
            if ($count_rider > 0) {
                $iFare = $db_result[0]['iFare'] + $db_result[0]['fWalletDebit'];
                if ('DeliverAll' === $db_result[0]['eSystem']) {
                    $db_result_order = $obj->MySQLSelect('SELECT fNetTotal,fWalletDebit FROM orders WHERE iOrderId = '.$db_result[0]['iOrderId']);
                    $iFare = $db_result_order[0]['fNetTotal'] + $db_result_order[0]['fWalletDebit'];
                }
                $refSql = "SELECT * FROM multi_level_referral_master WHERE eStatus = 'Active' ORDER BY iLevel LIMIT {$REFERRAL_LEVEL}";
                $refData = $obj->MySQLSelect($refSql);
                $sql = 'SELECT iMemberId,eUserType,tReferrerInfo from user_referrer_transaction where iMemberId = '.$db_result[0]['iUserId']." AND eUserType = 'Rider'";
                $db_rider_user = $obj->MySQLSelect($sql);
                $count_rider_user = \count($db_rider_user);
                if ($count_rider_user > 0) {
                    if ('' !== $db_rider_user[0]['tReferrerInfo']) {
                        $tReferrerInfo = json_decode($db_rider_user[0]['tReferrerInfo'], true);
                        $position = array_column($tReferrerInfo, 'Position of Referrer');
                        array_multisort($position, SORT_DESC, $tReferrerInfo);
                        $sql1 = "SELECT iUserId,vRideNo from trips where iUserId='".$db_rider_user[0]['iMemberId']."' AND iActive = 'Finished'";
                        $db_trips_user = $obj->MySQLSelect($sql1);
                        $count_rider = \count($db_trips_user);
                        $bsql1 = "SELECT iUserId,vBiddingPostNo,iBiddingPostId from bidding_post where iUserId='".$db_rider_user[0]['iMemberId']."' AND eStatus = 'Completed'";
                        $db_bidding_task_user = $obj->MySQLSelect($bsql1);
                        $count_rider_bidding = \count($db_bidding_task_user);
                        if (1 === $count_rider && 0 === $count_rider_bidding) {
                            $refRiderCount = 0;
                            foreach ($refData as $refLevel) {
                                if (isset($tReferrerInfo[$refRiderCount])) {
                                    $refMemberId = $tReferrerInfo[$refRiderCount]['iMemberId'];
                                    $refUserType = $tReferrerInfo[$refRiderCount]['eUserType'];
                                    $refAmount = $refLevel['iAmount'];
                                    if ('Percentage' === $REFERRAL_AMOUNT_EARN_STRATEGY) {
                                        $refAmount = ($refLevel['iAmount'] / 100) * $iFare;
                                    }
                                    $refAmount = setTwoDecimalPoint($refAmount, 2);
                                    $eFor = 'Referrer';
                                    $tDescription = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                                    $dDate = date('Y-m-d H:i:s');
                                    $ePaymentStatus = 'Unsettelled';
                                    if (!isset($walletArr['Credit'][$refUserType][$iTripId][$eFor])) {
                                        $wallet_id = $WALLET_OBJ->PerformWalletTransaction($refMemberId, $refUserType, $refAmount, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                                    }
                                    $where = ' iUserWalletId = '.$wallet_id;
                                    $wallet_update_data['fromUserId'] = $db_rider_user[0]['iMemberId'];
                                    $wallet_update_data['fromUserType'] = $db_rider_user[0]['eUserType'];
                                    $obj->MySQLQueryPerform('user_wallet', $wallet_update_data, 'update', $where);
                                }
                                ++$refRiderCount;
                            }
                        }
                    }
                }
                $sql = 'SELECT iMemberId,eUserType,tReferrerInfo from user_referrer_transaction where iMemberId = '.$db_result[0]['iDriverId']." AND eUserType = 'Driver'";
                $db_driver_user = $obj->MySQLSelect($sql);
                $count_driver_user = \count($db_driver_user);
                if ($count_driver_user > 0) {
                    if ('' !== $db_driver_user[0]['tReferrerInfo']) {
                        $tReferrerInfo = json_decode($db_driver_user[0]['tReferrerInfo'], true);
                        $position = array_column($tReferrerInfo, 'Position of Referrer');
                        array_multisort($position, SORT_DESC, $tReferrerInfo);
                        $sql1 = "SELECT iDriverId,vRideNo from trips where iDriverId='".$db_driver_user[0]['iMemberId']."' AND iActive = 'Finished'";
                        $db_trips_driver = $obj->MySQLSelect($sql1);
                        $count_driver = \count($db_trips_driver);
                        $bsql1 = "SELECT iDriverId,vBiddingPostNo,iBiddingPostId from bidding_post where iDriverId='".$db_driver_user[0]['iMemberId']."' AND eStatus = 'Completed'";
                        $db_bidding_task_driver = $obj->MySQLSelect($bsql1);
                        $count_driver_bidding = \count($db_bidding_task_driver);
                        if (1 === $count_driver && 0 === $count_driver_bidding) {
                            $refDriverCount = 0;
                            foreach ($refData as $refLevel) {
                                if (isset($tReferrerInfo[$refDriverCount])) {
                                    $refMemberId = $tReferrerInfo[$refDriverCount]['iMemberId'];
                                    $refUserType = $tReferrerInfo[$refDriverCount]['eUserType'];
                                    $refAmount = $refLevel['iAmount'];
                                    if ('Percentage' === $REFERRAL_AMOUNT_EARN_STRATEGY) {
                                        $refAmount = ($refLevel['iAmount'] / 100) * $iFare;
                                    }
                                    $refAmount = setTwoDecimalPoint($refAmount, 2);
                                    $eFor_Driver = 'Referrer';
                                    $tDescription_Driver = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                                    $dDate_Driver = date('Y-m-d H:i:s');
                                    $ePaymentStatus_Driver = 'Unsettelled';
                                    if (!isset($walletArr['Credit'][$refUserType][$iTripId][$eFor_Driver])) {
                                        $wallet_id = $WALLET_OBJ->PerformWalletTransaction($refMemberId, $refUserType, $refAmount, 'Credit', $iTripId, $eFor_Driver, $tDescription_Driver, $ePaymentStatus_Driver, $dDate_Driver);
                                    }
                                    $where = ' iUserWalletId = '.$wallet_id;
                                    $wallet_update_data['fromUserId'] = $db_driver_user[0]['iMemberId'];
                                    $wallet_update_data['fromUserType'] = $db_driver_user[0]['eUserType'];
                                    $obj->MySQLQueryPerform('user_wallet', $wallet_update_data, 'update', $where);
                                }
                                ++$refDriverCount;
                            }
                        }
                    }
                }
            }

            return $count_rider;
        }
    }

    public function CreditReferralAmountTakeAway($iOrderId): void
    {
        global $obj, $COMPANY_NAME, $REFERRAL_AMOUNT, $COMM_MEDIA_OBJ, $WALLET_OBJ, $CONFIG_OBJ;
        $orderSql = 'SELECT iUserId FROM orders WHERE iOrderId='.$iOrderId;
        $ordersData = $obj->MySQLSelect($orderSql);
        $sql = "SELECT vCurrencyPassenger AS currency,iUserId,iRefUserId ,eRefType, CONCAT(vName,' ',vLastName) as orderusername from register_user where iUserId=".$ordersData[0]['iUserId'];
        $db_order_user = $obj->MySQLSelect($sql);
        $count_order_user = \count($db_order_user);
        if ($count_order_user > 0) {
            $iRefUserId = $db_order_user[0]['iRefUserId'];
            $iUserId = $db_order_user[0]['iUserId'];
            $eRefType = $db_order_user[0]['eRefType'];
            if (0 !== $iRefUserId) {
                $eFor = 'Referrer';
                $tDescription = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                $dDate = date('Y-m-d H:i:s');
                $ePaymentStatus = 'Unsettelled';
                $WALLET_OBJ->PerformWalletTransaction($iRefUserId, $eRefType, $REFERRAL_AMOUNT, 'Credit', $iOrderId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                if ('Rider' === $eRefType) {
                    $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_user where iUserId='".$iRefUserId."'";
                } else {
                    $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_driver where iDriverId='".$iRefUserId."'";
                }
                $db_user_refer = $obj->MySQLSelect($sql12);
                $vEmailorder = $db_user_refer[0]['vEmail'];
            }
        }
        $vEmail = '';
        $currency = '';
        if (!empty($vEmailorder)) {
            $vEmail = $vEmailorder;
            $userName = $db_user_refer[0]['username'];
            $orderusername = $db_order_user[0]['orderusername'];
            $currency = $db_driver_user[0]['currency'];
        }
        if (!empty($vEmail)) {
            $REFERRAL_AMOUNT = $WALLET_OBJ->MemberCurrencyWalletBalance(0, $REFERRAL_AMOUNT, $currency);
            $maildatadeliverd['vEmail'] = $vEmail;
            $maildatadeliverd['UserName'] = $userName;
            $maildatadeliverd['TripUserName'] = $orderusername;
            if (empty($COMPANY_NAME)) {
                $COMPANY_NAME = $CONFIG_OBJ->getConfigurations('configurations', 'COMPANY_NAME');
                $COMPANY_NAME = $COMPANY_NAME[0]['vValue'];
            }
            $maildatadeliverd['CompanyName'] = $COMPANY_NAME;
            $maildatadeliverd['amount'] = $REFERRAL_AMOUNT;
            $mailResponse = $COMM_MEDIA_OBJ->SendMailToMember('REFERRAL_AMOUNT_CREDIT_TO_USER', $maildatadeliverd);
        }
    }

    public function CreditReferralAmountSingleBidding($iBiddingPostId)
    {
        global $obj, $COMPANY_NAME, $REFERRAL_AMOUNT, $tripDetailsArr, $userDetailsArr, $WALLET_OBJ, $COMM_MEDIA_OBJ, $CONFIG_OBJ, $BIDDING_OBJ;
        $db_result = $BIDDING_OBJ->getBiddingPost('webservice', $iBiddingPostId);
        $count_rider = \count($db_result);
        $getPaymentStatus = $obj->MySQLSelect("SELECT iBiddingPostId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iBiddingPostId='".$iBiddingPostId."'");
        $walletArr = [];
        for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
            $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iBiddingPostId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
        }
        if ($count_rider > 0) {
            if (isset($userDetailsArr['register_user_'.$db_result[0]['iUserId']])) {
                $db_rider_user = $userDetailsArr['register_user_'.$db_result[0]['iUserId']];
                if (\count($db_rider_user) > 0) {
                    $db_rider_user[0]['currency'] = $db_rider_user[0]['vCurrencyPassenger'];
                    $db_rider_user[0]['tripusername'] = $db_rider_user[0]['vName'].' '.$db_rider_user[0]['vLastName'];
                }
            } else {
                $db_rider_user = $obj->MySQLSelect("SELECT vCurrencyPassenger AS currency,iUserId,iRefUserId ,eRefType, CONCAT(vName,' ',vLastName) as tripusername from register_user where iUserId=".$db_result[0]['iUserId']);
            }
            $count_rider_user = \count($db_rider_user);
            if ($count_rider_user > 0) {
                $iRefUserId = $db_rider_user[0]['iRefUserId'];
                $iUserId = $db_rider_user[0]['iUserId'];
                $eRefType = $db_rider_user[0]['eRefType'];
                if (0 !== $iRefUserId) {
                    $sql1 = "SELECT iUserId,vRideNo from trips where iUserId='".$iUserId."' AND iActive = 'Finished'";
                    $db_trips_user = $obj->MySQLSelect($sql1);
                    $count_rider = \count($db_trips_user);
                    $bsql1 = "SELECT iUserId,vBiddingPostNo,iBiddingPostId from bidding_post where iUserId = '".$iUserId."' AND eStatus = 'Completed'";
                    $db_bidding_task_user = $obj->MySQLSelect($bsql1);
                    $count_rider_bidding = \count($db_bidding_task_user);
                    if (0 === $count_rider && 1 === $count_rider_bidding) {
                        $eFor = 'Referrer';
                        $tDescription = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                        $dDate = date('Y-m-d H:i:s');
                        $ePaymentStatus = 'Unsettelled';
                        if (!isset($walletArr['Credit'][$eRefType][$iBiddingPostId][$eFor])) {
                            $WALLET_OBJ->PerformWalletTransaction($iRefUserId, $eRefType, $REFERRAL_AMOUNT, 'Credit', $iBiddingPostId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                        }
                        if ('Driver' === $eRefType) {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_driver where iDriverId='".$iRefUserId."'";
                        } else {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_user where iUserId='".$iRefUserId."'";
                        }
                        $db_user_refer = $obj->MySQLSelect($sql12);
                        $vEmailrider = $db_user_refer[0]['vEmail'];
                    }
                }
            }
            if (isset($userDetailsArr['register_driver_'.$db_result[0]['iDriverId']])) {
                $db_driver_user = $userDetailsArr['register_driver_'.$db_result[0]['iDriverId']];
                if (\count($db_driver_user) > 0) {
                    $db_driver_user[0]['currency'] = $db_driver_user[0]['vCurrencyDriver'];
                    $db_driver_user[0]['tripusername'] = $db_driver_user[0]['vName'].' '.$db_driver_user[0]['vLastName'];
                }
            } else {
                $db_driver_user = $obj->MySQLSelect("SELECT iRefUserId,iDriverId,eRefType, CONCAT(vName,' ',vLastName) as tripusername,vCurrencyDriver As currency from register_driver where iDriverId=".$db_result[0]['iDriverId']);
            }
            $count_driver_user = \count($db_driver_user);
            if ($count_driver_user > 0) {
                $iRefUserId = $db_driver_user[0]['iRefUserId'];
                $iDriverId = $db_driver_user[0]['iDriverId'];
                $eRefType = $db_driver_user[0]['eRefType'];
                if (0 !== $iRefUserId) {
                    $sql1 = "SELECT iDriverId,vRideNo from trips where iDriverId='".$iDriverId."' AND iActive = 'Finished'";
                    $db_trips_driver = $obj->MySQLSelect($sql1);
                    $count_driver = \count($db_trips_driver);
                    $bsql1 = "SELECT iDriverId,vBiddingPostNo,iBiddingPostId from bidding_post where iDriverId='".$iDriverId."' AND eStatus = 'Completed'";
                    $db_bidding_task_driver = $obj->MySQLSelect($bsql1);
                    $count_driver_bidding = \count($db_bidding_task_driver);
                    if (0 === $count_driver && 1 === $count_driver_bidding) {
                        $eFor_Driver = 'Referrer';
                        $tDescription_Driver = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                        $dDate_Driver = date('Y-m-d H:i:s');
                        $ePaymentStatus_Driver = 'Unsettelled';
                        if (!isset($walletArr['Credit'][$eRefType][$iBiddingPostId][$eFor_Driver])) {
                            $WALLET_OBJ->PerformWalletTransaction($iRefUserId, $eRefType, $REFERRAL_AMOUNT, 'Credit', $iBiddingPostId, $eFor_Driver, $tDescription_Driver, $ePaymentStatus_Driver, $dDate_Driver);
                        }
                        if ('Driver' === $eRefType) {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_driver where iDriverId='".$iRefUserId."'";
                        } else {
                            $sql12 = "SELECT vEmail, CONCAT(vName,' ',vLastName) as username from register_user where iUserId='".$iRefUserId."'";
                        }
                        $db_driver_refer = $obj->MySQLSelect($sql12);
                        $vEmaildriver = $db_driver_refer[0]['vEmail'];
                    }
                }
            }
        }
        $vEmail = '';
        $currency = '';
        if (!empty($vEmaildriver)) {
            $vEmail = $vEmaildriver;
            $userName = $db_driver_refer[0]['username'];
            $tripusername = $db_driver_user[0]['tripusername'];
            $currency = $db_driver_user[0]['currency'];
        } elseif (!empty($vEmailrider)) {
            $vEmail = $vEmailrider;
            $userName = $db_user_refer[0]['username'];
            $tripusername = $db_rider_user[0]['tripusername'];
            $currency = $db_rider_user[0]['currency'];
        }
        if (!empty($vEmail)) {
            $REFERRAL_AMOUNT = $WALLET_OBJ->MemberCurrencyWalletBalance(0, $REFERRAL_AMOUNT, $currency);
            $maildatadeliverd['vEmail'] = $vEmail;
            $maildatadeliverd['UserName'] = $userName;
            $maildatadeliverd['TripUserName'] = $tripusername;
            if (empty($COMPANY_NAME)) {
                $COMPANY_NAME = $CONFIG_OBJ->getConfigurations('configurations', 'COMPANY_NAME');
                $COMPANY_NAME = $COMPANY_NAME[0]['vValue'];
            }
            $maildatadeliverd['CompanyName'] = $COMPANY_NAME;
            $maildatadeliverd['amount'] = $REFERRAL_AMOUNT;
            $mailResponse = $COMM_MEDIA_OBJ->SendMailToMember('REFERRAL_AMOUNT_CREDIT_TO_USER', $maildatadeliverd);
        }

        return $count_rider;
    }

    public function CreditReferralAmountBidding($iBiddingPostId)
    {
        global $obj, $COMPANY_NAME, $REFERRAL_AMOUNT_EARN_STRATEGY, $REFERRAL_SCHEME_ENABLE, $MODULES_OBJ, $REFERRAL_LEVEL, $WALLET_OBJ, $BIDDING_OBJ;
        if (!$MODULES_OBJ->isEnableMultiLevelReferralSystem()) {
            if ('YES' === strtoupper($REFERRAL_SCHEME_ENABLE)) {
                $this->CreditReferralAmountSingleBidding($iBiddingPostId);
            }
        } else {
            $getbiddingresult = $obj->MySQLSelect("SELECT * FROM bidding_post WHERE iBiddingPostId='".$iBiddingPostId."'");
            $count_rider = \count($getbiddingresult);
            $db_result = $BIDDING_OBJ->getBiddingPost('webservice', $iBiddingPostId);
            $getPaymentStatus = $obj->MySQLSelect("SELECT iBiddingPostId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iBiddingPostId='".$iBiddingPostId."'");
            $walletArr = [];
            for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
                $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iBiddingPostId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
            }
            if ($count_rider > 0) {
                $iFare = $db_result[0]['fBiddingAmount'];
                $refSql = "SELECT * FROM multi_level_referral_master WHERE eStatus = 'Active' ORDER BY iLevel LIMIT {$REFERRAL_LEVEL}";
                $refData = $obj->MySQLSelect($refSql);
                $sql = 'SELECT iMemberId,eUserType,tReferrerInfo from user_referrer_transaction where iMemberId = '.$db_result[0]['iUserId']." AND eUserType = 'Rider'";
                $db_rider_user = $obj->MySQLSelect($sql);
                $count_rider_user = \count($db_rider_user);
                if ($count_rider_user > 0) {
                    if ('' !== $db_rider_user[0]['tReferrerInfo']) {
                        $tReferrerInfo = json_decode($db_rider_user[0]['tReferrerInfo'], true);
                        $position = array_column($tReferrerInfo, 'Position of Referrer');
                        array_multisort($position, SORT_DESC, $tReferrerInfo);
                        $sql1 = "SELECT iUserId,vRideNo from trips where iUserId='".$db_rider_user[0]['iMemberId']."' AND iActive = 'Finished'";
                        $db_trips_user = $obj->MySQLSelect($sql1);
                        $count_rider = \count($db_trips_user);
                        $bsql1 = "SELECT iUserId,vBiddingPostNo,iBiddingPostId from bidding_post where iUserId='".$db_rider_user[0]['iMemberId']."' AND eStatus = 'Completed'";
                        $db_bidding_task_user = $obj->MySQLSelect($bsql1);
                        $count_rider_bidding = \count($db_bidding_task_user);
                        if (0 === $count_rider && 1 === $count_rider_bidding) {
                            $refRiderCount = 0;
                            foreach ($refData as $refLevel) {
                                if (isset($tReferrerInfo[$refRiderCount])) {
                                    $refMemberId = $tReferrerInfo[$refRiderCount]['iMemberId'];
                                    $refUserType = $tReferrerInfo[$refRiderCount]['eUserType'];
                                    $refAmount = $refLevel['iAmount'];
                                    if ('Percentage' === $REFERRAL_AMOUNT_EARN_STRATEGY) {
                                        $refAmount = ($refLevel['iAmount'] / 100) * $iFare;
                                    }
                                    $refAmount = setTwoDecimalPoint($refAmount, 2);
                                    $eFor = 'Referrer';
                                    $tDescription = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                                    $dDate = date('Y-m-d H:i:s');
                                    $ePaymentStatus = 'Unsettelled';
                                    if (!isset($walletArr['Credit'][$refUserType][$iBiddingPostId][$eFor])) {
                                        $wallet_id = $WALLET_OBJ->PerformWalletTransaction($refMemberId, $refUserType, $refAmount, 'Credit', $iBiddingPostId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                                        $where = ' iUserWalletId = '.$wallet_id;
                                        $wallet_update_data['fromUserId'] = $db_rider_user[0]['iMemberId'];
                                        $wallet_update_data['fromUserType'] = $db_rider_user[0]['eUserType'];
                                        $wallet_update_data['iBiddingPostId'] = $iBiddingPostId;
                                        $wallet_update_data['iTripId'] = 0;
                                        $obj->MySQLQueryPerform('user_wallet', $wallet_update_data, 'update', $where);
                                    }
                                }
                                ++$refRiderCount;
                            }
                        }
                    }
                }
                $sql = 'SELECT iMemberId,eUserType,tReferrerInfo from user_referrer_transaction where iMemberId = '.$db_result[0]['iDriverId']." AND eUserType = 'Driver'";
                $db_driver_user = $obj->MySQLSelect($sql);
                $count_driver_user = \count($db_driver_user);
                if ($count_driver_user > 0) {
                    if ('' !== $db_driver_user[0]['tReferrerInfo']) {
                        $tReferrerInfo = json_decode($db_driver_user[0]['tReferrerInfo'], true);
                        $position = array_column($tReferrerInfo, 'Position of Referrer');
                        array_multisort($position, SORT_DESC, $tReferrerInfo);
                        $sql1 = "SELECT iDriverId,vRideNo from trips where iDriverId='".$db_driver_user[0]['iMemberId']."' AND iActive = 'Finished'";
                        $db_trips_driver = $obj->MySQLSelect($sql1);
                        $count_driver = \count($db_trips_driver);
                        $bsql1 = "SELECT iDriverId,vBiddingPostNo,iBiddingPostId from bidding_post where iDriverId='".$db_driver_user[0]['iMemberId']."' AND eStatus = 'Completed'";
                        $db_bidding_task_driver = $obj->MySQLSelect($bsql1);
                        $count_driver_bidding = \count($db_bidding_task_driver);
                        if (0 === $count_driver && 1 === $count_driver_bidding) {
                            $refDriverCount = 0;
                            foreach ($refData as $refLevel) {
                                if (isset($tReferrerInfo[$refDriverCount])) {
                                    $refMemberId = $tReferrerInfo[$refDriverCount]['iMemberId'];
                                    $refUserType = $tReferrerInfo[$refDriverCount]['eUserType'];
                                    $refAmount = $refLevel['iAmount'];
                                    if ('Percentage' === $REFERRAL_AMOUNT_EARN_STRATEGY) {
                                        $refAmount = ($refLevel['iAmount'] / 100) * $iFare;
                                    }
                                    $refAmount = setTwoDecimalPoint($refAmount, 2);
                                    $eFor_Driver = 'Referrer';
                                    $tDescription_Driver = '#LBL_REFERRAL_AMOUNT_CREDIT#';
                                    $dDate_Driver = date('Y-m-d H:i:s');
                                    $ePaymentStatus_Driver = 'Unsettelled';
                                    if (!isset($walletArr['Credit'][$refUserType][$iBiddingPostId][$eFor_Driver])) {
                                        $wallet_id = $WALLET_OBJ->PerformWalletTransaction($refMemberId, $refUserType, $refAmount, 'Credit', $iBiddingPostId, $eFor_Driver, $tDescription_Driver, $ePaymentStatus_Driver, $dDate_Driver);
                                        $where = ' iUserWalletId = '.$wallet_id;
                                        $wallet_update_data['fromUserId'] = $db_driver_user[0]['iMemberId'];
                                        $wallet_update_data['fromUserType'] = $db_driver_user[0]['eUserType'];
                                        $wallet_update_data['iBiddingPostId'] = $iBiddingPostId;
                                        $wallet_update_data['iTripId'] = 0;
                                        $obj->MySQLQueryPerform('user_wallet', $wallet_update_data, 'update', $where);
                                    }
                                }
                                ++$refDriverCount;
                            }
                        }
                    }
                }
            }

            return $count_rider;
        }
    }
}
