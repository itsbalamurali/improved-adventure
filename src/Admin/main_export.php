<?php



use Models\Administrator;

include_once '../common.php';

$baseURL = $tconfig['tsite_url'];

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

// ini_set("display_errors", 1);

// error_reporting(E_ALL);

$section = $_REQUEST['section'] ?? '';

$sortby = $_REQUEST['sortby'] ?? 0;

$order = $_REQUEST['order'] ?? '';

$option = $_REQUEST['option'] ?? '';

$keyword = $_REQUEST['keyword'] ?? '';

$select_cat = $_REQUEST['selectcategory'] ?? '';

$eStatus = $_REQUEST['eStatus'] ?? '';

$startDate = $_REQUEST['startDate'] ?? '';

$endDate = $_REQUEST['endDate'] ?? '';

$type = $_REQUEST['exportType'] ?? '';

$ssql = '';

require 'fpdf/fpdf.php';

require 'TCPDF-master/tcpdf.php'; // Added By Hasmukh

$date = new DateTime();

$timestamp_filename = $date->getTimestamp();

$default_lang = $LANG_OBJ->FetchSystemDefaultLang();

function change_key($array, $old_key, $new_key)
{
    if (!array_key_exists($old_key, $array)) {
        return $array;
    }

    $keys = array_keys($array);

    $keys[array_search($old_key, $keys, true)] = $new_key;

    return array_combine($keys, $array);
}

function cleanData(&$str): void
{
    $str = preg_replace("/\t/", '\\t', $str);

    $str = preg_replace("/\r?\n/", '\\n', $str);

    if (strstr($str, '"')) {
        $str = '"'.str_replace('"', '""', $str).'"';
    }
}

if ('map_api' === $section) {
    $checkedvalues = $_REQUEST['checkedvalues'];

    $DbName = TSITE_DB;

    $TableName = 'auth_master_accounts_places';

    $TableName_Accounts = 'auth_accounts_places';

    $TableName_usage_report = 'auth_report_accounts_places';

    $siteUrl = $tconfig['tsite_url'];

    $data_drv['servicedata'] = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName);

    $data_drv['auth_accounts_places'] = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName_Accounts);

    $data_drv['usage_report'] = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName_usage_report);

    // $time = time();

    $date = date('d_m_Y_h_i_s');

    $file = 'map_api_export_'.$date.'.json';

    file_put_contents($file, json_encode($data_drv));

    header('Content-type: application/json');

    header('Content-Disposition: attachment; filename="'.basename($file).'"');

    header('Content-Length: '.filesize($file));

    // echo json_encode($data_drv) ."\t";

    echo json_encode($data_drv, JSON_PRETTY_PRINT)."\t";
}

if ('blocked_driver' === $section) {
    $cancel_for_hours = $CANCEL_DECLINE_TRIPS_IN_HOURS;

    $c_date = date('Y-m-d H:i:s');

    $s_date = date('Y-m-d H:i:s', strtotime('-'.$cancel_for_hours.' hours'));

    $ord = ' ORDER BY  `Total Cancelled Trips (Till now)` DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vEmail ASC';
        } else {
            $ord = ' ORDER BY rd.vEmail DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Cancelled Trips (In '.$cancel_for_hours.' hours)` ASC';
        } else {
            $ord = ' ORDER BY `Total Cancelled Trips (In '.$cancel_for_hours.' hours)` DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Declined Trips (In '.$cancel_for_hours.' hours)` ASC';
        } else {
            $ord = ' ORDER BY `Total Declined Trips (In '.$cancel_for_hours.' hours)` DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Cancelled Trips (Till now)` ASC';
        } else {
            $ord = ' ORDER BY `Total Cancelled Trips (Till now)` DESC';
        }
    }

    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Declined Trips (Till now)` ASC';
        } else {
            $ord = ' ORDER BY `Total Declined Trips (Till now)` DESC';
        }
    }

    if (7 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `eIsBlocked` ASC';
        } else {
            $ord = ' ORDER BY `eIsBlocked` DESC';
        }
    }

    if (8 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tBlockeddate ASC';
        } else {
            $ord = ' ORDER BY tBlockeddate DESC';
        }
    }

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('DriverName' === $option) {
                $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
            }

            if ('' !== $eIsBlocked) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND rd.eIsBlocked = '".clean($eIsBlocked)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if (ONLYDELIVERALL === 'Yes') {
                if ('' !== $eIsBlocked) {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%') AND rd.eIsBlocked = '".clean($eIsBlocked)."'";
                } else {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%')";
                }
            } else {
                if ('' !== $eIsBlocked) {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%') AND rd.eIsBlocked = '".clean($eIsBlocked)."'";
                } else {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%')";
                }
            }
        }
    } elseif ('' !== $eIsBlocked && '' === $keyword) {
        $ssql .= " AND rd.eIsBlocked = '".clean($eIsBlocked)."'";
    }

    // End Search Parameters

    $ssql1 = "AND (rd.vEmail != '' OR rd.vPhone != '')";

    $sql = "SELECT  CONCAT(rd.vName,' ',rd.vLastName) AS Name, rd.vEmail as Email , COALESCE( m.cnt , 0 ) AS `Total Cancelled Trips (In ".$cancel_for_hours.' hours)`,  COALESCE( d.cnt, 0 ) AS `Total Declined Trips (In '.$cancel_for_hours." hours)`,  COALESCE( mAll.cntAll, 0 ) AS   `Total Cancelled Trips (Till now)`, COALESCE( dAll.cntAll, 0 ) AS  `Total Declined Trips (Till now)` ,rd.eIsBlocked as `Block driver`,rd.tBlockeddate as `Block date` FROM  register_driver rd LEFT JOIN (SELECT  iDriverId,COUNT( tr.iTripId ) AS cnt,iActive,tEndDate FROM trips tr where tEndDate BETWEEN  '".$s_date."' AND  '".$c_date."'  AND  iActive =  'Canceled' AND eCancelledBy	='Driver' GROUP BY tr.iDriverId ) m ON rd.iDriverId = m.iDriverId LEFT JOIN (SELECT  iDriverId,COUNT( trAll.iTripId ) AS cntAll,iActive FROM trips trAll where  iActive =  'Canceled' AND eCancelledBy	='Driver' GROUP BY trAll.iDriverId ) mAll ON rd.iDriverId = mAll.iDriverId LEFT JOIN (SELECT  iDriverId,COUNT( dr.iDriverRequestId ) AS cnt,dAddedDate,eStatus FROM driver_request dr where  dr.dAddedDate BETWEEN  '".$s_date."'  AND  '".$c_date."'	AND dr.eStatus =  'Decline' GROUP BY  dr.iDriverId ) d ON rd.iDriverId = d.iDriverId LEFT JOIN (SELECT  iDriverId,COUNT( drAll.iDriverRequestId ) AS cntAll,dAddedDate,eStatus FROM driver_request drAll where  drAll.eStatus =  'Decline' GROUP BY  drAll.iDriverId ) dAll ON rd.iDriverId = dAll.iDriverId  where (mAll.cntAll >'0' {$ssql} {$ssql1}) OR  (dAll.cntAll >'0' {$ssql} {$ssql1})  {$ord}";

    // ini_set("display_errors", 1);

    // error_reporting(E_ALL);

    $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            $result[0] = change_key($result[0], 'Total Drivers', 'Total Providers');
        }

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Name' === $key) {
                    $val = clearCmpName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ($key === 'Total Cancelled Trips (In '.$cancel_for_hours.' hours)') {
                    $val = $val;
                }

                if ($key === 'Total Declined Trips (In '.$cancel_for_hours.' hours)') {
                    $val = $val;
                }

                if ('Total Cancelled Trips (Till now)' === $key) {
                    $val = $val;
                }

                if ('Total Declined Trips (Till now)' === $key) {
                    $val = $val;
                }

                if ('Block driver' === $key) {
                    $val = $val;
                }

                if ('Block date' === $key) {
                    $val = DateTime($val, 'No');
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } elseif ('PDF' === $type) {
        // Added By HJ On 18-01-2019 For Solved Client Bug - 6720 Start

        $heading = ['Provider Name', 'Email', 'A', 'B', 'C', 'D', 'Block driver', 'Block Date'];

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO, 'L', 'A4');

        // echo "<pre>";

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = 'blocked_driver_'.$configPdf['pdfName'];

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Decline / Canceled Trip / Jobs Alert For Drivers');

        $pdf->Ln();

        $aTxt = 'A-Total Cancelled Trips (In '.$cancel_for_hours.' hours)';

        $bTxt = 'B-Total Declined Trips (In '.$cancel_for_hours.' hours)';

        $cTxt = 'C-Total Cancelled Trips (Till now)';

        $dTxt = 'D-Total Declined Trips (Till now)';

        $pdf->SetFont($language, 'b', 9);

        $pdf->Cell(100, 5, $aTxt);

        $pdf->Ln();

        $pdf->Cell(100, 5, $bTxt);

        $pdf->Ln();

        $pdf->Cell(100, 5, $cTxt);

        $pdf->Ln();

        $pdf->Cell(100, 5, $dTxt);

        $pdf->Ln();

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Provider Name' === $column_heading || 'Email' === $column_heading) {
                $pdf->Cell(60, 10, $column_heading, 1);
            } elseif ('Block Date' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('Block driver' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Name' === $column || 'Email' === $column) {
                    $pdf->Cell(60, 10, $values, 1);
                } elseif ('Block date' === $column) {
                    $pdf->Cell(40, 10, $values, 1);
                } elseif ('Block driver' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(20, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');

        // Added By HJ On 18-01-2019 For Solved Client Bug - 6720 End
    }
}

if ('blocked_rider' === $section) {
    $cancel_for_hours = $CANCEL_DECLINE_TRIPS_IN_HOURS;

    $c_date = date('Y-m-d H:i:s');

    $s_date = date('Y-m-d H:i:s', strtotime('-'.$cancel_for_hours.' hours'));

    $ord = ' ORDER BY `Total Cancelled Trips (Till now)` DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vName ASC';
        } else {
            $ord = ' ORDER BY vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vEmail ASC';
        } else {
            $ord = ' ORDER BY rd.vEmail DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Cancelled Trips (In '.$cancel_for_hours.' hours)` ASC';
        } else {
            $ord = ' ORDER BY `Total Cancelled Trips (In '.$cancel_for_hours.' hours)` DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY `Total Cancelled Trips (Till now)` ASC';
        } else {
            $ord = ' ORDER BY `Total Cancelled Trips (Till now)` DESC';
        }
    }if (7 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eIsBlocked ASC';
        } else {
            $ord = ' ORDER BY eIsBlocked DESC';
        }
    }if (8 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tBlockeddate ASC';
        } else {
            $ord = ' ORDER BY tBlockeddate DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('RiderName' === $option) {
                $option_new = "CONCAT(vName,' ',vLastName)";
            }

            if ('' !== $eIsBlocked) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND eIsBlocked = '".clean($eIsBlocked)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' !== $eIsBlocked) {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".clean($keyword_new)."%' OR vEmail LIKE '%".clean($keyword_new)."%') AND eIsBlocked = '".clean($eIsBlocked)."'";
            } else {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".clean($keyword_new)."%' OR vEmail LIKE '%".clean($keyword_new)."%')";
            }
        }
    } elseif ('' !== $eIsBlocked && '' === $keyword) {
        $ssql .= " AND rd.eIsBlocked = '".clean($eIsBlocked)."'";
    }

    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

    $cmp_ssql = '';

    if ('' !== $eStatus) {
        $estatusquery = '';
    } else {
        $estatusquery = " AND eStatus != 'Deleted'";
    }

    $ssql1 = "AND (vEmail != '' OR vPhone != '')";

    $sql = "SELECT  CONCAT(rd.vName,' ',rd.vLastName) AS Name,rd.vEmail as Email,  COALESCE( m.cnt , 0 ) AS `Total Cancelled Trips (In ".$cancel_for_hours." hours)` ,  COALESCE( mAll.cnt , 0 ) AS `Total Cancelled Trips (Till now)`,rd.eIsBlocked as `Block Rider`,rd.tBlockeddate as `Block Date` FROM  register_user rd LEFT JOIN (SELECT  iUserId,COUNT( tr.iTripId ) AS cnt,iActive,tEndDate   FROM trips tr where tEndDate BETWEEN  '".$s_date."' AND  '".$c_date."'  AND   tr.iActive =  'Canceled' AND tr.eCancelledBy ='Passenger' GROUP BY tr.iUserId ) m ON rd.iUserId = m.iUserId LEFT JOIN (SELECT  iUserId,COUNT( trAll.iTripId ) AS cnt,iActive FROM trips trAll where trAll.iActive =  'Canceled' AND trAll.eCancelledBy ='Passenger' GROUP BY trAll.iUserId ) mAll ON rd.iUserId = mAll.iUserId where (mAll.cnt >'0') {$ssql} {$ssql1} {$ord}";

    $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            $result[0] = change_key($result[0], 'Total Drivers', 'Total Providers');
        }

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Name' === $key) {
                    $val = clearCmpName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ($key === 'Total Cancelled Trips (In '.$cancel_for_hours.' hours)') {
                    $val = $val;
                }

                if ('Total Cancelled Trips (Till now)' === $key) {
                    $val = $val;
                }

                if ('Block Rider' === $key) {
                    $val = $val;
                }

                if ('Block Date' === $key) {
                    $val = DateTime($val, 'No');
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } elseif ('PDF' === $type) {
        // Added By HJ On 18-01-2019 For Solved Client Bug - 6720 Start

        $heading = ['User Name', 'Email', 'A', 'B', 'Block Driver', 'Block Date'];

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        // echo "<pre>";

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = 'blocked_rider_'.$configPdf['pdfName'];

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Cancelled Trip/Jobs Alert For '.$langage_lbl_admin['LBL_RIDERS_ADMIN']);

        $pdf->Ln();

        $aTxt = 'A-Total Cancelled Trips (In '.$cancel_for_hours.' hours)';

        $bTxt = 'B-Total Cancelled Trips (Till now)';

        $pdf->SetFont($language, 'b', 9);

        $pdf->Cell(100, 5, $aTxt);

        $pdf->Ln();

        $pdf->Cell(100, 5, $bTxt);

        $pdf->Ln();

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('User Name' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Email' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Block Date' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('Block Driver' === $column_heading) {
                $pdf->Cell(23, 10, $column_heading, 1);
            } else {
                $pdf->Cell(15, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Name' === $column) {
                    $pdf->Cell(50, 10, $values, 1);
                } elseif ('Email' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Block Date' === $column) {
                    $pdf->Cell(40, 10, $values, 1);
                } elseif ('Block Rider' === $column) {
                    $pdf->Cell(23, 10, $values, 1);
                } else {
                    $pdf->Cell(15, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');

        // Added By HJ On 18-01-2019 For Solved Client Bug - 6720 End
    }
}

if ('admin' === $section) {
    $query = Administrator::with(['roles', 'locations']);

    $sortby = $_REQUEST['sortby'] ?? 0;

    $order = isset($_REQUEST['order']) && 1 === $_REQUEST['order'] ? 'ASC' : 'DESC';

    switch ($sortby) {
        case 1:
            $query->orderBy('vFirstName', $order);

            break;

        case 2:
            $query->orderBy('vEmail', $order);

            break;

        case 3:
            break;

        case 4:
            $query->orderBy('eStatus', $order);

            break;

        default:
            break;
    }

    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';

    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

    $searchDate = $_REQUEST['searchDate'] ?? '';

    if (!empty($keyword)) {
        if (!empty($option)) {
            if ('eStatus' === $option) {
                $query->where('eStatus', $keyword);
            }
        } else {
            $query->where(static function ($q) use ($keyword): void {
                $q->where(DB::raw('concat(`vFirstName`," ",`vLastName`)'), 'LIKE', "%{$keyword}%");

                $q->orWhere('vEmail', 'LIKE', "%{$keyword}%");

                $q->orwhere('vContactNo', 'LIKE', "%{$keyword}%");

                $q->orwhere('eStatus', 'LIKE', "%{$keyword}%");
            });
        }
    }

    if (!$userObj->hasRole(1)) {
        $query->where('iGroupId', $userObj->role_id);
    }

    if ('eStatus' !== $option) {
        $query->where('eStatus', '!=', 'Deleted');
    }

    $start = 0;

    $data_drv = $query->get();

    // echo "<pre>";

    $result = [];

    foreach ($data_drv as $key => $row) {
        $data = [];

        $data['Name'] = clearName($row['vFirstName'].' '.$row['vLastName']);

        $data['Email'] = clearEmail($row['vEmail']);

        $data['Admin Roles'] = $row->roles->vGroup;

        $data['Status'] = $row['eStatus'];

        $result[] = $data;
    }

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Name' === $key) {
                    $val = clearName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Name', 'Email', 'Admin Roles', 'Status'];

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Admin '.$langage_lbl_admin['LBL_RIDERS_ADMIN']);

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Id' === $column_heading) {
                $pdf->Cell(10, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Id' === $column) {
                    $pdf->Cell(10, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('company' === $section) {
    $ord = ' ORDER BY c.iCompanyId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vEmail ASC';
        } else {
            $ord = ' ORDER BY c.vEmail DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.eStatus ASC';
        } else {
            $ord = ' ORDER BY c.eStatus DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('MobileNumber' === $option) {
                $option_new = "CONCAT(c.vCode,'',c.vPhone)";
            }

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%'))";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
    }

    $cmp_ssql = '';

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND c.eStatus != 'Deleted'";
    }

    $eSystem = " AND  c.eSystem ='General'";

    $sql = "SELECT c.vCompany AS Name, c.vEmail AS Email,(SELECT count(rd.iDriverId) FROM register_driver AS rd WHERE rd.iCompanyId=c.iCompanyId) AS `Total Drivers`, CONCAT(c.vCode,' ',c.vPhone) AS Mobile,c.eStatus AS Status FROM company AS c WHERE 1 = 1 {$eSystem} {$eStatus_sql} {$ssql} {$cmp_ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            $result[0] = change_key($result[0], 'Total Drivers', 'Total Providers');
        }

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                if ('Name' === $key) {
                    $val = clearCmpName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
            $heading = ['Name', 'Email', 'Total Providers', 'Mobile', 'Status'];
        } else {
            $heading = ['Name', 'Email', 'Total Drivers', 'Mobile', 'Status'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Companies');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Total Drivers' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Total Providers' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Mobile' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(55, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Name' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Total Drivers' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Total Providers' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Mobile' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(55, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('store' === $section) {
    $ord = ' ORDER BY c.iCompanyId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vEmail ASC';
        } else {
            $ord = ' ORDER BY c.vEmail DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.eStatus ASC';
        } else {
            $ord = ' ORDER BY c.eStatus DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('MobileNumber' === $option) {
                $option_new = "CONCAT(c.vCode,'',c.vPhone)";
            }

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND c.eStatus = '".clean($eStatus)."'";
            }if ('' !== $select_cat) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND sc.iServiceId = '".clean($select_cat)."' ";
            }if ('' !== $select_cat && '' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND c.eStatus = '".clean($eStatus)."' AND sc.iServiceId = '".clean($select_cat)."' ";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' === $eStatus && '' !== $select_cat && '' !== $keyword_new) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND sc.iServiceId = '".clean($select_cat)."'";
            } elseif ('' !== $eStatus && '' !== $select_cat) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."' AND sc.iServiceId = '".clean($select_cat)."'";
            } elseif ('' !== $eStatus) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."'";
            } elseif ('' !== $select_cat) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."' AND sc.iServiceId = '".clean($select_cat)."'";
            } else {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%'))";
            }
        }
    } elseif ('' !== $eStatus && '' !== $select_cat && '' === $keyword) {
        $ssql .= " AND c.eStatus = '".clean($eStatus)."' AND sc.iServiceId = '".clean($select_cat)."'";
    } elseif ('' !== $eStatus && '' === $keyword && '' === $select_cat) {
        $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
    } elseif ('' === $eStatus && '' === $keyword && '' !== $select_cat) {
        $ssql .= " AND sc.iServiceId = '".clean($select_cat)."'";
    }

    $cmp_ssql = '';

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND c.eStatus != 'Deleted'";
    }

    $eSystem = " AND  c.eSystem ='DeliverAll'";

    $ssql .= ' AND sc.iServiceId IN('.$enablesevicescategory.')';

    if (!$MODULES_OBJ->isSingleStoreSelection()) {
        $sql = "SELECT c.vCompany AS Name, c.vEmail AS Email,(SELECT count(iFoodMenuId) FROM food_menu WHERE iCompanyId = c.iCompanyId AND eStatus != 'Deleted') as `Item Categories`, CONCAT(c.vCode,' ',c.vPhone) AS Mobile, c.tRegistrationDate `Registration Date`,c.eStatus AS Status FROM company AS c left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE 1 = 1 and sc.eStatus='Active' {$eSystem} {$eStatus_sql} {$ssql} {$cmp_ssql} {$ord}";
    } else {
        $sql = "SELECT c.vCompany AS Name, c.vEmail AS Email,(SELECT count(iFoodMenuId) FROM food_menu WHERE iCompanyId = c.iCompanyId AND eStatus != 'Deleted') as `Item Categories`, CONCAT(c.vCode,' ',c.vPhone) AS Mobile, c.tRegistrationDate `Registration Date`,c.eStatus AS Status FROM company AS c left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE 1 = 1 and sc.eStatus='Active' {$eSystem} {$eStatus_sql} {$ssql} {$cmp_ssql} GROUP BY sc.iServiceId  {$ord}";
    }

    // echo $sql;die;

    // added by SP on 28-06-2019

    // $catdata = serviceCategories;

    // $service_cat_data = json_decode($catdata, true);

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

        // echo "<pre>";print_r($result);die;

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            $result[0] = change_key($result[0], 'Total Drivers', 'Total Providers');
        }

        // $result[0] = change_key($result[0], 'iServiceId', 'Service Categories');

        echo implode("\t", array_keys($result[0]))."\r\n";

        // $result[0] = change_key($result[0], 'Service Categories', 'iServiceId');

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                if ('Name' === $key) {
                    $val = clearCmpName($val);
                }

                if ('Registration Date' === $key) {
                    $val = DateTime($val);
                }

                // added by SP on 28-06-2019

                /*if ($key == 'iServiceId') {

                    if (count($service_cat_data) > 1) {

                        foreach ($service_cat_data as $servicedata) {

                            if ($servicedata['iServiceId'] == $val) {

                                $val = (isset($servicedata['vServiceName']) ? $servicedata['vServiceName'] : '');

                            }

                        }

                    }

                }*/

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Name', 'Email', 'Item Categories', 'Mobile', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Store');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Item Categories' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Mobile' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(55, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Name' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Item Categories' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Mobile' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(55, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('organization' === $section) {
    $ord = ' ORDER BY c.iOrganizationId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vEmail ASC';
        } else {
            $ord = ' ORDER BY c.vEmail DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.eStatus ASC';
        } else {
            $ord = ' ORDER BY c.eStatus DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('MobileNumber' === $option) {
                $option_new = "CONCAT(c.vCode,'',c.vPhone)";
            }

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%'))";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
    }

    $cmp_ssql = '';

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND c.eStatus != 'Deleted'";
    }

    $sql = "SELECT c.vCompany AS 'Organization Name', c.iUserProfileMasterId AS 'Organization Type',c.ePaymentBy AS 'Payment Method', c.vEmail AS Email, CONCAT(c.vCode,' ',c.vPhone) AS Mobile, c.eStatus AS Status FROM organization AS c WHERE 1 = 1 {$eStatus_sql} {$ssql} {$cmp_ssql} {$ord}";

    $orgTypeArr = [];

    $orgType_sql = 'SELECT vProfileName,iUserProfileMasterId FROM user_profile_master ORDER BY iUserProfileMasterId ASC';

    $orgProfileData = $obj->MySQLSelect($orgType_sql);

    $default_lang = $_SESSION['sess_lang'];

    for ($p = 0; $p < count($orgProfileData); ++$p) {
        $profileName = (array) json_decode($orgProfileData[$p]['vProfileName']);

        $orgTypeArr[$orgProfileData[$p]['iUserProfileMasterId']] = $profileName['vProfileName_'.$default_lang];
    }

    // echo "<pre>";

    // print_r($orgTypeArr);die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'_organization.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Organization Type' === $key) {
                    $orgType = '';

                    if (isset($orgTypeArr[$val])) {
                        $orgType = $orgTypeArr[$val];
                    }

                    $val = $orgType;
                }

                if ('Payment Method' === $key) {
                    $payByName = $val;

                    if ('' === $val || 'Passenger' === $val) {
                        $payByName = $langage_lbl_admin['LBL_RIDER'];
                    }

                    $val = 'Pay By '.$payByName;
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                if ('Organization Name' === $key) {
                    $val = clearCmpName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Name', 'Email', 'Mobile', 'Status', 'Type', 'Payment'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO, 'L', 'A4');

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Organizations');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Mobile' === $column_heading || 'Type' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Payment' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Type' === $column) {
                    $orgType = '';

                    if (isset($orgTypeArr[$key])) {
                        $orgType = $orgTypeArr[$key];
                    }

                    $values = $orgType;
                }

                if ('Payment' === $column) {
                    $payByName = $key;

                    if ('' === $payByName) {
                        $payByName = $langage_lbl_admin['LBL_RIDER'];
                    }

                    $values = 'Pay By '.$payByName;
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Name' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Mobile' === $column || 'Type' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Payment' === $column) {
                    $pdf->Cell(45, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('rider' === $section) {
    $ord = ' ORDER BY iUserId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vName ASC';
        } else {
            $ord = ' ORDER BY vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vEmail ASC';
        } else {
            $ord = ' ORDER BY vEmail DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tRegistrationDate ASC';
        } else {
            $ord = ' ORDER BY tRegistrationDate DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    $rdr_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $rdr_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
    }

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('RiderName' === $option) {
                $option_new = "CONCAT(vName,' ',vLastName)";
            }

            if ('MobileNumber' === $option) {
                $option_new = "CONCAT(vPhoneCode,'',vPhone)";
            }

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".clean($keyword_new)."%' OR vEmail LIKE '%".clean($keyword_new)."%' OR (CONCAT(vPhoneCode,'',vPhone) LIKE '%".clean($keyword_new)."%')) AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".clean($keyword_new)."%' OR vEmail LIKE '%".clean($keyword_new)."%' OR (CONCAT(vPhoneCode,'',vPhone) LIKE '%".clean($keyword_new)."%'))";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    }

    $ssql1 = "AND (vEmail != '' OR vPhone != '') AND eHail='No'";

    $sql = "SELECT CONCAT(vName,' ',vLastName) as `User Name`,vEmail as Email,tRegistrationDate as `Signup Date`,CONCAT(vPhoneCode,' ',vPhone) AS Mobile,iUserId AS `Wallet Balance`,eStatus as Status FROM register_user WHERE 1=1 {$eStatus_sql} {$ssql} {$ssql1} {$rdr_ssql} {$ord}";

    $wallet_data = $obj->MySQLSelect("SELECT iUserId, SUM(COALESCE(CASE WHEN eType = 'Credit' THEN iBalance END,0)) - SUM(COALESCE(CASE WHEN eType = 'Debit' THEN iBalance END,0)) as balance FROM user_wallet WHERE eUserType = 'Rider' GROUP BY iUserId");

    $walletDataArr = [];
    foreach ($wallet_data as $wallet_balance) {
        $walletDataArr[$wallet_balance['iUserId']] = $wallet_balance['balance'];
    }
    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        // echo "<pre>";

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($value['Wallet Balance'], "Rider");
            $user_available_balance = 0;
            if (isset($walletDataArr[$value['Wallet Balance']])) {
                $user_available_balance = $walletDataArr[$value['Wallet Balance']];
            }

            $value['Wallet Balance'] = formateNumAsPerCurrency($user_available_balance, '');

            foreach ($value as $key => $val) {
                //                if ($key == "Signup Date") {

                //                    $val = DateTime($val);

                //                }

                if ('User Name' === $key) {
                    $val = clearName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Signup Date' === $key) {
                    $val = DateTime($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['User Name', 'Email', 'Signup Date', 'Mobile', 'Wallet Balance', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($row['Wallet Balance'], "Rider");
            $user_available_balance = 0;
            if (isset($walletDataArr[$row['Wallet Balance']])) {
                $user_available_balance = $walletDataArr[$row['Wallet Balance']];
            }

            $row['Wallet Balance'] = formateNumAsPerCurrency($user_available_balance, '');

            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO, 'L', 'A4');

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Riders');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Email' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Mobile' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Signup Date' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Wallet Balance' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } else {
                $pdf->Cell(50, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('User Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Signup Date' === $column) {
                    $values = DateTime($key);
                }

                if ('Email' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Mobile' === $column) {
                    $pdf->Cell(45, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Signup Date' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Wallet Balance' === $column) {
                    $pdf->Cell(40, 10, $values, 1);
                } else {
                    $pdf->Cell(50, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// make

if ('make' === $section) {
    $ord = ' ORDER BY vMake ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vMake ASC';
        } else {
            $ord = ' ORDER BY vMake DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vMake LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND eStatus = '".$keyword."'";
    } else {
        $eStatussql = " AND eStatus != 'Deleted'";
    }

    $sql = "SELECT vMake as Make, eStatus as Status FROM make where 1=1 {$eStatussql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Make', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Make');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// make

// //////// Package Start //////////////

if ('package_type' === $section) {
    $ord = ' ORDER BY vName ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vName ASC';
        } else {
            $ord = ' ORDER BY vName DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vName LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND eStatus = '".$keyword."'";
    } else {
        $eStatussql = " AND eStatus != 'Deleted'";
    }

    $sql = "SELECT vName as Name, eStatus as Status FROM package_type where 1=1 {$eStatussql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Name', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Package Type');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// //////// Package End //////////////

// model

if ('model' === $section) {
    $ord = ' ORDER BY mo.vTitle ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mo.vTitle ASC';
        } else {
            $ord = ' ORDER BY mo.vTitle DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mk.vMake ASC';
        } else {
            $ord = ' ORDER BY mk.vMake DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mo.eStatus ASC';
        } else {
            $ord = ' ORDER BY mo.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (mo.vTitle LIKE '%".$keyword."%' OR mo.eStatus LIKE '%".$keyword."%' OR mk.vMake LIKE '%".$keyword."%')";
        }
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND mo.eStatus = '".ucfirst($keyword)."'";
    } else {
        $eStatussql = " AND mo.eStatus != 'Deleted'";
    }

    $sql = "SELECT mo.vTitle AS Title, mk.vMake AS Make, mo.eStatus AS Status FROM model  AS mo LEFT JOIN make AS mk ON mk.iMakeId = mo.iMakeId WHERE 1=1 {$eStatussql} {$ssql} {$ord}";

    // die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Make', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Model');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Id' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(60, 10, $column_heading, 1);
            } else {
                $pdf->Cell(70, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Id' === $column) {
                    $pdf->Cell(45, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(60, 10, $key, 1);
                } else {
                    $pdf->Cell(70, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// model

// country

if ('country' === $section) {
    $ord = ' ORDER BY vCountry ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vCountry ASC';
        } else {
            $ord = ' ORDER BY vCountry DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vPhoneCode ASC';
        } else {
            $ord = ' ORDER BY vPhoneCode DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eUnit ASC';
        } else {
            $ord = ' ORDER BY eUnit DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (vCountry LIKE '%".stripslashes($keyword)."%' OR vPhoneCode LIKE '%".stripslashes($keyword)."%' OR vCountryCodeISO_3 LIKE '%".stripslashes($keyword)."%') AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (vCountry LIKE '%".stripslashes($keyword)."%' OR vPhoneCode LIKE '%".stripslashes($keyword)."%' OR vCountryCodeISO_3 LIKE '%".stripslashes($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    }

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND eStatus != 'Deleted'";
    }

    $sql = "SELECT vCountry as Country,vPhoneCode as PhoneCode, eUnit as Unit, eStatus as Status FROM country where 1 = 1 {$eStatus_sql} {$ssql}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Country', 'PhoneCode', 'Unit', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Country');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// State

if ('state' === $section) {
    $ord = ' ORDER BY s.vState ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCountry ASC';
        } else {
            $ord = ' ORDER BY c.vCountry DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY s.vState ASC';
        } else {
            $ord = ' ORDER BY s.vState DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY s.vStateCode ASC';
        } else {
            $ord = ' ORDER BY s.vStateCode DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY s.eStatus ASC';
        } else {
            $ord = ' ORDER BY s.eStatus DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 's.eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR s.vState LIKE '%".$keyword."%' OR s.vStateCode LIKE '%".$keyword."%' OR s.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT s.vState AS State,s.vStateCode AS `State Code`,c.vCountry AS Country,s.eStatus as Status FROM state AS s INNER JOIN country AS c ON c.iCountryId = s.iCountryId WHERE s.eStatus !=  'Deleted' {$ssql} {$ord}";

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['State', 'State Code', 'Country', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'State');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(40, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// State

if ('city' === $section) {
    $ord = ' ORDER BY vCity ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY st.vState ASC';
        } else {
            $ord = ' ORDER BY st.vState DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ct.vCity ASC';
        } else {
            $ord = ' ORDER BY ct.vCity DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCountry ASC';
        } else {
            $ord = ' ORDER BY c.vCountry DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ct.eStatus ASC';
        } else {
            $ord = ' ORDER BY ct.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (ct.vCity LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR c.vCountry LIKE '%".$keyword."%' OR ct.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT ct.vCity AS City,st.vState AS State,c.vCountry AS Country, ct.eStatus AS Status FROM city AS ct INNER JOIN country AS c ON c.iCountryId =ct.iCountryId INNER JOIN state AS st ON st.iStateId=ct.iStateId WHERE  ct.eStatus != 'Deleted' {$ssql} {$ord}";

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['City', 'State', 'Country', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'City');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(35, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(35, 10, $key, 1);
                } else {
                    $pdf->Cell(35, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// city

// faq

if ('faq' === $section) {
    $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY f.vTitle_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY fc.vTitle ASC';
        } else {
            $ord = ' ORDER BY fc.vTitle DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY f.iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.eStatus ASC';
        } else {
            $ord = ' ORDER BY f.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= ' AND (f.vTitle_'.$default_lang." LIKE '%".$keyword."%' OR fc.vTitle LIKE '%".$keyword."%' OR f.iDisplayOrder LIKE '%".$keyword."%' OR f.eStatus LIKE '%".$keyword."%')";
        }
    }

    $tbl_name = 'faqs';

    $sql = 'SELECT f.vTitle_'.$default_lang.' as `Title`, fc.vTitle as `Category` ,f.iDisplayOrder as `DisplayOrder` ,f.eStatus  as `Status` FROM '.$tbl_name." f, faq_categories fc WHERE f.iFaqcategoryId = fc.iUniqueId AND fc.vCode = '".$default_lang."' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Category', 'Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'FAQ');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Title' === $column_heading) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } elseif ('Category' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Order' === $column_heading) {
                $pdf->Cell(28, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else {
                $pdf->Cell(28, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Title' === $column) {
                    $pdf->Cell(80, 10, $key, 1);
                } elseif ('Category' === $column) {
                    $pdf->Cell(45, 10, $key, 1);
                } elseif ('Order' === $column) {
                    $pdf->Cell(28, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(28, 10, $key, 1);
                } else {
                    $pdf->Cell(28, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// faq

// help Detail

if ('help_detail' === $section) {
    $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY f.vTitle_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY fc.vTitle ASC';
        } else {
            $ord = ' ORDER BY fc.vTitle DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY f.iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.eStatus ASC';
        } else {
            $ord = ' ORDER BY f.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= ' AND (f.vTitle_'.$default_lang." LIKE '%".$keyword."%' OR fc.vTitle LIKE '%".$keyword."%' OR f.iDisplayOrder LIKE '%".$keyword."%' OR f.eStatus LIKE '%".$keyword."%')";
        }
    }

    $tbl_name = 'help_detail';

    $sql = 'SELECT f.vTitle_'.$default_lang.' as `Title`, fc.vTitle as `Category` ,f.iDisplayOrder as `DisplayOrder` ,f.eStatus  as `Status` FROM '.$tbl_name." f, help_detail_categories fc WHERE f.iHelpDetailCategoryId = fc.iUniqueId AND fc.vCode = '".$default_lang."' {$ssql} {$ord}";

    // die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Category', 'Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        // print_r($result);die;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Help Detail');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Title' === $column_heading) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } elseif ('Category' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Order' === $column_heading) {
                $pdf->Cell(28, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else {
                $pdf->Cell(28, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Title' === $column) {
                    $pdf->Cell(80, 10, $key, 1);
                } elseif ('Category' === $column) {
                    $pdf->Cell(45, 10, $key, 1);
                } elseif ('Order' === $column) {
                    $pdf->Cell(28, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(28, 10, $key, 1);
                } else {
                    $pdf->Cell(28, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// help detail end

// faq category

if ('faq_category' === $section) {
    $ord = ' ORDER BY vTitle ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vImage ASC';
        } else {
            $ord = ' ORDER BY vImage DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vTitle ASC';
        } else {
            $ord = ' ORDER BY vTitle DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vTitle LIKE '%".$keyword."%' OR iDisplayOrder LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT vTitle as `Title`, iDisplayOrder as `Order`, eStatus as `Status` FROM faq_categories where vCode = '".$default_lang."' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'FAQ Category');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// faq category

// Help Detail category

if ('help_detail_category' === $section) {
    $ord = ' ORDER BY vTitle ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vImage ASC';
        } else {
            $ord = ' ORDER BY vImage DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vTitle ASC';
        } else {
            $ord = ' ORDER BY vTitle DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vTitle LIKE '%".$keyword."%' OR iDisplayOrder LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT vTitle as `Title`, iDisplayOrder as `Order`, eStatus as `Status` FROM help_detail_categories where vCode = '".$default_lang."' {$ssql} {$ord}";

    // die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Help Detail Category');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(60, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(60, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// Help Detail category

// pages

if ('page' === $section) {
    $ord = ' ORDER BY vPageName ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vPageName ASC';
        } else {
            $ord = ' ORDER BY vPageName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vPageTitle_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vPageTitle_'.$default_lang.' DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vPageName LIKE '%".$keyword."%' OR vPageTitle_".$default_lang." LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = 'SELECT vPageName as `Name`, vPageTitle_'.$default_lang." as `PageTitle` FROM pages where ipageId NOT IN('5','20','21','20') AND eStatus != 'Deleted' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Name', 'PageTitle'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Pages');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Name' === $column_heading) {
                $pdf->Cell(57, 10, $column_heading, 1);
            } elseif ('PageTitle' === $column_heading) {
                $pdf->Cell(100, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Name' === $column) {
                    $pdf->Cell(57, 10, $key, 1);
                } elseif ('PageTitle' === $column) {
                    $pdf->Cell(100, 10, $key, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// pages

// languages

if ('languages' === $section) {
    $checktext = isset($_REQUEST['checktext']) ? stripslashes($_REQUEST['checktext']) : '';

    $selectedlanguage = isset($_REQUEST['selectedlanguage']) ? stripslashes($_REQUEST['selectedlanguage']) : '';

    if (!empty($selectedlanguage)) {
        $tbl_name = 'language_label_'.$selectedlanguage;
    } else {
        $tbl_name = 'language_label';
    }

    $ord = ' ORDER BY vValue ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vLabel ASC';
        } else {
            $ord = ' ORDER BY vLabel DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vValue ASC';
        } else {
            $ord = ' ORDER BY vValue DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.addslashes($option)." LIKE '".addslashes($keyword)."'";
            } else {
                if ('Yes' === $checktext && 'vValue' === $option) {
                    $ssql .= ' AND '.addslashes($option)." LIKE '".addslashes($keyword)."'";
                } else {
                    $ssql .= ' AND '.addslashes($option)." LIKE '%".addslashes($keyword)."%'";
                }
            }
        } else {
            $ssql .= " AND (vLabel  LIKE '%".addslashes($keyword)."%' OR vValue  LIKE '%".addslashes($keyword)."%') ";
        }
    }

    $sql = 'SELECT vLabel as `Code`,vValue as `Value in English Language`  FROM '.$tbl_name." WHERE vCode = '".$default_lang."' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Code', 'Value in English Language'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO, 'L', 'A4');

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Languages');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Code' === $column_heading) {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(185, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Code' === $column) {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(185, 10, $key, 1);

                    /*$html = 'sadasdasd<br>dsdfsdfsdf<br> dfsdsdf dfdsfsdf fsad <br>sdfsdfdsf';

                    $pdf->writeHTML($html, true, 0, false, false);

                    /*$parts = str_split($key, 120);

                    $final = implode("<br>", $parts);

                    $strText = str_replace("\n", "<br>", $final);

                    $pdf->MultiCell(185, 10, $strText, 1, 'J', 0, 1, '', '', true, null, true);*/
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// language label other

if ('language_label_other' === $section) {
    $checktext = isset($_REQUEST['checktext']) ? stripslashes($_REQUEST['checktext']) : '';

    $ord = ' ORDER BY vValue ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vLabel ASC';
        } else {
            $ord = ' ORDER BY vLabel DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vValue ASC';
        } else {
            $ord = ' ORDER BY vValue DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                if ('Yes' === $checktext && 'vValue' === $option) {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
                } else {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
                }
            }
        } else {
            $ssql .= " AND (vLabel LIKE '%".$keyword."%' OR vValue LIKE '%".$keyword."%')";
        }
    }

    $tbl_name = 'language_label_other';

    $sql = 'SELECT vLabel as `Code`,vValue as `Value in English Language`  FROM '.$tbl_name." WHERE vCode = '".$default_lang."' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Code', 'Value in English Language'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Admin Language Label');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(88, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(88, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// language label other

// vehicle_type

if ('vehicle_type' === $section) {
    $iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'] ?? '';

    $eType = isset($_REQUEST['eType']) ? ($_REQUEST['eType']) : '';

    $eStatus = $_REQUEST['eStatus'] ?? '';

    $iLocationid = isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '';

    $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.fPricePerKM ASC';
        } else {
            $ord = ' ORDER BY vt.fPricePerKM DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.fPricePerMin ASC';
        } else {
            $ord = ' ORDER BY vt.fPricePerMin DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.iPersonSize ASC';
        } else {
            $ord = ' ORDER BY vt.iPersonSize DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.eStatus ASC';
        } else {
            $ord = ' ORDER BY vt.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                if ('' !== $iVehicleCategoryId) {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'  AND vt.eStatus = '".$eStatus."'";
                } else {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.eStatus = '".$eStatus."'";
                }
            } else {
                if ('' !== $iVehicleCategoryId) {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
                } else {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
                }
            }
        } else {
            if ('' !== $eStatus) {
                if ('' !== $iVehicleCategoryId) {
                    $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize  LIKE '%".$keyword."%') AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."' AND vt.eStatus = '".$eStatus."'";
                } else {
                    $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize   LIKE '%".$keyword."%') AND vt.eStatus = '".$eStatus."'";
                }
            } else {
                if ('' !== $iVehicleCategoryId) {
                    $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize  LIKE '%".$keyword."%') AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
                } else {
                    $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize   LIKE '%".$keyword."%')";
                }
            }
        }
    } elseif ('' !== $iVehicleCategoryId && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."' AND vt.eStatus = '".clean($eStatus)."'";
    } elseif ('' !== $iVehicleCategoryId && '' === $keyword && '' === $eStatus) {
        $ssql .= " AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
    } elseif ('' !== $eType && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND vt.eType = '".$eType."' AND vt.eStatus = '".clean($eStatus)."'";
    } elseif ('' !== $eType && '' === $keyword && '' === $eStatus) {
        $ssql .= " AND vt.eType = '".$eType."'";
    } elseif ('' !== $iLocationid && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND vt.iLocationid = '".$iLocationid."' AND vt.eStatus = '".clean($eStatus)."'";
    } elseif ('' !== $iLocationid && '' === $keyword && '' === $eStatus) {
        $ssql .= " AND vt.iLocationid = '".$iLocationid."'";
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND vt.eStatus = '".clean($eStatus)."'";
    }

    if ('' !== $eStatus) {
        $eStatussql = '';
    } else {
        $eStatussql = " AND vt.eStatus != 'Deleted'";
    }

    if ('Delivery' === $APP_TYPE) {
        $Vehicle_type_name = 'Deliver';
    } elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
        $Vehicle_type_name = 'Ride-Delivery';
    } else {
        $Vehicle_type_name = $APP_TYPE;
    }

    if ('Ride-Delivery' === $Vehicle_type_name) {
        if (empty($eType)) {
            $ssql .= "AND (vt.eType ='Ride' or vt.eType ='Deliver')";
        }

        $sql = 'SELECT vt.vVehicleType_'.$default_lang.' as Type,vt.fPricePerKM as PricePer'.$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize,vt.eType as `Service Type`, vt.eStatus as Status, lm.vLocationName as location, vt.iLocationid as locationId from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where 1=1  {$eStatussql} {$ssql} {$ord}";
    } else {
        if ('UberX' === $APP_TYPE) {
            $sql = 'SELECT vt.vVehicleType_'.$default_lang.' as Type,vc.vCategory_'.$default_lang.' as Subcategory, vt.eStatus as Status, lm.vLocationName as location,vt.iLocationid as locationId from vehicle_type as vt  left join '.$sql_vehicle_category_table_name." as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' {$eStatussql} {$ssql} {$ord}";
        } elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
            $sql = 'SELECT vt.vVehicleType_'.$default_lang.' as Type,vt.fPricePerKM as PricePer'.$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize, vt.eStatus as Status ,lm.vLocationName as location,vt.iLocationid as locationId from vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid  where 1=1 {$eStatussql} {$ssql} {$ord}";
        } else {
            $sql = 'SELECT vt.vVehicleType_'.$default_lang.' as Type,vt.fPricePerKM as PricePer'.$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize, vt.eStatus as Status, lm.vLocationName as location,vt.iLocationid as locationId  from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."'  {$eStatussql} {$ssql} {$ord}";
        }
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        $data = array_keys($result[0]);

        $arr = array_diff($data, ['locationId']);

        echo implode("\t", $arr)."\r\n";

        $i = 0;

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('locationId' === $key) {
                    $val = '';
                }

                if ('location' === $key && '-1' === $value['locationId']) {
                    $val = 'All Location';
                }

                echo $val."\t";
            }

            echo "\r\n";

            ++$i;
        }
    } else {
        if ('UberX' === $APP_TYPE) {
            $heading = ['Type', 'Subcategory', 'Location Name'];
        } else {
            if ('Ride-Delivery' === $Vehicle_type_name) {
                $heading = ['Type', 'PricePer'.$DEFAULT_DISTANCE_UNIT, 'PricePerMin', 'BaseFare', 'Commision', 'PersonSize', $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'], 'Status', 'Location Name'];
            } else {
                $heading = ['Type', 'PricePer'.$DEFAULT_DISTANCE_UNIT, 'PricePerMin', 'BaseFare', 'Commision', 'PersonSize', 'Status', 'Location Name'];
            }
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']);

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Type' === $column_heading && 'UberX' === $APP_TYPE) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } elseif ('Type' === $column_heading && 'UberX' !== $APP_TYPE) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ($column_heading === $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']) {
                $pdf->Cell(22, 10, $column_heading, 1);
            } elseif ('PricePerKM' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('BaseFare' === $column_heading) {
                $pdf->Cell(18, 10, $column_heading, 1);
            } elseif ('Commision' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('PersonSize' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(15, 10, $column_heading, 1);
            } elseif ('Location Name' === $column_heading) {
                $pdf->Cell(26, 10, $column_heading, 1);
            } elseif ('Subcategory' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } else {
                $pdf->Cell(26, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Type' === $column && 'UberX' === $APP_TYPE) {
                    $pdf->Cell(80, 10, $key, 1);
                } elseif ('Type' === $column && 'UberX' !== $APP_TYPE) {
                    $pdf->Cell(30, 10, $key, 1);
                } elseif ('Service Type' === $column) {
                    $pdf->Cell(22, 10, $key, 1);
                } elseif ('PricePerKM' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('BaseFare' === $column) {
                    $pdf->Cell(18, 10, $key, 1);
                } elseif ('Commision' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('PersonSize' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(15, 10, $key, 1);
                } elseif ('location' === $column && '-1' === $row['locationId']) {
                    $pdf->Cell(26, 10, 'All Location', 1);
                } elseif ('locationId' === $column) {
                    $pdf->Cell(2, 10, '', 0);
                } elseif ('Subcategory' === $column) {
                    $pdf->Cell(50, 10, $key, 1);
                } else {
                    $pdf->Cell(26, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// service_type

if ('service_type' === $section) {
    $iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'] ?? '';

    $eType = isset($_REQUEST['eType']) ? ($_REQUEST['eType']) : '';

    $eStatus = isset($_REQUEST['eStatus']) ? ($_REQUEST['eStatus']) : '';

    $ord = ' ORDER BY vt.iVehicleCategoryId ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.eStatus ASC';
        } else {
            $ord = ' ORDER BY vt.eStatus DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY vt.iDisplayOrder DESC';
        }
    }

    if ($parent_ufx_catid > 0) {
        $getSubCat = $obj->MySQLSelect("SELECT GROUP_CONCAT(DISTINCT CONCAT('''',iVehicleCategoryId, '''')) SUB_CAT FROM ".$sql_vehicle_category_table_name." WHERE iParentId='".$parent_ufx_catid."'");

        if (count($getSubCat) > 0) {
            $ssql .= ' AND vt.iVehicleCategoryId IN ('.$getSubCat[0]['SUB_CAT'].')';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."' AND vt.eStatus = '".$eStatus."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize  LIKE '%".$keyword."%') AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."' AND vt.eStatus = '".$eStatus."'";
            } else {
                $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize  LIKE '%".$keyword."%') AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
            }
        }
    } elseif ('' !== $iVehicleCategoryId && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."' AND vt.eStatus='".$eStatus."'";
    } elseif ('' !== $iVehicleCategoryId && '' === $keyword && '' === $eStatus) {
        $ssql .= " AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
    } elseif ('' === $iVehicleCategoryId && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND vt.eStatus='".$eStatus."'";
    }

    // $Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ;

    if ('Delivery' === $APP_TYPE) {
        $Vehicle_type_name = 'Deliver';
    } elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
        $Vehicle_type_name = 'UberX';
    } else {
        $Vehicle_type_name = $APP_TYPE;
    }

    if ('' !== $eStatus) {
        $eStatussql = '';
    } else {
        $eStatussql = " AND vt.eStatus != 'Deleted'";
    }

    $sql = 'SELECT vt.vVehicleType_'.$default_lang.' as Type,vc.vCategory_'.$default_lang.' as Subcategory,vt.iDisplayOrder as `Display Order`,lm.vLocationName as location,vt.iLocationid as locationId from vehicle_type as vt  left join '.$sql_vehicle_category_table_name." as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' {$ssql} {$eStatussql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        $data = array_keys($result[0]);

        $arr = array_diff($data, ['locationId']);

        echo implode("\t", $arr)."\r\n";

        $i = 0;

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('locationId' === $key) {
                    $val = '';
                }

                if ('location' === $key && '-1' === $value['locationId']) {
                    $val = 'All Location';
                }

                echo $val."\t";
            }

            echo "\r\n";

            ++$i;
        }
    } else {
        if ('UberX' === $Vehicle_type_name) {
            $heading = ['Type', 'Subcategory', 'Display Order', 'Location Name'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Service Type');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Type' === $column_heading && 'UberX' === $Vehicle_type_name) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } elseif ('Location Name' === $column_heading) {
                $pdf->Cell(26, 10, $column_heading, 1);
            } elseif ('Subcategory' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(26, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Type' === $column && 'UberX' === $Vehicle_type_name) {
                    $pdf->Cell(80, 10, $key, 1);
                } elseif ('location' === $column && '-1' === $row['locationId']) {
                    $pdf->Cell(26, 10, 'All Location', 1);
                } elseif ('locationId' === $column) {
                    $pdf->Cell(2, 10, '', 0);
                } elseif ('Subcategory' === $column) {
                    $pdf->Cell(50, 10, $key, 1);
                } elseif ('Display Order' === $column) {
                    $pdf->Cell(25, 10, $key, 1);
                } else {
                    $pdf->Cell(26, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// service_type

// coupon

if ('coupon' === $section) {
    $sql = "select vSymbol from  currency where eDefault='Yes'";
    $db_currency = $obj->MySQLSelect($sql);

    $ord = ' ORDER BY iCouponId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vCouponCode ASC';
        } else {
            $ord = ' ORDER BY vCouponCode DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dActiveDate ASC';
        } else {
            $ord = ' ORDER BY dActiveDate DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dExpiryDate ASC';
        } else {
            $ord = ' ORDER BY dExpiryDate DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eValidityType ASC';
        } else {
            $ord = ' ORDER BY eValidityType DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iUsageLimit ASC';
        } else {
            $ord = ' ORDER BY iUsageLimit DESC';
        }
    }

    if (7 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iUsed ASC';
        } else {
            $ord = ' ORDER BY iUsed DESC';
        }
    }

    if (9 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vPromocodeType ASC';
        } else {
            $ord = ' ORDER BY vPromocodeType DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (vCouponCode LIKE '%".$keyword."%'  OR eValidityType LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    // added by SP for date changes and estatus on 28-06-2019

    if ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    } elseif ('' !== $eStatus) {
        $ssql .= " AND eStatus = '".$eStatus."'";
    } else {
        $ssql .= " AND eStatus != 'Deleted'";
    }

    $ufxEnable = $MODULES_OBJ->isUberXFeatureAvailable() ? 'Yes' : 'No'; // add function to modules availibility
    $rideEnable = $MODULES_OBJ->isRideFeatureAvailable() ? 'Yes' : 'No';
    $deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable() ? 'Yes' : 'No';
    $deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? 'Yes' : 'No';

    if ('Yes' !== $ufxEnable) {
        $ssql .= " AND eSystemType != 'UberX'";
    }
    if (!$MODULES_OBJ->isAirFlightModuleAvailable()) {
        $ssql .= " AND eFly = '0'";
    }
    if ('Yes' !== $rideEnable) {
        $ssql .= " AND eSystemType != 'Ride'";
    }
    if ('Yes' !== $deliveryEnable) {
        $ssql .= " AND eSystemType != 'Delivery'";
    }
    if ('Yes' !== $deliverallEnable) {
        $ssql .= " AND eSystemType != 'DeliverAll'";
    }

    $field = '';

    if (('Ride-Delivery-UberX' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE) && ONLYDELIVERALL === 'No') {
        $field = ',eSystemType as `System Type`';
    }

    $sql = "SELECT vCouponCode as 'GiftCertificate Code', (CASE WHEN eType = 'percentage' THEN CONCAT(fDiscount,'%') ELSE CONCAT( '".$db_currency[0]['vSymbol']."', fDiscount) END ) as `Discount`,eValidityType as `Validity`,vPromocodeType as `PromoCode Type`,

            CASE WHEN (DATE_FORMAT(dActiveDate,'%d/%m/%Y')='00/00/0000') THEN '-'

            ELSE DATE_FORMAT(dActiveDate,'%d/%m/%Y')

            END AS `Activation Date`,

            CASE WHEN (DATE_FORMAT(dExpiryDate,'%d/%m/%Y')='00/00/0000') THEN '-'

            ELSE DATE_FORMAT(dExpiryDate,'%d/%m/%Y')

            END AS `Expiry Date`,

            iUsageLimit as `Usage Limit`,iUsed as `Used`,iUsed as `UsedInScheduleBooking`,eStatus as `Status`{$field} FROM coupon WHERE 1 {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        $getCouponDataArray = $obj->MySQLSelect($sql) || exit('Query failed!');

        $couponArray = [];

        if (count($getCouponDataArray) > 0 && !empty($getCouponDataArray)) {
            for ($i = 0; $i < count($getCouponDataArray); ++$i) {
                $couponArray[] = $getCouponDataArray[$i]['GiftCertificate Code'];
            }

            $couponString = "'".implode("','", $couponArray)."'";

            $couponData = getUnUsedPromocode($couponString);
        }

        while ($row = mysqli_fetch_assoc($result)) {
            if (array_key_exists($row['GiftCertificate Code'], $couponData)) {
                $row['UsedInScheduleBooking'] = $couponData[$row['GiftCertificate Code']];
            } else {
                $row['UsedInScheduleBooking'] = 0;
            }
            if ('Defined' === $row['Validity']) {
                $row['Validity'] = 'Custom';
            }

            if (!$flag) {
                if (ONLYDELIVERALL === 'Yes') {
                    unset($row['UsedInScheduleBooking']);
                }

                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            if (ONLYDELIVERALL === 'Yes') {
                unset($row['UsedInScheduleBooking']);
            }

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Gift Certificate', 'Discount', 'ValidityType', 'PromoCode Type', 'Active Date', 'ExpiryDate', 'Usage Limit', 'Used', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Coupon');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Gift Certificate' === $column_heading) {
                $pdf->Cell(42, 10, $column_heading, 1);
            } elseif ('Discount' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Validity Type' === $column_heading) {
                $pdf->Cell(26, 10, $column_heading, 1);
            } elseif ('PromoCode Type' === $column_heading) {
                $pdf->Cell(26, 10, $column_heading, 1);
            } elseif ('Active Date' === $column_heading) {
                $pdf->Cell(28, 10, $column_heading, 1);
            } elseif ('ExpiryDate' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Usage Limit' === $column_heading) {
                $pdf->Cell(24, 10, $column_heading, 1);
            } elseif ('Used' === $column_heading) {
                $pdf->Cell(12, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(17, 10, $column_heading, 1);
            } else {
                $pdf->Cell(25, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            // echo "<pre>";

            $symbol = '$';

            if ('percentage' === $row['eType']) {
                $symbol = '%';
            }

            unset($row['eType']);

            // if($result[])

            foreach ($row as $column => $key) {
                if ('Gift Certificate' === $column) {
                    $pdf->Cell(42, 10, $key, 1);
                } elseif ('Discount' === $column) {
                    $key = $key.' '.$symbol;

                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('ValidityType' === $column) {
                    if ('Defined' === $key) {
                        $key = 'Custom';

                        $pdf->Cell(25, 10, $key, 1);
                    } else {
                        $pdf->Cell(25, 10, $key, 1);
                    }
                } elseif ('PromoCode Type' === $column) {
                    $pdf->Cell(17, 10, $key, 1);
                } elseif ('Active Date' === $column) {
                    $pdf->Cell(28, 10, $key, 1);
                } elseif ('ExpiryDate' === $column) {
                    $pdf->Cell(25, 10, $key, 1);
                } elseif ('Usage Limit' === $column) {
                    $pdf->Cell(24, 10, $key, 1);
                } elseif ('Used' === $column) {
                    $pdf->Cell(12, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(17, 10, $key, 1);
                } else {
                    $pdf->Cell(25, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// coupon

// driver

if ('driver' === $section) {
    $ord = ' ORDER BY rd.iDriverId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vEmail ASC';
        } else {
            $ord = ' ORDER BY rd.vEmail DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.tRegistrationDate ASC';
        } else {
            $ord = ' ORDER BY rd.tRegistrationDate DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.eStatus ASC';
        } else {
            $ord = ' ORDER BY rd.eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('MobileNumber' === $option) {
                $option_new = "CONCAT(rd.vCode,'',rd.vPhone)";
            }

            if ('DriverName' === $option) {
                $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
            }

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND rd.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if (ONLYDELIVERALL === 'Yes') {
                if ('' !== $eStatus) {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(rd.vCode,'',rd.vPhone) LIKE '%".clean($keyword_new)."%')) AND rd.eStatus = '".clean($eStatus)."'";
                } else {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(rd.vCode,'',rd.vPhone) LIKE '%".clean($keyword_new)."%'))";
                }
            } else {
                if ('' !== $eStatus) {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR c.vCompany LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(rd.vCode,'',rd.vPhone) LIKE '%".clean($keyword_new)."%')) AND rd.eStatus = '".clean($eStatus)."'";
                } else {
                    $ssql .= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword_new)."%' OR c.vCompany LIKE '%".clean($keyword_new)."%' OR rd.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(rd.vCode,'',rd.vPhone) LIKE '%".clean($keyword_new)."%'))";
                }
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND rd.eStatus = '".clean($eStatus)."'";
    }

    $dri_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $dri_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
    }

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND rd.eStatus != 'Deleted'";
    }

    $IsFeaturedEnable = 'No';

    if (ONLYDELIVERALL === 'No' && ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && 'Yes' === $ufxEnable) {
        $IsFeaturedEnable = 'Yes';
    }

    $ssql1 = "AND (rd.vEmail != '' OR rd.vPhone != '')";

    if (ONLYDELIVERALL === 'Yes') {
        $sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS `Driver Name`,rd.vEmail as `Email`, rd.tRegistrationDate as `Signup Date`,CONCAT(rd.vCode,' ',rd.vPhone) as `Mobile`,rd.iDriverId AS `Wallet Balance`,rd.eStatus as `Status`,rd.eIsFeatured AS IsFeatured FROM register_driver rd  WHERE 1 = 1  {$eStatus_sql} {$ssql} {$ssql1} {$dri_ssql} {$ord}";
    } else {
        $sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS `Driver Name`,c.vCompany as `Company Name`,rd.vEmail as `Email`, rd.tRegistrationDate as `Signup Date`,CONCAT(rd.vCode,' ',rd.vPhone) as `Mobile`,rd.iDriverId AS `Wallet Balance`,rd.eStatus as `Status`,rd.eIsFeatured AS IsFeatured FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId WHERE 1 = 1  {$eStatus_sql} {$ssql} {$ssql1} {$dri_ssql} {$ord}";
    }

    $wallet_data = $obj->MySQLSelect("SELECT iUserId, SUM(COALESCE(CASE WHEN eType = 'Credit' THEN iBalance END,0)) - SUM(COALESCE(CASE WHEN eType = 'Debit' THEN iBalance END,0)) as balance FROM user_wallet WHERE eUserType = 'Driver' GROUP BY iUserId");

    $walletDataArr = [];
    foreach ($wallet_data as $wallet_balance) {
        $walletDataArr[$wallet_balance['iUserId']] = $wallet_balance['balance'];
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].'_'.$timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            // $result[0] = change_key($result[0], 'Driver Name', 'Provider Name');
            $result[0] = change_key($result[0], 'Driver Name', $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name');
        }

        if ($MODULES_OBJ->isStorePersonalDriverAvailable() > 0) {
            $result[0] = change_key($result[0], 'Company Name', 'Company/Store Name');
        }
        // echo "<pre>";

        if ('No' === $IsFeaturedEnable) {
            unset($result[0]['IsFeatured']);
        }

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($value['Wallet Balance'], "Driver");
            $user_available_balance = 0;
            if (isset($walletDataArr[$value['Wallet Balance']])) {
                $user_available_balance = $walletDataArr[$value['Wallet Balance']];
            }

            $value['Wallet Balance'] = formateNumAsPerCurrency($user_available_balance, '');

            foreach ($value as $key => $val) {
                if ('IsFeatured' === $key) {
                    unset($val);
                }

                if ('Driver Name' === $key) {
                    $val = clearCmpName($val);
                }

                if ($langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name' === $key) {
                    // echo $val."<br>";

                    $val = clearCmpName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                if ('Company Name' === $key) {
                    $val = clearCmpName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if (ONLYDELIVERALL === 'Yes') {
            $heading = [$langage_lbl_admin['LBL_DRIVER_NAME_EXPORT'], 'Email', 'Signup Date', 'Mobile', 'Wallet Balance', 'Status', 'IsFeatured'];
        } else {
            $heading = [$langage_lbl_admin['LBL_DRIVER_NAME_EXPORT'], 'Company Name', 'Email', 'Signup Date', 'Mobile', 'Wallet Balance', 'Status', 'IsFeatured'];
        }

        // echo "<pre>";

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($row['Wallet Balance'], "Driver");
            $user_available_balance = 0;
            if (isset($walletDataArr[$row['Wallet Balance']])) {
                $user_available_balance = $walletDataArr[$row['Wallet Balance']];
            }

            $row['Wallet Balance'] = formateNumAsPerCurrency($user_available_balance, '');

            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO, 'L', 'A4');

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']);

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            // echo $column_heading;

            if ($column_heading === $langage_lbl_admin['LBL_DRIVER_NAME_EXPORT']) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Company Name' === $column_heading || 'Wallet Balance' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Email' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Signup Date' === $column_heading) {
                $pdf->Cell(37, 10, $column_heading, 1);
            } elseif ('Mobile' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading || 'IsFeatured' === $column_heading) {
                $pdf->Cell(22, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                // echo $column."<br>";

                if ('Driver Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Company Name' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Driver Name' === $column) {
                    $pdf->Cell(35, 10, $values, 1, 0, '1');
                } elseif ('Company Name' === $column || 'Wallet Balance' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Email' === $column) {
                    $pdf->Cell(50, 10, $values, 1);
                } elseif ('Signup Date' === $column) {
                    $pdf->Cell(37, 10, $values, 1);
                } elseif ('Mobile' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Status' === $column || 'IsFeatured' === $column) {
                    $pdf->Cell(22, 10, $values, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// driver

// vehicles

if ('vehicles' === $section) {
    $eType = $_REQUEST['eType'] ?? '';

    $ord = ' ORDER BY dv.iDriverVehicleId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY m.vMake ASC';
        } else {
            $ord = ' ORDER BY m.vMake DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dv.eType ASC';
        } else {
            $ord = ' ORDER BY dv.eType DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dv.eStatus ASC';
        } else {
            $ord = ' ORDER BY dv.eStatus DESC';
        }
    }

    // End Sorting

    $dri_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $dri_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
    }

    // Start Search Parameters

    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';

    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

    $searchDate = $_REQUEST['searchDate'] ?? '';

    $iDriverId = $_REQUEST['iDriverId'] ?? '';

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND dv.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if (ONLYDELIVERALL !== 'Yes') {
                if ('' !== $eStatus) {
                    $ssql .= " AND (m.vMake LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')  AND dv.eStatus = '".clean($eStatus)."'";
                } else {
                    $ssql .= " AND (m.vMake LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')";
                }
            } else {
                if ('' !== $eStatus) {
                    $ssql .= " AND (m.vMake LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')  AND dv.eStatus = '".clean($eStatus)."'";
                } else {
                    $ssql .= " AND (m.vMake LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')";
                }
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword && '' === $eType) {
        $ssql .= " AND dv.eStatus = '".clean($eStatus)."'";
    } elseif ('' !== $eType && '' === $keyword && '' === $eStatus) {
        $ssql .= " AND dv.eType = '".clean($eType)."'";
    } elseif ('' !== $eType && '' === $keyword && '' !== $eStatus) {
        $ssql .= " AND dv.eStatus = '".clean($eStatus)."' AND dv.eType = '".clean($eType)."'";
    }

    // End Search Parameters

    if ('' !== $iDriverId) {
        $query1 = "SELECT COUNT(iDriverVehicleId) as total FROM driver_vehicle where iDriverId ='".$iDriverId."'";

        $totalData = $obj->MySQLSelect($query1);

        $total_vehicle = $totalData[0]['total'];

        if ($total_vehicle > 1) {
            $ssql .= " AND dv.iDriverId='".$iDriverId."'";
        }
    }

    // Pagination Start

    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND dv.eStatus != 'Deleted' AND dv.eType != 'UberX'";
    }

    if (ONLYDELIVERALL !== 'Yes') {
        if ('UberX' === $APP_TYPE) {
            $sql = 'SELECT COUNT(dv.iDriverVehicleId) AS Total  FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId'.$eStatus_sql.$ssql.$dri_ssql;
        } else {
            $sql = 'SELECT COUNT(dv.iDriverVehicleId) AS Total FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId AND dv.iCompanyId = c.iCompanyId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId'.$eStatus_sql.$ssql.$dri_ssql;
        }
    } else {
        $sql = 'SELECT COUNT(dv.iDriverVehicleId) AS Total FROM driver_vehicle AS dv, register_driver rd, make m, model md WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId'.$eStatus_sql.$ssql.$dri_ssql;
    }

    $totalData = $obj->MySQLSelect($sql);

    $total_results = $totalData[0]['Total'];

    $total_pages = ceil($total_results / $per_page); // total pages we going to have

    $show_page = 1;

    // -------------if page is setcheck------------------//

    $start = 0;

    $end = $per_page;

    if (isset($_GET['page'])) {
        $show_page = $_GET['page'];             // it will telles the current page

        if ($show_page > 0 && $show_page <= $total_pages) {
            $start = ($show_page - 1) * $per_page;

            $end = $start + $per_page;
        }
    }

    // display pagination

    $page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;

    $tpages = $total_pages;

    if ($page <= 0) {
        $page = 1;
    }

    // Pagination End

    if (ONLYDELIVERALL !== 'Yes') {
        if ('UberX' === $APP_TYPE) {
            $sql = "SELECT dv.iDriverVehicleId,dv.eStatus,CONCAT(rd.vName,' ',rd.vLastName) AS driverName,dv.vLicencePlate, c.vCompany FROM driver_vehicle dv, register_driver rd,company c

        WHERE 1 = 1   AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId {$eStatus_sql} {$ssql} {$dri_ssql}";
        } else {
            if ('Ride-Delivery-UberX' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE) {
                $sql = "SELECT  CONCAT(m.vMake,' ', md.vTitle) AS Taxis, c.vCompany AS Company, CONCAT(rd.vName,' ',rd.vLastName) AS Driver,dv.eStatus as Status FROM driver_vehicle dv, register_driver rd, make m, model md, company c WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId AND dv.iCompanyId = c.iCompanyId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId {$eStatus_sql} {$ssql} {$dri_ssql} {$ord} ";
            } else {
                $sql = "SELECT  CONCAT(m.vMake,' ', md.vTitle) AS Taxis, c.vCompany AS Company, CONCAT(rd.vName,' ',rd.vLastName) AS Driver ,dv.eStatus as Status FROM driver_vehicle dv, register_driver rd, make m, model md, company c WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId AND dv.iCompanyId = c.iCompanyId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId {$eStatus_sql} {$ssql} {$dri_ssql} {$ord} ";
            }
        }
    } else {
        $sql = "SELECT  CONCAT(m.vMake,' ', md.vTitle) AS Taxis, CONCAT(rd.vName,' ',rd.vLastName) AS Driver ,dv.eStatus as Status FROM driver_vehicle dv, register_driver rd, make m, model md WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId {$eStatus_sql} {$ssql} {$dri_ssql} {$ord} ";
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) {
            $result[0] = change_key($result[0], 'Driver', 'Provider');
        }

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Taxis' === $key) {
                }

                if ('Company' === $key) {
                    $val = clearCmpName($val);
                }

                if ('Driver' === $key) {
                    $val = clearName($val);
                }

                if ('Status' === $key) {
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if (ONLYDELIVERALL === 'Yes') {
            $heading = ['Taxis', $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'], 'Status'];
        } elseif ('Ride-Delivery-UberX' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE) {
            $heading = ['Taxis', 'Company', $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'], 'Status'];
        } else {
            $heading = ['Taxis', 'Company', $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'], 'Status'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Taxis');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Taxis' === $column_heading) {
                $pdf->Cell(65, 10, $column_heading, 1);
            } elseif ('Company' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ($column_heading === $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Taxis' === $column) {
                    $pdf->Cell(65, 10, $key, 1);
                } elseif ('Company' === $column) {
                    $pdf->Cell(40, 10, clearCmpName($key), 1);
                } elseif ('Driver' === $column) {
                    $pdf->Cell(40, 10, clearName($key), 1); // }
                } /* else if ($column == 'Sevice Type') {

                  $pdf->Cell(25, 10, $key, 1);

                  } */ elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $key, 1);
                } else {
                    $pdf->Cell(45, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// vehicles

// email_template

if ('email_template' === $section) {
    $ord = ' ORDER BY vSubject_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vSubject_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vSubject_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vPurpose ASC';
        } else {
            $ord = ' ORDER BY vPurpose DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= ' AND (vSubject_'.$default_lang." LIKE '%".$keyword."%' OR vPurpose LIKE '%".$keyword."%')";
        }
    }

    $default_lang = $LANG_OBJ->FetchSystemDefaultLang();

    $tbl_name = 'email_templates';

    $sql = 'SELECT vSubject_'.$default_lang.' as `Email Subject`, vPurpose as `Purpose` FROM '.$tbl_name." WHERE eStatus = 'Active' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Email Subject', 'Purpose'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Email Templates');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Email Subject' === $column_heading) {
                $pdf->Cell(98, 10, $column_heading, 1);
            } elseif ('Purpose' === $column_heading) {
                $pdf->Cell(98, 10, $column_heading, 1);
            } else {
                $pdf->Cell(8, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Email Subject' === $column) {
                    $pdf->Cell(98, 10, $key, 1);
                } elseif ('Purpose' === $column) {
                    $pdf->Cell(98, 10, $key, 1);
                } else {
                    $pdf->Cell(8, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// email_template

// Restricted Area

if ('restrict_area' === $section) {
    $ord = ' ORDER BY lm.vLocationName ASC';

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY lm.vLocationName ASC';
        } else {
            $ord = ' ORDER BY lm.vLocationName DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ra.eRestrictType ASC';
        } else {
            $ord = ' ORDER BY ra.eRestrictType DESC';
        }
    }

    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ra.eStatus ASC';
        } else {
            $ord = ' ORDER BY ra.eStatus DESC';
        }
    }

    if (7 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ra.eType ASC';
        } else {
            $ord = ' ORDER BY ra.eType DESC';
        }
    }

    // End Sorting

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'ra.eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes(clean($keyword))."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes(clean($keyword))."%'";
            }
        } else {
            $ssql .= " AND (lm.vLocationName LIKE '%".clean($keyword)."%' OR ra.eStatus LIKE '%".clean($keyword)."%' OR ra.eRestrictType LIKE '%".clean($keyword)."%' OR ra.eType LIKE '%".clean($keyword)."%')";
        }
    }

    $sql = "SELECT lm.vLocationName as Address, ra.eRestrictType AS Area, ra.eType AS Type, ra.eStatus AS Status FROM restricted_negative_area AS ra LEFT JOIN location_master AS lm ON lm.iLocationId=ra.iLocationId WHERE 1=1 {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Address', 'Area', 'Type', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Address');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Area' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('Address' === $column_heading) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Area' === $column) {
                    $pdf->Cell(40, 10, $key, 1);
                } elseif ('Address' === $column) {
                    $pdf->Cell(80, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// visit location

if ('visitlocation' === $section) {
    $ord = ' ORDER BY iVisitId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tDestLocationName ASC';
        } else {
            $ord = ' ORDER BY tDestLocationName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tDestAddress ASC';
        } else {
            $ord = ' ORDER BY tDestAddress DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (tDestLocationName LIKE '%".$keyword."%' OR tDestAddress LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT vSourceAddresss as SourceAddress, tDestAddress as DestAddress,eStatus as Status FROM visit_address where eStatus != 'Deleted' {$ssql} {$ord}";

    // die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }

        $heading = ['SourceAddress', 'DestAddress', 'Status'];
    } else {
        $heading = ['SourceAddress', 'DestAddress', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Visit Location');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('SourceAddress' === $column_heading) {
                $pdf->Cell(75, 10, $column_heading, 1);
            } elseif ('DestAddress' === $column_heading) {
                $pdf->Cell(75, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('SourceAddress' === $column) {
                    $pdf->Cell(75, 10, clearCmpName($key), 1);
                } elseif ('DestAddress' === $column) {
                    $pdf->Cell(75, 10, clearName($key), 1); // }
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $key, 1);
                } else {
                    $pdf->Cell(45, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// hotel rider

if ('hotel_rider' === $section) {
    $ord = ' ORDER BY vName ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vName ASC';
        } else {
            $ord = ' ORDER BY vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vEmail ASC';
        } else {
            $ord = ' ORDER BY vEmail DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tRegistrationDate ASC';
        } else {
            $ord = ' ORDER BY tRegistrationDate DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    $rdr_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $rdr_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (concat(vFirstName,' ',vLastName) LIKE '%".$keyword."%' OR vEmail LIKE '%".$keyword."%' OR vPhone LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT  CONCAT(vName,' ',vLastName) as Name,vEmail as Email,CONCAT(vPhoneCode,' ',vPhone) AS Mobile,eStatus as Status FROM hotel WHERE eStatus != 'Deleted' {$ssql} {$rdr_ssql} {$ord}";

    // die;

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Name' === $key) {
                    $val = clearName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Mobile' === $key) {
                    $val = clearPhone($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Name', 'Email', 'Mobile', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Hotel Riders');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Email' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Mobile' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Name' === $column) {
                    $values = clearName($key);
                }

                if ('Email' === $column) {
                    $values = clearEmail($key);
                }

                if ('Mobile' === $column) {
                    $values = clearPhone($key);
                }

                if ('Email' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Mobile' === $column) {
                    $pdf->Cell(45, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('sub_service_category' === $section) {
    global $tconfig;

    $sub_cid = $_REQUEST['sub_cid'] ?? '';

    $ord = ' ORDER BY iDisplayOrder ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vCategory_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vCategory_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY Servicetypes ASC';
        } else {
            $ord = ' ORDER BY Servicetypes DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY iDisplayOrder DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'  AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND (vCategory_'.$default_lang." LIKE '%".clean($keyword)."%') AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND (vCategory_'.$default_lang." LIKE '%".clean($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    }

    if ('0' !== $parent_ufx_catid) {
        $sql = 'SELECT vCategory_'.$default_lang.' as SubCategory, (SELECT vCategory_'.$default_lang.' FROM '.$sql_vehicle_category_table_name." WHERE iVehicleCategoryId='".$sub_cid."') as Category, (select count(iVehicleTypeId) from vehicle_type where vehicle_type.iVehicleCategoryId = ".$sql_vehicle_category_table_name.'.iVehicleCategoryId) as `Service Types`, iDisplayOrder as `Display Order`, eStatus as Status FROM '.$sql_vehicle_category_table_name." WHERE (iParentId='".$sub_cid."' || iVehicleCategoryId='".$parent_ufx_catid."') AND  1 = 1 {$ssql} {$ord}";
    } else {
        $sql = 'SELECT vCategory_'.$default_lang.' as SubCategory, (SELECT vCategory_'.$default_lang.' FROM '.$sql_vehicle_category_table_name." WHERE iVehicleCategoryId='".$sub_cid."') as Category,(select count(iVehicleTypeId) from vehicle_type where vehicle_type.iVehicleCategoryId = ".$sql_vehicle_category_table_name.'.iVehicleCategoryId) as `Service Types`, iDisplayOrder as `Display Order`,eStatus as Status FROM '.$sql_vehicle_category_table_name." WHERE (iParentId='".$sub_cid."' || iVehicleCategoryId='".$parent_ufx_catid."') {$ssql} {$ord}";
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $section.'_'.date('Ymd').'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('SubCategory' === $key) {
                    $val = clearName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['SubCategory', 'Category', 'Service Types', 'Display Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Sub Category');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Service Types' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                $id = '';

                if ('iVehicleCategoryId' === $column) {
                    $id2 = $key;
                }

                if ('SubCategory' === $column) {
                    $values = clearName($key);
                }

                if ('Display Order' === $column) {
                    $values = clearName($key);
                }

                if ('Status' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Service Types' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Display Order' === $column) {
                    $pdf->Cell(20, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

if ('service_category' === $section) {
    global $tconfig;

    $sub_cid = $_REQUEST['sub_cid'] ?? '';

    $ord = ' ORDER BY iDisplayOrder ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vc.vCategory_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vc.vCategory_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vc.eStatus ASC';
        } else {
            $ord = ' ORDER BY vc.eStatus DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY SubCategories ASC';
        } else {
            $ord = ' ORDER BY SubCategories DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY iDisplayOrder DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vc.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND vc.(vCategory_'.$default_lang." LIKE '%".clean($keyword)."%') AND vc.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND vc.(vCategory_'.$default_lang." LIKE '%".clean($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND vc.eStatus = '".clean($eStatus)."'";
    }

    $sql = 'SELECT vc.vCategory_'.$default_lang.' as Category ,(select count(iVehicleCategoryId) from '.$sql_vehicle_category_table_name.' where iParentId=vc.iVehicleCategoryId) as SubCategories,vc.iDisplayOrder as `Display Order`,vc.eStatus as Status FROM '.$sql_vehicle_category_table_name." as vc WHERE  vc.iParentId='0' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Category' === $key) {
                    $val = clearName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Category', 'SubCategories', 'Display Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Category');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Category' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } elseif ('Total' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Category' === $column) {
                    $values = clearName($key);
                }

                if ('Total' === $column) {
                    $values = $key;
                }

                if ('Category' === $column) {
                    $pdf->Cell(55, 10, $values, 1);
                } elseif ('Total' === $column) {
                    $pdf->Cell(45, 10, $values, 1);
                } elseif ('Display Order' === $column) {
                    $pdf->Cell(45, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// mask_number

if ('mask_number' === $section) {
    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND (mask_number LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT masknum_id as `Id`, mask_number as `Masking Number`,adding_date as `Added Date`, eStatus as `Status` FROM masking_numbers where 1 = 1 {$ssql}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Id', 'Masking Number', 'Added Date', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        ${$pdf}->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Masking Numbers');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Id' === $column_heading) {
                $pdf->Cell(18, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } else {
                $pdf->Cell(55, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Id' === $column) {
                    $pdf->Cell(18, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(55, 10, $key, 1);
                } else {
                    $pdf->Cell(55, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// mask_number

// document master

// driver

if ('Document_Master' === $section) {
    $eType_value = isset($_REQUEST['eType_value']) ? stripslashes($_REQUEST['eType_value']) : '';

    $doc_userTypeValue = isset($_REQUEST['doc_userTypeValue']) ? stripslashes($_REQUEST['doc_userTypeValue']) : '';

    $ord = ' ORDER BY dm.doc_name ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCountry ASC';
        } else {
            $ord = ' ORDER BY c.vCountry DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dm.doc_usertype ASC';
        } else {
            $ord = ' ORDER BY dm.doc_usertype DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dm.doc_name ASC';
        } else {
            $ord = ' ORDER BY dm.doc_name DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dm.status ASC';
        } else {
            $ord = ' ORDER BY dm.status DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dm.eType ASC';
        } else {
            $ord = ' ORDER BY dm.eType DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eType_value) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND dm.eType = '".clean($eType_value)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }

            if ('' !== $doc_userTypeValue) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND dm.doc_usertype = '".clean($doc_userTypeValue)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eType_value) {
                $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR dm.doc_usertype LIKE '%".$keyword."%' OR dm.doc_name LIKE '%".$keyword."%' OR dm.status LIKE '%".$keyword."%') AND dm.eType = '".clean($eType_value)."'";
            } else {
                $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR dm.doc_usertype LIKE '%".$keyword."%' OR dm.doc_name LIKE '%".$keyword."%' OR dm.status LIKE '%".$keyword."%')";
            }

            if ('' !== $doc_userTypeValue) {
                $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR dm.doc_name LIKE '%".$keyword."%' OR dm.status LIKE '%".$keyword."%') AND dm.eType = '".clean($eType_value)."' AND dm.doc_usertype = '".clean($doc_userTypeValue)."'";
            } else {
                $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR dm.doc_usertype LIKE '%".$keyword."%' OR dm.doc_name LIKE '%".$keyword."%' OR dm.status LIKE '%".$keyword."%')";
            }
        }
    } elseif ('' !== $eType_value && '' === $keyword) {
        $ssql .= " AND dm.eType = '".clean($eType_value)."'";
    } elseif ('' !== $doc_userTypeValue && '' === $keyword) {
        $ssql .= " AND dm.doc_usertype = '".clean($doc_userTypeValue)."'";
    }

    if ('' !== $eType_value) {
        $ssql .= " AND dm.doc_usertype != 'company'";
    }

    if ('dm.status' === $option) {
        $eStatussql = " AND dm.status = '{$keyword}'";
    } else {
        $eStatussql = " AND dm.status != 'Deleted'";
    }

    $dri_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $dri_ssql = " And dm.doc_instime > '".WEEK_DATE."'";
    }

    /*if ($APP_TYPE == 'Ride-Delivery') {

        $eTypeQuery = " AND (dm.eType='Ride' OR dm.eType='Delivery')";

    } else if ($APP_TYPE == 'Ride-Delivery-UberX') {

        $eTypeQuery = " AND (dm.eType='Ride' OR dm.eType='Delivery' OR dm.eType='UberX')";

    } else {

        $eTypeQuery = " AND dm.eType='" . $APP_TYPE . "'";

    }*/

    if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
        $sql = "SELECT if(c.vCountry IS NULL,'All',c.vCountry) as Country,dm.doc_name as `Document Name`,dm.doc_usertype as `Document For`, dm.status as Status FROM `document_master` AS dm

 LEFT JOIN `country` AS c ON c.vCountryCode=dm.country

 WHERE 1=1 {$eStatussql} {$eTypeQuery} {$ssql} {$dri_ssql} {$ord}";
    } else {
        $sql = "SELECT if(c.vCountry IS NULL,'All',c.vCountry) as Country,dm.doc_name as `Document Name`,dm.doc_usertype as `Document For`, dm.status as Status FROM `document_master` AS dm

  LEFT JOIN `country` AS c ON c.vCountryCode=dm.country

  WHERE 1=1 {$eStatussql} {$eTypeQuery} {$ssql} {$dri_ssql} {$ord}";
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('UberX' === $val) {
                    $val = 'Other Services';
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
            $heading = ['Country', 'Document Name', 'Document For', 'Status'];
        } else {
            $heading = ['Country', 'Document Name', 'Document For', 'Status'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Documents');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Country' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Document For' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Document Name' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Service Type' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Country' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Document For' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Document Name' === $column) {
                    $pdf->Cell(50, 10, $values, 1);
                } elseif ('Service Type' === $column) {
                    if ('UberX' === $values) {
                        $values = 'Other Services';
                    }

                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// document master

// review page

if ('review' === $section) {
    $reviewtype = $_REQUEST['reviewtype'] ?? 'Driver';

    $adm_ssql = '';

    if (SITE_TYPE === 'Demo') {
        if ('Driver' === $reviewtype) {
            $adm_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
        } else {
            $adm_ssql = " And ru.tRegistrationDate > '".WEEK_DATE."'";
        }
    }

    $type = (isset($_REQUEST['reviewtype']) && '' !== $_REQUEST['reviewtype']) ? $_REQUEST['reviewtype'] : 'Driver';

    $reviewtype = $type;

    // Start Sorting

    $sortby = $_REQUEST['sortby'] ?? 0;

    $order = $_REQUEST['order'] ?? '';

    $ord = ' ORDER BY iRatingId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.vRideNo ASC';
        } else {
            $ord = ' ORDER BY t.vRideNo DESC';
        }
    }

    if (2 === $sortby) {
        if ('Driver' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY rd.vName ASC';
            } else {
                $ord = ' ORDER BY rd.vName DESC';
            }
        } else {
            if (0 === $order) {
                $ord = ' ORDER BY ru.vName ASC';
            } else {
                $ord = ' ORDER BY ru.vName DESC';
            }
        }
    }

    if (6 === $sortby) {
        if ('Driver' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY ru.vName ASC';
            } else {
                $ord = ' ORDER BY ru.vName DESC';
            }
        } else {
            if (0 === $order) {
                $ord = ' ORDER BY rd.vName ASC';
            } else {
                $ord = ' ORDER BY rd.vName DESC';
            }
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.vRating1 ASC';
        } else {
            $ord = ' ORDER BY r.vRating1 DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.tDate ASC';
        } else {
            $ord = ' ORDER BY r.tDate DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.vMessage ASC';
        } else {
            $ord = ' ORDER BY r.vMessage DESC';
        }
    }

    // End Sorting

    $adm_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $adm_ssql = " And ru.tRegistrationDate > '".WEEK_DATE."'";
    }

    // Start Search Parameters

    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';

    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

    $searchDate = $_REQUEST['searchDate'] ?? '';

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'r.eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".clean($keyword)."'";
            } else {
                $option_new = $option;

                if ('drivername' === $option) {
                    $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
                }

                if ('ridername' === $option) {
                    $option_new = "CONCAT(ru.vName,' ',ru.vLastName)";
                }

                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword)."%'";
            }
        } else {
            $ssql .= " AND (t.vRideNo LIKE '%".clean($keyword)."%' OR  concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR r.vRating1 LIKE '%".clean($keyword)."%')";
        }
    }

    // End Search Parameters

    // Pagination Start

    $chkusertype = '';

    if ('Driver' === $type) {
        $chkusertype = 'Passenger';
    } else {
        $chkusertype = 'Driver';
    }

    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

    $sql = "SELECT count(r.iRatingId) as Total FROM ratings_user_driver as r LEFT JOIN trips as t ON r.iTripId=t.iTripId LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId 	LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId WHERE eUserType='".$chkusertype."' And ru.eStatus!='Deleted' AND t.eSystem = 'General' {$ssql} {$adm_ssql}";

    $totalData = $obj->MySQLSelect($sql);

    $total_results = $totalData[0]['Total'];

    $total_pages = ceil($total_results / $per_page); // total pages we going to have

    $show_page = 1;

    // -------------if page is setcheck------------------//

    if (isset($_GET['page'])) {
        $show_page = $_GET['page'];             // it will telles the current page

        if ($show_page > 0 && $show_page <= $total_pages) {
            $start = ($show_page - 1) * $per_page;

            $end = $start + $per_page;
        } else {
            // error - show first set of results

            $start = 0;

            $end = $per_page;
        }
    } else {
        // if page isn't set, show first set of results

        $start = 0;

        $end = $per_page;
    }

    // display pagination

    $page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;

    $tpages = $total_pages;

    if ($page <= 0) {
        $page = 1;
    }

    // Pagination End

    $chkusertype = '';

    if ('Driver' === $type) {
        $chkusertype = 'Passenger';
    } else {
        $chkusertype = 'Driver';
    }
    $number_txt = $langage_lbl_admin['LBL_RIDE_TXT_ADMIN'].' Number';
    $driver_txt = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name';
    $user_txt = $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'].' Name';

    $sql = "SELECT t.vRideNo as '".$number_txt."',CONCAT(rd.vName,' ',rd.vLastName) as '".$driver_txt."',CONCAT(ru.vName,' ',ru.vLastName) as '".$user_txt."',r.vRating1 as Rate,r.tDate as Date,r.vMessage as Comment FROM ratings_user_driver as r LEFT JOIN trips as t ON r.iTripId=t.iTripId LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId WHERE 1=1 AND r.eUserType='".$chkusertype."' And ru.eStatus!='Deleted' AND t.eSystem = 'General' {$ssql} {$adm_ssql} {$ord}";

    $type = 'XLS';

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('RiderNumber' === $key) {
                    $val = $val;
                }

                if ('DriverName' === $key) {
                    $val = clearName($val);
                }

                if ('RiderName' === $key) {
                    $val = clearName($val);
                }

                if ('Driver' === $reviewtype) {
                    if ('DriverName' === $key) {
                        $val = $val;
                    }
                } else {
                    if ('RiderName' === $key) {
                        $val = $val;
                    }
                }

                if ('AverageRate' === $key) {
                    $val = $val;
                }

                if ('Driver' === $reviewtype) {
                    if ('RiderName' === $key) {
                        $val = $val;
                    }
                } else {
                    if ('DriverName' === $key) {
                        $val = $val;
                    }
                }

                if ('Rate' === $key) {
                    $val = $val;
                }

                if ('Date' === $key) {
                    $val = DateTime($val);
                }

                if ('Comment' === $key) {
                    $val = $val;
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if ('Driver' === $reviewtype) {
            $heading = ['RiderNumber', 'DriverName', 'AverageRate', 'RiderName', 'Rate', 'Date', 'Comment'];
        } else {
            $heading = ['RiderNumber', 'RiderName', 'AverageRate', 'DriverName', 'Rate', 'Date', 'Comment'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Review');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('RiderNumber' === $column_heading) {
                $pdf->Cell(22, 10, $column_heading, 1);
            } elseif ('DriverName' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('AverageRate' === $column_heading) {
                $pdf->Cell(21, 10, $column_heading, 1);
            } elseif ('RiderName' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Rate' === $column_heading) {
                $pdf->Cell(10, 10, $column_heading, 1);
            } elseif ('Date' === $column_heading) {
                $pdf->Cell(42, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('RiderNumber' === $column) {
                    $values = clearPhone($key);
                }

                if ('DriverName' === $column) {
                    $values = clearName($key);
                }

                if ('RiderName' === $column) {
                    $values = clearName($key);
                }

                if ('Date' === $column) {
                    $values = DateTime($key);
                }

                DateTime($val);

                if ('RiderNumber' === $column) {
                    $pdf->Cell(22, 10, $values, 1);
                } elseif ('DriverName' === $column) {
                    $pdf->Cell(40, 10, $values, 1);
                } elseif ('AverageRate' === $column) {
                    $pdf->Cell(21, 10, $values, 1);
                } elseif ('RiderName' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Rate' === $column) {
                    $pdf->Cell(10, 10, $values, 1);
                } elseif ('Date' === $column) {
                    $pdf->Cell(42, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// sms_template

if ('sms_template' === $section) {
    $ord = ' ORDER BY vEmail_Code ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vEmail_Code ASC';
        } else {
            $ord = ' ORDER BY vEmail_Code DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vSubject_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vSubject_'.$default_lang.' DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND vEmail_Code LIKE '%".$keyword."%' OR vSubject_".$default_lang." LIKE '%".$keyword."%'";
        }
    }

    $default_lang = $LANG_OBJ->FetchSystemDefaultLang();

    $tbl_name = 'send_message_templates';

    $sql = 'SELECT vSubject_'.$default_lang.' as `SMS Title`,vEmail_Code as `SMS Code` FROM '.$tbl_name." WHERE eStatus = 'Active' {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['SMS Title', 'SMS Code'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'SMS Templates');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('SMS Title' === $column_heading) {
                $pdf->Cell(82, 10, $column_heading, 1);
            } elseif ('SMS Code' === $column_heading) {
                $pdf->Cell(82, 10, $column_heading, 1);
            } else {
                $pdf->Cell(82, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('SMS Title' === $column) {
                    $pdf->Cell(82, 10, $key, 1);
                } elseif ('SMS Code' === $column) {
                    $pdf->Cell(82, 10, $key, 1);
                } else {
                    $pdf->Cell(82, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// locationwise fare

if ('airportsurcharge_fare' === $section) {
    $ord = ' ORDER BY ls.iLocatioId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.iLocationIds ASC';
        } else {
            $ord = ' ORDER BY ls.iLocationIds DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.fpickupsurchargefare ASC';
        } else {
            $ord = ' ORDER BY ls.fpickupsurchargefare DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.fdropoffsurchargefare ASC';
        } else {
            $ord = ' ORDER BY ls.fdropoffsurchargefare DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.eStatus ASC';
        } else {
            $ord = ' ORDER BY ls.eStatus DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.vVehicleType ASC';
        } else {
            $ord = ' ORDER BY vt.vVehicleType DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND lm2.vLocationName LIKE '%".$keyword."%' OR ls.eStatus LIKE '%".$keyword."%' OR vt.vVehicleType LIKE '%".$keyword."%'";
        }
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND ls.eStatus = '".ucfirst($keyword)."'";
    } else {
        $eStatussql = " AND ls.eStatus != 'Deleted'";
    }

    $sql = "SELECT lm2.vLocationName as `Location Name`, ls.fpickupsurchargefare as `Pickup Surcharge Fare`,ls.fpickupsurchargefare as `Dropoff Surcharge Fare`,vt.vVehicleType  as `Vehicle Type`,ls.eStatus as `Status` FROM `airportsurcharge_fare` ls left join location_master lm2 on ls.iLocationIds = lm2.iLocationId left join vehicle_type as vt on vt.iVehicleTypeId=ls.iVehicleTypeId WHERE 1 = 1 {$eStatussql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Location Name', 'Pickup Surcharge Fare', 'Dropoff Surcharge Fare', 'Vehicle Type', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Airport surcharge Fare');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Location Name' === $column_heading) {
                $pdf->Cell(65, 10, $column_heading, 1);
            } elseif ('Pickup Surcharge Fare' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Dropoff Surcharge Fare' === $column_heading) {
                $pdf->Cell(45, 10, $column_heading, 1);
            } elseif ('Vehicle Type' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(30, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Location Name' === $column) {
                    $pdf->Cell(65, 10, $key, 1);
                } elseif ('Pickup Surcharge Fare' === $column) {
                    $pdf->Cell(45, 10, $key, 1);
                } elseif ('Dropoff Surcharge Fare' === $column) {
                    $pdf->Cell(45, 10, $key, 1);
                } elseif ('Vehicle Type' === $column) {
                    $pdf->Cell(25, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } else {
                    $pdf->Cell(30, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// locationwise fare

if ('locationwise_fare' === $section) {
    $ord = ' ORDER BY ls.iLocatioId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY lm1.vLocationName ASC';
        } else {
            $ord = ' ORDER BY lm1.vLocationName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY lm2.vLocationName ASC';
        } else {
            $ord = ' ORDER BY lm2.vLocationName DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.fFlatfare ASC';
        } else {
            $ord = ' ORDER BY ls.fFlatfare DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ls.eStatus ASC';
        } else {
            $ord = ' ORDER BY ls.eStatus DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.vVehicleType ASC';
        } else {
            $ord = ' ORDER BY vt.vVehicleType DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            $ssql .= " AND lm1.vLocationName LIKE '%".$keyword."%' OR lm2.vLocationName LIKE '%".$keyword."%' OR ls.fFlatfare LIKE '%".$keyword."%' OR ls.eStatus LIKE '%".$keyword."%' OR vt.vVehicleType LIKE '%".$keyword."%'";
        }
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND ls.eStatus = '".ucfirst($keyword)."'";
    } else {
        $eStatussql = " AND ls.eStatus != 'Deleted'";
    }

    $sql = "SELECT lm2.vLocationName as `Source LocationName`,lm1.vLocationName as `Destination LocationName`,ls.fFlatfare as `Flat Fare`,vt.vVehicleType as `Vehicle Type`,ls.eStatus as `Status` FROM `location_wise_fare` ls left join location_master lm1 on ls.iToLocationId = lm1.iLocationId left join location_master lm2 on ls.iFromLocationId = lm2.iLocationId left join vehicle_type as vt on vt.iVehicleTypeId=ls.iVehicleTypeId  WHERE 1 = 1 {$eStatussql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Source LocationName', 'Destination LocationName', 'Flat Fare', 'Vehicle Type', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Locationwise Fare');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Source LocationName' === $column_heading) {
                $pdf->Cell(65, 10, $column_heading, 1);
            } elseif ('Destination LocationName' === $column_heading) {
                $pdf->Cell(65, 10, $column_heading, 1);
            } elseif ('Flat Fare' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(30, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Source LocationName' === $column) {
                    $pdf->Cell(65, 10, $key, 1);
                } elseif ('Destination LocationName' === $column) {
                    $pdf->Cell(65, 10, $key, 1);
                } elseif ('Flat Fare' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } else {
                    $pdf->Cell(30, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// locationwise fare

// FoodMenu

if ('FoodMenu' === $section) {
    $eStatus = $_REQUEST['eStatus'] ?? '';

    $ord = ' ORDER BY f.iFoodMenuId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.vMenu_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY f.vMenu_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY f.iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY MenuItems ASC';
        } else {
            $ord = ' ORDER BY MenuItems DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.eStatus ASC';
        } else {
            $ord = ' ORDER BY f.eStatus DESC';
        }
    }

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            $option_new = $option;

            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword)."%' AND f.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword)."%' OR f.vMenu_".$default_lang." LIKE '%".clean($keyword)."%') AND f.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword)."%' OR f.vMenu_".$default_lang." LIKE '%".clean($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND f.eStatus = '".clean($eStatus)."'";
    }

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND f.eStatus != 'Deleted'";
    }

    $sql = 'SELECT f.vMenu_'.$default_lang." as Title,c.vCompany as Store,f.iDisplayOrder as `Display Order`,(select count(iMenuItemId) from menu_items where iFoodMenuId = f.iFoodMenuId) as `Items`, f.eStatus as Status  FROM  `food_menu` as f LEFT JOIN company c ON f.iCompanyId = c.iCompanyId  WHERE 1=1 {$eStatus_sql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Title' === $key) {
                    $val = clearName($val);
                }

                if ('Store' === $key) {
                    $val = clearName($val);
                }

                if ('Display Order' === $key) {
                    $val = clearPhone($val);
                }

                if ('Status' === $key) {
                    $val = clearCmpName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Title', 'Store', 'Display Order', 'Items', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Item Categories');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Title' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Store' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Items' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Title' === $column) {
                    $values = clearName($key);
                }

                if ('Store' === $column) {
                    $values = clearEmail($key);
                }

                if ('Display Order' === $column) {
                    $values = clearPhone($key);
                }

                if ('Status' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Title' === $column) {
                    $pdf->Cell(50, 10, $values, 1, 0, '1');
                } elseif ('Store' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Display Order' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Items' === $column) {
                    $pdf->Cell(30, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// FoodMenu

// MenuItems

if ('MenuItems' === $section) {
    $eStatus = $_REQUEST['eStatus'] ?? '';

    $ord = ' ORDER BY mi.iMenuItemId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mi.vItemType_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY mi.vItemType_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY f.vMenu_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY f.vMenu_'.$default_lang.' DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mi.iDisplayOrder ASC';
        } else {
            $ord = ' ORDER BY mi.iDisplayOrder DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY mi.eStatus ASC';
        } else {
            $ord = ' ORDER BY mi.eStatus DESC';
        }
    }

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND mi.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND (f.vMenu_'.$default_lang." LIKE '%".clean($keyword)."%' OR c.vCompany LIKE '%".clean($keyword)."%' OR mi.vItemType_".$default_lang." LIKE '%".clean($keyword)."%') AND mi.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND (f.vMenu_'.$default_lang." LIKE '%".clean($keyword)."%' OR c.vCompany LIKE '%".clean($keyword)."%' OR mi.vItemType_".$default_lang." LIKE '%".clean($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND mi.eStatus = '".clean($eStatus)."'";
    }

    if ('' !== $eStatus) {
        $eStatus_sql = '';
    } else {
        $eStatus_sql = " AND mi.eStatus != 'Deleted'";
    }

    $sql = 'SELECT mi.vItemType_'.$default_lang.' as Item, f.vMenu_'.$default_lang." as Category, c.vCompany as Store, mi.iDisplayOrder as `Display Order`,mi.eStatus as Status  FROM  `menu_items` as mi INNER JOIN food_menu f ON f.iFoodMenuId = mi.iFoodMenuId INNER JOIN company as c on c.iCompanyId=f.iCompanyId WHERE 1=1 {$eStatus_sql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Item' === $key) {
                    $val = clearName($val);
                }

                if ('Category' === $key) {
                    $val = clearName($val);
                }

                if ('Store' === $key) {
                    $val = clearName($val);
                }

                if ('Display Order' === $key) {
                    $val = clearPhone($val);
                }

                if ('Status' === $key) {
                    $val = clearCmpName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = ['Item', 'Category', 'Store', 'Display Order', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Items');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Item' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Category' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } elseif ('Store' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Item' === $column) {
                    $values = clearName($key);
                }

                if ('Category' === $column) {
                    $values = clearName($key);
                }

                if ('Store' === $column) {
                    $values = clearEmail($key);
                }

                if ('Display Order' === $column) {
                    $values = clearPhone($key);
                }

                if ('Status' === $column) {
                    $values = clearCmpName($key);
                }

                if ('Item' === $column) {
                    $pdf->Cell(50, 10, $values, 1);
                } elseif ('Category' === $column) {
                    $pdf->Cell(50, 10, $values, 1);
                } elseif ('Store' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } elseif ('Display Order' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(20, 10, $values, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// MenuItems

// cuisine

if ('cuisine' === $section) {
    $ord = ' ORDER BY c.cuisineName_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.cuisineName_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY c.cuisineName_'.$default_lang.' DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.eStatus ASC';
        } else {
            $ord = ' ORDER BY c.eStatus DESC';
        }
    }

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND (c.cuisineName_'.$default_lang." LIKE '%".$keyword."%' OR sc.vServiceName_".$default_lang." LIKE '%".$keyword."%') AND c.eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND (c.cuisineName_'.$default_lang." LIKE '%".$keyword."%' OR sc.vServiceName_".$default_lang." LIKE '%".$keyword."%') ";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
    }

    if ('' !== $eStatus) {
        $eStatussql = '';
    } else {
        $eStatussql = " AND c.eStatus != 'Deleted'";
    }

    if (ONLYDELIVERALL === 'Yes') {
        $sql = 'SELECT c.cuisineName_'.$default_lang." as `Item Type`, c.eStatus as Status FROM cuisine as c LEFT JOIN service_categories as sc on sc.iServiceId=c.iServiceId where 1=1 {$eStatussql} {$ssql} {$ord}";
    } else {
        $sql = 'SELECT c.cuisineName_'.$default_lang.' as `Item Type`,sc.vServiceName_'.$default_lang." as `DeliveryAll Service Category`, c.eStatus as Status FROM cuisine as c LEFT JOIN service_categories as sc on sc.iServiceId=c.iServiceId where 1=1 {$eStatussql} {$ssql} {$ord}";
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        if (ONLYDELIVERALL === 'Yes') {
            $heading = ['Item Type', 'Status'];
        } else {
            $heading = ['Item Type', 'DeliveryAll Service Category', 'Status'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Service Categories');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Status' === $column_heading) {
                $pdf->Cell(50, 10, $column_heading, 1);
            } else {
                $pdf->Cell(70, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Status' === $column) {
                    $pdf->Cell(50, 10, $key, 1);
                } else {
                    $pdf->Cell(70, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// Cuisine

// vehicle_type

if ('store_vehicle_type' === $section) {
    $eSystem = " AND eType = 'DeliverAll' ";

    $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';
        } else {
            $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.fDeliveryCharge ASC';
        } else {
            $ord = ' ORDER BY vt.fDeliveryCharge DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vt.fRadius ASC';
        } else {
            $ord = ' ORDER BY vt.fRadius DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.eStatus = '".$eStatus."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fDeliveryCharge LIKE '%".$keyword."%' OR vt.fDeliveryChargeCancelOrder LIKE '%".$keyword."%' OR vt.fRadius LIKE '%".$keyword."%' OR vt.iPersonSize   LIKE '%".$keyword."%') AND vt.eStatus = '".$eStatus."'";
            } else {
                $ssql .= ' AND (vt.vVehicleType_'.$default_lang." LIKE '%".$keyword."%' OR vt.fDeliveryCharge LIKE '%".$keyword."%' OR vt.fDeliveryChargeCancelOrder LIKE '%".$keyword."%' OR vt.fRadius LIKE '%".$keyword."%' OR vt.iPersonSize   LIKE '%".$keyword."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND vt.eStatus = '".$eStatus."'";
    }

    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);

        $ssql .= " AND vt.iLocationid IN(-1, {$locations})";
    }

    if ('' !== $eStatus) {
        $eStatussql = '';
    } else {
        $eStatussql = " AND vt.eStatus != 'Deleted'";
    }

    $sql = 'SELECT vt.vVehicleType_'.$default_lang." as Type,vt.fDeliveryCharge as `Delivery Fees Completed Orders`,vt.fDeliveryChargeCancelOrder as `Delivery Fees Cancelled Orders`,vt.fRadius as Radius,vt.eStatus as Status, lm.vLocationName as location,vt.iLocationid as locationId  from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where 1 = 1 {$eSystem} {$eStatussql} {$ssql} {$ord}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        $data = array_keys($result[0]);

        $arr = array_diff($data, ['locationId']);

        echo implode("\t", $arr)."\r\n";

        $i = 0;

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('locationId' === $key) {
                    $val = '';
                }

                if ('location' === $key && '-1' === $value['locationId']) {
                    $val = 'All Location';
                }

                echo $val."\t";
            }

            echo "\r\n";

            ++$i;
        }
    } else {
        if ('UberX' === $APP_TYPE) {
            $heading = ['Type', 'Subcategory', 'Location Name'];
        } else {
            $heading = ['Type', 'Delivery Fees Completed Orders', 'Delivery Fees Cancelled Orders', 'Radius', 'Status', 'Location Name'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Vehicle Type');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Type' === $column_heading && 'UberX' === $APP_TYPE) {
                $pdf->Cell(80, 10, $column_heading, 1);
            } elseif ('Type' === $column_heading && 'UberX' !== $APP_TYPE) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Delivery Fees Completed Orders' === $column_heading) {
                $pdf->Cell(54, 10, $column_heading, 1);
            } elseif ('Delivery Fees Cancelled Orders' === $column_heading) {
                $pdf->Cell(54, 10, $column_heading, 1);
            } elseif ('Radius' === $column_heading) {
                $pdf->Cell(15, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Location Name' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Type' === $column && 'UberX' === $APP_TYPE) {
                    $pdf->Cell(80, 10, $key, 1);
                } elseif ('Type' === $column && 'UberX' !== $APP_TYPE) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('Delivery Fees Completed Orders' === $column) {
                    $pdf->Cell(54, 10, $key, 1);
                } elseif ('Delivery Fees Cancelled Orders' === $column) {
                    $pdf->Cell(54, 10, $key, 1);
                } elseif ('Radius' === $column) {
                    $pdf->Cell(15, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(20, 10, $key, 1);
                } elseif ('location' === $column && '-1' === $row['locationId']) {
                    $pdf->Cell(35, 10, 'All Location', 1);
                } elseif ('location' === $column) {
                    $pdf->Cell(35, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// vehicle_type

// review page

if ('store_review' === $section) {
    $reviewtype = $_REQUEST['reviewtype'] ?? 'Driver';

    $adm_ssql = '';

    if (SITE_TYPE === 'Demo') {
        $adm_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
    }

    $ord = ' ORDER BY iRatingId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY o.vOrderNo ASC';
        } else {
            $ord = ' ORDER BY o.vOrderNo DESC';
        }
    }

    if (2 === $sortby) {
        if ('Driver' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY rd.vName ASC';
            } else {
                $ord = ' ORDER BY rd.vName DESC';
            }
        } elseif ('Company' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY c.vCompany ASC';
            } else {
                $ord = ' ORDER BY c.vCompany DESC';
            }
        } else {
            if (0 === $order) {
                $ord = ' ORDER BY ru.vName ASC';
            } else {
                $ord = ' ORDER BY ru.vName DESC';
            }
        }
    }

    if (6 === $sortby) {
        if ('Driver' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY ru.vName ASC';
            } else {
                $ord = ' ORDER BY ru.vName DESC';
            }
        } elseif ('Company' === $reviewtype) {
            if (0 === $order) {
                $ord = ' ORDER BY ru.vName ASC';
            } else {
                $ord = ' ORDER BY ru.vName DESC';
            }
        } else {
            if (0 === $order) {
                $ord = ' ORDER BY rd.vName ASC';
            } else {
                $ord = ' ORDER BY rd.vName DESC';
            }
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.vRating1 ASC';
        } else {
            $ord = ' ORDER BY r.vRating1 DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.tDate ASC';
        } else {
            $ord = ' ORDER BY r.tDate DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY r.vMessage ASC';
        } else {
            $ord = ' ORDER BY r.vMessage DESC';
        }
    }

    // End Sorting

    $ssql = '';

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'r.eStatus')) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '".clean($keyword)."'";
            } else {
                $option_new = $option;

                if ('drivername' === $option) {
                    $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
                }

                if ('ridername' === $option) {
                    $option_new = "CONCAT(ru.vName,' ',ru.vLastName)";
                }

                $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword)."%'";
            }
        } else {
            if ('Driver' === $reviewtype) {
                $ssql .= " AND (o.vOrderNo LIKE '%".clean($keyword)."%' OR  concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR r.vRating1 LIKE '%".clean($keyword)."%')";
            } elseif ('Company' === $reviewtype) {
                $ssql .= " AND (o.vOrderNo LIKE '%".clean($keyword)."%' OR  c.vCompany LIKE '%".clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR r.vRating1 LIKE '%".clean($keyword)."%')";
            } else {
                $ssql .= " AND (o.vOrderNo LIKE '%".clean($keyword)."%' OR  concat(rd.vName,' ',rd.vLastName) LIKE '%".clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR r.vRating1 LIKE '%".clean($keyword)."%')";
            }
        }
    }

    // End Search Parameters

    $chkusertype = '';

    if ('Driver' === $reviewtype) {
        $chkusertype = 'Driver';
    } elseif ('Company' === $reviewtype) {
        $chkusertype = 'Company';
    } else {
        $chkusertype = 'Passenger';
    }

    if ('Driver' === $reviewtype) {
        $sql = "SELECT o.vOrderNo as `Order Number`, CONCAT(ru.vName,' ',ru.vLastName) as `From User Name`,CONCAT(rd.vName,' ',rd.vLastName) as `To Driver Name` ,rd.vAvgRating as AverageRate,r.vRating1 as Rate,r.tDate as `Date`,r.vMessage as Comment FROM ratings_user_driver as r LEFT JOIN orders as o ON r.iOrderId=o.iOrderId LEFT JOIN company as c ON c.iCompanyId=o.iCompanyId LEFT JOIN register_driver as rd ON rd.iDriverId=o.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=o.iUserId WHERE 1=1 AND r.eToUserType='".$chkusertype."' And ru.eStatus!='Deleted' {$ssql} {$adm_ssql} {$ord}";
    } elseif ('Company' === $reviewtype) {
        $store_txt = $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'];
        $sql = "SELECT o.vOrderNo as `Order Number`,CONCAT(ru.vName,' ',ru.vLastName) as `From User Name`,c.vCompany as `To {$store_txt} Name`,r.vRating1 as Rate,r.tDate as `Date`,c.vAvgRating as AverageRate,r.vMessage as Comment FROM ratings_user_driver as r LEFT JOIN orders as o ON r.iOrderId=o.iOrderId LEFT JOIN company as c ON c.iCompanyId=o.iCompanyId LEFT JOIN register_driver as rd ON rd.iDriverId=o.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=o.iUserId WHERE 1=1 AND r.eToUserType='".$chkusertype."' AND ru.eStatus!='Deleted' {$ssql} {$adm_ssql} {$ord}";
    } else {
        $sql = "SELECT o.vOrderNo as `Order Number`,CONCAT(rd.vName,' ',rd.vLastName) as `From Delivery Driver Name`,CONCAT(ru.vName,' ',ru.vLastName) as `To User Name`,ru.vAvgRating as AverageRate,vRating1 as Rate,r.tDate as `Date`,r.vMessage as Comment FROM ratings_user_driver as r LEFT JOIN orders as o ON r.iOrderId=o.iOrderId LEFT JOIN company as c ON c.iCompanyId=o.iCompanyId LEFT JOIN register_driver as rd ON rd.iDriverId=o.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=o.iUserId WHERE 1=1 AND r.eToUserType='".$chkusertype."' And ru.eStatus!='Deleted'  {$ssql} {$adm_ssql} {$ord}";
    }

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('RiderNumber' === $key) {
                    $val = clearName($val);
                }

                if ('Driver' === $reviewtype) {
                    if ('DriverName' === $key) {
                        $val = clearName($val);
                    }
                } else {
                    if ('RiderName' === $key) {
                        $val = clearName($val);
                    }
                }

                if ('AverageRate' === $key) {
                    $val = $val;
                }

                if ('Driver' === $reviewtype) {
                    if ('RiderName' === $key) {
                        $val = clearName($val);
                    }
                } else {
                    if ('DriverName' === $key) {
                        $val = clearName($val);
                    }
                }

                if ('Rate' === $key) {
                    $val = $val;
                }

                if ('Date' === $key) {
                    $val = DateTime($val);
                }

                if ('Comment' === $key) {
                    $val = $val;
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        if ('Driver' === $reviewtype) {
            $heading = ['RiderNumber', 'DriverName', 'AverageRate', 'RiderName', 'Rate', 'Date', 'Comment'];
        } else {
            $heading = ['RiderNumber', 'RiderName', 'AverageRate', 'DriverName', 'Rate', 'Date', 'Comment'];
        }

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Review');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('RiderNumber' === $column_heading) {
                $pdf->Cell(22, 10, $column_heading, 1);
            } elseif ('DriverName' === $column_heading) {
                $pdf->Cell(40, 10, $column_heading, 1);
            } elseif ('AverageRate' === $column_heading) {
                $pdf->Cell(21, 10, $column_heading, 1);
            } elseif ('RiderName' === $column_heading) {
                $pdf->Cell(25, 10, $column_heading, 1);
            } elseif ('Rate' === $column_heading) {
                $pdf->Cell(10, 10, $column_heading, 1);
            } elseif ('Date' === $column_heading) {
                $pdf->Cell(42, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                $values = $key;

                if ('DriverName' === $column) {
                    $values = clearName($key);
                }

                if ('Date' === $column) {
                    $values = DateTime($key);
                }

                DateTime($val);

                if ('RiderNumber' === $column) {
                    $pdf->Cell(22, 10, $values, 1);
                } elseif ('DriverName' === $column) {
                    $pdf->Cell(40, 10, $values, 1);
                } elseif ('AverageRate' === $column) {
                    $pdf->Cell(21, 10, $values, 1);
                } elseif ('RiderName' === $column) {
                    $pdf->Cell(25, 10, $values, 1);
                } elseif ('Rate' === $column) {
                    $pdf->Cell(10, 10, $values, 1);
                } elseif ('Date' === $column) {
                    $pdf->Cell(42, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// Cancel Reason

if ('cancel_reason' === $section) {
    $eType = isset($_REQUEST['eType']) ? stripslashes($_REQUEST['eType']) : '';

    $ord = ' ORDER BY vTitle ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vTitle ASC';
        } else {
            $ord = ' ORDER BY vTitle DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if (str_contains($option, 'eStatus')) {
                if ('' !== $eType) {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."' AND eType = '".$eType."' ";
                } else {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
                }
            } else {
                if ('' !== $eType) {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND eType = '".$eType."' ";
                } else {
                    $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
                }
            }
        } else {
            if ('' !== $eType) {
                $ssql .= ' AND ( vTitle_'.$default_lang." LIKE '%".$keyword."%') AND  eType='".$eType."'";
            } else {
                $ssql .= ' AND ( vTitle_'.$default_lang." LIKE '%".$keyword."%')";
            }
        }
    } elseif ('' !== $eType && '' === $keyword) {
        $ssql .= " AND eType = '".clean($eType)."'";
    }

    if ('eStatus' === $option) {
        $eStatussql = " AND eStatus = '".$keyword."'";
    } else {
        $eStatussql = " AND eStatus != 'Deleted'";
    }

    $sql = "SELECT vTitle_EN as Title, eType as `Service Type` ,eStatus as Status FROM cancel_reason where 1=1 {$eStatussql} {$ssql}";

    // filename for download

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->ExecuteQuery($sql) || exit('Query failed!');

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row

                echo implode("\t", array_keys($row))."\r\n";

                $flag = true;
            }

            array_walk($row, __NAMESPACE__.'\cleanData');

            echo implode("\t", array_values($row))."\r\n";
        }
    } else {
        $heading = ['Title', 'Service Type', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Cancel Reason');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Title' === $column_heading) {
                $pdf->Cell(100, 10, $column_heading, 1);
            } elseif ('Status' === $column_heading) {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else {
                $pdf->Cell(30, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ('Title' === $column) {
                    $pdf->Cell(100, 10, $key, 1);
                } elseif ('Status' === $column) {
                    $pdf->Cell(30, 10, $key, 1);
                } else {
                    $pdf->Cell(30, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// Cancel Reason

// Added By Hasmukh On 1-11-2018 For Set Common PDF Configuration Start

function manage_tcpdf($pageOrientation, $unit, $imagePath, $imageName, $pageType = 'P', $pageSize = 'A4')
{
    $pdf = new TCPDF($pageOrientation, $unit, 'Letter', true, 'UTF-8', false);

    $image_file = $imagePath.$imageName;

    // print_r($image_file);die;

    $pdf->AddPage($pageType, $pageSize);

    $pdf->Image($image_file, 90, 6, 30);

    $lg = [];

    $lg['a_meta_charset'] = 'UTF-8';

    $lg['a_meta_language'] = 'ar';

    // set some language-dependent strings (optional)

    $pdf->setLanguageArray($lg);

    $language = 'dejavusans';

    // $language = "Arial";

    $pdfName = time().'.pdf';

    return ['pdf' => $pdf, 'language' => $language, 'pdfName' => $pdfName];
}

// Added By Hasmukh On 1-11-2018 For Set Common PDF Configuration End

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 1 Start

if ('movement_report_before' === $section) {
    $ssql = '';

    $searchDriver = $_REQUEST['searchDriver'] ?? '';

    if ('' !== $searchDriver) {
        $ssql .= " AND t.iDriverId ='".$searchDriver."'";
    }

    if ('' !== $startDate) {
        $ssql .= " AND Date(tDate) >='".$startDate." 00:00:00'";
    }

    if ('' !== $endDate) {
        $ssql .= " AND Date(tDate) <='".$endDate." 23:59:59'";
    }

    $sql = "SELECT tl.*,rd.vName, rd.vLastName, t.vRideNo,t.iDriverId,t.fDistance,t.tStartDate AS dStartTime,t.tEndDate AS dEndTime,concat(rd.vName,' ',rd.vLastName) as Driver FROM trips_locations tl, register_driver as rd, trips as t WHERE  t.iDriverId = rd.iDriverId AND tl.iTripId = t.iTripId AND t.iActive = 'Active' {$ssql} ORDER BY iTripId DESC, iTripLocationId";

    $db_movement = $obj->MySQLSelect($sql);

    if ('XLS' === $type) {
        $filename = $section.'_'.date('Ymd').'.xls';

        $flag = false;

        $header .= 'Driver'."\t";

        $header .= 'Trip No.'."\t";

        $header .= 'Distance (Mile)'."\t";

        $header .= 'Type'."\t";

        $header .= 'Date'."\t";

        $header .= 'Total Time'."\t";

        $header .= 'Location'."\t";

        for ($i = 0; $i < count($db_movement); ++$i) {
            $tPlatitudes = explode(',', $db_movement[$i]['tPlatitudes']);

            $tPlongitudes = explode(',', $db_movement[$i]['tPlongitudes']);

            $lat = $tPlatitudes[0];

            $lng = $tPlongitudes[0];

            $address = getaddress($lat, $lng);

            if ($db_movement[$i]['fDistance'] > 0.1) {
                $fDistance = $db_movement[$i]['fDistance'];
            } else {
                $fDistance = round($db_movement[$i]['fDistance']);
            }

            $fDistance = getUnitToMiles($db_movement[$i]['fDistance'], 'Miles');

            $data_movement .= $db_movement[$i]['Driver']."\t";

            $data_movement .= $db_movement[$i]['vRideNo']."\t";

            $data_movement .= $fDistance."\t";

            $data_movement .= "Period 1 \t";

            $data_movement .= $db_movement[$i]['tDate']."\t";

            $time = TimeDifference($db_movement[$i]['dStartTime'], $db_movement[$i]['dEndTime']);

            $data_movement .= $time."\t";

            if ($address) {
                $data_movement .= $address;
            } else {
                $data_movement .= '--';
            }

            $data_movement .= "\n";
        }

        $data_movement = str_replace("\r", '', $data_movement);

        // echo "<pre>";print_r($header);die;

        ob_clean();

        header('Content-type: application/octet-stream');

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Pragma: no-cache');

        header('Expires: 0');

        echo "{$header}\n{$data_movement}";

        exit;
    }
}

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 1 End

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 2 Start

if ('movement_report_arriving' === $section) {
    $ssql = '';

    $searchDriver = $_REQUEST['searchDriver'] ?? '';

    if ('' !== $searchDriver) {
        $ssql .= " AND t.iDriverId ='".$searchDriver."'";
    }

    if ('' !== $startDate) {
        $ssql .= " AND Date(tDate) >='".$startDate." 00:00:00'";
    }

    if ('' !== $endDate) {
        $ssql .= " AND Date(tDate) <='".$endDate." 23:59:59'";
    }

    $sql = "SELECT tl.*,rd.vName, rd.vLastName, t.vRideNo,t.iDriverId,t.fDistance,t.tStartDate AS dStartTime,t.tEndDate AS dEndTime,concat(rd.vName,' ',rd.vLastName) as Driver FROM trips_locations tl, register_driver as rd, trips as t WHERE  t.iDriverId = rd.iDriverId AND tl.iTripId = t.iTripId AND t.iActive = 'Arrived' {$ssql} ORDER BY iTripId DESC, iTripLocationId";

    $db_movement = $obj->MySQLSelect($sql);

    if ('XLS' === $type) {
        $filename = $section.'_'.date('Ymd').'.xls';

        $flag = false;

        $header .= 'Driver'."\t";

        $header .= 'Trip No.'."\t";

        $header .= 'Distance (Mile)'."\t";

        $header .= 'Type'."\t";

        $header .= 'Date'."\t";

        $header .= 'Total Time'."\t";

        $header .= 'Location'."\t";

        for ($i = 0; $i < count($db_movement); ++$i) {
            $tPlatitudes = explode(',', $db_movement[$i]['tPlatitudes']);

            $tPlongitudes = explode(',', $db_movement[$i]['tPlongitudes']);

            $lat = $tPlatitudes[0];

            $lng = $tPlongitudes[0];

            $address = getaddress($lat, $lng);

            if ($db_movement[$i]['fDistance'] > 0.1) {
                $fDistance = $db_movement[$i]['fDistance'];
            } else {
                $fDistance = round($db_movement[$i]['fDistance']);
            }

            $fDistance = getUnitToMiles($db_movement[$i]['fDistance'], 'Miles');

            $data_movement .= $db_movement[$i]['Driver']."\t";

            $data_movement .= $db_movement[$i]['vRideNo']."\t";

            $data_movement .= $fDistance."\t";

            $data_movement .= "Period 2 \t";

            $data_movement .= $db_movement[$i]['tDate']."\t";

            $time = TimeDifference($db_movement[$i]['dStartTime'], $db_movement[$i]['dEndTime']);

            $data_movement .= $time."\t";

            if ($address) {
                $data_movement .= $address;
            } else {
                $data_movement .= '--';
            }

            $data_movement .= "\n";
        }

        $data_movement = str_replace("\r", '', $data_movement);

        // echo "<pre>";print_r($data_movement);die;

        ob_clean();

        header('Content-type: application/octet-stream');

        // header('Content-Type: text/html; charset=utf-8');

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        header('Pragma: no-cache');

        header('Expires: 0');

        echo "{$header}\n{$data_movement}";

        exit;
    }
}

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 2 End

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 3 Start

if ('movement_report_ontrip' === $section) {
    $ssql = '';

    $searchDriver = $_REQUEST['searchDriver'] ?? '';

    if ('' !== $searchDriver) {
        $ssql .= " AND tl.iDriverId ='".$searchDriver."'";
    }

    if ('' !== $startDate) {
        $ssql .= " AND Date(tDate) >='".$startDate." 00:00:00'";
    }

    if ('' !== $endDate) {
        $ssql .= " AND Date(tDate) <='".$endDate." 23:59:59'";
    }

    $sql = "SELECT tl.*,rd.vName, rd.vLastName, t.vRideNo,t.iDriverId,t.fDistance,t.tStartDate AS dStartTime,t.tEndDate AS dEndTime,concat(rd.vName,' ',rd.vLastName) as Driver FROM trips_locations tl, register_driver as rd, trips as t WHERE  t.iDriverId = rd.iDriverId AND tl.iTripId = t.iTripId AND t.iActive = 'On Going Trip' {$ssql} ORDER BY iTripId DESC, iTripLocationId";

    $db_movement = $obj->MySQLSelect($sql);

    if ('XLS' === $type) {
        $filename = $section.'_'.date('Ymd').'.xls';

        $flag = false;

        $header .= 'Driver'."\t";

        $header .= 'Trip No.'."\t";

        $header .= 'Distance (Mile)'."\t";

        $header .= 'Type'."\t";

        $header .= 'Date'."\t";

        $header .= 'Total Time'."\t";

        $header .= 'Location'."\t";

        for ($i = 0; $i < count($db_movement); ++$i) {
            $tPlatitudes = explode(',', $db_movement[$i]['tPlatitudes']);

            $tPlongitudes = explode(',', $db_movement[$i]['tPlongitudes']);

            $lat = $tPlatitudes[0];

            $lng = $tPlongitudes[0];

            $address = getaddress($lat, $lng);

            if ($db_movement[$i]['fDistance'] > 0.1) {
                $fDistance = $db_movement[$i]['fDistance'];
            } else {
                $fDistance = round($db_movement[$i]['fDistance']);
            }

            $fDistance = getUnitToMiles($db_movement[$i]['fDistance'], 'Miles');

            $data_movement .= $db_movement[$i]['Driver']."\t";

            $data_movement .= $db_movement[$i]['vRideNo']."\t";

            $data_movement .= $fDistance."\t";

            $data_movement .= "Period 3 \t";

            $data_movement .= $db_movement[$i]['tDate']."\t";

            $time = TimeDifference($db_movement[$i]['dStartTime'], $db_movement[$i]['dEndTime']);

            $data_movement .= $time."\t";

            if ($address) {
                $data_movement .= $address;
            } else {
                $data_movement .= '--';
            }

            $data_movement .= "\n";
        }

        $data_movement = str_replace("\r", '', $data_movement);

        // echo "<pre>";print_r($data_movement);die;

        ob_clean();

        header('Content-type: application/octet-stream');

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Pragma: no-cache');

        header('Expires: 0');

        echo "{$header}\n{$data_movement}";

        exit;
    }
}

// Added By Hasmukh On 11-12-2018 For Export Data of Movement Report For Period 3 End

// Added By Hasmukh On 12-12-2018 For Export Data of Advertisement Banners Start

if ('advertise_banners' === $section) {
    global $tconfig;

    $sub_cid = $_REQUEST['sub_cid'] ?? '';

    $ord = ' ORDER BY iDispOrder ASC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vBannerTitle ASC';
        } else {
            $ord = ' ORDER BY vBannerTitle DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ePosition ASC';
        } else {
            $ord = ' ORDER BY ePosition DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY iDispOrder ASC';
        } else {
            $ord = ' ORDER BY iDispOrder DESC';
        }
    }

    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND vBannerTitle LIKE '%".clean($keyword)."%') AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND vBannerTitle LIKE '%".clean($keyword)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    }

    $sql = "SELECT iAdvertBannerId AS SrNo,vBannerTitle AS Name,ePosition AS Position,iDispOrder AS DisplayOrder,concat(dStartDate,' To ',dExpiryDate) as TimePeriod,dAddedDate AS AddedDate,iImpression AS TotalImpression,eImpression AS UsedImpression,eStatus AS Status FROM advertise_banners as vc WHERE eStatus != 'Deleted' {$ssql} {$ord}";

    // filename for download

    $getUserCount = $obj->MySQLSelect('SELECT * FROM banner_impression WHERE iAdvertBannerId > 0');

    // echo "<pre>";

    $usedCountArr = [];

    for ($c = 0; $c < count($getUserCount); ++$c) {
        $bannerId = $getUserCount[$c]['iAdvertBannerId'];

        if (isset($usedCountArr[$bannerId]) && $usedCountArr[$bannerId] > 0) {
            ++$usedCountArr[$bannerId];
        } else {
            $usedCountArr[$bannerId] = 1;
        }
    }

    echo '<pre>';

    // print_r($usedCountArr);die;

    if ('XLS' === $type) {
        $filename = $timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query failed!');

        // print_r($result);die;

        echo implode("\t", array_keys($result[0]))."\r\n";

        $sr = 1;

        foreach ($result as $value) {
            $bannerUsedCount = '-----';

            $impressionCount = 'Unlimited';

            if (isset($usedCountArr[$value['SrNo']]) && $usedCountArr[$value['SrNo']] > 0 && 'Limited' === $value['UsedImpression']) {
                $bannerUsedCount = $usedCountArr[$value['SrNo']];

                $impressionCount = $value['TotalImpression'];
            }

            $value['UsedImpression'] = $bannerUsedCount;

            $value['TotalImpression'] = $impressionCount;

            $value['SrNo'] = $sr;

            // print_r($value);die;

            foreach ($value as $key => $val) {
                if ('Category' === $key) {
                    $val = clearName($val);
                }

                echo $val."\t";
            }

            echo "\r\n";

            ++$sr;
        }
    } else {
        $heading = ['SrNo#', 'Name', 'Position', 'Display Order', 'Time Period', 'Added Date', 'Total Impression', 'Used Impression', 'Status'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Name');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ('Position' === $column_heading || 'Status' === $column_heading) {
                $pdf->Cell(20, 10, $column_heading, 1);
            } elseif ('Display Order' === $column_heading || 'Name' === $column_heading || 'Added Date' === $column_heading) {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        // echo "<pre>";

        // print_r($heading);die;

        $sr = 1;

        foreach ($result as $row) {
            $pdf->Ln();

            unset($row['URL']);

            $bannerUsedCount = '-----';

            $impressionCount = 'Unlimited';

            if (isset($usedCountArr[$row['SrNo']]) && $usedCountArr[$row['SrNo']] > 0 && 'Limited' === $row['UsedImpression']) {
                $bannerUsedCount = $usedCountArr[$row['SrNo']];

                $impressionCount = $row['TotalImpression'];
            }

            $row['UsedImpression'] = $bannerUsedCount;

            $row['TotalImpression'] = $impressionCount;

            $row['SrNo'] = $sr;

            foreach ($row as $column => $key) {
                $values = $key;

                if ('Name' === $column) {
                    $values = clearName($key);
                }

                if ('Position' === $column || 'Status' === $column) {
                    $pdf->Cell(20, 10, $values, 1);
                } elseif ('DisplayOrder' === $column || 'Name' === $column || 'AddedDate' === $column) {
                    $pdf->Cell(35, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }

            ++$sr;
        }

        // print_r($pdf);die;

        $pdf->Output($pdfFileName, 'D');
    }
}

// Added By Hasmukh On 12-12-2018 For Export Data of Advertisement Banners End

// Added By Hasmukh On 14-12-2018 For Export Data of Newsletter Start

if ('newsletter' === $section) {
    $tbl_name = 'newsletter';

    $ord = ' ORDER BY iNewsLetterId DESC';

    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vName ASC';
        } else {
            $ord = ' ORDER BY vName DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY vEmail ASC';
        } else {
            $ord = ' ORDER BY vEmail DESC';
        }
    }

    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY eStatus ASC';
        } else {
            $ord = ' ORDER BY eStatus DESC';
        }
    }

    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tDate ASC';
        } else {
            $ord = ' ORDER BY tDate DESC';
        }
    }

    $ssql = " WHERE eStatus != 'Deleted'";

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            if ('' !== $eStatus) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword_new)."%' AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            if ('' !== $eStatus) {
                $ssql .= " AND (vName LIKE '%".$keyword_new."%'  OR vEmail LIKE '%".clean($keyword_new)."%') AND eStatus = '".clean($eStatus)."'";
            } else {
                $ssql .= " AND (vName LIKE '%".$keyword_new."%'  OR vEmail LIKE '%".clean($keyword_new)."%')";
            }
        }
    } elseif ('' !== $eStatus && '' === $keyword) {
        $ssql .= " AND eStatus = '".clean($eStatus)."'";
    }

    // added by SP for status on 28-06-2019

    $sql = 'SELECT vName AS Name,vEmail AS Email,eStatus as Status,tDate AS Date,vIP AS IP FROM '.$tbl_name." {$ssql} {$ord}";

    if ('XLS' === $type) {
        $filename = $tbl_name.'_'.$timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

        echo implode("\t", array_keys($result[0]))."\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('Name' === $key) {
                    $val = clearCmpName($val);
                }

                if ('Email' === $key) {
                    $val = clearEmail($val);
                }

                if ('Date' === $key) {
                    $val = DateTime($val, 'No');
                }

                echo $val."\t";
            }

            echo "\r\n";
        }
    } else {
        $heading = [$langage_lbl_admin['LBL_USER_NAME_HEADER_SLIDE_TXT'], $langage_lbl_admin['LBL_EMAIL_LBL_TXT'], $langage_lbl_admin['LBL_DATE_SIGNUP'], 'IP'];

        $result = $obj->ExecuteQuery($sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        $result = $resultset;

        $configPdf = manage_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, K_PATH_IMAGES, PDF_HEADER_LOGO);

        $pdf = $configPdf['pdf'];

        $language = $configPdf['language'];

        $pdfFileName = $file.$configPdf['pdfName'];

        // $pdf = new FPDF('P', 'mm', 'Letter');

        // $pdf->AddPage();

        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont($language, 'b', 15);

        $pdf->Cell(100, 16, 'Newsletter');

        $pdf->Ln();

        $pdf->SetFont($language, 'b', 9);

        $pdf->Ln();

        foreach ($heading as $column_heading) {
            if ($column_heading === $langage_lbl_admin['LBL_DATE_SIGNUP'] || $column_heading === $langage_lbl_admin['LBL_EMAIL_LBL_TXT']) {
                $pdf->Cell(55, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1);
            }
        }

        $pdf->SetFont($language, '', 9);

        foreach ($result as $row) {
            $pdf->Ln();

            foreach ($row as $column => $key) {
                if ($column === $langage_lbl_admin['LBL_DATE_SIGNUP']) {
                    $key = DateTime($key);

                    $pdf->Cell(55, 10, $key, 1);
                } if ($column === $langage_lbl_admin['LBL_EMAIL_LBL_TXT']) {
                    $key = clearEmail($key);

                    $pdf->Cell(55, 10, $key, 1);
                } if ($column === $langage_lbl_admin['LBL_USER_NAME_HEADER_SLIDE_TXT']) {
                    $key = clearName($key);

                    $pdf->Cell(40, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }

        $pdf->Output($pdfFileName, 'D');
    }
}

// Added By Hasmukh On 14-12-2018 For Export Data of Newsletter End

if ('driversubscription' === $section) {
    $sortby = $_REQUEST['sortby'] ?? 0;

    $order = $_REQUEST['order'] ?? '';

    $ord = ' ORDER BY iDriverSubscriptionPlanId DESC';

    $option = $_REQUEST['option'] ?? '';

    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

    $searchDriver = isset($_REQUEST['searchDriver']) ? stripslashes($_REQUEST['searchDriver']) : '';

    $searchDate = $_REQUEST['searchDate'] ?? '';

    $eStatus = $_REQUEST['eStatus'] ?? '';

    $defaultDetails = $obj->MySQLSelect("SELECT * FROM `language_master` WHERE `eDefault` ='Yes' AND eStatus = 'Active'");

    // $currencySymbol = $obj->MySQLSelect("SELECT vSymbol FROM currency WHERE eDefault = 'Yes'")[0]['vSymbol'];

    $vcode = $defaultDetails[0]['vCode'];

    $currencySymbol = $defaultDetails[0]['vCurrencySymbol'];

    $ssql = '';

    if ('' !== $keyword) {
        $keyword_new = $keyword;

        $chracters = ['(', '+', ')'];

        $removespacekeyword = preg_replace('/\s+/', '', $keyword);

        $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));

        if (is_numeric($keyword_new)) {
            $keyword_new = $keyword_new;
        } else {
            $keyword_new = $keyword;
        }

        if ('' !== $option) {
            $option_new = $option;

            if ('providerName' === $option_new) {
                $ssql .= " AND (rd.vName LIKE '%".clean($keyword_new)."%' OR rd.vLastName LIKE '%".clean($keyword_new)."%' OR CONCAT( vName,  ' ', vLastName ) LIKE  '%".clean($keyword_new)."%' )";
            } else {
                $ssql .= ' AND d.'.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            }
        } else {
            $ssql .= " AND (d.vPlanName LIKE '%".clean($keyword_new)."%' OR d.ePlanValidity LIKE '%".clean($keyword_new)."%')";

            $ssql .= " OR (rd.vName LIKE '%".clean($keyword_new)."%' OR rd.vLastName LIKE '%".clean($keyword_new)."%' OR CONCAT( vName,  ' ', vLastName ) LIKE  '%".clean($keyword_new)."%')";
        }
    }

    if ('' !== $searchDriver) {
        $ssql .= " AND rd.iDriverId = {$searchDriver}";
    }

    // End Search Parameters

    // Pagination Start

    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

    $tblPlan = 'driver_subscription_plan';

    $tblDetails = 'driver_subscription_details';

    // $getField = "eSubscriptionStatus, p.vPlanName, p.vPlanDescription,p.vPlanPeriod,p.ePlanValidity,CONCAT('$currencySymbol',p.fPrice) as fPlanPrice,d.tSubscribeDate,d.tExpiryDate,IFNULL(DATEDIFF(d.tExpiryDate,CURDATE()),'0') AS planLeftDays, d.iDriverId,CONCAT(rd.vName,' ',rd.vLastName) as name";

    $getField = "d.eSubscriptionStatus, d.vPlanName, d.vPlanDescription,d.vPlanPeriod,d.ePlanValidity,d.fPrice as fPlanPrice,d.tSubscribeDate,d.tExpiryDate,d.tClosedDate,IFNULL(DATEDIFF(d.tExpiryDate,CURDATE()),'0') AS planLeftDays,d.tSubscribeDate, d.iDriverId,CONCAT(rd.vName,' ',rd.vLastName) as name";

    // $sql = "SELECT $getField FROM $tblDetails d INNER JOIN $tblPlan p ON d.iDriverSubscriptionPlanId = p.iDriverSubscriptionPlanId  LEFT JOIN register_driver rd ON rd.iDriverId=d.iDriverId WHERE 1 $ssql ORDER BY d.tSubscribeDate DESC, d.tExpiryDate DESC";

    $sql = "SELECT {$getField} FROM {$tblDetails} d LEFT JOIN register_driver rd ON rd.iDriverId=d.iDriverId WHERE 1 {$ssql} ORDER BY d.tSubscribeDate DESC, d.tExpiryDate DESC";

    if ('XLS' === $type) {
        $filename = $tblDetails.'_'.$timestamp_filename.'.xls';

        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        header('Content-Type: application/vnd.ms-excel');

        // echo "\xEF\xBB\xBF";

        $flag = false;

        $result = $obj->MySQLSelect($sql) || exit('Query Failed!');

        // echo implode("\t", array_keys($result[0])) . "\r\n";

        foreach ($result[0] as $key => $val) {
            if ('planLeftDays' === $key || 'iDriverId' === $key) { // $key == 'eSubscriptionStatus' ||
                continue;
            }

            echo $key."\t";
        }

        echo "\r\n";

        foreach ($result as $value) {
            foreach ($value as $key => $val) {
                if ('planLeftDays' === $key || 'iDriverId' === $key) { // $key == 'eSubscriptionStatus'
                    continue;
                }

                if ('vPlanDescription' === $key) {
                    $val = str_replace(',', '|', $val);
                }

                if ('vPlanName' === $key) {
                    $val = str_replace(',', '|', $val);
                }

                if ('name' === $key) {
                    $val = clearName(' '.$val);
                }

                if ('fPlanPrice' === $key) {
                    $val = formateNumAsPerCurrency($val, '');
                }

                if ('vPlanPeriod' === $key) {
                    if ('Weekly' === $val) {
                        $val = $langage_lbl_admin['LBL_SUB_WEEKS'];
                    }

                    if ('Monthly' === $val) {
                        $val = $langage_lbl_admin['LBL_SUB_MONTH'];
                    }
                }

                /*if ($key == 'Subscribed Date') {

                    $val = DateTime($val);

                }*/

                echo $val."\t";
            }

            echo "\r\n";
        }
    }
}
