<?php



namespace Kesk\Web\Common;


use CacheMemcache;
use CI_Security;
use CommMedia;
use ConfigurationSettings;
use Dashboard;
use DataHelper;
use Kesk\Web\Admin\Library\User;

class SystemInfo
{
    private static string $EXPIRY_DATE = '0000-00-00';
    private static array $ALLOWED_INHOUSE_DOMAINS = [];
    private static array $ALLOWED_CLIENT_DOMAINS = ['www.localservicespro.com', 'localservicespro.com', 'dev.localservicespro.com', 'beta.localservicespro.com', 'test.localservicespro.com', 'staging.localservicespro.com'];

    public function __construct()
    {
    }

    public static function _construct_($BASE_DOCUMENT_PATH): void
    {
        global $DOCUMENT_ROOT, $IS_INHOUSE_DOMAINS;
        ob_start();
        @session_start();
        @header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        \define('_TEXEC', 1);
        \define('TPATH_BASE', $BASE_DOCUMENT_PATH);
        \define('DS', \DIRECTORY_SEPARATOR);
        $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        \defined('_TEXEC') || exit('Restricted access');
        \define('TPATH_ROOT', TPATH_BASE);
        \define('TPATH_CLASS', TPATH_ROOT.'/assets/libraries/');
        if (!file_exists(TPATH_CLASS.'server_configurations_params.php')) {
            header('Location: error.php');
            exit;
        }


        if (empty(self::$ALLOWED_INHOUSE_DOMAINS)) {
            self::configureSystemType();
        }


        include_once TPATH_CLASS.'system_global_functions.php';

        include_once TPATH_CLASS.'system_general_functions.php';

        require_once TPATH_CLASS.'server_configurations_params.php';

        require_once TPATH_CLASS.'server_configurations.php';

        require_once TPATH_CLASS.'server_sc_configuration.php';

        require_once TPATH_CLASS.'project_settings.php';

        self::configureSystemType();
        extract(self::declareCurrentScopeVariables(), EXTR_REFS | EXTR_OVERWRITE);
        if (!\defined('SITE_TYPE')) {
            \define('SITE_TYPE', 'Live');
        }

        require_once TPATH_CLASS.'module_configurations.php';

        require_once TPATH_CLASS.'db_info.php';


        self::redefineVariables(get_defined_vars());
    }

    public static function redefineVariables($all_variables): void
    {
        foreach ($all_variables as $var => $value) {
            global ${$var};
            ${$var} = $value;
        }
    }

    public static function declareCurrentScopeVariables()
    {
        $super_global_variables_arr = ['GLOBALS', '_ENV', 'HTTP_ENV_VARS', '_POST', 'HTTP_POST_VARS', '_GET', 'HTTP_GET_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_SERVER', 'HTTP_SERVER_VARS', '_FILES', 'HTTP_POST_FILES', '_REQUEST', 'HTTP_SESSION_VARS', '_SESSION'];
        $returnArr = [];
        foreach ($GLOBALS as $var => $value) {
            if (!\in_array($var, $super_global_variables_arr, true)) {
                $returnArr[$var] = $value;
            }
        }

        return $returnArr;
    }

    public static function Initiate($BASE_DOCUMENT_PATH)
    {
        
        self::_construct_($BASE_DOCUMENT_PATH);
        extract(self::declareCurrentScopeVariables(), EXTR_REFS | EXTR_OVERWRITE);
        include_once TPATH_CLASS.'Models/class.configuration.settings.php';
        $tconfig = ConfigurationSettings::getTconfigVar();

        require_once TPATH_CLASS.'class.cache.php';
        $oCache = new CacheMemcache();
        $THEME_OBJ = new SystemTheme();

        self::redefineVariables(get_defined_vars());
        if (!isset($obj)) {
            require_once TPATH_CLASS.'models/class.dbquery.php';
            $obj = new \DBConnection(TSITE_SERVER, TSITE_DB, TSITE_USERNAME, TSITE_PASS);
        }

        require_once TPATH_CLASS.'cache_keys.php';
        self::redefineVariables(get_defined_vars());
        $CONFIG_OBJ = new ConfigurationSettings();
        if (!isset($obj_security)) {
            require_once TPATH_CLASS.'security_params.php';
            $obj_security = new CI_Security();
        }
        extract(self::declareCurrentScopeVariables(), EXTR_REFS | EXTR_OVERWRITE);
        $exclude_webservice = [WEBSERVICE_API_FILE_NAME];
        $inwebservice = 0;
        if (false !== stripos_arr($_SERVER['REQUEST_URI'], $exclude_webservice)) {
            $inwebservice = 1;
        }

        self::redefineVariables(get_defined_vars());
        $obj_security->xss_cleaner_all();
        $LANG_OBJ = new Language();
        $default_lang = $LANG_OBJ->FetchSystemDefaultLang();
        $def_lang_name = $LANG_OBJ->FetchSystemDefaultLangName();
        if (!isset($_SESSION['sess_lang']) || '' === $_SESSION['sess_lang']) {
            $defaultLngDataArr = $LANG_OBJ->getDefaultLanguageData();
            $_SESSION['sess_lang'] = $defaultLngDataArr['vSystemDefaultLangCode'];
            $_SESSION['eDirectionCode'] = $defaultLngDataArr['vSystemDefaultLangDirection'];
        }
        $languageApcKey = md5($cacheKeysArr['language_master']);
        $getLanguageCacheData = $oCache->getData($languageApcKey);
        if (!empty($getLanguageCacheData) && \count($getLanguageCacheData) > 0) {
            $Data_ALL_langArr = $getLanguageCacheData;
        } else {
            $Data_ALL_langArr = $obj->MySQLSelect('SELECT *, eDirectionCode as eType FROM language_master ORDER BY iDispOrder ASC');
            $setLanguageCacheData = $oCache->setData($languageApcKey, $Data_ALL_langArr);
        }
        $language_codes_arr = $active_language_codes_arr = $languageAssociateArr = [];
        foreach ($Data_ALL_langArr as $language_item) {
            if ('YES' === strtoupper($language_item['eDefault'])) {
                $vSystemDefaultLangCode = $language_item['vCode'];
                $vSystemDefaultLangName = $language_item['vTitle'];
                $vSystemDefaultLangDirection = $language_item['eDirectionCode'];
                $vSystemDefaultLangvGMapLangCode = $language_item['vGMapLangCode'];
            }
            $language_codes_arr[] = $language_item['vCode'];
            $languageAssociateArr[$language_item['vCode']] = $language_item;
            if ('Active' === $language_item['eStatus']) {
                $active_language_codes_arr[] = $language_item['vCode'];
            }
        }
        $vSystemDefaultLangCode = empty($vSystemDefaultLangCode) ? 'EN' : $vSystemDefaultLangCode;
        $vSystemDefaultLangName = empty($vSystemDefaultLangName) ? 'English' : $vSystemDefaultLangName;
        $vSystemDefaultLangDirection = empty($vSystemDefaultLangDirection) ? 'ltr' : $vSystemDefaultLangDirection;
        $vSystemDefaultLangvGMapLangCode = empty($vSystemDefaultLangvGMapLangCode) ? 'en' : $vSystemDefaultLangvGMapLangCode;
        $Data_langArr[0]['vCode'] = $vSystemDefaultLangCode;
        if (isset($_SESSION['sess_lang']) && '' !== $_SESSION['sess_lang'] && 0 === $inwebservice) {
            $langugaeCode = $_SESSION['sess_lang'];
        }
        if (empty($langugaeCode)) {
            $langugaeCode = isset($_REQUEST['vLang']) ? ('' === $_REQUEST['vLang'] ? $Data_langArr[0]['vCode'] : $_REQUEST['vLang']) : $Data_langArr[0]['vCode'];
        }
        if (empty($langugaeCode) || false === in_array_ci($langugaeCode, $active_language_codes_arr)) {
            $langugaeCode = $vSystemDefaultLangCode;
        }
        $serviceCatApcKey = md5($cacheKeysArr['service_categories'].'_'.$langugaeCode);
        $getServiceCacheData = $oCache->getData($serviceCatApcKey);
        if (!empty($getServiceCacheData) && \count($getServiceCacheData) > 0) {
            $ServiceData = $getServiceCacheData;
        } else {
            $ServiceData = $obj->MySQLSelect('SELECT eType AS ispriceshow,iServiceId,vService, vServiceName_'.$langugaeCode." as vServiceName,vImage,if(tDescription != '',JSON_UNQUOTE(json_extract(`tDescription`, '$.tDescription_".$langugaeCode."')),'') AS tDescription FROM `service_categories` WHERE iServiceId IN (".$enablesevicescategory.") AND eStatus='Active' order by iDisplayOrder ASC");
            $setServiceCacheData = $oCache->setData($serviceCatApcKey, $ServiceData);
        }
        $serviceCategoriesTmp = $serviceCategoriesIdsArrTmp = [];
        $service_id_admin = -1;
        if (!empty($ServiceData)) {
            foreach ($ServiceData as $key => $value) {
                if ('' !== $value['vImage']) {
                    $value['vImage'] = $tconfig['tsite_upload_service_categories_images'].$value['vImage'];
                }
                $serviceCategoriesTmp[] = $value;
                $serviceCategoriesIdsArrTmp[] = $value['iServiceId'];
                if (-1 === $service_id_admin && $value['iServiceId'] > 1) {
                    $service_id_admin = $value['iServiceId'];
                }
            }
        }
        $iServiceId = $_REQUEST['iServiceId'] ?? $ServiceData[0]['iServiceId'];
        if (empty($_REQUEST['iServiceId'])) {
            $iServiceId = $ServiceData[0]['iServiceId'];
            $_REQUEST['iServiceId'] = $iServiceId;
        }
        if (empty($_REQUEST['iServiceId'])) {
            $ServiceDataNew = $obj->MySQLSelect('SELECT iServiceId FROM `service_categories` WHERE iServiceId IN ('.$enablesevicescategory.')');
            $iServiceId = $ServiceData[0]['iServiceId'] = $ServiceDataNew[0]['iServiceId'];
            $_REQUEST['iServiceId'] = $iServiceId;
        }
        if (empty($iServiceId)) {
            $iServiceId = $_REQUEST['iServiceId'] = '';
        }
        \define('serviceCategories', json_encode($serviceCategoriesTmp));
        \define('ServiceData', json_encode($ServiceData));
        if (isset($_SESSION['sess_user']) && 'company' === $_SESSION['sess_user']) {
            $dbQueryData = $obj->MySQLSelect("SELECT iServiceId FROM company WHERE iCompanyId = '".$_SESSION['sess_iUserId']."'");
            if (\count($dbQueryData) > 0) {
                $iServiceIdWeb = $dbQueryData[0]['iServiceId'];
            } else {
                $iServiceIdWeb = $ServiceData[0]['iServiceId'];
            }
        } else {
            $iServiceIdWeb = $ServiceData[0]['iServiceId'];
        }
        if ('0' !== $iServiceIdWeb) {
            $langLabelApcKey = md5($cacheKeysArr['language_label_'].$iServiceIdWeb.'_'.$langugaeCode);
            $getLabelCacheData = $oCache->getData($langLabelApcKey);
            if (!empty($getLabelCacheData) && \count($getLabelCacheData) > 0) {
                $db_lbl = $getLabelCacheData;
            } else {
                $db_lbl = $obj->MySQLSelect('SELECT vLabel,vValue,LanguageLabelId FROM language_label_'.$iServiceIdWeb." WHERE vCode='".$langugaeCode."'");
                $setLabelCacheData = $oCache->setData($langLabelApcKey, $db_lbl);
            }
            if (!empty($db_lbl)) {
                foreach ($db_lbl as $key => $value) {
                    if (isset($_SESSION['sess_editingToken']) && $_SESSION['sess_editingToken'] === $db_config[0]['vValue']) {
                        $langage_lbl[$value['vLabel']] = "<em class='label-dynmic'><i class='fa fa-edit label-i' data-id='".$value['LanguageLabelId']."' data-value='main'></i>".$value['vValue'].'</em>';
                    } else {
                        $langage_lbl[$value['vLabel']] = $value['vValue'];
                    }
                }
            }
        }

        $LangLabelsApcKey = md5('language_label_union_other_'.$langugaeCode);
        $getLangLabelsCacheData = $oCache->getData($LangLabelsApcKey);
        if (!empty($getLangLabelsCacheData) && \count($getLangLabelsCacheData) > 0) {
            $all_label_en = $getLangLabelsCacheData;
        } else {
            $sql_en = "SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = '".$langugaeCode."' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = '".$langugaeCode."'";
            $all_label_en = $obj->MySQLSelect($sql_en);
            $oCache->setData($LangLabelsApcKey, $all_label_en);
        }
        if (\count($all_label_en) > 0) {
            for ($i = 0; $i < \count($all_label_en); ++$i) {
                $vLabel_tmp = $all_label_en[$i]['vLabel'];
                $vValue_tmp = $all_label_en[$i]['vValue'];
                if (isset($langage_lbl[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $langage_lbl)) {
                    if ('' === $langage_lbl[$vLabel_tmp]) {
                        $langage_lbl[$vLabel_tmp] = $vValue_tmp;
                    }
                } else {
                    $langage_lbl[$vLabel_tmp] = $vValue_tmp;
                }
            }
        }
        if (empty($langage_lbl) || !empty($_SESSION['sess_iAdminUserId'])) {
            $LangLabelsENApcKey = md5('language_label_union_other_EN');
            $getLangLabelsENCacheData = $oCache->getData($LangLabelsENApcKey);
            if (!empty($getLangLabelsENCacheData) && \count($getLangLabelsENCacheData) > 0) {
                $all_label_en = $getLangLabelsENCacheData;
            } else {
                $all_label_en = $obj->MySQLSelect("SELECT `vLabel` , `vValue` FROM `language_label` WHERE `vCode` = 'EN' UNION SELECT `vLabel` , `vValue` FROM `language_label_other` WHERE `vCode` = 'EN'");
                $oCache->setData($LangLabelsENApcKey, $all_label_en);
            }
        }
        if (empty($langage_lbl)) {
            $LabelsApcKey = md5($cacheKeysArr['language_label_'.$langugaeCode]);
            $getLabelsCacheData = $oCache->getData($LabelsApcKey);
            if (!empty($getLabelsCacheData) && \count($getLabelsCacheData) > 0) {
                $db_lbl = $getLabelsCacheData;
            } else {
                $db_lbl = $obj->MySQLSelect("SELECT vLabel,vValue,LanguageLabelId FROM language_label WHERE vCode='".$langugaeCode."'");
                $oCache->setData($LabelsApcKey, $db_lbl);
            }
            foreach ($db_lbl as $key => $value) {
                if (isset($_SESSION['sess_editingToken']) && $_SESSION['sess_editingToken'] === $db_config[0]['vValue']) {
                    $langage_lbl[$value['vLabel']] = "<em class='label-dynmic'><i class='fa fa-edit label-i' data-id='".$value['LanguageLabelId']."' data-value='other'></i>".$value['vValue'].'</em>';
                } else {
                    $langage_lbl[$value['vLabel']] = $value['vValue'];
                }
            }
            if (\count($all_label_en) > 0) {
                for ($i = 0; $i < \count($all_label_en); ++$i) {
                    $vLabel_tmp = $all_label_en[$i]['vLabel'];
                    $vValue_tmp = $all_label_en[$i]['vValue'];
                    if (isset($langage_lbl[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $langage_lbl)) {
                        if ('' === $langage_lbl[$vLabel_tmp]) {
                            $langage_lbl[$vLabel_tmp] = $vValue_tmp;
                        }
                    } else {
                        $langage_lbl[$vLabel_tmp] = $vValue_tmp;
                    }
                }
            }
        }

        $langage_lbl_admin = [];
        if (!empty($_SESSION['sess_iAdminUserId']) && \count($all_label_en) > 0) {
            for ($i = 0; $i < \count($all_label_en); ++$i) {
                $vLabel_tmp = $all_label_en[$i]['vLabel'];
                $vValue_tmp = $all_label_en[$i]['vValue'];
                if (isset($langage_lbl_admin[$vLabel_tmp]) || \array_key_exists($vLabel_tmp, $langage_lbl_admin)) {
                    if ('' === $langage_lbl_admin[$vLabel_tmp]) {
                        $langage_lbl_admin[$vLabel_tmp] = $vValue_tmp;
                    }
                } else {
                    $langage_lbl_admin[$vLabel_tmp] = $vValue_tmp;
                }
            }
            if ($ServiceData[0]['iServiceId'] > 0) {
                if (-1 !== $service_id_admin) {
                    $iServiceIdWeb = $service_id_admin;
                } else {
                    $iServiceIdWeb = $ServiceData[0]['iServiceId'];
                }
                $db_lbl_admin = $obj->MySQLSelect('SELECT vLabel,vValue FROM language_label_'.$iServiceIdWeb." WHERE vCode='EN'");
                foreach ($db_lbl_admin as $key => $value) {
                    $langage_lbl_admin[$value['vLabel']] = $value['vValue'];
                }
            }
        } else {
            $langage_lbl_admin = $langage_lbl;
        }
        $lang = $_REQUEST['lang'] ?? '';
        if (isset($lang) && '' !== $lang) {
            $_SESSION['sess_lang'] = $lang;
            $sql1 = "select vTitle, vCode, vCurrencyCode, eDefault,eDirectionCode from language_master where vCode = '".$_SESSION['sess_lang']."' limit 0,1";
            $db_lng_mst1 = $obj->MySQLSelect($sql1);
            $_SESSION['eDirectionCode'] = $db_lng_mst1[0]['eDirectionCode'];
            $posturi = $_SERVER['HTTP_REFERER'];
            if (isset($_REQUEST['HTTP_REFERER'])) {
                $posturi = urldecode($_REQUEST['HTTP_REFERER']);
            }
            header('Location:'.$posturi);

            exit;
        }
        \define('RIIDE_LATER', 'YES');
        \define('PROMO_CODE', 'YES');

        require_once TPATH_CLASS.'Models/class.payment.gateways.php';

        require_once TPATH_CLASS.'Models/class.comm_media.php';

        require_once TPATH_CLASS.'Models/class.change_file.php';

        require_once TPATH_CLASS.'site_variables.php';

        $MODULES_OBJ = new Modules();
        $AUTH_OBJ = new AuthLogin();
        $COMM_MEDIA_OBJ = new CommMedia();
        $REFERRAL_OBJ = new Referral();
        $WALLET_OBJ = new Wallet();
        $STATIC_PAGE_OBJ = new StaticPage();
        $UPLOAD_OBJ = new UploadFile();
        $THEME_OBJ->getTheme();
        $template = $THEME_OBJ->getTemplate();
        $templatePath = $THEME_OBJ->getTemplatePath();
        $logogpath = $THEME_OBJ->getLogoPath();
        if (!empty($GOOGLE_ANALYTICS)) {
            $GOOGLE_ANALYTICS = "<!-- Global site tag (gtag.js) - Google Analytics --><script async src='https://www.googletagmanager.com/gtag/js?id={$GOOGLE_ANALYTICS}'></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '{$GOOGLE_ANALYTICS}');</script>";
        }


        self::redefineVariables(get_defined_vars());
        if (str_contains($_SERVER['REQUEST_URI'], '/'.SITE_ADMIN_URL)) {
            include_once $tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/library/common_include.php';
            $userObj = new User();
            if (!\in_array(basename($_SERVER['REQUEST_URI']), ['index.php', 'ajax_login_action.php'], true)) {
                $userObj->isLogin(true);
            }
        }
        if ('Yes' === $MAINTENANCE_WEBSITE) {
            $exclude_maintenance = ['/'.SITE_ADMIN_URL, 'safety_checklist.php', 'system_payment.php', 'payment_mode_select.php', 'webview', WEBSERVICE_API_FILE_NAME];
            if (false !== stripos_arr($_SERVER['REQUEST_URI'], $exclude_maintenance)) {
            } elseif (!isset($_REQUEST['maintanance'])) {
                header('Location:'.$tconfig['tsite_url'].'maintanance?maintanance=yes');

                exit;
            }
        } else {
            if (isset($_REQUEST['maintanance'])) {
                header('Location:'.$tconfig['tsite_url']);

                exit;
            }
        }

        require_once $BASE_DOCUMENT_PATH.'/DataHelper.php';

        $dataHelperObj = new DataHelper();
        $_SESSION['sess_hosttype'] = 'ufxall';
        $IS_CONTINUE_DELETE_PROCESS = empty($IS_INHOUSE_DOMAINS) || false === $IS_INHOUSE_DOMAINS ? true : false;

        include_once $tconfig['tpanel_path'].'include_config.php';
        $generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
        if (empty($isUfxAvailable)) {
            $isUfxAvailable = $MODULES_OBJ->isUfxFeatureAvailable();
        }

        require_once TPATH_CLASS.'class.ExifCleaning.php';

        require_once TPATH_CLASS.'Imagecrop.class.php';

        include_once $tconfig['tpanel_path'].'assets/libraries/include_advance_api.php';

        $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
        $currencyApcKey = md5($cacheKeysArr['currency']);
        $getCurrencyCacheData = $oCache->getData($currencyApcKey);
        if (!empty($getCurrencyCacheData) && \count($getCurrencyCacheData) > 0) {
            $Data_ALL_currency_Arr = $getCurrencyCacheData;
        } else {
            $Data_ALL_currency_Arr = $obj->MySQLSelect('SELECT * FROM currency ORDER BY iDispOrder ASC');
            $setCurrencyCacheData = $oCache->setData($currencyApcKey, $Data_ALL_currency_Arr);
        }
        $active_currency_name_arr = $currency_arr = $currencyAssociateArr = [];
        foreach ($Data_ALL_currency_Arr as $currency_item) {
            if ('YES' === strtoupper($currency_item['eDefault'])) {
                $vSystemDefaultCurrencyName = $currency_item['vName'];
                $vSystemDefaultCurrencySymbol = $currency_item['vSymbol'];
                $vSystemDefaultCurrencyRatio = $currency_item['Ratio'];
            }
            $currency_arr[] = $currency_item;
            $currencyAssociateArr[trim($currency_item['vName'])] = $currency_item;
            if ('Active' === $currency_item['eStatus']) {
                $active_currency_name_arr[] = $currency_item['vName'];
            }
        }
        $vSystemDefaultCurrencyName = empty($vSystemDefaultCurrencyName) ? 'USD' : $vSystemDefaultCurrencyName;
        $vSystemDefaultCurrencySymbol = empty($vSystemDefaultCurrencySymbol) ? '$' : $vSystemDefaultCurrencySymbol;
        $vSystemDefaultCurrencyRatio = empty($vSystemDefaultCurrencyRatio) ? '1.00' : $vSystemDefaultCurrencyRatio;
        $countryApcKey = md5($cacheKeysArr['country']);
        $getCountryCacheData = $oCache->getData($countryApcKey);
        if (!empty($getCountryCacheData) && \count($getCountryCacheData) > 0) {
            $country_data_retrieve = $getCountryCacheData;
        } else {
            $country_data_retrieve = $obj->MySQLSelect('SELECT * FROM country');
            $setCountryCacheData = $oCache->setData($countryApcKey, $country_data_retrieve);
        }
        $country_data_arr = $countryAssociateArr = [];
        foreach ($country_data_retrieve as $country_data_retrieve_item) {
            $country_data_arr[$country_data_retrieve_item['vCountryCode']] = $country_data_retrieve_item;
            $countryAssociateArr[$country_data_retrieve_item['iCountryId']] = $country_data_retrieve_item;
        }
        $VehicleCategoryApcKey = md5($cacheKeysArr['vehicle_category'].'_ufx');
        $getVehicleCategoryCacheData = $oCache->getData($VehicleCategoryApcKey);
        if (!empty($getVehicleCategoryCacheData) && \count($getVehicleCategoryCacheData) > 0) {
            $allUfxVehicleCategoryData = $getVehicleCategoryCacheData;
        } else {
            $allUfxVehicleCategoryData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE 1=1 AND eCatType = 'ServiceProvider'");
            $oCache->setData($VehicleCategoryApcKey, $allUfxVehicleCategoryData);
        }
        $vehcleCategoryAssocArr = [];
        for ($hj = 0; $hj < \count($allUfxVehicleCategoryData); ++$hj) {
            $vehcleCategoryAssocArr[$allUfxVehicleCategoryData[$hj]['iVehicleCategoryId']] = $allUfxVehicleCategoryData[$hj];
        }
        $MasterCategoryApcKey = md5($cacheKeysArr['master_vehicle_category']);
        $getMasterCategoryCacheData = $oCache->getData($MasterCategoryApcKey);
        if (!empty($getMasterCategoryCacheData) && \count($getMasterCategoryCacheData) > 0) {
            $allUfxMasterCategory = $getMasterCategoryCacheData;
        } else {
            $allUfxMasterCategory = $obj->MySQLSelect('SELECT * FROM `master_vehicle_category` WHERE 1=1');
            $oCache->setData($MasterCategoryApcKey, $allUfxMasterCategory);
        }
        $masterVehcleCategoryAssocArr = [];
        for ($jh = 0; $jh < \count($allUfxMasterCategory); ++$jh) {
            $masterVehcleCategoryAssocArr[$allUfxMasterCategory[$jh]['iMasterVehicleCategoryId']] = $allUfxMasterCategory[$jh];
        }

        require_once TPATH_CLASS.'Models/class.dashboard.php';
        $DASHBOARD_OBJ = new Dashboard();

        require_once TPATH_CLASS.'include_features.php';

        if (file_exists(TPATH_CLASS.'include_common.php')) {
            require_once TPATH_CLASS.'include_common.php';
        }

        self::redefineVariables(get_defined_vars());


        return self::declareCurrentScopeVariables();
    }

    public static function configureSystemType(): void
    {
        extract(self::declareCurrentScopeVariables(), EXTR_REFS | EXTR_OVERWRITE);
        if (false !== self::strpos_arr($_SERVER['HTTP_HOST'], self::$ALLOWED_INHOUSE_DOMAINS)) {
            if (!empty($_REQUEST['CUS_APP_TYPE'])) {
                $APP_TYPE = $_REQUEST['CUS_APP_TYPE'];
                \define('APP_TYPE', $APP_TYPE);
            }
            if (!empty($_REQUEST['CUS_PACKAGE_TYPE'])) {
                $PACKAGE_TYPE = $_REQUEST['CUS_PACKAGE_TYPE'];
                \define('PACKAGE_TYPE', $PACKAGE_TYPE);
            }
            if (!empty($_REQUEST['CUS_PARENT_UFX_CATID'])) {
                $CUS_PARENT_UFX_CATID = $_REQUEST['CUS_PARENT_UFX_CATID'];
                \define('CUS_PARENT_UFX_CATID', $CUS_PARENT_UFX_CATID);
            }
        } elseif (!self::checkClientServer()) {
            header('Location: error.php');

            exit;
        }
        if (empty($APP_TYPE)) {
            $APP_TYPE = 'Ride-Delivery-UberX';
        }
        if (!\defined('APP_TYPE')) {
            \define('APP_TYPE', $APP_TYPE);
        }
        if (empty($PACKAGE_TYPE)) {
            $PACKAGE_TYPE = 'SHARK';
        }
        if (!\defined('PACKAGE_TYPE')) {
            \define('PACKAGE_TYPE', $PACKAGE_TYPE);
        }
        if (empty($CUS_PARENT_UFX_CATID)) {
            $parent_ufx_catid = '0';
        } else {
            $parent_ufx_catid = $CUS_PARENT_UFX_CATID;
        }
        self::redefineVariables(get_defined_vars());

    }

    public static function checkClientServer()
    {
        // if (!in_array($_SERVER["HTTP_HOST"], SystemInfo::$ALLOWED_CLIENT_DOMAINS)) {
        //     return false;
        // } elseif (strtotime(SystemInfo::$EXPIRY_DATE) < strtotime(date('Y-m-d')) && SystemInfo::$EXPIRY_DATE != '0000-00-00') {
        //     return false;
        // }
        return true;
    }

    private static function strpos_arr($haystack, $needle, $offset = 0)
    {
        if (!\is_array($needle)) {
            $needle = [$needle];
        }
        foreach ($needle as $query) {
            if (false !== strpos($haystack, $query, $offset)) {
                return true;
            }
        }

        return false;
    }
}
