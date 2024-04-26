<?php
include_once('../common.php');
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : ''; // iUniqueId
$parentid = isset($_REQUEST['parentid']) ? $_REQUEST['parentid'] : 0;
if (!$userObj->hasPermission('edit-places-nearby')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
$action = ($id != '') ? 'Edit' : 'Add';
$script = 'nearbyPlaces';
$tbl_name = "nearby_places";


$db_master = $obj->MySQLSelect("SELECT * FROM `language_master` ORDER BY `iDispOrder`");
$count_all = count($db_master);
$vTitle = isset($_POST['vTitle']) ? $_POST['vTitle'] : '';
$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : 'Inactive';
$vPlacesLocation = isset($_POST['vPlacesLocation']) ? $_POST['vPlacesLocation'] : '';
$vPlacesLocationLat = isset($_POST['vPlacesLocationLat']) ? $_POST['vPlacesLocationLat'] : '';
$vPlacesLocationLong = isset($_POST['vPlacesLocationLong']) ? $_POST['vPlacesLocationLong'] : '';
$vAddress = isset($_POST['vAddress']) ? $_POST['vAddress'] : '';
$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
$vOfferDiscount = isset($_POST['vOfferDiscount']) ? $_POST['vOfferDiscount'] : '';
$vAboutPlaces = isset($_POST['vAboutPlaces']) ? $_POST['vAboutPlaces'] : '';
$iCompanyId = isset($_POST['iCompanyId']) ? $_POST['iCompanyId'] : 0;
$iNearByCategoryId = isset($_POST['iNearByCategoryId']) ? $_POST['iNearByCategoryId'] : $_REQUEST['iNearByCategoryId'];
$vOfferDiscount = isset($_POST['vOfferDiscount']) ? $_POST['vOfferDiscount'] : '';
$iServiceId = isset($_POST['iServiceId']) ? $_POST['iServiceId'] : '';
$vCode = isset($_POST['vCountry']) ? $_POST['vCountry'] : $DEFAULT_COUNTRY_CODE_WEB;

$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

$thumb = new thumbnail();
/*--------------------- time slot ------------------*/
$timingArray = array(
    'vMonFromSlot',
    'vMonToSlot',
    'vTueFromSlot',
    'vTueToSlot',
    'vWedFromSlot',
    'vWedToSlot',
    'vThuFromSlot',
    'vThuToSlot',
    'vFriFromSlot',
    'vFriToSlot',
    'vSatFromSlot',
    'vSatToSlot',
    'vSunFromSlot',
    'vSunToSlot'
);
$orgtimingArray = array(
    'vMonFromSlot1',
    'vMonToSlot1',
    'vTueFromSlot1',
    'vTueToSlot1',
    'vWedFromSlot1',
    'vWedToSlot1',
    'vThuFromSlot1',
    'vThuToSlot1',
    'vFriFromSlot1',
    'vFriToSlot1',
    'vSatFromSlot1',
    'vSatToSlot1',
    'vSunFromSlot1',
    'vSunToSlot1',
    'vMonFromSlot2',
    'vMonToSlot2',
    'vTueFromSlot2',
    'vTueToSlot2',
    'vWedFromSlot2',
    'vWedToSlot2',
    'vThuFromSlot2',
    'vThuToSlot2',
    'vFriFromSlot2',
    'vFriToSlot2',
    'vSatFromSlot2',
    'vSatToSlot2',
    'vSunFromSlot2',
    'vSunToSlot2'
);
$sltAry = array(
    1,
    2
);
/*--------------------- time slot ------------------*/
if (isset($_POST['submit'])) {
    if (SITE_TYPE == 'Demo') {
        $_SESSION['success'] = 2;
        //header("Location:near_by_places.php");
        header("location:" . $backlink);
        exit;
    }

    if ($action == "Add" && !$userObj->hasPermission('create-places-nearby')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create Near by Places.';
        //header("Location:near_by_places.php");
        header("location:" . $backlink);
        exit;
    }

    if ($action == "Edit" && !$userObj->hasPermission('edit-places-nearby')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update Near by Places.';
        //header("Location:near_by_places.php");
        header("location:" . $backlink);
        exit;
    }

    $i = $iDisplayOrder;
    $temp_order = $_REQUEST['oldDisplayOrder'];
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order - 1; $i >= $iDisplayOrder; $i--) {
            $obj->sql_query("UPDATE rent_items_category SET iDisplayOrder = '" . ($i + 1) . "' WHERE iDisplayOrder = '" . $i . "' AND iParentId = '$parentid'");
        }
    } else if ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order + 1; $i <= $iDisplayOrder; $i++) {
            $obj->sql_query("UPDATE rent_items_category SET iDisplayOrder = '" . ($i - 1) . "' WHERE iDisplayOrder = '" . $i . "' AND iParentId = '$parentid'");
            $obj->sql_query($sql1);
        }
    }
    $image_object = $_FILES['vImage']['tmp_name'];
    $image_name = $_FILES['vImage']['name'];
    $image_update = "";
    if ($image_name != "") {
        $filecheck = basename($_FILES['vImage']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
            $flag_error = 1;
            $var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp";
        }
        $image_info = getimagesize($_FILES["vImage"]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if ($flag_error == 1) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header("Location:near_by_places_action.php");
            exit;
        } else {
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $Photo_Gallery_folder = $tconfig["tsite_upload_images_nearby_item_path"];
            $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
            $vImage = $img[0];
            $query_p['vImage'] = $vImage;
            if (!empty($_POST['vImage_old']) && file_exists($Photo_Gallery_folder . $_POST['vImage_old'])) {
                unlink($Photo_Gallery_folder . $_POST['vImage_old']);
            }
        }
    }
    foreach ($orgtimingArray as $time) {
        $query_pp[$time] = $_POST[$time];
    }
    if (empty($iCompanyId) || $action == "Add") {
        $query_p['vWorkingHours'] = json_encode($query_pp);
    }
    if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) {
        $query_p['iCompanyId'] = $iCompanyId;
        $query_p['iServiceId'] = $iServiceId;
    }
    for ($i = 0; $i < count($db_master); $i++) {
        $vAboutPlaces = "";
        if (isset($_POST['vAboutPlaces_' . $db_master[$i]['vCode']])) {
            $vAboutPlaces = $_POST['vAboutPlaces_' . $db_master[$i]['vCode']];
        }
        $vAboutPlacesArr["vAboutPlaces_" . $db_master[$i]['vCode']] = $vAboutPlaces;
    }
    $jsonAboutPlaces = getJsonFromAnArr($vAboutPlacesArr);
    $sql = "SELECT vPhoneCode FROM `country` WHERE vCountryCode = '" . $vCode . "'";
    $CountryData = $obj->MySQLSelect($sql);
    $query_p['iNearByCategoryId'] = $iNearByCategoryId;
    $query_p['vPlacesLocation'] = $vPlacesLocation;
    $query_p['vPlacesLocationLat'] = $vPlacesLocationLat;
    $query_p['vPlacesLocationLong'] = $vPlacesLocationLong;
    $query_p['vAddress'] = $vAddress;
    $query_p['vPhone'] = $vPhone;
    $query_p['vOfferDiscount'] = $vOfferDiscount;
    $query_p['vAboutPlaces'] = $jsonAboutPlaces;
    $query_p['eStatus'] = $eStatus;
    $query_p['vTitle'] = $vTitle;
    $query_p['vCountry'] = $vCode;
    $query_p['vCode'] = $CountryData[0]['vPhoneCode'];
    if ($id != '') {
        $where = " iNearByPlacesId = '$id'";
        $data = $obj->MySQLQueryPerform($tbl_name, $query_p, 'update', $where);
    } else {
        $obj->MySQLQueryPerform($tbl_name, $query_p, 'insert');
    }
    // $obj->sql_query($query);
    if ($id != '') {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }

    //header("Location:near_by_places.php");
    header("location:" . $backlink);
    exit();
}
// for Edit
$userEditDataArr = array();
$vDescriptionArr = array();
if ($action == 'Edit') {
    $NearByPlace = $NEARBY_OBJ->getNearByPlace('admin', $id);
    $vIconImage = $NearByPlace['vImage'];
    $eStatus = $NearByPlace['eStatus'];
    $iNearByPlacesId = $NearByPlace['iNearByPlacesId'];
    $iNearByCategoryId = $NearByPlace['iNearByCategoryId'];
    $vPlacesLocation = $NearByPlace['vPlacesLocation'];
    $vPlacesLocationLat = $NearByPlace['vPlacesLocationLat'];
    $vPlacesLocationLong = $NearByPlace['vPlacesLocationLong'];
    $vAddress = $NearByPlace['vAddress'];
    $vWorkingHours = json_decode($NearByPlace['vWorkingHours'], true);
    $vPhone = $NearByPlace['vPhone'];
    $vTitle = $NearByPlace['vTitle'];
    $userEditDataArr = $vAboutPlaces = json_decode($NearByPlace['vAboutPlacesOrg'], true);
    $vOfferDiscount = $NearByPlace['vOfferDiscount'];
    $iCompanyId = $NearByPlace['iCompanyId'];
    $iServiceId = $NearByPlace['iServiceId'];
    $Code = $NearByPlace['vCode'];
    $sql = "SELECT vCountryCode FROM `country` WHERE vPhoneCode = '" . $Code . "'";
    $CountryData = $obj->MySQLSelect($sql);
    $vCode = $CountryData[0]['vCountryCode'];
} else {
    $sql = "SELECT vPhoneCode FROM `country` WHERE vCountryCode = '" . $vCode . "'";
    $CountryData = $obj->MySQLSelect($sql);
    $Code = $CountryData[0]['vPhoneCode'];
}
$rentitem = $RENTITEM_OBJ->getRentItemMaster('admin');
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$maxDisplayOrderData = $obj->MySQLSelect("SELECT max(iDisplayOrder) as maxDisplayOrder FROM $tbl_name ");
$maxDisplayOrder = $maxDisplayOrderData[0]['maxDisplayOrder'];
if ($action == 'Add') {
    $maxDisplayOrder = $maxDisplayOrder + 1;
}
/*--------------------- near by category ------------------*/
$ssql = 'AND estatus = "Active"';
$categories = $NEARBY_OBJ->getNearByCategory('admin', $ssql);
/*--------------------- near by category ------------------*/
/*--------------------- store list ------------------*/
$storeList = $NEARBY_OBJ->getStore('admin', $id);
$selected_company_id = $storeList['iCompanyId'];
/*--------------------- store list ------------------*/
/*--------------------- service  ------------------*/
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
foreach ($allservice_cat_data as $k => $val) {
    $iServiceIdArr[] = $val['iServiceId'];
}
$serviceIds = implode(",", $iServiceIdArr);
$service_category = "SELECT iServiceId,vServiceName_" . $default_lang . " as servicename,eStatus FROM service_categories WHERE iServiceId IN (" . $serviceIds . ") AND eStatus = 'Active'";
$service_cat_list = $obj->MySQLSelect($service_category);
$sql = "SELECT * FROM country WHERE eStatus='Active' ORDER BY vCountry ASC";
$db_country = $obj->MySQLSelect($sql);
?>
<!DOCTYPE html><!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | NearBy Places <?= $action; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <? include_once('global_files.php'); ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<style>
    option:disabled {
        background-color: #ededed;
    }

    .form-select-21 {
        float: left;
        margin: -1px 0 0;
        padding: 7px 0;
        border: 1px solid #cdcdd3;
        border-radius: 0;
        background: #e6e6e9;
        width: 55px;
        text-align: center;
    }

    .storeService {
        border: 1px solid #cdcdd3;
        padding: 18px;
        margin-bottom: 20px;
    }
    .grouplabel{
        margin-right: -15px;
        margin-left: -15px;
    }
</style>
<!-- MAIN WRAPPER -->
<div id="wrap">
    <? include_once('header.php'); ?>

    <? include_once('left_menu.php'); ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2><?php echo $action; ?> NearBy Places</h2>
                    <a class="back_link"  href="near_by_places.php">
                        <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                </div>
            </div>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <? if ($success == 0 && $_REQUEST['var_msg'] != "") { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <? echo $_REQUEST['var_msg']; ?>
                        </div>
                        <br/>
                    <? } ?>

                    <? if ($success == 1) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                        </div>
                        <br/>
                    <? } ?>

                    <? if ($success == 2) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <br/>
                    <? } ?>
                    <form method="post" action="" enctype="multipart/form-data" id="nearBy_Places_action">
                        <input type="hidden" name="id" value="<?= $id; ?>"/>
                        <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                        <input type="hidden" name="backlink" id="backlink" value="near_by_places.php"/>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Name
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="text" class="form-control" id="vTitle" name="vTitle"
                                           value="<?php echo $vTitle; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Category
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <select class="form-control" name='iNearByCategoryId' id="iNearByCategoryId">
                                        <option value="">Select</option>
                                        <?php for ($i = 0; $i < count($categories); $i++) { ?>
                                            <option value="<?= $categories[$i]['iNearByCategoryId'] ?>"
                                                    <?php if ($iNearByCategoryId == $categories[$i]['iNearByCategoryId']) { ?>selected<?php } ?>><?= $categories[$i]['vTitle'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Address
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input value="<?php echo $vPlacesLocation; ?>" type="text" id="vPlacesLocation"
                                           class="form-control" name="vPlacesLocation" required>
                                </div>
                                <input type="hidden" name="vPlacesLocationLat" id="vPlacesLocationLat"
                                       value="<?php echo $vPlacesLocationLat; ?>">
                                <input type="hidden" name="vPlacesLocationLong" id="vPlacesLocationLong"
                                       value="<?php echo $vPlacesLocationLong; ?>">
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Exact Address
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input value="<?php echo $vAddress; ?>" type="text" id="vAddress"
                                           class="form-control" name="vAddress">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Country
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <?php
                                    if (count($db_country) > 1) {
                                        $style = "";
                                    } else {
                                        $style = " disabled=disabled";
                                    } ?>
                                    <select <?= $style ?> class="form-control" id='vCountry' name='vCountry'
                                                          onChange="changeCode(this.value);">
                                        <?php
                                        if (count($db_country) > 1) { ?>
                                            <option value="">Select</option>
                                        <?php } ?>
                                        <?php for ($i = 0; $i < count($db_country); $i++) { ?>
                                            <option value="<?= $db_country[$i]['vCountryCode'] ?>" <?php if ($DEFAULT_COUNTRY_CODE_WEB == $db_country[$i]['vCountryCode'] && $action == 'Add') { ?> selected <?php } else if ($vCode == $db_country[$i]['vCountryCode']) { ?>selected<?php } ?>><?= $db_country[$i]['vCountry'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Phone
                                        <span class="red"> *</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="text" class="form-select-2 form-select-21" id="code" readonly
                                           name="vPhoneCode" value="<?= $Code ?>">
                                    <input style="width: calc(100% - 55px);" value="<?php echo $vPhone; ?>" type="text" id="vPhone"
                                           class="mobile-text form-control form-select-3" name="vPhone">
                                </div>
                            </div>
                            <?php
                            if (count($service_cat_list) > 0) {
                                if (count($allservice_cat_data) <= 1) {
                                    ?>
                                    <input name="iServiceId" type="hidden" id="iServiceId" class="create-account-input"
                                           value="<?php echo $service_cat_list[0]['iServiceId']; ?>"/>
                                <?php } else { ?>
                                    <div class="grouplabel col-md-12 col-sm-12">
                                        <label>Choose Existing Store/Restaurant </label>
                                    </div>
                                    <div class="storeService">
                                    <div class="row">

                                        <div style="margin-bottom: 10px" class="col-md-12 col-sm-12">
                                            <span >Select below details if this place is already registered as a Store/Restaurant</span>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Service Type</label>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <select class="form-control" name='iServiceId' id="iServiceId"
                                                    onchange="changeserviceCategory(this.value)" id="iServiceId">
                                                <option value="">Select</option>
                                                <? for ($i = 0; $i < count($service_cat_list); $i++) { ?>
                                                    <option value="<?= $service_cat_list[$i]['iServiceId'] ?>" <? if ($iServiceId == $service_cat_list[$i]['iServiceId'] && $action == 'Add') { ?> selected <?php } else if ($iServiceId == $service_cat_list[$i]['iServiceId']) { ?>selected<? } ?>><?= $service_cat_list[$i]['servicename'] ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                               
                                <div id="sectionStore" class="row" style="display: none">
                                    <div class="col-lg-12">
                                        <label>Store</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select onchange="changestore(this.value)" class="form-control"
                                                name='iCompanyId' id="iStoreId"></select>
                                    </div>
                                </div>
                                </div>
                            <?php } 
                             
                            } ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Offers & Discount</label>
                                </div>
                                <div class="col-lg-12">
                                    <textarea id="vOfferDiscount" class="form-control"
                                              name="vOfferDiscount"><?php echo $vOfferDiscount; ?></textarea>
                                </div>
                            </div>
                            <?php if (count($db_master) > 0) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>About Place
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="<?= ($id != "") ? 'col-md-10 col-sm-10' : 'col-md-12 col-sm-12' ?>">
                                        <textarea class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                  name="vAboutPlaces_Default" id="vAboutPlaces_Default"
                                                  readonly="readonly"
                                                  data-originalvalue="<?= $vAboutPlaces['vAboutPlaces_' . $default_lang]; ?>" <?php if ($id == "") { ?> onclick="editAboutPlaceInfo('Add')" <?php } ?>> <?= $vAboutPlaces['vAboutPlaces_' . $default_lang]; ?></textarea>
                                    </div>
                                    <?php if ($id != "") { ?>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                    data-original-title="Edit"
                                                    onclick="editAboutPlaceInfo('Edit', 'vAboutPlaces_Modal')">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="modal fade" id="vAboutPlaces_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="modal_action"></span>
                                                    <label>About Place</label>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vAboutPlaces_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vAboutPlacesId = $vAboutPlaces = 'vAboutPlaces_' . $vCode;
                                                    $vAboutPlaces = $userEditDataArr[$vAboutPlaces];
                                                    $page_title_class = 'col-lg-12';
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>About Place (<?= $vLTitle ?>)</label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?> desc-block">
                                                            <textarea class="form-control"
                                                                      name="<?= $vAboutPlacesId; ?>"
                                                                      id="<?= $vAboutPlacesId; ?>"
                                                                      data-originalvalue="<?= $vAboutPlaces; ?>"
                                                                      placeholder="<?= $vLTitle; ?> Value"> <?= $vAboutPlaces; ?></textarea>
                                                            <div class="text-danger"
                                                                 id="<?= $vAboutPlacesId . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vAboutPlaces_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vAboutPlaces_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                </h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveAboutPlaceInfo()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vAboutPlaces_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>About Place
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <textarea class="form-control" name="vAboutPlaces_<?= $default_lang ?>"
                                                  id="vAboutPlaces_<?= $default_lang ?>"> <?= $vAboutPlaces['vAboutPlaces_' . $default_lang]; ?></textarea>
                                        <div class="text-danger" id="vAboutPlaces_<?= $default_lang ?>"
                                             style="display: none;">This field is required.
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <input type="hidden" name="vImage_old" value="<?= $vIconImage ?>">
                                <div class="col-lg-12">
                                    <label>Image</label>
                                </div>
                                <div class="col-lg-12">
                                    <? if ($vIconImage != '') { ?>
                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images_nearby_item'] . $vIconImage; ?>"
                                             style="height:150px">
                                        <input type="file" class="form-control" name="vImage" id="vImage" value=""/>
                                    <? } else { ?>
                                        <input type="file" class="form-control" name="vImage" id="vImage" value=""/>
                                    <? } ?>
                                    <br>
                                    <div>[Note: Recommended dimension for image is 2880 * 1620.]</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Status</label>
                                </div>
                                <div class="col-lg-6">
                                    <div class="make-switch" data-on="success" data-off="warning">
                                        <input type="checkbox"
                                               name="eStatus" <?= ($id != '' && $eStatus == 'Inactive') ? '' : 'checked'; ?>
                                               value="Active"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="submit" class="save btn-info" name="submit" id="submit"
                                           value="<?= $action . ' ' . 'NearBy Place'; ?>" style="margin-right: 10px">

                                        <a href="near_by_places.php?parentid=<?= $parentid; ?>"  class="btn btn-default back_link">
                                            Cancel
                                        </a>
                             
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <?php for ($j = 0; $j < count($sltAry); $j++) {
                                $sl1 = $sltAry[$j]; ?>
                                <div class="col-lg-12">
                                    <label>Working Hours
                                        <label>Slot <?= $sl1 ?> </label>
                                    </label>
                                </div>
                                <?php for ($i = 0; $i < count($timingArray); $i++) {
                                    $slotVarName1 = $timingArray[$i] . $sl1; ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class='col-lg-5'>
                                                <div class="form-group">
                                                    <span><?= substr($timingArray[$i], 1, -8) ?></span> <?php
                                                    if ($sl1 == 1) { ?>
                                                        <span class="red"> *</span><?php } ?>
                                                    <div id="<?= $slotVarName1 ?>_<?= $j + 1 ?>"
                                                         class='input-group date timepickerField timeslotnew'>
                                                        <?php
                                                        if ($sl1 == 1) {
                                                            $required = "required";
                                                        } else {
                                                            $required = "";
                                                        }
                                                        if ($vWorkingHours[$slotVarName1] == '00:00:00') {
                                                            $timedate = '';
                                                        } else {
                                                            $timedate = $vWorkingHours[$slotVarName1];
                                                        }
                                                        ?>
                                                        <input type='text' class="form-control slotTime"
                                                               name="<?= $slotVarName1 ?>" id="<?= $slotVarName1 ?>"
                                                               value="<?= $timedate ?>" <?= $required ?> />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span> </span>
                                                    </div>
                                                    <span class="FromError1"></span>
                                                </div>
                                            </div>
                                            <div class='col-lg-2' style="text-align: center;">
                                                <div style="font-weight: bold;">To</div>
                                            </div>
                                            <?php
                                            $i++;
                                            $slotVarName2 = $timingArray[$i] . $sl1;
                                            if ($vWorkingHours[$slotVarName2] == '00:00:00') {
                                                $timedate = '';
                                            } else {
                                                $timedate = $vWorkingHours[$slotVarName2];
                                            }
                                            ?>
                                            <div class='col-lg-5'>
                                                <div class="form-group">
                                                    <span><?= substr($timingArray[$i], 1, -6) ?></span>
                                                    <div id="<?= $slotVarName2 ?>_<?= $j + 1 ?>"
                                                         class='input-group date timepickerField timeslotnew'>
                                                        <input type='text' class="form-control TimeField"
                                                               id="<?= $slotVarName2 ?>" name="<?= $slotVarName2 ?>"
                                                               value="<?= $timedate; ?>"/>
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span> </span>
                                                    </div>
                                                    <span class="ToError1"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<? include_once('footer.php'); ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen"
      href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="css/select2/select2.min.css"/>
<script src="js/plugins/select2.min.js"></script>
<script type="text/javascript">
    var isDeliverAllFeatureAvailable = "<?php echo $MODULES_OBJ->isDeliverAllFeatureAvailable(); ?>";
    var iCompanyId = "<?php echo $iCompanyId; ?>";
    $(document).ready(function () {
        var referrer;
        if ($("#previousLink").val() == "") {
            referrer = document.referrer;
        } else {
            referrer = $("#previousLink").val();
        }
        if (referrer == "") {
            referrer = "store.php";
        } else {
            $("#backlink").val(referrer);
        }
        $(".back_link").attr('href', referrer);
    });
    function editCategoryName(action) {
        $('#modal_action').html(action);
        $('#Category_Modal').modal('show');
    }

    function saveCategoryName() {
        if ($('#vTitle_<?= $default_lang ?>').val() == "") {
            $('#vTitle_<?= $default_lang ?>_error').show();
            $('#vTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vTitle_Default').val($('#vTitle_<?= $default_lang ?>').val());
        $('#vTitle_Default').closest('.row').removeClass('has-error');
        $('#vTitle_Default-error').remove();
        $('#Category_Modal').modal('hide');
    }

    function editDescription(action) {
        $('#tDescriptionmodal_action').html(action);
        $('#tDescription_Modal').modal('show');
    }

    function saveDescription() {
        if ($('#tDescription_<?= $default_lang ?>').val() == "") {
            $('#tDescription_<?= $default_lang ?>_error').show();
            $('#tDescription_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#tDescription_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tDescription_Default').val($('#tDescription_<?= $default_lang ?>').val());
        $('#tDescription_Default').closest('.row').removeClass('has-error');
        $('#tDescription_Default-error').remove();
        $('#tDescription_Modal').modal('hide');
    }

    $('#iListMaxCount').keyup(function (e) {
        if (/\D/g.test(this.value)) {
            this.value = this.value.replace(/\D/g, '');
        }
    });
    $(document).ready(function () {
        var selected_u = false;
        $(function () {
            $('#vPlacesLocation').keyup(function (e) {
                selected_u = false;
                buildAutoComplete("vPlacesLocation", e, "<?= $MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE; ?>", "<?= $_SESSION['sess_lang']; ?>", function (latitude, longitude, address) {
                    $("#vPlacesLocationLat").val(latitude);
                    $("#vPlacesLocationLong").val(longitude);
                    selected_u = true;
                });
            });
        });
        $('#vPlacesLocation').on('focus', function () {
            if ($('#vPlacesLocationLat').val() == "" || $('#vPlacesLocationLong').val() == "") {
                selected_u = false;
            }
        }).on('blur', function () {
            setTimeout(function () {
                if (!selected_u) {
                    $('#vPlacesLocation').val('');
                    $('#vPlacesLocationLat').val('');
                    $('#vPlacesLocationLong').val('');
                }
            }, 500);
        });
        var iServiceId = '<?php echo $iServiceId; ?>';
        changeserviceCategory(iServiceId, 1);
    });
    /*$('#vMonFromSlot1, #vMonToSlot1, #vTueFromSlot1, #vTueToSlot1, #vWedFromSlot1, #vWedToSlot1, #vThuFromSlot1, #vThuToSlot1, #vFriFromSlot1, #vFriToSlot1, #vSatFromSlot1, #vSatToSlot1, #vSunFromSlot1, #vSunToSlot1, #vMonFromSlot2, #vMonToSlot2, #vTueFromSlot2, #vTueToSlot2, #vWedFromSlot2, #vWedToSlot2, #vThuFromSlot2, #vThuToSlot2, #vFriFromSlot2, #vFriToSlot2, #vSatFromSlot2, #vSatToSlot2, #vSunFromSlot2, #vSunToSlot2').datetimepicker({
        format: 'HH:mm A',
        ignoreReadonly: true,
    });*/

    /*$('#vMonFromSlot1_1, #vMonToSlot1_1, #vTueFromSlot1_1, #vTueToSlot1_1, #vWedFromSlot1_1, #vWedToSlot1_1, #vThuFromSlot1_1, #vThuToSlot1_1, #vFriFromSlot1_1, #vFriToSlot1_1, #vSatFromSlot1_1, #vSatToSlot1_1, #vSunFromSlot1_1, #vSunToSlot1_1, #vMonFromSlot2_2, #vMonToSlot2_2, #vTueFromSlot2_2, #vTueToSlot2_2, #vWedFromSlot2_2, #vWedToSlot2_2, #vThuFromSlot2_2, #vThuToSlot2_2, #vFriFromSlot2_2, #vFriToSlot2_2, #vSatFromSlot2_2, #vSatToSlot2_2, #vSunFromSlot2_2, #vSunToSlot2_2').datetimepicker({
       format: 'HH:mm A',
       ignoreReadonly: true,
   });*/


    $('.timepickerField').datetimepicker({
        format: 'HH:mm A',
        ignoreReadonly: true,
    });

    function changeserviceCategory(iServiceId, $ready = 0) {
        if (iServiceId > 0 && isDeliverAllFeatureAvailable == 1) {
            $("#iStoreId").empty().trigger('change');
            $(".slotTime,.TimeField").val('').removeAttr('disabled');
            getTheStore(iServiceId);
        } else {
            $("#iStoreId").empty().trigger('change');
            if ($ready == 0) {
                $(".slotTime,.TimeField").val('').removeAttr('disabled');
            }
            $('#sectionStore').hide();
        }
        return false;
        if (iServiceId > 0 && isDeliverAllFeatureAvailable == 1) {
            var iCompanyId = '<?php echo $iCompanyId; ?>';
            if (iCompanyId > 0) {
                $(".slotTime , .TimeField").val('')
                $(".slotTime , .TimeField").prop('disabled', false);
            }
            var selected_company_id = '<?php echo $selected_company_id; ?>';
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url_main_admin'] ?>ajax_get_nearby_company_filter.php',
                'AJAX_DATA': {
                    module: 'NearBy',
                    selected_company_id: selected_company_id,
                    iServiceIdNew: iServiceId,
                    iCompanyId: iCompanyId
                },
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    try {
                        var returnedData = JSON.parse(data);
                        $("#iCompanyId").html('');
                        $("#iCompanyId").html(returnedData.store_option_html);
                        $("#iCompanyId").trigger("change");
                    } catch (error) {
                        console.log(error);
                        //handle error -> visual feedback for user
                    }
                } else {
                    console.log(response.result);
                }
            });
        }
    }

    function changestore(iCompanyId) {
        var iServiceId = $('#iServiceId').val();
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url_main_admin'] ?>ajax_get_nearby_company_filter.php',
            'AJAX_DATA': {
                module: 'NearBy',
                iCompanyId: iCompanyId,
                iServiceIdNew: iServiceId,
                type: 'getCompanyDetails'
            },
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                var returnedData = JSON.parse(data);
                $.each(returnedData.company_data, function (key, value) {
                    $("#" + key).val(value);
                    $("#" + key).prop('disabled', true);
                });
            } else {
                console.log(response.result);
            }
        });
    }

    function changeCode(id) {
        if (id != '') {
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url_main_admin'] ?>change_code.php',
                'AJAX_DATA': 'id=' + id,
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                console.log('changeCode'.response);
                if (response.action == "1") {
                    var data = response.result;


                    document.getElementById("code").value = data;
                } else {
                    console.log(response.result);
                }
            });
        }
    }


    //changeCode('<?php //echo $vCode; ?>');

    // select 2 store
    function formatDesign(item) {
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        if (!item.id) {
            return item.text;
        }
        var selectionText = item.text.split("--");
        if (selectionText[2] != null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + "</br>" + selectionText[2] + '</span>');
        } else if (selectionText[2] == null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + '</span>');
        } else if (selectionText[2] != null && selectionText[1] == null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[2] + '</span>');
        }
        return $returnString;
    }


    function formatDesignnew(item) {

        if (!item.id) {
            return item.text;
        }
        var selectionText = item.text.split("--");
        return selectionText[0];

    }


    function getTheStore(iServiceId) {
        console.log(iServiceId);


        var iCompanyId = '<?php echo $iCompanyId; ?>';
        var selected_company_id = '<?php echo $selected_company_id; ?>';
        $('#sectionStore').show();


        $("#iStoreId").select2({
            allowClear: true,
            placeholder: $(this).attr('data-text'),
            templateResult: formatDesign,
            templateSelection: formatDesignnew,
            ajax: {
                url: 'ajax_get_nearby_company_filter.php',
                dataType: "json",
                type: "POST",
                async: true,
                delay: 250,
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        page: params.page || 1,
                        usertype: 'Store',
                        iServiceIdNew: iServiceId,
                        module: 'NearBy',
                        selected_company_id: selected_company_id,
                        iCompanyId: iCompanyId,
                        term: params.term,
                    }
                    return queryParameters;
                },
                processResults: function (data, params) {

                    data = data.db_company;
                    params.page = params.page || 1;
                    if (data.length < 10) {
                        var more = false;
                    } else {
                        var more = (params.page * 10) <= data[0].total_count;
                    }
                    $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
                    return {
                        results: $.map(data, function (item) {
                            if (item.Phoneno != '' && item.vEmail != '') {
                                var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                            } else if (item.Phoneno == '' && item.vEmail != '') {
                                var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                            } else if (item.Phoneno != '' && item.vEmail == '') {
                                var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                            }
                            return {
                                text: textdata,
                                id: item.id
                            }
                        }),
                        pagination: {
                            more: more
                        }
                    };
                },
                cache: false
            }
        }); //theme: 'classic'
    }


    var sSelectCompany = $('#iStoreId');
    var sIdCompany = '<?= $iCompanyId;?>';
    var iServiceId = '<?php echo $iServiceId; ?>';
    if (sIdCompany != '') {

        var ajaxData = {

            'URL': '<?= $tconfig['tsite_url_main_admin'] ?>ajax_get_nearby_company_filter.php?type=getCompanyDetails&iServiceIdNew=' + iServiceId + '&iCompanyId=' + sIdCompany + '&usertype=Store',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'

        };

        getDataFromAjaxCall(ajaxData, function (response) {

            if (response.action == "1") {

                var data = response.result;
                data = data.db_company;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelectCompany.append(option);
                changestore(sIdCompany);
            } else {
                console.log(response.result);
            }

        });

    }

    // select 2 store

    function editAboutPlaceInfo(action) {
        $('#modal_action').html(action);
        $('#vAboutPlaces_Modal').modal('show');
    }

    function saveAboutPlaceInfo() {
        if ($('#vAboutPlaces_<?= $default_lang ?>').val().trim() == "") {
            $('#vAboutPlaces_<?= $default_lang ?>_error').show();
            $('#vAboutPlaces_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vAboutPlaces_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vAboutPlaces_Default').val($('#vAboutPlaces_<?= $default_lang ?>').val());
        $('#vAboutPlaces_Default').closest('.row').removeClass('has-error');
        $('#vAboutPlaces_Default-error').remove();
        $('#vAboutPlaces_Modal').modal('hide');
    }
</script>
</body>
<!-- END BODY-->
</html>