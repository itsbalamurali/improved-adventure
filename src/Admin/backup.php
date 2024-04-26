<?php
include_once '../common.php';
ini_set('memory_limit', '-1');
set_time_limit(0);

if (!$userObj->hasPermission('view-db-backup')) {
    $userObj->redirect();
}
$script = 'Back-up';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY iBackupId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vFile ASC';
    } else {
        $ord = ' ORDER BY vFile DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY dDate ASC';
    } else {
        $ord = ' ORDER BY dDate DESC';
    }
}

if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eType ASC';
    } else {
        $ord = ' ORDER BY eType DESC';
    }
}
// End Sorting

$adm_ssql = '';
// if (SITE_TYPE == 'Demo') {
// $adm_ssql = " And ad.tRegistrationDate > '" . WEEK_DATE . "'";
// }

$method = $_POST['method'] ?? '';
if (SITE_TYPE !== 'Demo') {
    $conn_vars = $obj->GetConnection();
    if ('' !== $method && 'backupNow' === $method) {
        $tables = [];
        $result = mysqli_query($conn_vars, 'SHOW TABLES');
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        $return = '';
        foreach ($tables as $table) {
            $result = mysqli_query($conn_vars, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);

            $return .= 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = mysqli_fetch_row(mysqli_query($conn_vars, 'SHOW CREATE TABLE '.$table));
            $return .= "\n\n".$row2[1].";\n\n";

            for ($i = 0; $i < $num_fields; ++$i) {
                while ($row = mysqli_fetch_row($result)) {
                    $return .= 'INSERT INTO '.$table.' VALUES(';
                    for ($j = 0; $j < $num_fields; ++$j) {
                        $row[$j] = addslashes($row[$j]);
                        // $row[$j] = ereg_replace("\n", "\\n", $row[$j]);
                        $row[$j] = preg_replace("/\n/", '\\n', $row[$j]);
                        if (isset($row[$j])) {
                            $return .= '"'.$row[$j].'"';
                        } else {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
            }
            $return .= "\n\n\n";
        }
        // save file
        $backuppath = $tconfig['tsite_upload_files_db_backup_path'];
        $filename = 'backup_'.date('Y_m_d').'_'.date('H_i').'.sql';
        $outputfilename = $backuppath.$filename;
        $handle = fopen($outputfilename, 'w+');
        /* $return = "";
          $return = "#######################";
          $return.="\n";
          $return.="//this is sample file. Original backup file is removed from here due to security reasons.";
          $return.="\n";
          $return.="#######################"; */
        fwrite($handle, $return);
        fclose($handle);

        $q = 'insert';
        $query = $q." `backup_database` SET
			`vFile` = '".$filename."',
			`eType` = 'Manual',
			`dDate` = '".date('Y-m-d h:i:s')."'";
        $id = $obj->sql_query($query);
        $_SESSION['success'] = 1;
        $_SESSION['var_msg'] = 'Database backup has been taken successfully completed.';
    }
} else {
    $_SESSION['success'] = 2;
    $_SESSION['var_msg'] = 'This feature has been disabled on the Demo Admin Panel. It will be enabled on the main script we will provide you.';
}

// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if (str_contains($option, 'dDate')) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        $ssql .= " AND (vFile LIKE '%".$keyword."%' OR dDate LIKE '%".$keyword."%' OR eType LIKE '%".$keyword."%')";
    }
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT count(iBackupId) as Total FROM backup_database WHERE 1=1 {$ssql} {$adm_ssql}";
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

$sql = "SELECT * FROM backup_database WHERE 1=1 {$ssql} {$adm_ssql} {$ord} LIMIT {$start}, {$per_page}";

$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}

$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Back-up</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once 'header.php'; ?>
            <?php include_once 'left_menu.php'; ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>DB Backup</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <div class="bkp001">
                        <div class="right_bkp001 bkp0011 bkpSelectTime2">

                            <span>
                                <!--	<form name="_backup_form" id="_backup_form" method="post" >
                                                <b><input type="checkbox" name="BACKUP_ENABLE" id="backupEn" <?php if ('Yes' === $backupEn) {
                                                    echo 'checked';
                                                } ?> value="Yes">&nbsp;&nbsp;&nbsp;Take schedule backup everyday at &nbsp;</b>
                                                <select class="form-control bkpSelectTime bkpSelectTime1" name="BACKUP_TIME">
                                <?php for ($i = 0; $i < 24; ++$i) { ?>
                                                                            <option value="<?php echo $i; ?>" <?php if ($backupTm === $i) {
                                                                                echo 'selected';
                                                                            } ?>><?php echo $i % 12 ? $i % 12 : 12; ?>:00 <?php echo $i >= 12 ? 'pm' : 'am'; ?></option>
                                <?php } ?>
                                                </select>
                                                <a href="javascript:void(0);" onClick="saveSchedule()" class="btn btn-success">Save</a>
                                        </form> -->
                                <?php if ($userObj->hasPermission('create-db-backup')) { ?>
                                    <form method="post" action="" class="but">
                                        <input type="hidden" name="method" value="backupNow">
                                        <div class="left_bkp001 bkp0011"><button type="submit" class="btn btn-primary">Take Back-up Now</button></div>
                                    </form>
                                <?php } ?>
                            </span>

                        </div>

                    </div>
                    <?php include 'valid_msg.php'; ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                            <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                            <option value="">All</option>
                                            <option value="vFile" <?php
                                            if ('vFile' === $option) {
                                                echo 'selected';
                                            }
?> >File Name</option>
                                            <option value="dDate" <?php
if ('dDate' === $option) {
    echo 'selected';
}
?> >Date</option>
                                            <option value="eType" <?php
if ('eType' === $option) {
    echo 'selected';
}
?> >Type</option>
                                        </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'backup.php'"/>
                                    </td><td width="15%"></td>
                                </tr>
                            </tbody>
                        </table>

                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-nir-export">
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>

                                                    <th width="25%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
            if ('1' === $sortby) {
                echo $order;
            } else {
                ?>0<?php } ?>)">File Name <?php
                               if (1 === $sortby) {
                                   if (0 === $order) {
                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                       }
                               } else {
                                   ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="20%" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                        if ('2' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Date <?php
                                                                           if (2 === $sortby) {
                                                                               if (0 === $order) {
                                                                                   ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                                   }
                                                                           } else {
                                                                               ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="10%" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                        if ('3' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Type<?php
                                                                           if (3 === $sortby) {
                                                                               if (0 === $order) {
                                                                                   ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                                   }
                                                                           } else {
                                                                               ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                            <?php if ($userObj->hasPermission('download-db-backup')) { ?>
                                                        <th width="10%" style="text-align:center;" >Download</th>
                                                    <?php } ?>
													  <?php if ($userObj->hasPermission('delete-db-backup')) { ?>   <th width="8%" style="text-align:center;" >Action</th>   <?php } ?>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); ++$i) {
                                                        $default = '';
                                                        if (isset($data_drv[$i]['eDefault']) && 'Yes' === $data_drv[$i]['eDefault']) {
                                                            $default = 'disabled';
                                                        }
                                                        ?>
                                                        <tr class="gradeA">
                                                            <td><?php echo clearEmail($data_drv[$i]['vFile']); ?></td>
                                                            <td align="center"><?php echo $data_drv[$i]['dDate']; ?></td>
                                                            <td align="center"><?php echo $data_drv[$i]['eType']; ?></td>
                                                            <?php if (SITE_TYPE !== 'Demo') { ?>
                                                                <?php if ($userObj->hasPermission('download-db-backup')) { ?>
                                                                    <td align='center'><a href="download_file.php?file=<?php echo $data_drv[$i]['vFile']; ?>" target="_blank" ><img src="img/download.png" alt="Delete"></a></td>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if ($userObj->hasPermission('delete-db-backup')) { ?>
                                                                    <td align="center"><a href="javascript:void(0);" data-toggle="tooltip" title="You can not download in demo version."><img src="img/download.png" alt="Delete"></a></a></td>
                                                                <?php
                                                                }
                                                            }
                                                        ?>
        <?php if ($userObj->hasPermission('delete-db-backup')) { ?>
                                                                <td width="10%" align="center">
                                                                    <a href="javascript:void(0);" onClick="changeStatusDelete('<?php echo $data_drv[$i]['iBackupId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                        <img src="img/delete-icon.png" alt="Delete" >
                                                                    </a>


                                                                </td>
                                                        <?php } ?>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <tr class="gradeA">
                                                        <td colspan="7" align="center"> No Records Found.</td>
                                                    </tr>
<?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
<?php include 'pagination_n.php'; ?>
                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                    <div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li>
                                DB Backup feature will use for taking backup of database.
                            </li>
                            <li>
                                Click on "Take Back-up Now" for taking backup of current database. It will store SQL file to your server.
                            </li>
                            <li>
                                Admin can download it from "Download" button from the list.
                            </li>
                            <!--li>
                                    "Export by Search Data" will export only search result data in XLS or PDF format.
                            </li-->
                        </ul>
                    </div>

                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <form name="pageForm" id="pageForm" action="action/backup.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iBackupId" id="iMainId01" value="" >
            <input type="hidden" name="status" id="status01" value="" >
            <input type="hidden" name="statusVal" id="statusVal" value="" >
            <input type="hidden" name="option" value="<?php echo $option; ?>" >
            <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >
            <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
            <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
            <input type="hidden" name="method" id="method" value="" >
        </form>
        <?php
        include_once 'footer.php';
?>
        <script>

            function saveSchedule() {
                var formData = $("#_backup_form").serialize();

                // $.ajax({
                //     type: 'post',
                //     url: 'save_backup_schedule.php',
                //     data: formData,
                //     success: function (response) {
                //         window.location.href = "backup.php?success=3";
                //     },
                //     error: function (response) {

                //     }
                // });

                var ajaxData = {
                    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>save_backup_schedule.php',
                    'AJAX_DATA': formData,
                    'REQUEST_DATA_TYPE': 'html'
                };
                getDataFromAjaxCall(ajaxData, function(response) {
                    if(response.action == "1") {
                        window.location.href = "backup.php?success=3";
                    }
                    else {
                        console.log(response.result);
                    }
                });
            }


        </script>
        <script>

            $("#setAllCheck").on('click', function () {
                if ($(this).prop("checked")) {
                    jQuery("#_list_form input[type=checkbox]").each(function () {
                        if ($(this).attr('disabled') != 'disabled') {
                            this.checked = 'true';
                        }
                    });
                } else {
                    jQuery("#_list_form input[type=checkbox]").each(function () {
                        this.checked = '';
                    });
                }
            });

            $("#Search").on('click', function () {
                //$('html').addClass('loading');
                var action = $("#_list_form").attr('action');
                // alert(action);
                var formValus = $("#frmsearch").serialize();
                //                alert(action+formValus);
                window.location.href = action + "?" + formValus;
            });

            $('.entypo-export').click(function (e) {
                e.stopPropagation();
                var $this = $(this).parent().find('div');
                $(".openHoverAction-class div").not($this).removeClass('active');
                $this.toggleClass('active');
            });

            $(document).on("click", function (e) {
                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                    $(".show-moreOptions").removeClass("active");
                }
            });

        </script>
    </body>
    <!-- END BODY-->
</html>