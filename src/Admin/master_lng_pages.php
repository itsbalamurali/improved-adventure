<?php

include_once '../common.php';

$default_lang = $LANG_OBJ->FetchSystemDefaultLang();

if ($displayInhousePage <= 0) {
    $userObj->redirect();
}

if (!$userObj->hasPermission('view-general-label')) {
    $userObj->redirect();
}

$tbl_name = 'master_lng_pages';

$script = 'MasterLanguagePages';

$adm_ssql = '';

if (SITE_TYPE === 'Demo') {
    // $adm_ssql = " And ad.tRegistrationDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters

$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';

$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

$platformtype = isset($_REQUEST['platformtype']) ? stripslashes($_REQUEST['platformtype']) : '';

$apptype = isset($_REQUEST['apptype']) ? stripslashes($_REQUEST['apptype']) : '';

$success = $_REQUEST['success'] ?? 0;

$ssql = '';

if ('' !== $keyword) {
    if ('' !== $option) {
        if ('' !== $platformtype && '' === $apptype) {
            $ssql .= ' WHERE '.stripslashes($option)." LIKE '%".clean($keyword_new)."%' AND ePlatformType = '".clean($platformtype)."'";
        } elseif ('' === $platformtype && '' !== $apptype) {
            $ssql .= ' WHERE '.stripslashes($option)." LIKE '%".clean($keyword_new)."%' AND eFor = '".clean($apptype)."'";
        } else {
            $ssql .= ' WHERE '.stripslashes($option)." LIKE '%".clean($keyword_new)."%'";
        }
    } else {
        $ssql .= " WHERE vTitle LIKE '%".clean($keyword_new)."%' OR tFileName LIKE '%".clean($keyword_new)."%' OR tFilePath LIKE '%".clean($keyword_new)."%'";
    }
} elseif ('' !== $platformtype && '' === $apptype) {
    $ssql .= " WHERE ePlatformType = '".clean($platformtype)."'";
} elseif ('' === $platformtype && '' !== $apptype) {
    $ssql .= " WHERE eFor = '".clean($apptype)."'";
}

// End Search Parameters

$sql = 'SELECT * FROM '.$tbl_name." {$ssql}";

$data = $obj->MySQLSelect($sql);

?>

<!DOCTYPE html>

<html lang="en">

    <!-- BEGIN HEAD-->

    <head>

        <meta charset="UTF-8" />

        <title><?php echo $SITE_NAME; ?> | Language Label - Pages</title>

        <meta content="width=device-width, initial-scale=1.0" name="viewport" />

        <?php include_once 'global_files.php'; ?>

        <style type="text/css">

            .language-pages-section {

                margin: 30px 0;

            }



            .language-pages-section .card {

              box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.3);

              margin: 30px 0;

            }



            .language-pages-section .container {

              padding: 15px;
              min-height: 241px;

            }



            .language-pages-section .container::after, .language-pages-section .row::after {

              content: "";

              clear: both;

              display: table;

            }



            .language-pages-section .title {

                font-size: 28px;

            }



            .language-pages-section img {

                width: 100%;

                object-fit: cover;

                height: 190px

            }



            .full-width {

                width: 100%

            }



        </style>

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

                                <h2><?php echo $langage_lbl_admin['LBL_LANGUAGE_ADMIN']; ?></h2>

                            </div>

                        </div>

                        <hr />

                        <div class="clearfix"></div>

                    </div>

                    <?php include 'valid_msg.php'; ?>

                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table" >

                            <tbody>

                                <tr>

                                    <td width="3%"><label for="textfield"><strong>Search:</strong></label></td>

                                    <td width="7%" class=" padding-right10">

                                        <select name="option" id="option" class="form-control">

                                            <option value="">All</option>

                                            <option value="vTitle" <?php

                                                if ('vTitle' === $option) {
                                                    echo 'selected';
                                                }

?> >Title</option>

                                            <option value="tFileName" <?php

if ('tFileName' === $option) {
    echo 'selected';
}

?> >File Name</option>

                                            <option value="tFilePath" <?php

if ('tFilePath' === $option) {
    echo 'selected';
}

?> >File Path</option>

                                        </select>

                                    </td>

                                    <td width="10%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>

                                    <td width="10%" class=" padding-right10">

                                        <select name="platformtype" id="platformtype" class="form-control">

                                            <option value="">Select Platform Type</option>

                                            <option value="Web" <?php

if ('Web' === $platformtype) {
    echo 'selected';
}

?> >Web</option>

                                            <option value="App" <?php

if ('App' === $platformtype) {
    echo 'selected';
}

?> >App</option>

                                        </select>

                                    </td>

                                    <td width="10%" class=" padding-right10">

                                        <select name="apptype" id="apptype" class="form-control">

                                            <option value="">Select App Type</option>

                                            <option value="General" <?php

if ('General' === $apptype) {
    echo 'selected';
}

?> >General</option>

                                            <option value="Ride" <?php

if ('Ride' === $apptype) {
    echo 'selected';
}

?> >Ride</option>

                                            <option value="Delivery" <?php

if ('Delivery' === $apptype) {
    echo 'selected';
}

?> >Delivery</option>

                                            <option value="Ride,Delivery" <?php

if ('Ride,Delivery' === $apptype) {
    echo 'selected';
}

?> >Ride,Delivery</option>

                                            <option value="UberX" <?php

if ('UberX' === $apptype) {
    echo 'selected';
}

?> >UberX</option>

                                            <option value="Ride,Delivery,UberX" <?php

if ('Ride,Delivery,UberX' === $apptype) {
    echo 'Ride,Delivery,UberX';
}

?> >Ride,Delivery,UberX</option>

                                            <option value="Ride-Delivery-UberX" <?php

if ('Ride-Delivery-UberX' === $apptype) {
    echo 'selected';
}

?> >Ride-Delivery-UberX</option>

                                            <option value="Multi-Delivery" <?php

if ('Multi-Delivery' === $apptype) {
    echo 'selected';
}

?> >Multi-Delivery</option>

                                            <option value="DeliverAll" <?php

if ('DeliverAll' === $apptype) {
    echo 'selected';
}

?> >DeliverAll</option>

                                            <option value="Kiosk" <?php

if ('Kiosk' === $apptype) {
    echo 'selected';
}

?> >Kiosk</option>

                                            <option value="Fly" <?php

if ('Fly' === $apptype) {
    echo 'selected';
}

?> >Fly</option>

                                        </select>

                                    </td>

                                    <td width="10%">

                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />

                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'master_lng_pages.php'"/>

                                    </td>

                                    <td width="5%">

                                        <a class="add-btn" href="language_page_action.php">Add Page</a>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </form>
                    <div class="language-pages-section">
                        <div class="row">
                            <?php if (!empty($data)) {
                                // echo $tconfig['tsite_upload_images_lng_page'];
                                for ($i = 0; $i < count($data); ++$i) {
                                    $dataPageId = $data[$i]['iPageId'];
                                    $dataImage = $data[$i]['vImage'];
                                    $dataTitle = $data[$i]['vTitle'];
                                    $dataFileName = $data[$i]['tFileName'];
                                    $dataFilePath = $data[$i]['tFilePath'];
                                    $dataPlatformType = $data[$i]['ePlatformType'];
                                    $dataAppType = $data[$i]['eFor']; ?>
                                <div class="col-md-4">

                                    <div class="card">
                                        <?php $filePath = $tconfig['tsite_upload_images_lng_page_path'].$dataImage;
                                    if (('' !== $dataImage) && (null !== $dataImage) && ('None' !== $dataImage) && ('NULL' !== $dataImage && file_exists($filePath))) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=523&h=190&src='.$tconfig['tsite_upload_images_lng_page'].$dataImage; ?>" alt="">
                                        <?php } else { ?>
                                            <img src="../assets/img/placeholder-img.png" alt="">
                                        <?php } ?>
                                        <div class="container">
                                            <div class="datablock">
                                                <h2><?php echo $dataTitle; ?></h2>
                                                <ul>
                                                    <?php if (isset($_REQUEST['SHOW_FILE_NAME']) && $_REQUEST['SHOW_FILE_NAME'] > 0) { ?>
                                                        <li><strong>File Name: </strong><span><?php echo $dataFileName; ?></span></li>
                                                        <li><strong>File Path: </strong><span><?php echo $dataFilePath; ?></span></li>
                                                    <?php } ?>
                                                    <li><strong>Platform Type: </strong><span><?php echo $dataPlatformType; ?></span></li>
                                                    <li><strong>App Type: </strong><span><?php echo $dataAppType; ?></span></li>
                                                </ul>
                                                <a href="language_page_action.php?id=<?php echo $dataPageId; ?>" class="btn btn-primary full-width">Edit</a>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            <?php

                                }
                            }

?>

                        </div>

                    </div>

                    <div class="admin-notes">

                        <h4>Notes:</h4>

                        <ul>

                            <li>



                            </li>

                            <li>



                            </li>

                            <li>



                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            <!--END PAGE CONTENT -->

        </div>

        <!--END MAIN WRAPPER -->



        <?php include_once 'footer.php'; ?>

        <script></script>

    </body>

    <!-- END BODY-->

</html>