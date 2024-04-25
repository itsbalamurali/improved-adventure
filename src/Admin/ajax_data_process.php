<?php



/*
 * File Type : PHP
 * File Created On  : 19-06-2020
 * File Created By : HJ
 * Purpose : For Upload Item CSV file as Bulk Upload
 */
include_once '../common.php';

$returnArr = $skipItemSrArr = [];
$returnArr['action'] = 0;
$successMsg = 'Success';
$stepProcess = 'validate';
$validate = 'Yes';
if (isset($_POST['step'])) {
    $stepProcess = trim($_POST['step']);
}
if (isset($_POST['validate'])) {
    $validate = trim($_POST['validate']);
}
if (isset($_POST['itemSrSkip'])) {
    $itemSrSkip = trim($_POST['itemSrSkip']);
    if ('' !== $itemSrSkip) {
        $skipItemSrArr = explode(',', $itemSrSkip);
        // echo "<pre>";print_r($skipItemSrArr);die;
    }
}
$returnArr['step'] = $stepProcess;
$allLangCodeArr = [];
$langData = $obj->MySQLSelect("SELECT * FROM language_master WHERE eStatus='Active'");
for ($v = 0; $v < count($langData); ++$v) {
    $allLangCodeArr[] = $langData[$v]['vCode'];
}
if (in_array('EN', $allLangCodeArr, true)) {
    $default_lang_new = 'EN';
} else {
    $default_lang_new = $default_lang;
}
// echo "<pre>";print_r($langData);die;
// $default_lang_new = "EN";
// $default_lang_new = $default_lang;
if (isset($_POST['file'])) {
    $fileName = $_POST['file'];
    if (isset($_POST['iServiceId']) && $_POST['iServiceId'] > 0) {
        $serviceId = $_POST['iServiceId'];
    } else {
        $returnArr['action'] = 8;
        $returnArr['message'] = $langage_lbl_admin['LBL_SELECT_SERVICE_CATEGORY_TXT'];
        echo json_encode($returnArr);

        exit;
    }
    if (isset($_POST['iCompanyId']) && $_POST['iCompanyId'] > 0) {
        $companyId = $_POST['iCompanyId'];
    } else {
        $returnArr['action'] = 9;
        $returnArr['message'] = $langage_lbl_admin['LBL_SELECT_STORE_TXT'];
        echo json_encode($returnArr);

        exit;
    }
    if ('' !== trim($fileName)) {
        // echo "<pre>";print_r($uploadFile);die;
        // Get Data For Item Process Start
        // $langData = $obj->MySQLSelect("SELECT * FROM language_master WHERE eStatus='Active'");

        $header = ['SR', 'SKU', 'ITEM_NAME'];
        for ($l = 0; $l < count($langData); ++$l) {
            $header[] = 'ITEM_NAME_'.$langData[$l]['vCode'];
        }
        $header[] = 'ITEM_DESC';
        for ($l = 0; $l < count($langData); ++$l) {
            $header[] = 'ITEM_DESC_'.$langData[$l]['vCode'];
        }
        $header[] = 'ITEM_CATEGORY';
        for ($l = 0; $l < count($langData); ++$l) {
            $header[] = 'ITEM_CATEGORY_'.$langData[$l]['vCode'];
        }
        $otherHeader = ['IMAGE_URL', 'ITEM_PRICE', 'ITEM_TYPE', 'OFFER_PER', 'IS_AVAILABLE', 'IS_RECOMMENDED', 'IS_ACTIVE', 'DISPLAY_ORDER'];
        if (ENABLE_PRESCRIPTION_UPLOAD === 'Yes' && '5' === $serviceId) {
            $otherHeader[] = 'PRESCRIPTION_REQUIRED';
        }
        $finalHeader = array_merge($header, $otherHeader);
        $getStoreCategory = $obj->MySQLSelect('SELECT iCompanyId,iFoodMenuId,vMenu_'.$default_lang_new." AS CATNAME FROM food_menu WHERE eStatus!='Deleted' AND iCompanyId='".$companyId."'");
        $storeCatArr = $itemNameArr = $menuItemArr = $catNameArr = $cuisineArr = $serviceArr = $foodMenuIdArr = $foodMenuItemNameArr = [];
        for ($k = 0; $k < count($getStoreCategory); ++$k) {
            $storeCatArr[$getStoreCategory[$k]['iCompanyId']][] = $getStoreCategory[$k]['iFoodMenuId'];
            $catNameArr[$getStoreCategory[$k]['iCompanyId']][strtolower($getStoreCategory[$k]['CATNAME'])] = $getStoreCategory[$k]['iFoodMenuId'];
            if (!in_array($getStoreCategory[$k]['iFoodMenuId'], $foodMenuIdArr, true)) {
                $foodMenuIdArr[] = $getStoreCategory[$k]['iFoodMenuId'];
            }
        }
        $whereFoodMenuId = '';
        if (count($foodMenuIdArr) > 0) {
            $foodMenuIds = implode(',', $foodMenuIdArr);
            $whereFoodMenuId = " AND iFoodMenuId IN ({$foodMenuIds})";
        }
        $getItemData = $obj->MySQLSelect('SELECT iMenuItemId,iFoodMenuId,vItemType_'.$default_lang_new." AS ITEM_NAME FROM menu_items WHERE eStatus!='Deleted' {$whereFoodMenuId}");
        for ($t = 0; $t < count($getItemData); ++$t) {
            $menuItemArr[$getItemData[$t]['iFoodMenuId']][strtolower($getItemData[$t]['ITEM_NAME'])] = $getItemData[$t]['iMenuItemId'];
            $foodMenuItemNameArr[$getItemData[$t]['iFoodMenuId']][] = strtolower($getItemData[$t]['ITEM_NAME']);
        }
        // Get Data For Item Process End
        // echo "<pre>";print_r($foodMenuItemNameArr);die;
        // Start Code For Item File Data
        $file1 = fopen($fileName, 'r');
        $tmpArr1 = $tmpArr2 = $tableDataArr1 = $tableDataArr2 = $tableFieldArr = $newHeadArr = [];
        while (($data1 = fgetcsv($file1, 0, ',')) !== false) {
            $tmpArr1[] = $data1;
        }
        $tmpArr1 = array_map('array_filter', $tmpArr1);
        $tmpArr1 = array_values(array_filter($tmpArr1));

        $headerArr = $tmpArr1[0];
        for ($v = 0; $v < count($headerArr); ++$v) {
            $newHeadArr[] = trim($headerArr[$v]);
        }
        // $resultDiff =array_diff($finalHeader,$newHeadArr);
        $resultDiff = array_udiff($finalHeader, $newHeadArr, 'strcasecmp');
        // print_r($resultDiff);die;
        if (in_array('SR', $resultDiff, true) && 1 === count($resultDiff)) {
            // SR Not Chekced as Per discuss with KS ON 26-08-2020
        } else {
            if (count($resultDiff) > 0) {
                $returnArr['action'] = 9;
                $returnArr['message'] = $langage_lbl_admin['LBL_UPLOAD_EXPORTED_CSV_FILE_TXT'];
                echo json_encode($returnArr);

                exit;
            }
        }
        for ($f = 0; $f < count($headerArr); ++$f) {
            $tableFieldArr[$headerArr[$f]] = $f;
        }
        // echo "<pre>";print_r($tableFieldArr);die;
        unset($tmpArr1[0]);
        $tmpArr1 = array_values($tmpArr1);
        for ($i = 0; $i < count($tmpArr1); ++$i) {
            $tableName1 = $tmpArr1[$i][0];
            $tableDataArr1[$tableName1][] = $tmpArr1[$i];
        }

        // End Code For Item File Data
        // echo "<pre>";print_r($tableDataArr1);die;
        /*for ($h = 1; $h <= count($tableDataArr1); $h++) {
            $itemData1 = $tableDataArr1[$h];
            $newItemData = array();
            for ($m = 0; $m < count($itemData1); $m++) {
                $newDataArr = array();
                foreach ($itemData1[$m] as $key => $val) {
                    $newDataArr[trim($headerArr[$key])] = trim($val);
                }
                $newItemData[$m] = $newDataArr;
            }
            $tableDataArr2[$h] = $newItemData;
        }*/
        $h = 1;
        foreach ($tableDataArr1 as $key1 => $itemData1) {
            // $itemData1 = $tableDataArr1[$h];
            // echo "<pre>";print_r($itemData1);die;
            $newItemData = [];
            for ($m = 0; $m < count($itemData1); ++$m) {
                $newDataArr = [];
                foreach ($itemData1[$m] as $key => $val) {
                    $newDataArr[trim($headerArr[$key])] = trim($val);
                }
                $newItemData[$m] = $newDataArr;
            }
            $tableDataArr2[$h] = $newItemData;
            ++$h;
        }
        // echo "<pre>";print_r($tableDataArr2);die;
        for ($i = 1; $i <= count($tableDataArr2); ++$i) {
            $itemData = $tableDataArr2[$i];
            if ('validate' === $stepProcess) {
                for ($j = 0; $j < count($itemData); ++$j) {
                    $rowData = $itemData[$j];
                    $itemName = trim($rowData['ITEM_NAME_'.$default_lang_new]);
                    $category = trim($rowData['ITEM_CATEGORY_'.$default_lang_new]);
                    $imgUrl = trim($rowData['IMAGE_URL']);
                    $itemPrice = trim($rowData['ITEM_PRICE']);
                    $offerPer = trim($rowData['OFFER_PER']);
                    $itemSrNo = trim($rowData['SR']);
                    $itemSKU = trim($rowData['SKU']);

                    if ('' === $itemName) {
                        $skipItemSrArr[] = $itemSrNo;
                        if ('YES' === strtoupper($validate)) {
                            $returnArr['action'] = 12;
                            $returnArr['message'] = $langage_lbl_admin['LBL_ITEM_NAME_REQUIRED_TXT'];
                            echo json_encode($returnArr);

                            exit;
                        }
                    }

                    if ($MODULES_OBJ->isEnableRequireMenuItemSKU()) {
                        if ('' === $itemSKU) {
                            $skipItemSrArr[] = $itemSKU;
                            if ('YES' === strtoupper($validate)) {
                                $returnArr['action'] = 12;
                                $returnArr['message'] = $langage_lbl_admin['LBL_ITEM_SKU_REQUIRED_TXT'];
                                echo json_encode($returnArr);

                                exit;
                            }
                        }
                    }

                    if (in_array(strtolower($itemName), $itemNameArr, true)) {
                        $skipItemSrArr[] = $itemSrNo;
                        if ('YES' === strtoupper($validate)) {
                            $returnArr['action'] = 7;
                            $returnArr['message'] = $itemName.'=> '.$langage_lbl_admin['LBL_DUPLICATE_ITEM_NAME_TXT'];
                            echo json_encode($returnArr);

                            exit;
                        }
                    }
                    $itemNameArr[] = strtolower($itemName);
                    // echo "<pre>";print_r($rowData);die;
                    $companyCatIdArr = $storeCatArr[$companyId];
                    // Category Validation Not require Because If Category Exists then Edit else Insert as a New
                    /* $checkCategory = checkItemCategory($category, $companyId, $companyCatIdArr);
                      if ($checkCategory > 0) {
                      $returnArr['action'] = $checkCategory;
                      $returnArr['message'] = $category . "=> Same category already exists";
                      echo json_encode($returnArr);
                      die;
                      } */
                    // echo "<pre>";print_r($companyCat);die;
                    // $foodMenuItemNameArr
                    for ($g = 0; $g < count($companyCatIdArr); ++$g) {
                        $foodMenuCatId = $companyCatIdArr[$g];
                        $foodItemDataArr = [];
                        if (isset($foodMenuItemNameArr[$foodMenuCatId])) {
                            $foodItemDataArr = $foodMenuItemNameArr[$foodMenuCatId];
                        }
                        for ($x = 0; $x < count($foodItemDataArr); ++$x) {
                            if (strtolower($itemName) === $foodItemDataArr[$x]) {
                                $skipItemSrArr[] = $itemSrNo;
                                if ('YES' === strtoupper($validate)) {
                                    $returnArr['action'] = 1;
                                    $returnArr['message'] = $itemName.'=> '.$langage_lbl_admin['LBL_ITEM_NAME_EXISTS_TXT'];
                                    echo json_encode($returnArr);

                                    exit;
                                }
                            }
                        }
                    }
                    // Optimized Above For Check Item Name So Comment This Start
                    /* $checkItemName = checkItemName($itemName, $companyId, $companyCatIdArr);
                      if ($checkItemName > 0) {
                      $returnArr['action'] = $checkItemName;
                      $returnArr['message'] = $itemName . "=> Item name already exists";
                      echo json_encode($returnArr);
                      die;
                      } */
                    // Optimized Above For Check Item Name So Comment This End
                    if ($offerPer >= 0) {
                        // Item Price Ok
                    } else {
                        $skipItemSrArr[] = $itemSrNo;
                        if ('YES' === strtoupper($validate)) {
                            $returnArr['action'] = 10;
                            $returnArr['message'] = $itemName.'=> '.$langage_lbl_admin['LBL_ITEM_OFFER_GREATER_THAN_ZERO_TXT'];
                            echo json_encode($returnArr);

                            exit;
                        }
                    }
                    if ($itemPrice > 0) {
                        // Item Price Ok
                    } else {
                        $skipItemSrArr[] = $itemSrNo;
                        if ('YES' === strtoupper($validate)) {
                            $returnArr['action'] = 2;
                            $returnArr['message'] = $itemName.'=> '.$langage_lbl_admin['LBL_ITEM_PRICE_GREATER_THAN_ZERO_TXT'];
                            echo json_encode($returnArr);

                            exit;
                        }
                    }
                    /*if (@getimagesize($imgUrl)) {
                        //image exists!
                    } else {
                        $skipItemSrArr[] = $itemSrNo;
                        if(strtoupper($validate) == "YES"){
                            $returnArr['action'] = 3;
                            $returnArr['message'] = $itemName . "=> ".$langage_lbl_admin['LBL_ITEM_IMAGE_NOT_EXISTS_TXT'];
                            echo json_encode($returnArr);
                            die;
                        }
                    }*/
                    if ('' === $category) {
                        $skipItemSrArr[] = $itemSrNo;
                        if ('YES' === strtoupper($validate)) {
                            $returnArr['action'] = 11;
                            $returnArr['message'] = $itemName.'=> '.$langage_lbl_admin['LBL_ITEM_CATEGORY_REQUIRED_TXT'];
                            echo json_encode($returnArr);

                            exit;
                        }
                    }
                    $successMsg = $langage_lbl_admin['LBL_ITEM_VALIDATED_SUCCESS_TXT'];
                }
            } elseif ('importCat' === $stepProcess) {
                for ($j = 0; $j < count($itemData); ++$j) {
                    $rowData = $itemData[$j];
                    $itemDesc = trim($rowData['ITEM_DESC']);
                    $category = trim($rowData['ITEM_CATEGORY_'.$default_lang_new]);
                    $itemSrNo = trim($rowData['SR']);
                    $itemSKU = trim($rowData['SKU']);
                    if (!in_array($itemSrNo, $skipItemSrArr, true)) {
                        $iFoodMenuId = 0;
                        if (isset($catNameArr[$companyId][strtolower($category)]) && $catNameArr[$companyId][strtolower($category)] > 0) {
                            $iFoodMenuId = $catNameArr[$companyId][strtolower($category)];
                        } else {
                            $checkCategory = $obj->MySQLSelect('SELECT iFoodMenuId,iCompanyId FROM food_menu WHERE vMenu_'.$default_lang_new."='".$category."' AND iCompanyId='".$companyId."' AND eStatus!='Deleted'");
                            if (count($checkCategory) > 0) {
                                $iFoodMenuId = $checkCategory[0]['iFoodMenuId'];
                            }
                        }
                        if ($iFoodMenuId > 0) {
                            // Update Category here
                        } else {
                            $insert_category = [];
                            $insert_category['iCompanyId'] = $companyId;
                            for ($l = 0; $l < count($langData); ++$l) {
                                if (isset($rowData['ITEM_CATEGORY_'.$langData[$l]['vCode']]) && '' !== trim($rowData['ITEM_CATEGORY_'.$langData[$l]['vCode']])) {
                                    $insert_category['vMenu_'.$langData[$l]['vCode']] = trim($rowData['ITEM_CATEGORY_'.$langData[$l]['vCode']]);
                                } else {
                                    $insert_category['vMenu_'.$langData[$l]['vCode']] = $category;
                                }
                            }
                            // $insert_category['vMenu_EN'] = $category;
                            $insert_category['vMenuDesc_EN'] = '';
                            $insert_category['iDisplayOrder'] = 0;
                            $insert_category['vImage'] = '';
                            $insert_category['eStatus'] = 'Active';

                            // echo "<pre>";print_r($insert_category);die;
                            $iFoodMenuId = $obj->MySQLQueryPerform('food_menu', $insert_category, 'insert');
                        }

                        if ($MODULES_OBJ->isEnableStoreMultiServiceCategories()) {
                            $obj->sql_query("UPDATE food_menu SET iServiceId = '{$serviceId}' WHERE iCompanyId = '{$companyId}'");
                        }
                    }
                }
                $successMsg = $langage_lbl_admin['LBL_CATEGORY_IMPORTED_SUCCESS_TXT'];
            } elseif ('importItem' === $stepProcess) {
                for ($j = 0; $j < count($itemData); ++$j) {
                    $rowData = $itemData[$j];
                    $itemName = trim($rowData['ITEM_NAME_'.$default_lang_new]);
                    $itemDesc = trim($rowData['ITEM_DESC']);
                    $category = trim($rowData['ITEM_CATEGORY_'.$default_lang_new]);
                    $itemPrice = trim($rowData['ITEM_PRICE']);
                    $itemFoodType = trim($rowData['ITEM_TYPE']);
                    $itemDispOrder = trim($rowData['DISPLAY_ORDER']);
                    $itemSrNo = trim($rowData['SR']);
                    $itemSKU = trim($rowData['SKU']);

                    if (ENABLE_PRESCRIPTION_UPLOAD === 'Yes' && '5' === $serviceId) {
                        $prescriptionRequired = trim($rowData['PRESCRIPTION_REQUIRED']);
                    }
                    // echo $itemName;die;
                    if (!in_array($itemSrNo, $skipItemSrArr, true)) {
                        // echo "<pre>";print_r($skipItemSrArr);die;
                        if ('nonveg' !== strtolower($itemFoodType)) {
                            $itemFoodType = 'Veg';
                        }
                        $offerPer = trim($rowData['OFFER_PER']);
                        $isAvailabel = trim($rowData['IS_AVAILABLE']);
                        if ('yes' !== strtolower($isAvailabel)) {
                            $isAvailabel = 'No';
                        }
                        $isRecommended = trim($rowData['IS_RECOMMENDED']);
                        if ('yes' !== strtolower($isRecommended)) {
                            $isRecommended = 'No';
                        }

                        $eStatusChk = trim($rowData['IS_ACTIVE']);
                        $eStatus = 'Active';
                        if ('yes' !== strtolower($eStatusChk)) {
                            $eStatus = 'Inactive';
                        }
                        $iFoodMenuId = $iMenuItemId = 0;
                        // echo "<pre>";print_r($catNameArr);die;
                        if (isset($catNameArr[$companyId][strtolower($category)]) && $catNameArr[$companyId][strtolower($category)] > 0) {
                            $iFoodMenuId = $catNameArr[$companyId][strtolower($category)];
                        } else {
                            $getCategory = $obj->MySQLSelect('SELECT iFoodMenuId,iCompanyId FROM food_menu WHERE vMenu_'.$default_lang_new."='".$category."' AND iCompanyId='".$companyId."' AND eStatus!='Deleted'");
                            if (count($getCategory) > 0) {
                                $iFoodMenuId = $getCategory[0]['iFoodMenuId'];
                            }
                        }
                        if (isset($menuItemArr[$iFoodMenuId][strtolower($itemName)]) && $menuItemArr[$iFoodMenuId][strtolower($itemName)] > 0) {
                            $iMenuItemId = $menuItemArr[$iFoodMenuId][strtolower($itemName)];
                        } else {
                            $getItemId = $obj->MySQLSelect('SELECT iMenuItemId,iFoodMenuId FROM menu_items WHERE vItemType_'.$default_lang_new."='".$itemName."' AND iFoodMenuId='".$iFoodMenuId."' AND eStatus!='Deleted'");
                            if (count($getItemId) > 0) {
                                $iMenuItemId = $getItemId[0]['iMenuItemId'];
                            }
                        }
                        $inset_item = [];
                        $inset_item['iFoodMenuId'] = $iFoodMenuId;
                        for ($l = 0; $l < count($langData); ++$l) {
                            if (isset($rowData['ITEM_NAME_'.$langData[$l]['vCode']]) && '' !== trim($rowData['ITEM_NAME_'.$langData[$l]['vCode']])) {
                                $inset_item['vItemType_'.$langData[$l]['vCode']] = trim($rowData['ITEM_NAME_'.$langData[$l]['vCode']]);
                            } else {
                                $inset_item['vItemType_'.$langData[$l]['vCode']] = $itemName;
                            }
                            if (isset($rowData['ITEM_DESC_'.$langData[$l]['vCode']]) && '' !== trim($rowData['ITEM_DESC_'.$langData[$l]['vCode']])) {
                                $inset_item['vItemDesc_'.$langData[$l]['vCode']] = trim($rowData['ITEM_DESC_'.$langData[$l]['vCode']]);
                            } else {
                                $inset_item['vItemDesc_'.$langData[$l]['vCode']] = $itemDesc;
                            }
                        }
                        // $inset_item['vItemType_' . $default_lang_new] = $itemName;
                        // $inset_item['vItemDesc_' . $default_lang_new] = $itemDesc;
                        $inset_item['fPrice'] = setTwoDecimalPoint($itemPrice);
                        $inset_item['eFoodType'] = ucwords($itemFoodType);
                        $inset_item['fOfferAmt'] = setTwoDecimalPoint($offerPer);
                        $inset_item['eStatus'] = ucwords($eStatus);
                        $inset_item['eAvailable'] = ucwords($isAvailabel);

                        $inset_item['eRecommended'] = ucwords($isRecommended);
                        $inset_item['iDisplayOrder'] = $itemDispOrder;
                        $inset_item['vSKU'] = trim($itemSKU);
                        if (ENABLE_PRESCRIPTION_UPLOAD === 'Yes' && '5' === $serviceId) {
                            $inset_item['prescription_required'] = $prescriptionRequired;
                        }
                        // echo "<pre>";print_r($inset_item);die;
                        if ($iMenuItemId > 0) {
                            $where = "iMenuItemId='".$iMenuItemId."'";
                            $obj->MySQLQueryPerform('menu_items', $inset_item, 'update', $where);
                        } else {
                            $iMenuItemId = $obj->MySQLQueryPerform('menu_items', $inset_item, 'insert');
                        }
                    }
                }
                $successMsg = $langage_lbl_admin['LBL_ITEM_IMPORTED_SUCCESS_TXT'];
            } elseif ('configImage' === $stepProcess) {
                for ($j = 0; $j < count($itemData); ++$j) {
                    $rowData = $itemData[$j];
                    $itemName = trim($rowData['ITEM_NAME_'.$default_lang_new]);
                    $imgUrl = trim($rowData['IMAGE_URL']);
                    $imgUrlOrg = $imgUrl = str_replace(["\r\n", "\n\r", "\n", "\r"], '', trim($rowData['IMAGE_URL']));
                    // Added By HJ On 16-10-2020 For Solved Issue When Image Name With Space Start
                    $explodeUrl = explode('/', $imgUrl);
                    $imageOrgName = array_pop($explodeUrl);
                    $urlencodeImg = rawurlencode($imageOrgName);
                    $imgUrl = str_replace($imageOrgName, $urlencodeImg, $imgUrl);
                    // Added By HJ On 16-10-2020 For Solved Issue When Image Name With Space End

                    $category = trim($rowData['ITEM_CATEGORY_'.$default_lang_new]);
                    $itemSrNo = trim($rowData['SR']);
                    if (!in_array($itemSrNo, $skipItemSrArr, true) && '' !== $imgUrl) {
                        // echo strtolower($category)."<br>";
                        // if (@getimagesize($imgUrl)) {

                        $iMenuItemId = $iFoodMenuId = 0;
                        if (isset($catNameArr[$companyId][strtolower($category)]) && $catNameArr[$companyId][strtolower($category)] > 0) {
                            $iFoodMenuId = $catNameArr[$companyId][strtolower($category)];
                        } else {
                            $getCategory = $obj->MySQLSelect('SELECT iFoodMenuId,iCompanyId FROM food_menu WHERE vMenu_'.$default_lang_new."='".$category."' AND iCompanyId='".$companyId."' AND eStatus!='Deleted'");
                            if (count($getCategory) > 0) {
                                $iFoodMenuId = $getCategory[0]['iFoodMenuId'];
                            }
                        }
                        if (isset($menuItemArr[$iFoodMenuId][strtolower($itemName)]) && $menuItemArr[$iFoodMenuId][strtolower($itemName)] > 0) {
                            $iMenuItemId = $menuItemArr[$iFoodMenuId][strtolower($itemName)];
                        } else {
                            $getItemId = $obj->MySQLSelect('SELECT iMenuItemId,iFoodMenuId FROM menu_items WHERE vItemType_'.$default_lang_new."='".$itemName."' AND iFoodMenuId='".$iFoodMenuId."' AND eStatus!='Deleted'");
                            if (count($getItemId) > 0) {
                                $iMenuItemId = $getItemId[0]['iMenuItemId'];
                            }
                        }
                        if ($iMenuItemId > 0) {
                            if (filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                                // Get the file
                                // $fileextension = end(explode('.', $imgUrlOrg));
                                $fileextension = end(explode('.', reconstruct_url($imgUrlOrg)));
                                // echo "<pre>";print_r($ext);die;
                                $menuItempath = $tconfig['tsite_upload_images_menu_item_path'];
                                $time_val = time();
                                $filename = random_int(11_111, 99_999);
                                $imgFullName = $time_val.'_'.$filename.'.'.$fileextension;
                                $itemFullPath = $menuItempath.'/'.$imgFullName;
                                // echo $itemFullPath . "<br>";
                                // $checkItem = file_put_contents($itemFullPath, file_get_contents($imgUrl));
                                if ('jpg' === $fileextension || 'gif' === $fileextension || 'png' === $fileextension || 'jpeg' === $fileextension || 'bmp' === $fileextension || 'heic' === $fileextension) {
                                    grab_image($imgUrl, $itemFullPath);
                                    $successMsg = $langage_lbl_admin['LBL_ITEM_IMAGE_CONFIGURED_SUCCESS_TXT'];
                                    $where = "iMenuItemId='".$iMenuItemId."'";
                                    $update_image = [];
                                    if (file_exists($itemFullPath) && filesize($itemFullPath) > 0) {
                                        $update_image['vImage'] = $imgFullName;
                                        $obj->MySQLQueryPerform('menu_items', $update_image, 'update', $where);

                                        if ('Yes' === $ENABLE_ITEM_MULTIPLE_IMAGE_VIDEO_UPLOAD) {
                                            $Data_update_option['vImage'] = $imgFullName;
                                            $Data_update_option['iMenuItemId'] = $iMenuItemId;
                                            $obj->MySQLQueryPerform('menu_item_media', $Data_update_option, 'insert');
                                        }
                                    }
                                }
                            }
                        }
                        // }
                    }
                }
            }
        }
    // echo "<pre>";print_r($tableDataArr2);die;
    } else {
        $returnArr['action'] = 4;
        $returnArr['message'] = $langage_lbl_admin['LBL_SELECT_FILE_TXT'];
        echo json_encode($returnArr);

        exit;
    }
}
$skipItemSrNo = '';
if (count($skipItemSrArr) > 0) {
    $skipItemSrArr = array_unique($skipItemSrArr, SORT_REGULAR);
    $skipItemSrNo = implode(',', $skipItemSrArr);
}
$returnArr['skipItemSrNo'] = $skipItemSrNo;
$returnArr['message'] = $successMsg;
echo json_encode($returnArr);

exit;

function checkItemCategory($category, $iCompanyId, $companyCatIdArr)
{
    global $obj, $default_lang_new;
    $checkCategory = $obj->MySQLSelect('SELECT iFoodMenuId,iCompanyId FROM food_menu WHERE vMenu_'.$default_lang_new."='".$category."' AND iCompanyId='".$iCompanyId."' AND eStatus!='Deleted'");
    $duplicateCatArr = [];
    if (count($checkCategory) > 0) {
        for ($l = 0; $l < count($checkCategory); ++$l) {
            $companyId = $checkCategory[$l]['iCompanyId'];
            if ($iCompanyId === $companyId) {
                $duplicateCatArr[] = $checkCategory[$l]['iFoodMenuId'];
                $iFoodMenuId = $checkCategory[$l]['iFoodMenuId'];
            }
        }
        // echo "<pre>";print_r($duplicateCatArr);die;
        if (count($duplicateCatArr) > 1) {
            return 6;
        }
    } /* else {
      $insert_category = array();
      $insert_category['iCompanyId'] = $iCompanyId;
      $insert_category['vMenu_EN'] = $category;
      $insert_category['vMenuDesc_EN'] = "";
      $insert_category['iDisplayOrder'] = 0;
      $insert_category['vImage'] = "";
      $insert_category['eStatus'] = "Active";
      $iFoodMenuId = $obj->MySQLQueryPerform('food_menu', $insert_category, 'insert');
      }
      if ($iFoodMenuId > 0) {
      $catData = $obj->MySQLSelect("SELECT iFoodMenuId FROM food_menu WHERE iFoodMenuId='" . $iFoodMenuId . "' AND eStatus!='Deleted'");
      $storeData = array();
      if (count($catData) > 0) {
      return $catData;
      } else {
      return 7;
      }
      } else {
      return 7;
      } */

    // echo "<pre>";print_r($duplicateCatArr);die;
    return 0;
}

function checkStore($storeName)
{
    global $obj, $default_lang_new;
    $checkStore = $obj->MySQLSelect("SELECT iCompanyId FROM company WHERE vCompany='".$storeName."' AND eStatus!='Deleted'");
    $storeData = [];
    if (count($checkStore) > 0) {
        $storeData = $checkStore;
    }

    return $storeData;
}

function checkItemName($itemName, $iCompanyId, $companyCatIdArr)
{
    global $obj, $default_lang_new;
    $checkItem = $obj->MySQLSelect('SELECT iMenuItemId,iFoodMenuId FROM menu_items WHERE vItemType_'.$default_lang_new."='".$itemName."' AND eStatus!='Deleted'");
    // echo "SELECT iMenuItemId,iFoodMenuId FROM menu_items WHERE vItemType_" . $default_lang_new . "='" . $itemName . "' AND eStatus!='Deleted'";die;
    if (count($checkItem) > 0) {
        return 1;
    }
    for ($g = 0; $g < count($checkItem); ++$g) {
        $iFoodMenuId = $checkItem[$g]['iFoodMenuId'];
        if (in_array($iFoodMenuId, $companyCatIdArr, true)) {
            return 1;
        }
    }

    return 0;
}
