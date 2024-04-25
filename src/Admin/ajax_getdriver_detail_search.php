<?php



include_once '../common.php';

$page = $_REQUEST['page'] ?? 1;
$term = $_REQUEST['term'] ?? '';
$usertype = $_REQUEST['usertype'] ?? 'Driver';
$id = $_REQUEST['id'] ?? '';
$company_id = $_REQUEST['company_id'] ?? '';
$iServiceId = $_REQUEST['selectedserviceId'] ?? '';
$searchDriverHotel = $_REQUEST['searchDriverHotel'] ?? '';
$searchRiderHotel = $_REQUEST['searchRiderHotel'] ?? '';
$trackingCompany = $_REQUEST['trackingCompany'] ?? '';
if (isset($trackingCompany) && !empty($trackingCompany) && 1 === $trackingCompany) {
    if ('Rider' === $usertype) {
        $usertype = 'TrackServiceRider';
    }
    if ('Driver' === $usertype) {
        $usertype = 'TrackServiceDriver';
    }
}

$resultCount = 10;
$end = ($page - 1) * $resultCount;
$start = $end + $resultCount;
$rdr_ssql = $cSql = $driveridarr = $useridarr = '';
if (SITE_TYPE === 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
}
if ('' !== $company_id) {
    $cSql = " AND iCompanyId = '".$company_id."'";
}
if ('' !== $iServiceId) {
    $srSql = " AND iServiceId = '".$iServiceId."'";
}
if ('Driver' === $usertype) {
    if ('' !== $searchDriverHotel) {
        $driveridarr = " AND iDriverId IN({$searchDriverHotel})";
    }
    if ('' !== $id) {
        $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iDriverId = '".$id."' {$cSql} {$rdr_ssql} {$driveridarr} order by vName";
        $db_drivers = $obj->MySQLSelect($sql);
        if (!empty($db_drivers)) {
            $db_drivers[0]['fullName'] = clearName($db_drivers[0]['fullName']);
            $db_drivers[0]['vEmail'] = clearEmail($db_drivers[0]['vEmail']);
            $db_drivers[0]['Phoneno'] = clearPhone($db_drivers[0]['Phoneno']);
            echo json_encode($db_drivers);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId = 0 AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%') {$cSql} {$rdr_ssql} {$driveridarr} order by vName LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId = 0 {$cSql} {$rdr_ssql} {$driveridarr} order by vName LIMIT {$end},{$start}";
        }
        $db_drivers = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId = 0 AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%') {$cSql} {$rdr_ssql} {$driveridarr}");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId = 0 {$cSql} {$rdr_ssql} {$driveridarr}");
        }
        foreach ($db_drivers as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_drivers[$key][$k] = '';
                }
                $db_drivers[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_drivers)) {
            // print_r($db_drivers);die;
            echo json_encode($db_drivers);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} elseif ('Company' === $usertype) {
    if ('' !== $id) {
        $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND iCompanyId = '".$id."' AND eBuyAnyService = 'No' order by vCompany";
        $db_company = $obj->MySQLSelect($sql);
        if (!empty($db_company)) {
            $db_company[0]['fullName'] = clearName($db_company[0]['fullName']);
            $db_company[0]['vEmail'] = clearEmail($db_company[0]['vEmail']);
            $db_company[0]['Phoneno'] = clearPhone($db_company[0]['Phoneno']);
            echo json_encode($db_company);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' ) AND eBuyAnyService = 'No' order by vCompany LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND eBuyAnyService = 'No' order by vCompany LIMIT {$end},{$start}";
        }
        $db_company = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' ) AND eBuyAnyService = 'No'");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND eBuyAnyService = 'No'");
        }
        foreach ($db_company as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearCmpName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_company[$key][$k] = '';
                }
                $db_company[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_company)) {
            echo json_encode($db_company);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} elseif ('Store' === $usertype) {
    $eSystem = " AND eSystem = 'DeliverAll'";
    $ssqlsc = ' AND iServiceId IN('.$enablesevicescategory.')';
    if ('' !== $id) {
        $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND iCompanyId = '".$id."' {$eSystem} {$ssqlsc} {$srSql} AND eBuyAnyService = 'No' order by vCompany";
        $db_company = $obj->MySQLSelect($sql);
        if (!empty($db_company)) {
            echo json_encode($db_company);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' )  {$eSystem} {$ssqlsc} {$srSql} AND eBuyAnyService = 'No' order by vCompany LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' {$eSystem} {$ssqlsc} {$srSql} AND eBuyAnyService = 'No' order by vCompany LIMIT {$end},{$start}";
        }
        $db_company = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' )  {$eSystem} {$ssqlsc} {$srSql} AND eBuyAnyService = 'No'");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno from company WHERE eStatus != 'Deleted' {$eSystem} {$ssqlsc} {$srSql} AND eBuyAnyService = 'No' order by vCompany ");
        }
        foreach ($db_company as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearCmpName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_company[$key][$k] = '';
                }
                $db_company[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_company)) {
            echo json_encode($db_company);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} elseif ('TrackServiceRider' === $usertype) {
    if ('' !== $id) {
        $sql = "SELECT iTrackServiceUserId as id,CONCAT(vName,'- ',vLastName) AS fullName,vEmail,CONCAT(vPhoneCode,'- ',vPhone) AS Phoneno FROM track_service_users WHERE eStatus != 'Deleted' AND iTrackServiceUserId = '".$id."' ORDER BY vName";
        $db_company = $obj->MySQLSelect($sql);

        if (!empty($db_company)) {
            $db_company[0]['fullName'] = clearName($db_company[0]['fullName']);
            $db_company[0]['vEmail'] = clearEmail($db_company[0]['vEmail']);
            $db_company[0]['Phoneno'] = clearPhone($db_company[0]['Phoneno']);
            echo json_encode($db_company);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iTrackServiceUserId as id,CONCAT(vName,'- ',vLastName) AS fullName,vEmail,CONCAT(vPhoneCode,'- ',vPhone) AS Phoneno FROM track_service_users WHERE eStatus != 'Deleted' AND (vName LIKE '%".$term."%' OR vLastName LIKE '%".$term."%' OR CONCAT(vPhoneCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vPhoneCode,'-',vPhone) LIKE '%".$term."%' ) ORDER BY vName LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iTrackServiceUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhoneCode,'- ',vPhone) AS Phoneno FROM track_service_users WHERE eStatus != 'Deleted' ORDER BY vName LIMIT {$end},{$start}";
        }
        $db_company = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iTrackServiceUserId as id,vName AS fullName,vEmail,CONCAT(vPhoneCode,'- ',vPhone) AS Phoneno FROM track_service_users WHERE eStatus != 'Deleted'  AND (vName LIKE '%".$term."%' OR vLastName LIKE '%".$term."%' OR CONCAT(vPhoneCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vPhoneCode,'-',vPhone) LIKE '%".$term."%' ) ");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iTrackServiceUserId as id,vName AS fullName,vEmail,CONCAT(vPhoneCode,'- ',vPhone) AS Phoneno FROM track_service_users WHERE eStatus != 'Deleted' ");
        }
        foreach ($db_company as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearCmpName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_company[$key][$k] = '';
                }
                $db_company[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_company)) {
            echo json_encode($db_company);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} elseif ('TrackServiceCompany' === $usertype) {
    if ('' !== $id) {
        $sql = "SELECT iTrackServiceCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno FROM track_service_company WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId = '".$id."' ORDER BY vCompany";
        $db_company = $obj->MySQLSelect($sql);
        if (!empty($db_company)) {
            $db_company[0]['fullName'] = clearName($db_company[0]['fullName']);
            $db_company[0]['vEmail'] = clearEmail($db_company[0]['vEmail']);
            $db_company[0]['Phoneno'] = clearPhone($db_company[0]['Phoneno']);
            echo json_encode($db_company);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iTrackServiceCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno FROM track_service_company WHERE eStatus != 'Deleted' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' ) ORDER BY vCompany LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iTrackServiceCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno FROM track_service_company WHERE eStatus != 'Deleted' ORDER BY vCompany LIMIT {$end},{$start}";
        }
        $db_company = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iTrackServiceCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno FROM track_service_company WHERE eStatus != 'Deleted' AND eSystem = 'General' AND (vCompany LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%' ) ");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iTrackServiceCompanyId as id,vCompany AS fullName,vEmail,CONCAT(vCode,'- ',vPhone) AS Phoneno FROM track_service_company WHERE eStatus != 'Deleted' ");
        }
        foreach ($db_company as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearCmpName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_company[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_company[$key][$k] = '';
                }
                $db_company[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_company)) {
            echo json_encode($db_company);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} elseif ('TrackServiceDriver' === $usertype) {
    if ('' !== $searchDriverHotel) {
        $driveridarr = " AND iDriverId IN({$searchDriverHotel})";
    }
    $cSql = ' AND iTrackServiceCompanyId > 0 ';
    if ('' !== $company_id) {
        $cSql .= " AND iTrackServiceCompanyId = '".$company_id."'";
    }
    if ('' !== $id) {
        $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND iDriverId = '".$id."' {$cSql} {$rdr_ssql} {$driveridarr} order by vName";
        $db_drivers = $obj->MySQLSelect($sql);
        if (!empty($db_drivers)) {
            $db_drivers[0]['fullName'] = clearName($db_drivers[0]['fullName']);
            $db_drivers[0]['vEmail'] = clearEmail($db_drivers[0]['vEmail']);
            $db_drivers[0]['Phoneno'] = clearPhone($db_drivers[0]['Phoneno']);
            echo json_encode($db_drivers);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%') {$cSql} {$rdr_ssql} {$driveridarr} order by vName LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' {$cSql} {$rdr_ssql} {$driveridarr} order by vName LIMIT {$end},{$start}";
        }
        $db_drivers = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vCode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vCode,'-',vPhone) LIKE '%".$term."%') {$cSql} {$rdr_ssql} {$driveridarr}");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iDriverId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vCode,'-',vPhone) AS Phoneno from register_driver WHERE eStatus != 'Deleted' {$cSql} {$rdr_ssql} {$driveridarr}");
        }
        foreach ($db_drivers as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_drivers[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_drivers[$key][$k] = '';
                }
                $db_drivers[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_drivers)) {
            echo json_encode($db_drivers);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
} else {
    if ('' !== $searchRiderHotel) {
        $useridarr = " AND iUserId IN({$searchRiderHotel})";
    }
    if ('' !== $id) {
        $sql = "SELECT iUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhonecode,'- ',vPhone) AS Phoneno from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' AND iUserId = '".$id."' {$rdr_ssql} {$useridarr} order by vName";
        $db_rider = $obj->MySQLSelect($sql);
        if (!empty($db_rider)) {
            echo json_encode($db_rider);

            exit;
        }
    } else {
        if ('' !== $term) {
            $sql = "SELECT iUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhonecode,'- ',vPhone) AS Phoneno from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vPhonecode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vPhonecode,'-',vPhone) LIKE '%".$term."%' ) {$rdr_ssql} {$useridarr} order by vName LIMIT {$end},{$start}";
        } else {
            $sql = "SELECT iUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhonecode,'- ',vPhone) AS Phoneno from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' {$rdr_ssql} {$useridarr} order by vName LIMIT {$end},{$start}";
        }
        $db_rider = $obj->MySQLSelect($sql);
        if ('' !== $term) {
            $countdata = $obj->MySQLSelect("SELECT iUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhonecode,'- ',vPhone) AS Phoneno from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' AND (CONCAT(vName,' ',vLastName) LIKE '%".$term."%' OR vEmail LIKE '%".$term."%' OR CONCAT(vPhonecode,'',vPhone) LIKE '%".$term."%' OR CONCAT(vPhonecode,'-',vPhone) LIKE '%".$term."%' ) {$rdr_ssql} {$useridarr} order by vName ");
        } else {
            $countdata = $obj->MySQLSelect("SELECT iUserId as id,CONCAT(vName,' ',vLastName) AS fullName,vEmail,CONCAT(vPhonecode,'- ',vPhone) AS Phoneno from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' {$rdr_ssql} {$useridarr}");
        }
        foreach ($db_rider as $key => $value) {
            if ($value && SITE_TYPE === 'Demo') {
                $value = array_map('utf8_encode', $value);
            }
            foreach ($value as $k => $val) {
                if ('fullName' === $k && '' !== $val) {
                    $db_rider[$key][$k] = clearName($val);
                }
                if ('vEmail' === $k && '' !== $val) {
                    $db_rider[$key][$k] = clearEmail($val);
                }
                if ('Phoneno' === $k && '' !== $val) {
                    $db_rider[$key][$k] = clearPhone($val);
                }
                if ('Phoneno' === $k && '-' === $val) {
                    $db_rider[$key][$k] = '';
                }
                $db_rider[$key]['total_count'] = count($countdata);
            }
        }
        if (!empty($db_rider)) {
            echo json_encode($db_rider);

            exit;
        }

        $emptydata[0]['Phoneno'] = '';
        $emptydata[0]['fullName'] = '';
        $emptydata[0]['id'] = '';
        $emptydata[0]['total_count'] = '';
        $emptydata[0]['vEmail'] = '';
        $emptydata[0]['total_count'] = '';
        echo json_encode($emptydata);

        exit;
    }
}
