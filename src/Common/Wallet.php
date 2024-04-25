<?php



namespace Kesk\Web\Common;

class Wallet
{
    public function __construct() {}

    public function PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate, $iOrderId = 0, $iTmpRentItemPostId = 0, $iBookingId = 0)
    {
        global $obj, $Data_ALL_currency_Arr, $currencyAssociateArr, $vSystemDefaultCurrencySymbol, $vSystemDefaultCurrencyRatio, $COMM_MEDIA_OBJ;
        $sql = "INSERT INTO `user_wallet` (`iUserId`,`eUserType`,`iBalance`,`eType`,`iTripId`, `eFor`, `tDescription`, `ePaymentStatus`, `dDate`,`iOrderId`,`iTmpRentItemPostId` , `iBookingId`) VALUES ('".$iUserId."','".$eUserType."', '".$iBalance."','".$eType."', '".$iTripId."', '".$eFor."', '".$tDescription."', '".$ePaymentStatus."', '".$dDate."','".$iOrderId."','".$iTmpRentItemPostId."' ,'".$iBookingId."' )";
        $result = $obj->MySQLInsert($sql);
        $db_curr = [];
        for ($g = 0; $g < \count($Data_ALL_currency_Arr); ++$g) {
            if ('ACTIVE' === strtoupper($Data_ALL_currency_Arr[$g]['eStatus'])) {
                $db_curr[] = $Data_ALL_currency_Arr[$g];
            }
        }
        $where = " iUserWalletId = '".$result."'";
        $data_currency_ratio = [];
        for ($i = 0; $i < \count($db_curr); ++$i) {
            $data_currency_ratio['fRatio_'.$db_curr[$i]['vName']] = $db_curr[$i]['Ratio'];
        }
        $obj->MySQLQueryPerform('user_wallet', $data_currency_ratio, 'update', $where);
        if ('Rider' === $eUserType) {
            $fieldname = 'iUserId';
            $tablename = 'register_user as ru';
            $tablename_alt = 'register_user';
            $getfields = 'ru.vCurrencyPassenger as currency, cu.ratio, cu.vSymbol, cu.vName, ru.vEmail, CONCAT( ru.vName, " ", ru.vLastName ) AS username';
            $currencyField = 'vCurrencyPassenger as vCurrency,iUserId as iMemberId, CONCAT( ru.vName, " ", ru.vLastName ) AS username';
            $currencyFieldName = 'vCurrencyPassenger';
            $onfields = 'ON ru.vCurrencyPassenger = cu.vName';
        } else {
            $fieldname = 'iDriverId';
            $tablename = 'register_driver as rd';
            $tablename_alt = 'register_driver';
            $getfields = 'rd.vCurrencyDriver as currency, cu.ratio, cu.vSymbol, cu.vName, rd.vEmail, CONCAT( rd.vName, " ", rd.vLastName) AS username';
            $currencyField = 'vCurrencyDriver as vCurrency,iDriverId as iMemberId, CONCAT( rd.vName, " ", rd.vLastName) AS username';
            $currencyFieldName = 'vCurrencyDriver';
            $onfields = 'ON rd.vCurrencyDriver = cu.vName';
        }
        if (isset($userDetailsArr[$tablename_alt.'_'.$iUserId])) {
            $getUserData = $userDetailsArr[$tablename_alt.'_'.$iUserId];
        } else {
            $getUserData = $obj->MySQLSelect("SELECT *,{$currencyField} FROM ".$tablename." WHERE {$fieldname}='".$iUserId."'");
            $userDetailsArr[$tablename_alt.'_'.$iUserId] = $getUserData;
        }
        if (\count($getUserData) > 0) {
            $getUserData[0]['vCurrency'] = $getUserData[0][$currencyFieldName];
            $getUserData[0]['username'] = $getUserData[0]['vName'].' '.$getUserData[0]['vLastName'];
        }
        $currencySymbol = $currencyRatio = $currencyCode = '';
        if (isset($currencyAssociateArr[$getUserData[0][$currencyFieldName]])) {
            $currencySymbol = $currencyAssociateArr[$getUserData[0][$currencyFieldName]]['vSymbol'];
            $currencyRatio = $currencyAssociateArr[$getUserData[0][$currencyFieldName]]['Ratio'];
            $currencyCode = $currencyAssociateArr[$getUserData[0][$currencyFieldName]]['vName'];
        }
        if (('' === $currencySymbol || null === $currencySymbol) || ('' === $currencyRatio || null === $currencyRatio)) {
            if (!empty($vSystemDefaultCurrencySymbol) && !empty($vSystemDefaultCurrencyRatio)) {
                $DefaultCurrencyData = [];
                $DefaultCurrencyData[0]['vSymbol'] = $vSystemDefaultCurrencySymbol;
                $DefaultCurrencyData[0]['ratio'] = $vSystemDefaultCurrencyRatio;
            } else {
                $DefaultCurrencyData = get_value('currency', 'vSymbol,ratio,vName', 'eDefault', 'Yes');
            }
            $currencySymbol = $DefaultCurrencyData[0]['vSymbol'];
            $currencyRatio = $DefaultCurrencyData[0]['ratio'];
            $currencyCode = $DefaultCurrencyData[0]['vName'];
        }
        $fAmount = $iBalance * $currencyRatio;
        $fAmount = number_format($fAmount, 2);
        $maildata['username'] = $getUserData[0]['username'];
        $maildata['vEmail'] = $getUserData[0]['vEmail'];
        $maildata['amount'] = formateNumAsPerCurrency($fAmount, $currencyCode);
        if ('Referrer' !== $eFor) {
            if ('Credit' === $eType) {
                $status = $COMM_MEDIA_OBJ->SendMailToMember('WALLET_MONEY_CREDITED', $maildata);
            } elseif ('Debit' === $eType) {
                $status = $COMM_MEDIA_OBJ->SendMailToMember('WALLET_MONEY_DEBITED', $maildata);
            }
        } else {
            $Data_Referrer_Email['tMailInfo'] = json_encode($maildata);
            $Data_Referrer_Email['dDate'] = date('Y-m-d H:i:s');
            $obj->MySQLQueryPerform('wallet_money_referrer_email', $Data_Referrer_Email, 'insert');
        }

        return $result;
    }

    public function FetchMemberWalletBalance($sess_iMemberId, $type, $IS_DEDUCT_ACTIVE_TRIP_AMOUNT = false)
    {
        global $obj, $SYSTEM_PAYMENT_FLOW, $ePayWallet, $MODULES_OBJ;
        $getUserWallet = $obj->MySQLSelect("SELECT eType,SUM(iBalance) as totBalance FROM user_wallet WHERE iUserId = '".$sess_iMemberId."' AND eUserType = '".$type."' GROUP BY eType");
        $debitBalance = $creditBalance = 0;
        if (\count($getUserWallet) > 0) {
            for ($d = 0; $d < \count($getUserWallet); ++$d) {
                $eType = $getUserWallet[$d]['eType'];
                $totBalance = $getUserWallet[$d]['totBalance'];
                if ('Credit' === $eType) {
                    $creditBalance += $totBalance;
                } elseif ('Debit' === $eType) {
                    $debitBalance += $totBalance;
                }
            }
        }
        $balance = $creditBalance - $debitBalance;
        if (true === $IS_DEDUCT_ACTIVE_TRIP_AMOUNT && 'Rider' === $type && 'YES' === strtoupper($ePayWallet)) {
            $sql_auth_chk = "SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM trips as tr WHERE tr.iActive != 'Canceled' AND tr.iActive != 'Finished' AND tr.tUserWalletBalance != '' AND tr.vTripPaymentMode = 'Wallet' AND tr.iUserId = '".$sess_iMemberId."'";
            $data_user_bal_trips = $obj->MySQLSelect($sql_auth_chk);
            $currDateTime = date('Y-m-d H:i:s');
            $sql_auth_riderLater = $obj->MySQLSelect("SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM cab_booking as CB WHERE CB.eStatus NOT IN ('Declined','Failed','Cancel','Completed') AND CB.tUserWalletBalance != '' AND CB.iUserId = '".$sess_iMemberId."' AND dBooking_date >='".$currDateTime."'");
            if ('YES' === strtoupper(DELIVERALL)) {
                $sql_auth_orders_chk = "SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM orders as ord WHERE ord.ePaid = 'No' AND ord.iStatusCode IN(1,2,4,5,12) AND ord.ePaymentOption = 'Wallet' AND ord.iUserId = '".$sess_iMemberId."'";
                $data_user_bal_orders = $obj->MySQLSelect($sql_auth_orders_chk);
            }
            if ($MODULES_OBJ->isEnableRideShareService()) {
                $todayDate = date('Y-m-d H:i:s');
                $sql_auth_rideshare_chk = "SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM ride_share_bookings WHERE eStatus = 'Pending' AND ePaymentOption = 'Wallet' AND iUserId = '".$sess_iMemberId."' AND dBooking_date > '{$todayDate}' ";
                $data_user_bal_rideshare = $obj->MySQLSelect($sql_auth_rideshare_chk);
            }
            $tUserWalletBalance = 0;
            $returnArr = [];
            $returnArr['WalletBalance'] = (string) $balance;
            $returnArr['AutorizedWalletBalance'] = (string) $balance;
            if (!empty($data_user_bal_trips) && \count($data_user_bal_trips) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $data_user_bal_trips[0]['tUserWalletBalance'];
            }
            if (!empty($sql_auth_riderLater) && \count($sql_auth_riderLater) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $sql_auth_riderLater[0]['tUserWalletBalance'];
            }
            if (!empty($data_user_bal_orders) && \count($data_user_bal_orders) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $data_user_bal_orders[0]['tUserWalletBalance'];
            }
            if (!empty($data_user_bal_rideshare) && \count($data_user_bal_rideshare) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $data_user_bal_rideshare[0]['tUserWalletBalance'];
            }
            if ($returnArr['AutorizedWalletBalance'] < 0) {
                $returnArr['AutorizedWalletBalance'] = 0;
            }
            $returnArr['TotalAuthorizedAmount'] = (string) 0;
            $returnArr['CurrentBalance'] = (string) $balance;
            if (\count($data_user_bal_trips) > 0 && !empty($data_user_bal_trips)) {
                if ($data_user_bal_trips[0]['tUserWalletBalance'] < 0) {
                    $data_user_bal_trips[0]['tUserWalletBalance'] = 0;
                }
                $tUserWalletBalance += round($data_user_bal_trips[0]['tUserWalletBalance'], 2);
            }
            if (!empty($sql_auth_riderLater) && \count($sql_auth_riderLater) > 0) {
                $tUserWalletBalance += round($sql_auth_riderLater[0]['tUserWalletBalance'], 2);
            }
            if (!empty($data_user_bal_orders) && \count($data_user_bal_orders) > 0) {
                $tUserWalletBalance += round($data_user_bal_orders[0]['tUserWalletBalance'], 2);
            }
            if (!empty($data_user_bal_rideshare) && \count($data_user_bal_rideshare) > 0) {
                $tUserWalletBalance += round($data_user_bal_rideshare[0]['tUserWalletBalance'], 2);
            }
            $balance -= $tUserWalletBalance;
            if ($balance < 0) {
                $balance = 0;
            }
            $returnArr['TotalAuthorizedAmount'] = (string) $tUserWalletBalance;
            $returnArr['CurrentBalance'] = (string) $balance;

            return $returnArr;
        }

        return $balance;
    }

    public function FetchMemberCurrencyWalletBalance($sess_iMemberId, $type, $IS_DEDUCT_ACTIVE_TRIP_AMOUNT = false)
    {
        global $obj, $SYSTEM_PAYMENT_FLOW, $ePayWallet;
        if ('Rider' === $type) {
            $sqld = "SELECT ru.vCurrencyPassenger as vCurrency,cu.vSymbol FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$sess_iMemberId."'";
        } else {
            $sqld = "SELECT rd.vCurrencyDriver as vCurrency,cu.vSymbol FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$sess_iMemberId."'";
        }
        $db_currency = $obj->MySQLSelect($sqld);
        $vCurrency = $db_currency[0]['vCurrency'];
        $vSymbol = $db_currency[0]['vSymbol'];
        if ('' === $vCurrency || null === $vCurrency) {
            $sql = "SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'";
            $currencyData = $obj->MySQLSelect($sql);
            $vCurrency = $currencyData[0]['vName'];
            $vSymbol = $currencyData[0]['vSymbol'];
        }
        $getUserWallet = $obj->MySQLSelect('SELECT eType,SUM(iBalance*fRatio_'.$vCurrency.") as totBalance FROM user_wallet WHERE iUserId = '".$sess_iMemberId."' AND eUserType = '".$type."' GROUP BY eType");
        $debitBalance = $creditBalance = 0;
        if (\count($getUserWallet) > 0) {
            for ($d = 0; $d < \count($getUserWallet); ++$d) {
                $eType = $getUserWallet[$d]['eType'];
                $totBalance = $getUserWallet[$d]['totBalance'];
                if ('Credit' === $eType) {
                    $creditBalance += $totBalance;
                } elseif ('Debit' === $eType) {
                    $debitBalance += $totBalance;
                }
            }
        }
        $balance = $creditBalance - $debitBalance;
        if (true === $IS_DEDUCT_ACTIVE_TRIP_AMOUNT && 'Rider' === $type && 'YES' === strtoupper($ePayWallet)) {
            $sql_auth_chk = "SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM trips as tr WHERE tr.iActive != 'Canceled' AND tr.iActive != 'Finished' AND tr.tUserWalletBalance != '' AND tr.vTripPaymentMode = 'Wallet' AND tr.iUserId = '".$sess_iMemberId."'";
            $data_user_bal_trips = $obj->MySQLSelect($sql_auth_chk);
            $currDateTime = date('Y-m-d H:i:s');
            $sql_auth_riderLater = $obj->MySQLSelect("SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM cab_booking as CB WHERE CB.eStatus NOT IN ('Declined','Failed','Cancel','Completed') AND CB.tUserWalletBalance != '' AND CB.iUserId = '".$sess_iMemberId."' AND dBooking_date >='".$currDateTime."'");
            if ('YES' === strtoupper(DELIVERALL)) {
                $sql_auth_orders_chk = "SELECT SUM(tUserWalletBalance) as tUserWalletBalance FROM orders as ord WHERE ord.ePaid = 'No' AND ord.iStatusCode IN(1,2,4,5,12) AND ord.ePaymentOption = 'Card' AND ord.iUserId = '".$sess_iMemberId."'";
                $data_user_bal_orders = $obj->MySQLSelect($sql_auth_orders_chk);
            }
            $tUserWalletBalance = 0;
            $returnArr = [];
            $returnArr['WalletBalance'] = (string) $balance;
            $returnArr['AutorizedWalletBalance'] = (string) $balance;
            if (!empty($data_user_bal_trips) && \count($data_user_bal_trips) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $data_user_bal_trips[0]['tUserWalletBalance'];
            }
            if (!empty($sql_auth_riderLater) && \count($sql_auth_riderLater) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $sql_auth_riderLater[0]['tUserWalletBalance'];
            }
            if (!empty($data_user_bal_orders) && \count($data_user_bal_orders) > 0) {
                $returnArr['AutorizedWalletBalance'] -= $data_user_bal_orders[0]['tUserWalletBalance'];
            }
            if ($returnArr['AutorizedWalletBalance'] < 0) {
                $returnArr['AutorizedWalletBalance'] = 0;
            }
            $returnArr['TotalAuthorizedAmount'] = (string) 0;
            $returnArr['CurrentBalance'] = (string) $balance;
            if (\count($data_user_bal_trips) > 0 && !empty($data_user_bal_trips)) {
                if ($data_user_bal_trips[0]['tUserWalletBalance'] < 0) {
                    $data_user_bal_trips[0]['tUserWalletBalance'] = 0;
                }
                $tUserWalletBalance += round($data_user_bal_trips[0]['tUserWalletBalance'], 2);
            }
            if (!empty($sql_auth_riderLater) && \count($sql_auth_riderLater) > 0 && !empty($sql_auth_riderLater)) {
                $tUserWalletBalance += round($sql_auth_riderLater[0]['tUserWalletBalance'], 2);
            }
            if (!empty($data_user_bal_orders) && \count($data_user_bal_orders) > 0 && !empty($data_user_bal_orders)) {
                $tUserWalletBalance += round($data_user_bal_orders[0]['tUserWalletBalance'], 2);
            }
            $balance -= $tUserWalletBalance;
            if ($balance < 0) {
                $balance = 0;
            }
            $returnArr['TotalAuthorizedAmount'] = (string) $tUserWalletBalance;
            $returnArr['CurrentBalance'] = (string) $balance;

            return $returnArr;
        }

        return $balance;
    }

    public function FetchMemberWalletBalanceApp($sess_iMemberId, $type, $Original = 'No', $IS_RETRIVE_DATA_ORIG = 'No')
    {
        global $obj, $UserCurrencyLanguageDetailsArr, $DriverCurrencyLanguageDetailsArr, $vSystemDefaultCurrencyName, $vSystemDefaultCurrencySymbol, $currencyAssociateArr;
        if ('Rider' === $type) {
            $tblname = 'register_user';
            $fieldName = 'iUserId';
            $currencyField = 'vCurrencyPassenger';
        } else {
            $tblname = 'register_driver';
            $fieldName = 'iDriverId';
            $currencyField = 'vCurrencyDriver';
        }
        if (isset($userDetailsArr[$tblname.'_'.$sess_iMemberId])) {
            $memberData = $userDetailsArr[$tblname.'_'.$sess_iMemberId];
        } else {
            $memberData = $obj->MySQLSelect("SELECT *,{$fieldName} AS iMemberId FROM ".$tblname." WHERE {$fieldName}='".$sess_iMemberId."'");
            $userDetailsArr[$tblname.'_'.$sess_iMemberId] = $memberData;
        }
        $uservSymbol = $memberData[0][$currencyField];
        if (isset($currencyAssociateArr[$uservSymbol])) {
            $userCurrencyData = $currencyAssociateArr[$uservSymbol];
            $userCurrencySymbol = $vSymbol = $userCurrencyData['vSymbol'];
            $vCurrency = $userCurrencyData['vName'];
            $Ratio = $userCurrencyData['Ratio'];
        } else {
            $userCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='".$uservSymbol."'");
            $vSymbol = $userCurrencyData[0]['vSymbol'];
            $vCurrency = $userCurrencyData[0]['vName'];
            $Ratio = $userCurrencyData[0]['Ratio'];
        }
        if ('' === $vCurrency || null === $vCurrency) {
            if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol) && !empty($vSystemDefaultCurrencyRatio)) {
                $vCurrency = $vSystemDefaultCurrencyName;
                $vSymbol = $vSystemDefaultCurrencySymbol;
                $Ratio = $vSystemDefaultCurrencyRatio;
            } else {
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'");
                $vCurrency = $currencyData[0]['vName'];
                $vSymbol = $currencyData[0]['vSymbol'];
                $Ratio = $currencyData[0]['Ratio'];
            }
        }
        $getUserWallet = $obj->MySQLSelect("SELECT eType,SUM(iBalance) as totBalance FROM user_wallet WHERE iUserId = '".$sess_iMemberId."' AND eUserType = '".$type."' GROUP BY eType");
        $debitBalance = $creditBalance = 0;
        if (\count($getUserWallet) > 0) {
            for ($d = 0; $d < \count($getUserWallet); ++$d) {
                $eType = $getUserWallet[$d]['eType'];
                $totBalance = $getUserWallet[$d]['totBalance'];
                if ('Credit' === $eType) {
                    $creditBalance += $totBalance;
                } elseif ('Debit' === $eType) {
                    $debitBalance += $totBalance;
                }
            }
        }
        $balance = $creditBalance - $debitBalance;
        $balance *= $Ratio;
        if ('YES' === strtoupper($IS_RETRIVE_DATA_ORIG)) {
            if ($balance <= 0) {
                if ('Rider' === $type) {
                    $finalamt = '0.00';
                } else {
                    $finalamt = number_format($balance, 2, '.', '');
                }
            } else {
                $finalamt = number_format($balance, 2, '.', '');
            }
            $arr_data = [];
            $arr_data['CURRENCY_SYMBOL'] = $vSymbol;
            $arr_data['ORIG_AMOUNT'] = $finalamt;
            $arr_data['DISPLAY_AMOUNT'] = formateNumAsPerCurrency($finalamt, $vCurrency);

            return $arr_data;
        }
        if ('Yes' === $Original) {
            if ($balance <= 0) {
                $finalamt = formateNumAsPerCurrency(0.00, $vCurrency);
            } else {
                $finalamt = formateNumAsPerCurrency($balance, $vCurrency);
            }
        } else {
            if ($balance <= 0) {
                $finalamt = formateNumAsPerCurrency(0.00, $vCurrency);
            } else {
                $finalamt = formateNumAsPerCurrency($balance, $vCurrency);
            }
        }

        return $finalamt;
    }

    public function MemberCurrencyWalletBalance($currencyratio, $amount, $currencysymbol)
    {
        global $obj, $currencyAssociateArr;
        $parameter = 2;
        if (isset($currencyAssociateArr[$currencysymbol])) {
            $userCurrencyData = $currencyAssociateArr[$currencysymbol];
            $db_currency = [];
            $db_currency[0]['vSymbol'] = $userCurrencyData['vSymbol'];
            $db_currency[0]['vName'] = $userCurrencyData['vName'];
            $db_currency[0]['Ratio'] = $userCurrencyData['Ratio'];
        } else {
            $db_currency = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='".$currencysymbol."'");
        }
        $db_currency_name = $db_currency[0]['vName'];
        if ('' === $currencyratio || 0 === $currencyratio) {
            $amt = $amount * $db_currency[0]['Ratio'];
        } else {
            $amt = $amount * $currencyratio;
        }
        if (0 === $amt) {
            $finalamt = $db_currency[0]['vSymbol'].' 0.00';

            return $finalamt;
        }
        $finalamt = formateNumAsPerCurrency($amt, $db_currency_name);

        return $finalamt;
    }

    public function MemberCurrencyWalletBalanceFront($currencyratio, $amount, $currencysymbol)
    {
        global $obj, $currencyAssociateArr;
        $parameter = 2;
        if (isset($currencyAssociateArr[$currencysymbol])) {
            $userCurrencyData = $currencyAssociateArr[$currencysymbol];
            $db_currency = [];
            $db_currency[0]['vSymbol'] = $userCurrencyData['vSymbol'];
            $db_currency[0]['vName'] = $userCurrencyData['vName'];
            $db_currency[0]['Ratio'] = $userCurrencyData['Ratio'];
        } else {
            $db_currency = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='".$currencysymbol."'");
        }
        if ('' === $currencyratio || 0 === $currencyratio) {
            $amt = $amount;
        } else {
            $amt = $amount * $currencyratio;
        }
        if (0 === $amt) {
            $finalamt = $db_currency[0]['vSymbol'].' 0.00';

            return $finalamt;
        }
        $finalamt = formateNumAsPerCurrency($amt, $currencysymbol);

        return $finalamt;
    }
}
