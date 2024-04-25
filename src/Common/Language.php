<?php



namespace Kesk\Web\Common;

class Language
{
    public function __construct() {}

    public function FetchMemberSelectedLanguage($vEmail)
    {
        global $obj, $vSystemDefaultLangCode;
        $preflang = 'EN';
        $sql = "select vLang from register_user where vEmail ='".$vEmail."'";
        $res = $obj->MySQLSelect($sql);
        if (\count($res) > 0) {
            $preflang = $res[0]['vLang'];
        } else {
            $sql = "select vLang From register_driver where vEmail = '".$vEmail."'";
            $res1 = $obj->MySQLSelect($sql);
            if (\count($res1) > 0) {
                $preflang = $res1[0]['vLang'];
            } else {
                if (!empty($vSystemDefaultLangCode)) {
                    $preflang = $vSystemDefaultLangCode;
                }
                if (empty($preflang)) {
                    $sql = "select vCode from language_master where eDefault = 'Yes'";
                    $lang1 = $obj->MySQLSelect($sql);
                    if (\count($lang1) > 0) {
                        $preflang = $lang1[0]['vCode'];
                    }
                }
            }
        }

        return $preflang;
    }

    public function FetchMemberSelectedLanguageDir($lang)
    {
        global $obj, $Data_ALL_langArr;
        if (!empty($Data_ALL_langArr) && \count($Data_ALL_langArr) > 0) {
            foreach ($Data_ALL_langArr as $language_item) {
                if (strtoupper($language_item['vCode']) === strtoupper($lang)) {
                    $preflangdir = $language_item['eDirectionCode'];
                }
            }
        }
        if (empty($preflangdir)) {
            $sql = "select eDirectionCode from language_master where vCode = '".$lang."'";
            $lang1 = $obj->MySQLSelect($sql);
            $preflangdir = $lang1[0]['eDirectionCode'];
        }

        return $preflangdir;
    }

    public function FetchSystemDefaultLangName()
    {
        global $obj, $vSystemDefaultLangCode, $vSystemDefaultLangName;
        if (!empty($vSystemDefaultLangName)) {
            return $vSystemDefaultLangName;
        }
        $sql = "SELECT vTitle FROM language_master where eStatus='Active' AND eDefault = 'Yes'";
        $data = $obj->MySQLSelect($sql);
        $vTitle = $data[0]['vTitle'] ?? 'EN';

        return $vTitle;
    }

    public function FetchSystemDefaultLang()
    {
        global $obj, $vSystemDefaultLangCode, $vSystemDefaultLangName, $vSystemDefaultLangDirection, $oCache;
        if (empty($vSystemDefaultLangCode)) {
            $defaultLangApcKey = md5('system_default_lang');
            $getDefaultLangCacheData = $oCache->getData($defaultLangApcKey);
            if (!empty($getDefaultLangCacheData) && \count($getDefaultLangCacheData) > 0) {
                $default_label = $getDefaultLangCacheData;
            } else {
                $sql = "SELECT `vTitle`, `vCode`, `eDirectionCode` FROM `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
                $default_label = $obj->MySQLSelect($sql);
                $oCache->setData($defaultLangApcKey, $default_label);
            }
            $vSystemDefaultLangCode = (isset($default_label[0]['vCode']) && !empty($default_label[0]['vCode'])) ? $default_label[0]['vCode'] : 'EN';
            $vSystemDefaultLangName = (isset($default_label[0]['vTitle']) && !empty($default_label[0]['vTitle'])) ? $default_label[0]['vTitle'] : 'EN';
            $vSystemDefaultLangDirection = (isset($default_label[0]['eDirectionCode']) && !empty($default_label[0]['eDirectionCode'])) ? $default_label[0]['eDirectionCode'] : 'ltr';
        }

        return $vSystemDefaultLangCode;
    }

    public function getDefaultLanguageData()
    {
        global $obj, $vSystemDefaultLangCode, $vSystemDefaultLangName, $vSystemDefaultLangDirection, $oCache;
        if (empty($vSystemDefaultLangCode)) {
            $defaultLangApcKey = md5('system_default_lang_data');
            $getDefaultLangCacheData = $oCache->getData($defaultLangApcKey);
            if (!empty($getDefaultLangCacheData) && \count($getDefaultLangCacheData) > 0) {
                $default_label = $getDefaultLangCacheData;
            } else {
                $sql = "SELECT `vTitle`, `vCode`, `eDirectionCode` FROM `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
                $default_label = $obj->MySQLSelect($sql);
                $oCache->setData($defaultLangApcKey, $default_label);
            }
            $vSystemDefaultLangCode = (isset($default_label[0]['vCode']) && !empty($default_label[0]['vCode'])) ? $default_label[0]['vCode'] : 'EN';
            $vSystemDefaultLangName = (isset($default_label[0]['vTitle']) && !empty($default_label[0]['vTitle'])) ? $default_label[0]['vTitle'] : 'EN';
            $vSystemDefaultLangDirection = (isset($default_label[0]['eDirectionCode']) && !empty($default_label[0]['eDirectionCode'])) ? $default_label[0]['eDirectionCode'] : 'ltr';
        }
        $dataArr['vSystemDefaultLangCode'] = $vSystemDefaultLangCode;
        $dataArr['vSystemDefaultLangName'] = $vSystemDefaultLangName;
        $dataArr['vSystemDefaultLangDirection'] = $vSystemDefaultLangDirection;

        return $dataArr;
    }

    public function FetchLanguageLabels($lCode = '', $directValue = '', $iServiceId = '')
    {
        global $obj, $APP_TYPE, $oCache;
        $defaultLangCodeApcKey = md5('system_default_lang_code');
        $getDefaultLangCodeCacheData = $oCache->getData($defaultLangCodeApcKey);
        if (!empty($getDefaultLangCodeCacheData) && \count($getDefaultLangCodeCacheData) > 0) {
            $default_label = $getDefaultLangCodeCacheData;
        } else {
            $sql = "SELECT `vCode` FROM `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
            $default_label = $obj->MySQLSelect($sql);
            $oCache->setData($defaultLangCodeApcKey, $default_label);
        }
        if ('' === $lCode) {
            $lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';
        }
        $LangLabelsApcKey = md5('language_label_union_other_'.$lCode);
        $getLangLabelsCacheData = $oCache->getData($LangLabelsApcKey);
        if (!empty($getLangLabelsCacheData) && \count($getLangLabelsCacheData) > 0) {
            $all_label = $getLangLabelsCacheData;
        } else {
            $sql = "SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = '".$lCode."' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = '".$lCode."' ";
            $all_label = $obj->MySQLSelect($sql);
            $oCache->setData($LangLabelsApcKey, $all_label);
        }
        $x = [];
        for ($i = 0; $i < \count($all_label); ++$i) {
            $vLabel = $all_label[$i]['vLabel'];
            $vValue = $all_label[$i]['vValue'];
            $x[$vLabel] = $vValue;
        }
        $LangLabelsENApcKey = md5('language_label_union_other_EN');
        $getLangLabelsENCacheData = $oCache->getData($LangLabelsENApcKey);
        if (!empty($getLangLabelsENCacheData) && \count($getLangLabelsENCacheData) > 0) {
            $all_label_en = $getLangLabelsENCacheData;
        } else {
            $sql_en = "SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = 'EN' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = 'EN'";
            $all_label_en = $obj->MySQLSelect($sql_en);
            $oCache->setData($LangLabelsENApcKey, $all_label_en);
        }
        if (\count($all_label_en) > 0) {
            for ($i = 0; $i < \count($all_label_en); ++$i) {
                $vLabel_tmp = $all_label_en[$i]['vLabel'];
                $vValue_tmp = $all_label_en[$i]['vValue'];
                if (isset($x[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $x)) {
                    if ('' === $x[$vLabel_tmp]) {
                        $x[$vLabel_tmp] = $vValue_tmp;
                    }
                } else {
                    $x[$vLabel_tmp] = $vValue_tmp;
                }
            }
        }
        if ('Delivery' !== $APP_TYPE) {
            if ($iServiceId > 0) {
                $LangLabelsServiceApcKey = md5('language_label_service_'.$iServiceId.'_'.$lCode);
                $getLangLabelsServiceCacheData = $oCache->getData($LangLabelsServiceApcKey);
                if (!empty($getLangLabelsServiceCacheData) && \count($getLangLabelsServiceCacheData) > 0) {
                    $all_label = $getLangLabelsServiceCacheData;
                } else {
                    $sql = 'SELECT `vLabel` , `vValue` FROM `language_label_'.$iServiceId."` WHERE `vCode` = '".$lCode."'";
                    $all_label = $obj->MySQLSelect($sql);
                    $oCache->setData($LangLabelsServiceApcKey, $all_label);
                }
                if (!empty($all_label)) {
                    for ($i = 0; $i < \count($all_label); ++$i) {
                        $vLabel = $all_label[$i]['vLabel'];
                        $vValue = $all_label[$i]['vValue'];
                        $x[$vLabel] = $vValue;
                    }
                    $LangLabelsServiceENApcKey = md5('language_label_service_'.$iServiceId.'_EN');
                    $getLangLabelsServiceENCacheData = $oCache->getData($LangLabelsServiceENApcKey);
                    if (!empty($getLangLabelsServiceENCacheData) && \count($getLangLabelsServiceENCacheData) > 0) {
                        $all_label_en = $getLangLabelsServiceENCacheData;
                    } else {
                        $sql_en = 'SELECT `vLabel` , `vValue` FROM `language_label_'.$iServiceId."` WHERE `vCode` = 'EN'";
                        $all_label_en = $obj->MySQLSelect($sql_en);
                        $oCache->setData($LangLabelsServiceENApcKey, $all_label_en);
                    }
                    if (\count($all_label_en) > 0) {
                        for ($i = 0; $i < \count($all_label_en); ++$i) {
                            $vLabel_tmp = $all_label_en[$i]['vLabel'];
                            $vValue_tmp = $all_label_en[$i]['vValue'];
                            if (isset($x[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $x)) {
                                if ('' === $x[$vLabel_tmp]) {
                                    $x[$vLabel_tmp] = $vValue_tmp;
                                }
                            } else {
                                $x[$vLabel_tmp] = $vValue_tmp;
                            }
                        }
                    }
                }
            }
        }
        $x['vCode'] = $lCode;
        if ('' === $directValue) {
            $returnArr['Action'] = '1';
            $returnArr['LanguageLabels'] = $x;

            return $returnArr;
        }

        return $x;
    }

    public function FetchLanguageLabelsWeb($lCode = '', $directValue = '', $iServiceId = '')
    {
        global $obj, $APP_TYPE, $vSystemDefaultLangCode, $languageLabelDataArr, $oCache;
        if (!empty($vSystemDefaultLangCode)) {
            $defaultLangCode = $vSystemDefaultLangCode;
        } else {
            $default_label = $obj->MySQLSelect("SELECT `vCode` FROM `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ");
            $defaultLangCode = $default_label[0]['vCode'];
        }
        if ('' === $lCode) {
            $lCode = (isset($defaultLangCode) && $defaultLangCode) ? $defaultLangCode : 'EN';
        }
        $LangLabelsWebApcKey = md5('language_label_union_other_'.$lCode);
        $getLangLabelsWebCacheData = $oCache->getData($LangLabelsWebApcKey);
        if (!empty($getLangLabelsWebCacheData) && \count($getLangLabelsWebCacheData) > 0) {
            $all_label = $getLangLabelsWebCacheData;
        } else {
            $sql = "SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = '".$lCode."' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = '".$lCode."' ";
            $all_label = $obj->MySQLSelect($sql);
            $oCache->setData($LangLabelsWebApcKey, $all_label);
        }
        $x = [];
        for ($i = 0; $i < \count($all_label); ++$i) {
            $vLabel = $all_label[$i]['vLabel'];
            $vValue = $all_label[$i]['vValue'];
            $x[$vLabel] = $vValue;
        }
        $LangLabelsWebENApcKey = md5('language_label_union_other_EN');
        $getLangLabelsWebENCacheData = $oCache->getData($LangLabelsWebENApcKey);
        if (!empty($getLangLabelsWebENCacheData) && \count($getLangLabelsWebENCacheData) > 0) {
            $all_label_en = $getLangLabelsWebENCacheData;
        } else {
            $sql_en = "SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = 'EN' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = 'EN'";
            $all_label_en = $obj->MySQLSelect($sql_en);
            $oCache->setData($LangLabelsWebENApcKey, $all_label_en);
        }
        if (\count($all_label_en) > 0) {
            for ($i = 0; $i < \count($all_label_en); ++$i) {
                $vLabel_tmp = $all_label_en[$i]['vLabel'];
                $vValue_tmp = $all_label_en[$i]['vValue'];
                if (isset($x[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $x)) {
                    if ('' === $x[$vLabel_tmp]) {
                        $x[$vLabel_tmp] = $vValue_tmp;
                    }
                } else {
                    $x[$vLabel_tmp] = $vValue_tmp;
                }
            }
        }
        if ('Delivery' !== $APP_TYPE) {
            if ($iServiceId > 0) {
                $LangLabelsServiceApcKey = md5('language_label_service_'.$iServiceId.'_'.$lCode);
                $getLangLabelsServiceCacheData = $oCache->getData($LangLabelsServiceApcKey);
                if (!empty($getLangLabelsServiceCacheData) && \count($getLangLabelsServiceCacheData) > 0) {
                    $all_label = $getLangLabelsServiceCacheData;
                } else {
                    $sql = 'SELECT `vLabel` , `vValue` FROM `language_label_'.$iServiceId."` WHERE `vCode` = '".$lCode."'";
                    $all_label = $obj->MySQLSelect($sql);
                    $oCache->setData($LangLabelsServiceApcKey, $all_label);
                }
                for ($i = 0; $i < \count($all_label); ++$i) {
                    $vLabel = $all_label[$i]['vLabel'];
                    $vValue = $all_label[$i]['vValue'];
                    $x[$vLabel] = $vValue;
                }
                $LangLabelsServiceENApcKey = md5('language_label_service_'.$iServiceId.'_EN');
                $getLangLabelsServiceENCacheData = $oCache->getData($LangLabelsServiceENApcKey);
                if (!empty($getLangLabelsServiceENCacheData) && \count($getLangLabelsServiceENCacheData) > 0) {
                    $all_label_en = $getLangLabelsServiceENCacheData;
                } else {
                    $sql_en = 'SELECT `vLabel` , `vValue` FROM `language_label_'.$iServiceId."` WHERE `vCode` = 'EN'";
                    $all_label_en = $obj->MySQLSelect($sql_en);
                    $oCache->setData($LangLabelsServiceENApcKey, $all_label_en);
                }
                if (\count($all_label_en) > 0) {
                    for ($i = 0; $i < \count($all_label_en); ++$i) {
                        $vLabel_tmp = $all_label_en[$i]['vLabel'];
                        $vValue_tmp = $all_label_en[$i]['vValue'];
                        if (isset($x[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $x)) {
                            if ('' === $x[$vLabel_tmp]) {
                                $x[$vLabel_tmp] = $vValue_tmp;
                            }
                        } else {
                            $x[$vLabel_tmp] = $vValue_tmp;
                        }
                    }
                }
            }
        }
        $x['vCode'] = $lCode;
        if ('' === $directValue) {
            $returnArr['Action'] = '1';
            $returnArr['LanguageLabels'] = $x;

            return $returnArr;
        }

        return $x;
    }

    public function checkOtherLangDataExist($data, $lang, $type_arr)
    {
        global $tconfig, $template;
        if ('EN' === $lang) {
            return $data;
        }
        foreach ($type_arr as $key => $value) {
            if (empty($data[$value.$lang]) && !empty($data[$value.'EN'])) {
                $data[$value.$lang] = $data[$value.'EN'];
            }
            $pos = strpos($value, 'img');
            if (false !== $pos) {
                if (!file_exists($tconfig['tsite_upload_apptype_page_images_panel'].$template.'/'.$data[$value.$lang])) {
                    $data[$value.$lang] = $data[$value.'EN'];
                }
            }
        }

        return $data;
    }

    public function FetchDefaultLangData($field = '')
    {
        global $vSystemDefaultLangCode, $vSystemDefaultLangName, $vSystemDefaultLangDirection, $vSystemDefaultLangvGMapLangCode, $Data_ALL_langArr;
        if ('VCODE' === strtoupper($field)) {
            if (!empty($vSystemDefaultLangCode)) {
                return $vSystemDefaultLangCode;
            }
            $vCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

            return $vCode;
        }
        if ('VTITLE' === strtoupper($field)) {
            if (!empty($vSystemDefaultLangName)) {
                return $vSystemDefaultLangName;
            }
            $vTitle = get_value('language_master', 'vTitle', 'eDefault', 'Yes', '', 'true');

            return $vTitle;
        }
        if ('EDIRECTIONCODE' === strtoupper($field)) {
            if (!empty($vSystemDefaultLangDirection)) {
                return $vSystemDefaultLangDirection;
            }
            $eDirectionCode = get_value('language_master', 'eDirectionCode', 'eDefault', 'Yes', '', 'true');

            return $eDirectionCode;
        }
        if ('VGMAPLANGCODE' === strtoupper($field)) {
            if (!empty($vSystemDefaultLangvGMapLangCode)) {
                return $vSystemDefaultLangvGMapLangCode;
            }
            $vGMapLangCode = get_value('language_master', 'vTitle', 'eDefault', 'Yes', '', 'true');

            return $vGMapLangCode;
        }

        return $Data_ALL_langArr;
    }

    public function checkLanguageExist($langCode = 'EN')
    {
        global $obj;
        $langData = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE vCode = '".strtoupper($langCode)."' ");
        if (\count($langData) > 0) {
            return true;
        }

        return false;
    }

    public function getLangDataDefaultFirst($db_master_data)
    {
        global $default_lang;
        $db_master = $db_master_data;
        $EN_available = $this->checkLanguageExist();
        foreach ($db_master as $dkey => $dval) {
            if ($EN_available) {
                if ('EN' === $dval['vCode']) {
                    unset($db_master[$dkey]);
                    array_unshift($db_master, $dval);
                }
            } else {
                if ($dval['vCode'] === $default_lang) {
                    unset($db_master[$dkey]);
                    array_unshift($db_master, $dval);
                }
            }
        }

        return $db_master;
    }

    public function getLanguageData($lang)
    {
        global $obj, $Data_ALL_langArr;
        if (!empty($Data_ALL_langArr) && \count($Data_ALL_langArr) > 0) {
            foreach ($Data_ALL_langArr as $language_item) {
                if (strtoupper($language_item['vCode']) === strtoupper($lang)) {
                    return $language_item;
                }
            }
        }
        $result = $obj->MySQLSelect("SELECT * FROM language_master WHERE vCode = '".$lang."'");

        return $result[0];
    }
}
