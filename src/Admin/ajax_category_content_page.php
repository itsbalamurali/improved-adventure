<?php


function cardOfCategory($parent = 0, $sub = 0, $vcat = [])
{
    global $tconfig, $iLanguageMasId, $booking_ids;
    $disabled = $checkbooking = '';
    $disabled = 'disabled';
    if (in_array($vcat['iVehicleCategoryId'], $booking_ids, true)) {
        $checkbooking = 'checked';
    }
    $img = $tconfig['tsite_url'].'resizeImg.php?h=75&src='.$tconfig['tsite_upload_home_page_service_images'].'/'.$vcat['vHomepageLogoOurServices'];
    $vehicle_category_page = $tconfig['tsite_url_main_admin'].'vehicle_category_action.php?id='.$vcat['iVehicleCategoryId'].'&homepage=1';
    $adminurl = $tconfig['tsite_url_main_admin'].$vcat['adminurl'].'&id='.$iLanguageMasId;
    $videoConsultUrl = $tconfig['tsite_url_main_admin'].$vcat['adminvideoconsulturl'].'&id='.$iLanguageMasId;
    $data = '';
    $data .= ' <li>
        <div class="toggle-list-inner">
            <div class="toggle-combo">
                <label>
                    <div align="center">
                        <img src="'.$img.'" >
                    </div>
                    <div style="margin: 0 0 0 15px;">
                        <td>'.$vcat['vCatName'].'</td>
                    </div>
                </label>
                   <span style="display: none" onclick="showAlert()" class="toggle-switch">
                        <input style="z-index: -1;" type="checkbox"
                            id="statusbutton"
                            class="chk"
                            name="statusbutton"
                            value="1" '.$checkbooking.' '.$disabled.'>
                        <span class="toggle-base"></span>
                   </span>
            </div>
            <div class="check-combo">
                <label id="defaultText_246">
                    <ul>
                        <li class="entypo-twitter"
                            data-network="twitter">
                            <a target="_blank" href="'.$vehicle_category_page.'"
                               data-toggle="tooltip"
                               title="Edit">
                                <img src="img/edit-new.png"
                                     alt="Edit">
                            </a>
                        </li>
                        <li class="entypo-twitter"
                            data-network="twitter">
                            <a target="_blank" href="'.$adminurl.'"
                               data-toggle="tooltip"
                               title="Edit Inner Page">
                                <img src="img/edit-doc.png"
                                     alt="Edit">
                            </a>
                        </li>';
    if ('Yes' === $vcat['eSubVideoConsultEnable']) {
        $data .= '<li class="entypo-twitter"
                        data-network="twitter">
                        <a  target="_blank" href="'.$videoConsultUrl.'"
                           data-toggle="tooltip"
                           title="Edit Video Consult Page">
                            <img src="img/live-line.png"
                                 alt="Edit">


                        </a>
                    </li>';
    }
    $data .= '</ul>
                    <div class="medical-service-note">
                    </div>
                </label>
            </div>
        </div>
      </li>';

    return $data;
}
function masterService(): void
{
    global $parentCat,$booking_ids;
    if (isset($parentCat) && !empty($parentCat)) {
        $medicalServiceHtml = '';
        foreach ($parentCat as $cat) {
            $display_order = $cat['iDisplayOrderHomepage'];
            $checked = '';
            if (in_array($cat['iVehicleCategoryId'], $booking_ids, true)) {
                $checked = 'checked';
            }
            $select_options = '';
            for ($i = 1; $i <= count($parentCat); ++$i) {
                $select_options .= '<option value="'.$cat['iVehicleCategoryId'].'-'.$i.'" '.($i === $display_order ? 'selected' : '').'>'.$i.'</option>';
            }
            $medicalServiceHtml .= '<tr><td>'.$cat['vCatName'].'</td><td><select class="form-control" name="ms_display_order[]" >'.$select_options.'</select></td><td><div class="meds-action"><div class="make-switch" data-on="success" data-off="warning"><input type="checkbox" name="iVehicleCategoryId[]" value="'.$cat['iVehicleCategoryId'].'" '.$checked.' /></div></div></td>';
        }

        echo $medicalServiceHtml;

        exit;
    }
}

function subService(): void
{
    global $subCat,$booking_ids;
    if (isset($subCat) && !empty($subCat)) {
        $medicalServiceHtml = '';
        foreach ($subCat as $cat) {
            $display_order = $cat['iDisplayOrderHomepage'];
            $checked = '';
            if (in_array($cat['iVehicleCategoryId'], $booking_ids, true)) {
                $checked = 'checked';
            }
            $select_options = '';
            for ($i = 1; $i <= count($subCat); ++$i) {
                $select_options .= '<option value="'.$cat['iVehicleCategoryId'].'-'.$i.'" '.($i === $display_order ? 'selected' : '').'>'.$i.'</option>';
            }
            $medicalServiceHtml .= '<tr><td>'.$cat['vCatName'].'</td><td><select class="form-control" name="ms_display_order[]" >'.$select_options.'</select></td><td><div class="meds-action"><div class="make-switch" data-on="success" data-off="warning"><input type="checkbox" name="iVehicleCategoryId[]" value="'.$cat['iVehicleCategoryId'].'" '.$checked.' /></div></div></td>';
        }
        echo $medicalServiceHtml;

        exit;
    }
}

function vehicleCategoryDisplayTOHomePage(): void
{
    global $tbl_name,$obj,$vCode,$booking_ids;

    $sql_vehicle_category_table_name = getVehicleCategoryTblName();
    $iVehicleCategoryIdArr = $_POST['iVehicleCategoryIdArr'] ?? '';
    $iVehicleCategoryIdRemoveArr = $_POST['iVehicleCategoryIdRemoveArr'] ?? '';
    $iDisplayOrderArr = $_POST['iDisplayOrderArr'] ?? '';

    if (isset($iVehicleCategoryIdRemoveArr) && !empty($iVehicleCategoryIdRemoveArr)) {
        $iVehicleCategoryIdRemoveArr = explode(',', $iVehicleCategoryIdRemoveArr);
        $booking_ids = array_diff($booking_ids, $iVehicleCategoryIdRemoveArr);
    }

    if (!empty($iVehicleCategoryIdArr)) {
        $iVehicleCategoryIdArr = explode(',', $iVehicleCategoryIdArr);
        $booking_ids = array_unique(array_merge($booking_ids, $iVehicleCategoryIdArr), SORT_REGULAR);
    }
    $booking_ids = implode(',', array_filter($booking_ids));
    $where = " vCode = '".$vCode."'";
    $Update['booking_ids'] = $booking_ids;
    $obj->MySQLQueryPerform($tbl_name, $Update, 'update', $where);

    if (!empty($iDisplayOrderArr)) {
        $iDisplayOrderArr = explode(',', $iDisplayOrderArr);
        $query = "UPDATE {$sql_vehicle_category_table_name} SET iDisplayOrderHomepage = (CASE iVehicleCategoryId  ";

        $ids = [];
        foreach ($iDisplayOrderArr as $iDisplayOrder) {
            $data = explode('-', $iDisplayOrder);
            $id = $data[0];
            $w = $data[1];
            if (isset($id) && !empty($id)) {
                $query .= "WHEN {$id} THEN {$w} ";
                $ids[] = $id;
            }
        }

        $ids = implode(',', $ids);
        $query .= "END) WHERE iVehicleCategoryId  IN({$ids});";
        $obj->sql_query($query);
    }

    $arrReturn['active'] = '1';
    echo json_encode($arrReturn);

    exit;
}
