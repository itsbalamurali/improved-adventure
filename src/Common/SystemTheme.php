<?php



namespace Kesk\Web\Common;

class SystemTheme
{
    private $sys_template = '';
    private $sys_templatePath = '';
    private $sys_logogpath = '';

    public function __construct() {}

    public static function getInstance()
    {
        return new self();
    }

    public function getTheme(): void
    {
        global $APP_TYPE, $tconfig, $obj, $parent_ufx_catid;
        $tab = 'false';
        if ('Ride' === $APP_TYPE) {
            $tab = 'true';
            $template = 'Ride';
        } elseif ('Delivery' === $APP_TYPE) {
            $tab = 'true';
            $template = 'Delivery';
        } elseif ('Ride-Delivery' === $APP_TYPE) {
            $tab = 'true';
            $template = 'Ride-Delivery';
        } elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
            $tab = 'true';
            $template = 'Ride-Delivery-UberX';
        } elseif ('UberX' === $APP_TYPE) {
            if ('Yes' === self::isServiceXThemeActive()) {
                $tName = 'ServiceX';
            } elseif ('Yes' === self::isServiceXv2ThemeActive()) {
                $tName = 'ServiceXv2';
            } elseif ('Yes' === self::isProSPThemeActive()) {
                $tName = 'ProService';
            } else {
                $tName = 'UberX';
            }
            $Ssql = "SELECT iMasterVehicleCategoryId FROM `vehicle_category` WHERE eStatus='Active' and iParentId=0 and iMasterVehicleCategoryId > 0 AND iVehicleCategoryId='".$parent_ufx_catid."'";
            $ServiceData = $obj->MySQLSelect($Ssql);
            if (!empty($ServiceData)) {
                if (\count($ServiceData) < 1) {
                    $vService = $tName;
                } else {
                    $vService = $iMasterVehicleCategoryId = $ServiceData[0]['iMasterVehicleCategoryId'];
                }
            } else {
                $vService = $tName;
                if ('Yes' === self::isServiceXv2ThemeActive() || 'Yes' === self::isProSPThemeActive()) {
                    $vService = 'ServiceX';
                }
            }
            $template = $tName.'/'.$vService;
            if (file_exists($tconfig['tpanel_path'].'templates/'.$template.'/')) {
                $template = $tName.'/'.$vService;
            } else {
                $template = $tName.'/'.$tName;
            }
        } elseif ('Delivery' === $APP_TYPE) {
            $template = 'uber';
        }
        if ('Yes' === self::isCubexThemeActive()) {
            $template = 'Cubex';
        } elseif ('Yes' === self::isCubeJekXThemeActive()) {
            $template = 'CJX';
        } elseif ('Yes' === self::isCubeJekXv2ThemeActive()) {
            $template = 'CJXv2';
        } elseif ('Yes' === self::isCubeJekXv3ThemeActive()) {
            $template = 'CJXv3';
        } elseif ('Yes' === self::isCubeJekXv3ProThemeActive()) {
            $template = 'CJXv3Pro';
        } elseif ('Yes' === self::isCJXDoctorv2ThemeActive()) {
            $template = 'CJDoc';
        } elseif ('Yes' === self::isRideCXThemeActive()) {
            $template = 'RideCX';
        } elseif ('Yes' === self::isDeliveryXThemeActive()) {
            $template = 'DeliveryX';
        } elseif ('Yes' === self::isDeliveryKingThemeActive()) {
            $template = 'DeliveryKing';
        } elseif ('Yes' === self::isRideDeliveryXThemeActive()) {
            $template = 'Ride-Delivery-X';
        } elseif ('Yes' === self::isRideCXv2ThemeActive()) {
            $template = 'RideCXv2';
        } elseif ('Yes' === self::isCubeXv2ThemeActive()) {
            $template = 'CubeXv2';
        } elseif ('Yes' === self::isDeliveryKingXv2ThemeActive()) {
            $template = 'DeliveryKingv2';
        } elseif ('Yes' === self::isDeliveryXv2ThemeActive()) {
            $template = 'DeliveryXv2';
        } elseif ('Yes' === self::isMedicalServicev2ThemeActive()) {
            $template = 'CJDocv2';
        } elseif ('Yes' === self::isPXTProThemeActive()) {
            $template = 'PXTPro';
        } elseif ('Yes' === self::isPXRDProThemeActive()) {
            $template = 'PXRDPro';
        } elseif ('Yes' === self::isPXCProThemeActive()) {
            $template = 'PXCPro';
        } elseif ('Yes' === self::isProDeliveryThemeActive()) {
            $template = 'ProDY';
        } elseif ('Yes' === self::isProDeliveryKingThemeActive()) {
            $template = 'ProDK';
        }
        if (ONLYDELIVERALL === 'Yes') {
            $template = $this->getDeliverAllTheme();
        }
        $this->sys_template = $template;
        $this->sys_templatePath = 'templates/'.$template.'/';
        $this->sys_logogpath = 'assets/img/apptype/'.$template.'/';
        SystemInfo::redefineVariables(get_defined_vars());
    }

    public function getTemplate()
    {
        return $this->sys_template;
    }

    public function getTemplatePath()
    {
        return $this->sys_templatePath;
    }

    public function getLogoPath()
    {
        return $this->sys_logogpath;
    }

    public static function isCubeJekXThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_CUBEJEK_X_THEME) && ENABLE_CUBEJEK_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCubeJekXv2ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_CUBEJEK_X_V2_THEME) && ENABLE_CUBEJEK_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCJXDoctorv2ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_CJX_X_DOCTOR_V2_THEME) && ENABLE_CJX_X_DOCTOR_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isXThemeActive()
    {
        return 'Yes';
    }

    public static function isRideCXThemeActive()
    {
        global $APP_TYPE;
        if ('Ride' === $APP_TYPE && !empty(ENABLE_RIDE_CX_THEME) && ENABLE_RIDE_CX_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliverallXThemeActive()
    {
        if (ONLYDELIVERALL === 'Yes' && !empty(ENABLE_DELIVERALL_X_THEME) && ENABLE_DELIVERALL_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliverallXv2ThemeActive()
    {
        if (ONLYDELIVERALL === 'Yes' && !empty(ENABLE_DELIVERALL_X_V2_THEME) && ENABLE_DELIVERALL_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isRideDeliveryXThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery' === $APP_TYPE && !empty(ENABLE_RIDE_DELIVERY_X_THEME) && ENABLE_RIDE_DELIVERY_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliveryXThemeActive()
    {
        global $APP_TYPE;
        if ('Delivery' === $APP_TYPE && !empty(ENABLE_DELIVERY_X_THEME) && ENABLE_DELIVERY_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliveryKingThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_DELIVERYKING_THEME) && ENABLE_DELIVERYKING_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isServiceXThemeActive()
    {
        global $APP_TYPE;
        if ('UBERX' === strtoupper(APP_TYPE) && !empty(ENABLE_SERVICE_X_THEME) && ENABLE_SERVICE_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCubexThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(IS_CUBE_X_THEME) && IS_CUBE_X_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCubeJekXv3ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_CUBEJEK_X_V3_THEME) && ENABLE_CUBEJEK_X_V3_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCubeJekXv3ProThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_CUBEJEK_X_V3_PRO_THEME) && ENABLE_CUBEJEK_X_V3_PRO_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isServiceXv2ThemeActive()
    {
        global $APP_TYPE;
        if ('UBERX' === strtoupper(APP_TYPE) && !empty(ENABLE_SERVICE_X_V2_THEME) && ENABLE_SERVICE_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isRideCXv2ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride' === $APP_TYPE && !empty(ENABLE_RIDE_CX_V2_THEME) && ENABLE_RIDE_CX_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isCubeXv2ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(IS_CUBE_X_V2_THEME) && IS_CUBE_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliveryKingXv2ThemeActive()
    {
        global $APP_TYPE;
        define('ENABLE_DELIVERYKING_X_V2_THEME', '');
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_DELIVERYKING_X_V2_THEME) && ENABLE_DELIVERYKING_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isDeliveryXv2ThemeActive()
    {
        global $APP_TYPE;
        define('ENABLE_DELIVERY_X_V2_THEME', '');
        if ('Delivery' === $APP_TYPE && !empty(ENABLE_DELIVERY_X_V2_THEME) && ENABLE_DELIVERY_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isRideDeliveryXv2ThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery' === $APP_TYPE && !empty(\ENABLE_RIDE_DELIVERY_X_V2_THEME) && \ENABLE_RIDE_DELIVERY_X_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isMedicalServicev2ThemeActive()
    {
        global $APP_TYPE;
        define('ENABLE_MEDICAL_SERVICE_V2_THEME', '');
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(\ENABLE_MEDICAL_SERVICE_V2_THEME) && \ENABLE_MEDICAL_SERVICE_V2_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isPXTProThemeActive()
    {
        global $APP_TYPE;
        if ('Ride' === $APP_TYPE && !empty(\ENABLE_PXTPRO_THEME) && \ENABLE_PXTPRO_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isPXRDProThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery' === $APP_TYPE && !empty(\ENABLE_PXRDPRO_THEME) && \ENABLE_PXRDPRO_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isPXCProThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_PXCPRO_THEME) && ENABLE_PXCPRO_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isProSPThemeActive()
    {
        global $APP_TYPE;
        if ('UberX' === $APP_TYPE && !empty(ENABLE_PROSP_THEME) && ENABLE_PROSP_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isProDeliverallThemeActive()
    {
        if (ONLYDELIVERALL === 'Yes' && !empty(ENABLE_PRO_DELIVERALL_THEME) && ENABLE_PRO_DELIVERALL_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isProDeliveryThemeActive()
    {
        global $APP_TYPE;
        if ('Delivery' === $APP_TYPE && !empty(ENABLE_PRO_DELIVERY_THEME) && ENABLE_PRO_DELIVERY_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isProDeliveryKingThemeActive()
    {
        global $APP_TYPE;
        if ('Ride-Delivery-UberX' === $APP_TYPE && !empty(ENABLE_PRO_DELIVERYKING_THEME) && ENABLE_PRO_DELIVERYKING_THEME === 'Yes') {
            return 'Yes';
        }

        return 'No';
    }

    public static function isProThemeActive()
    {
        if ('Yes' === self::isCubeJekXv3ProThemeActive() || 'Yes' === self::isPXTProThemeActive() || 'Yes' === self::isPXRDProThemeActive() || 'Yes' === self::isPXCProThemeActive() || 'Yes' === self::isProDeliverallThemeActive() || 'Yes' === self::isProSPThemeActive() || 'Yes' === self::isProDeliveryThemeActive() || 'Yes' === self::isProDeliveryKingThemeActive()) {
            return 'Yes';
        }

        return 'No';
    }

    private function getDeliverAllTheme()
    {
        global $tconfig;
        $tab = 'true';
        $template = '';
        $eDeliverallTheme = self::isDeliverallXThemeActive();
        $eDeliverallThemev2 = self::isDeliverallXv2ThemeActive();
        $eProDeliverallTheme = self::isProDeliverallThemeActive();
        $serviceCategories_data = json_decode(serviceCategories);
        if (!empty($serviceCategories_data)) {
            if (\count($serviceCategories_data) > 1) {
                $template = 'Deliverall/deliverall';
                if ('YES' === strtoupper($eDeliverallTheme)) {
                    $template = 'DeliverallX/deliverall';
                }
                if ('YES' === strtoupper($eDeliverallThemev2)) {
                    $template = 'DeliverallXv2/deliverall';
                }
                if ('YES' === strtoupper($eProDeliverallTheme)) {
                    $template = 'ProDAX/deliverall';
                }
            } else {
                $vService = $serviceCategories_data[0]->vService;
                $template = 'Deliverall/'.$vService;
                if ('YES' === strtoupper($eDeliverallTheme)) {
                    $template = 'DeliverallX/'.$vService;
                }
                if ('YES' === strtoupper($eDeliverallThemev2)) {
                    $template = 'DeliverallXv2/'.$vService;
                }
                if ('YES' === strtoupper($eProDeliverallTheme)) {
                    $template = 'ProDAX/'.$vService;
                }
                if (!file_exists($tconfig['tpanel_path'].'templates/'.$template.'/')) {
                    $template = 'Deliverall/deliverall';
                    if ('YES' === strtoupper($eDeliverallTheme)) {
                        $template = 'DeliverallX/deliverall';
                    }
                    if ('YES' === strtoupper($eDeliverallThemev2)) {
                        $template = 'DeliverallXv2/deliverall';
                    }
                    if ('YES' === strtoupper($eProDeliverallTheme)) {
                        $template = 'ProDAX/deliverall';
                    }
                }
            }
        }
        SystemInfo::redefineVariables(get_defined_vars());

        return $template;
    }
}
