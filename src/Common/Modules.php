<?php



namespace Kesk\Web\Common;

class Modules
{
    public function __construct() {}

    public function isAirFlightModuleAvailable($admin = '', $isFromConfig = 'No')
    {
        global $ENABLE_FLY_VEHICLES, $tconfig, $APP_TYPE, $obj;
        if ('RIDE' === strtoupper($APP_TYPE) || 'DELIVERY' === strtoupper($APP_TYPE) || 'RIDE-DELIVERY' === strtoupper($APP_TYPE) || 'UBERX' === strtoupper($APP_TYPE) || 'YES' === strtoupper(ONLYDELIVERALL)) {
            return false;
        }
        $fly_stations_filepath = $tconfig['tpanel_path'].'include/features/include_fly_stations.php';
        if (empty($ENABLE_FLY_VEHICLES)) {
            $ENABLE_FLY_VEHICLES = get_value('configurations', 'vValue', 'vName', 'ENABLE_FLY_VEHICLES', '', true);
        }
        $fly_data = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM '.getVehicleCategoryTblName()." WHERE eCatType = 'Fly' AND eStatus != 'Deleted'");
        if (file_exists($fly_stations_filepath) && 'YES' === strtoupper($ENABLE_FLY_VEHICLES) && !empty($fly_data)) {
            $flyModuleAvailable = true;
            if ($flyModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig)) {
                return $this->isRideFeatureAvailable();
            }

            return $flyModuleAvailable;
        }

        return false;
    }

    public function isGojekGopayModuleAvailable()
    {
        global $obj, $APP_TYPE, $PACKAGE_TYPE, $generalSystemConfigDataArr, $tconfig;
        $gojek_gopay_filepath = $tconfig['tpanel_path'].'include/features/include_gojek_gopay.php';
        if (!empty($generalSystemConfigDataArr['ENABLE_GOPAY'])) {
            $EnableGopay = $generalSystemConfigDataArr['ENABLE_GOPAY'];
        } else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY', '', true);
        }
        if (file_exists($gojek_gopay_filepath) && 'YES' === strtoupper($EnableGopay)) {
            return true;
        }

        return false;
    }

    public function isDriverSubscriptionModuleAvailable()
    {
        global $obj, $APP_TYPE, $PACKAGE_TYPE, $CONFIG_OBJ, $generalSystemConfigDataArr, $DRIVER_SUBSCRIPTION_ENABLE, $tconfig;
        $DriverSubscriptionFilepath = $tconfig['tpanel_path'].'include/features/include_driver_subscription.php';
        if (empty($DRIVER_SUBSCRIPTION_ENABLE)) {
            $DRIVER_SUBSCRIPTION_ENABLE = $DRIVER_SUBSCRIPTION_ENABLE[0]['vValue'];
        }
        if (file_exists($DriverSubscriptionFilepath) && 'YES' === strtoupper($DRIVER_SUBSCRIPTION_ENABLE) && ONLYDELIVERALL !== 'Yes') {
            return true;
        }

        return false;
    }

    public function isOrganizationModuleEnable()
    {
        global $tab, $ENABLE_CORPORATE_PROFILE;
        if (PACKAGE_TYPE === 'SHARK' && ('true' === $tab) && 'Yes' === $ENABLE_CORPORATE_PROFILE && 'DELIVERY' !== strtoupper(APP_TYPE) && 'UBERX' !== strtoupper(APP_TYPE) && 'YES' !== strtoupper(ONLYDELIVERALL)) {
            $IS_CORPORATE_PROFILE_ENABLED = 'YES' === strtoupper($ENABLE_CORPORATE_PROFILE) ? true : false;
        } else {
            $IS_CORPORATE_PROFILE_ENABLED = false;
        }

        return $IS_CORPORATE_PROFILE_ENABLED;
    }

    public function isInsuranceReportEnable()
    {
        global $ENABLE_INSURANCE_IDLE_REPORT, $ENABLE_INSURANCE_ACCEPT_REPORT, $ENABLE_INSURANCE_TRIP_REPORT;
        if (PACKAGE_TYPE === 'SHARK' && 'UBERX' !== strtoupper(APP_TYPE) && 'YES' !== strtoupper(ONLYDELIVERALL) && ('Yes' === $ENABLE_INSURANCE_IDLE_REPORT || 'Yes' === $ENABLE_INSURANCE_ACCEPT_REPORT || 'Yes' === $ENABLE_INSURANCE_TRIP_REPORT)) {
            return true;
        }

        return false;
    }

    public function isManualBookingAvailable()
    {
        global $THEME_OBJ;
        if (('YES' === strtoupper($THEME_OBJ->isXThemeActive())) && 'YES' === strtoupper(ENABLE_EXTENDED_VERSION_MANUAL_BOOKING)) {
            return true;
        }

        return false;
    }

    public function isMongoDBAvailable()
    {
        global $obj;
        if ('YES' === strtoupper(ENABLE_MONGO_CONNECTION) && $obj->isMongoDBConnected()) {
            return true;
        }

        return false;
    }

    public function isMemcachedAvailable()
    {
        if (!empty(ENABLE_MEMCACHED) && 'YES' === strtoupper(ENABLE_MEMCACHED)) {
            return true;
        }

        return false;
    }

    public function mapAPIreplacementAvailable()
    {
        global $tconfig, $addOnsDataArr_orig, $obj;
        $isMapiReplacementAvail = false;
        if ($this->isMongoDBAvailable() && (file_exists($tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/map_api_setting.php') && file_exists($tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/map_api_mongo_auth_places_action.php') && file_exists($tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/map_api_mongo_auth_places.php'))) {
            if (empty($addOnsDataArr_orig)) {
                $addOnsDataArr_orig = $obj->MySQLSelect('SELECT lAddOnConfiguration,eCubejekX,eCubeX FROM setup_info LIMIT 0,1');
            }
            $addOnsJSONObj = json_decode($addOnsDataArr_orig[0]['lAddOnConfiguration'], true);
            $GOOGLE_PLAN_VAL = (int) $addOnsJSONObj['GOOGLE_PLAN'];
            $GOOGLE_PLAN = empty($addOnsJSONObj['GOOGLE_PLAN']) ? 'No' : ((1 === $GOOGLE_PLAN_VAL || 2 === $GOOGLE_PLAN_VAL || 3 === $GOOGLE_PLAN_VAL) ? 'Yes' : 'No');
            if ('Yes' === $GOOGLE_PLAN) {
                $isMapiReplacementAvail = true;
            }
        }

        return $isMapiReplacementAvail;
    }

    public function isDocumentExpiredFeatureEnable()
    {
        return !empty(ENABLE_EXPIRE_DOCUMENT) && 'YES' === strtoupper(ENABLE_EXPIRE_DOCUMENT) ? true : false;
    }

    public function isAutoCreditToDriverModuleAvailable()
    {
        global $obj, $generalSystemConfigDataArr, $tconfig;
        $auto_credit_driver_filepath = $tconfig['tpanel_path'].'include/features/include_auto_credit_driver.php';
        if (!empty($generalSystemConfigDataArr['CREDIT_TO_WALLET_ENABLE'])) {
            $EnableCreditWalletDriver = $generalSystemConfigDataArr['CREDIT_TO_WALLET_ENABLE'];
        } else {
            $EnableCreditWalletDriver = get_value('configurations_payment', 'vValue', 'vName', 'CREDIT_TO_WALLET_ENABLE', '', true);
        }
        if (file_exists($auto_credit_driver_filepath) && 'YES' === strtoupper($EnableCreditWalletDriver)) {
            return true;
        }

        return false;
    }

    public function isEnableHotelPanel()
    {
        if ('UBERX' === strtoupper(APP_TYPE) || 'DELIVERY' === strtoupper(APP_TYPE) || 'YES' === strtoupper(ONLYDELIVERALL) || !$this->isRideFeatureAvailable()) {
            return false;
        }

        return !empty(ENABLEHOTELPANEL) && 'YES' === strtoupper(ENABLEHOTELPANEL) ? true : false;
    }

    public function isEnableKioskPanel()
    {
        if ('UBERX' === strtoupper(APP_TYPE) || 'DELIVERY' === strtoupper(APP_TYPE) || 'YES' === strtoupper(ONLYDELIVERALL) || !$this->isRideFeatureAvailable()) {
            return false;
        }

        return !empty(ENABLEKIOSKPANEL) && 'YES' === strtoupper(ENABLEKIOSKPANEL) ? true : false;
    }

    public function isStoreClassificationEnable()
    {
        global $ENABLE_STORE_CATEGORIES_MODULE;
        if (!empty($ENABLE_STORE_CATEGORIES_MODULE) && 'YES' === strtoupper($ENABLE_STORE_CATEGORIES_MODULE)) {
            return true;
        }

        return false;
    }

    public function isFavouriteStoreModuleAvailable()
    {
        global $ENABLE_FAVORITE_STORE_MODULE, $tconfig;
        $fav_store_file_path = $tconfig['tpanel_path'].'include/features/include_fav_store.php';
        if (file_exists($fav_store_file_path) && 'YES' === strtoupper($ENABLE_FAVORITE_STORE_MODULE) && 'YES' === strtoupper(DELIVERALL)) {
            return true;
        }

        return false;
    }

    public function isDeliveryPreferenceEnable()
    {
        global $ENABLE_DELIVERY_PREFERENCE;

        return !empty($ENABLE_DELIVERY_PREFERENCE) && 'YES' === strtoupper($ENABLE_DELIVERY_PREFERENCE) ? true : false;
    }

    public function isTakeAwayEnable()
    {
        global $APP_PAYMENT_MODE, $ENABLE_TAKE_AWAY;
        if ('Yes' === $ENABLE_TAKE_AWAY && 'CASH' !== strtoupper($APP_PAYMENT_MODE)) {
            return true;
        }

        return false;
    }

    public function isSingleStoreSelection()
    {
        global $IS_SINGLE_STORE_SELECTION;
        if (!empty($IS_SINGLE_STORE_SELECTION) && 'YES' === strtoupper($IS_SINGLE_STORE_SELECTION)) {
            return true;
        }

        return false;
    }

    public function isStorePersonalDriverAvailable()
    {
        global $ENABLE_ADD_PROVIDER_FROM_STORE;
        if (!empty($ENABLE_ADD_PROVIDER_FROM_STORE) && 'YES' === strtoupper($ENABLE_ADD_PROVIDER_FROM_STORE)) {
            return true;
        }

        return false;
    }

    public function isEnableStoreSafetyProcedure()
    {
        global $ENABLE_SAFETY_PRACTICE;
        if (!empty($ENABLE_SAFETY_PRACTICE) && 'YES' === strtoupper($ENABLE_SAFETY_PRACTICE)) {
            return true;
        }

        return false;
    }

    public function isRideFeatureAvailable($isFromConfig = 'No')
    {
        if ((!empty(ONLYDELIVERALL) && 'YES' === strtoupper(ONLYDELIVERALL)) || 'DELIVERY' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || (\defined('ENABLE_DELIVERYKING_THEME') && 'YES' === strtoupper(ENABLE_DELIVERYKING_THEME)) || 'NO' === strtoupper(RIDE_ENABLED)) {
            return false;
        }
        $rideModuleAvailable = (!empty(RIDE_MODULE_AVAILABLE) && 'NO' === strtoupper(RIDE_MODULE_AVAILABLE)) || 'NO' === strtoupper(RIDE_ENABLED) ? false : true;
        if ($rideModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('Ride') ? true : false;
        }

        return $rideModuleAvailable;
    }

    public function isDeliverAllFeatureAvailable($isFromConfig = 'No')
    {
        if ((!empty(DELIVERALL) && 'NO' === strtoupper(DELIVERALL)) || 'RIDE' === strtoupper(APP_TYPE) || 'DELIVERY' === strtoupper(APP_TYPE) || 'RIDE-DELIVERY' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || 'NO' === strtoupper(DELIVERALL_ENABLED)) {
            return false;
        }
        $deliverallModuleAvailable = (!empty(DELIVERALL_MODULE_AVAILABLE) && 'NO' === strtoupper(DELIVERALL_MODULE_AVAILABLE)) || 'NO' === strtoupper(DELIVERALL_ENABLED) ? false : true;
        if ($deliverallModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            if ($this->isEnableAppHomeScreenLayoutV3()) {
                return isMasterServiceCategoryAvailable('DeliverAll') ? true : false;
            }

            return isMasterServiceCategoryAvailable('Deliver') ? true : false;
        }

        return $deliverallModuleAvailable;
    }

    public function isDeliveryFeatureAvailable($isFromConfig = 'No')
    {
        if ((!empty(ONLYDELIVERALL) && 'YES' === strtoupper(ONLYDELIVERALL)) || 'RIDE' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || 'NO' === strtoupper(DELIVERY_ENABLED)) {
            return false;
        }
        $deliveryModuleAvailable = (!empty(DELIVERY_MODULE_AVAILABLE) && 'NO' === strtoupper(DELIVERY_MODULE_AVAILABLE)) || 'NO' === strtoupper(DELIVERY_ENABLED) ? false : true;
        if ($deliveryModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('Deliver') ? true : false;
        }

        return $deliveryModuleAvailable;
    }

    public function isUberXFeatureAvailable($isFromConfig = 'No')
    {
        global $obj, $oCache;
        if ('NO' === strtoupper(ENABEL_SERVICE_PROVIDER_MODULE) || (\defined('ENABLE_DELIVERYKING_THEME') && 'YES' === strtoupper(ENABLE_DELIVERYKING_THEME)) || 'NO' === strtoupper(UFX_ENABLED)) {
            return false;
        }
        if ('YES' === strtoupper(ONLYDELIVERALL)) {
            return false;
        }
        if (!empty(IS_CUBE_X_THEME) && IS_CUBE_X_THEME === 'Yes') {
            return false;
        }
        if ('RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || 'YES' === strtoupper(UFX_ENABLED)) {
            $ufx_data = $obj->MySQLSelect('SELECT COUNT(iVehicleCategoryId) AS Total FROM '.getVehicleCategoryTblName()." WHERE eCatType = 'ServiceProvider'");
            if (!empty($ufx_data[0]['Total']) && $ufx_data[0]['Total'] > 0) {
                $ufxModuleAvailable = true;
                if ($ufxModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
                    return isMasterServiceCategoryAvailable('UberX') ? true : false;
                }

                return $ufxModuleAvailable;
            }

            return false;
        }

        return false;
    }

    public function isRentalFeatureAvailable()
    {
        if ((!empty(ONLYDELIVERALL) && 'YES' === strtoupper(ONLYDELIVERALL)) || 'DELIVERY' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || (\defined('ENABLE_DELIVERYKING_THEME') && 'YES' === strtoupper(ENABLE_DELIVERYKING_THEME))) {
            return false;
        }

        return (!empty(ENABLE_RENTAL_OPTION) && 'YES' === strtoupper(ENABLE_RENTAL_OPTION)) ? true : false;
    }

    public function isEnableServerRequirementValidation()
    {
        if (!empty(ENABLE_SERVER_REQUIREMENT_VALIDATION) && 'YES' === strtoupper(ENABLE_SERVER_REQUIREMENT_VALIDATION)) {
            return true;
        }

        return false;
    }

    public function isEnableTermsServiceCategories()
    {
        global $ENABLE_TERMS_SERVICE_CATEGORIES;

        return !empty($ENABLE_TERMS_SERVICE_CATEGORIES) && 'YES' === strtoupper($ENABLE_TERMS_SERVICE_CATEGORIES) ? true : false;
    }

    public function isEnableProofUploadServiceCategories()
    {
        global $ENABLE_PROOF_UPLOAD_SERVICE_CATEGORIES;

        return !empty($ENABLE_PROOF_UPLOAD_SERVICE_CATEGORIES) && 'YES' === strtoupper($ENABLE_PROOF_UPLOAD_SERVICE_CATEGORIES) ? true : false;
    }

    public function isOnlyDeliverAllSystem()
    {
        if (false === $this->isRideFeatureAvailable() && false === $this->isDeliveryFeatureAvailable() && false === $this->isUberXFeatureAvailable() && true === $this->isDeliverAllFeatureAvailable()) {
            return true;
        }

        return false;
    }

    public function isEnableNewWalletWithdrawalFlowForDriver()
    {
        return !empty(ENABLE_NEW_WALLET_WITHDRAWAL_FLOW_DRIVER) && 'YES' === strtoupper(ENABLE_NEW_WALLET_WITHDRAWAL_FLOW_DRIVER) ? true : false;
    }

    public function isEnableBulkImportItems()
    {
        return !empty(ENABLE_BULK_ITEM_DATA) && 'YES' === strtoupper(ENABLE_BULK_ITEM_DATA) ? true : false;
    }

    public function isEnableDistanceWiseDeliveryChargeOrder()
    {
        global $ENABLE_CUSTOM_DELIVERY_CHARGE_ORDER;
        if (!empty($ENABLE_CUSTOM_DELIVERY_CHARGE_ORDER) && 'YES' === strtoupper($ENABLE_CUSTOM_DELIVERY_CHARGE_ORDER)) {
            return true;
        }

        return false;
    }

    public function isEnableDeliveryTipFeatureDeliverAll()
    {
        global $ENABLE_TIP_MODULE_DELIVERALL;
        if (!empty($ENABLE_TIP_MODULE_DELIVERALL) && 'YES' === strtoupper($ENABLE_TIP_MODULE_DELIVERALL)) {
            return true;
        }

        return false;
    }

    public function isEnableRoundingMethod()
    {
        global $ENABLE_ROUNDING_OPTIONS;
        if (!empty($ENABLE_ROUNDING_OPTIONS) && 'YES' === strtoupper($ENABLE_ROUNDING_OPTIONS)) {
            return true;
        }

        return false;
    }

    public function isEnableReverseFormatFeature()
    {
        global $ENABLE_REVERSE_FORMATTING;
        if (!empty($ENABLE_REVERSE_FORMATTING) && 'YES' === strtoupper($ENABLE_REVERSE_FORMATTING)) {
            return true;
        }

        return false;
    }

    public function isEnableMultiLevelReferralSystem()
    {
        global $MULTI_LEVEL_REFERRAL_SCHEME_ENABLE, $REFERRAL_SCHEME_ENABLE;
        if (!empty($MULTI_LEVEL_REFERRAL_SCHEME_ENABLE) && !empty($REFERRAL_SCHEME_ENABLE) && 'YES' === strtoupper($MULTI_LEVEL_REFERRAL_SCHEME_ENABLE) && 'YES' === strtoupper($REFERRAL_SCHEME_ENABLE)) {
            return true;
        }

        return false;
    }

    public function isEnableAnywhereDeliveryFeature($isFromConfig = 'No')
    {
        global $ENABLE_BUY_ANY_SERVICE_MODULE;
        if ((!empty($ENABLE_BUY_ANY_SERVICE_MODULE) && 'YES' === strtoupper($ENABLE_BUY_ANY_SERVICE_MODULE)) && ('YES' === strtoupper(GENIE_ENABLED) || 'YES' === strtoupper(RUNNER_ENABLED))) {
            $genieModuleAvailable = true;
            if ($genieModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig)) {
                return isMasterServiceCategoryAvailable('Deliver') ? true : false;
            }

            return $genieModuleAvailable;
        }

        return false;
    }

    public function isEnableOutstandingRestriction()
    {
        global $ENABLE_OUTSTANDING_RESTRICTION;
        if (!empty($ENABLE_OUTSTANDING_RESTRICTION) && 'YES' === strtoupper($ENABLE_OUTSTANDING_RESTRICTION)) {
            return true;
        }

        return false;
    }

    public function isEnableAppHomePageListView()
    {
        global $APP_HOME_PAGE_LIST_VIEW_ENABLED;
        if (!empty($APP_HOME_PAGE_LIST_VIEW_ENABLED) && 'YES' === strtoupper($APP_HOME_PAGE_LIST_VIEW_ENABLED)) {
            return true;
        }

        return false;
    }

    public function isEnableServiceWiseProviderDoc()
    {
        global $ENABLE_SERVICE_TYPE_WISE_PROVIDER_DOC;
        if (!empty($ENABLE_SERVICE_TYPE_WISE_PROVIDER_DOC) && 'YES' === strtoupper($ENABLE_SERVICE_TYPE_WISE_PROVIDER_DOC)) {
            return true;
        }

        return false;
    }

    public function isEnableDeliveryScheduleLaterBooking()
    {
        global $DELIVERY_LATER_BOOKING_ENABLED;
        if (!empty($DELIVERY_LATER_BOOKING_ENABLED) && 'YES' === strtoupper($DELIVERY_LATER_BOOKING_ENABLED)) {
            return true;
        }

        return false;
    }

    public function isEnableRideFareModelStrategy()
    {
        global $ENABLE_FARE_MODEL_STRATEGY;
        if (!empty($ENABLE_FARE_MODEL_STRATEGY) && 'YES' === strtoupper($ENABLE_FARE_MODEL_STRATEGY) && true === $this->isRideFeatureAvailable()) {
            return true;
        }

        return false;
    }

    public function isEnableItemSearchStoreOrder()
    {
        global $ENABLE_ITEM_SEARCH_STORE_ORDER;
        if (!empty($ENABLE_ITEM_SEARCH_STORE_ORDER) && 'YES' === strtoupper($ENABLE_ITEM_SEARCH_STORE_ORDER) && true === $this->isDeliverAllFeatureAvailable()) {
            return true;
        }

        return false;
    }

    public function isEnableTimeslotFeature()
    {
        global $ENABLE_TIMESLOT_ADDON;
        if (!empty($ENABLE_TIMESLOT_ADDON) && 'YES' === strtoupper($ENABLE_TIMESLOT_ADDON)) {
            return true;
        }

        return false;
    }

    public function isEnableRestrictPassengerLimit()
    {
        global $ENABLE_RESTRICT_PASSENGER_LIMIT;
        if (!empty($ENABLE_RESTRICT_PASSENGER_LIMIT) && 'YES' === strtoupper($ENABLE_RESTRICT_PASSENGER_LIMIT)) {
            return true;
        }

        return false;
    }

    public function isEnableSafetyGuidelines()
    {
        global $ENABLE_SAFETY_CHECKLIST;
        if (!empty($ENABLE_SAFETY_CHECKLIST) && 'YES' === strtoupper($ENABLE_SAFETY_CHECKLIST)) {
            return true;
        }

        return false;
    }

    public function isEnableStoreWiseCommission()
    {
        global $ENABLE_STORE_WISE_COMMISSION;
        if (!empty($ENABLE_STORE_WISE_COMMISSION) && 'YES' === strtoupper($ENABLE_STORE_WISE_COMMISSION)) {
            return true;
        }

        return false;
    }

    public function autoDeductDriverCommision($eSystem)
    {
        global $COMMISION_DEDUCT_ENABLE, $COMMISION_DEDUCT_ENABLE_DELIVERALL;
        if ('DELIVERALL' === strtoupper($eSystem)) {
            return $COMMISION_DEDUCT_ENABLE_DELIVERALL;
        }

        return $COMMISION_DEDUCT_ENABLE;
    }

    public function isApplyTaxOnTollAndOtherCharges()
    {
        return !empty(ENABLE_TAX_IN_TOLL_OTHER_CHARGES) && 'YES' === strtoupper(ENABLE_TAX_IN_TOLL_OTHER_CHARGES) ? true : false;
    }

    public function isEnableMsiteFacility()
    {
        global $ENABLE_MSITE_FACILITY;

        return !empty($ENABLE_MSITE_FACILITY) && 'YES' === strtoupper($ENABLE_MSITE_FACILITY) ? true : false;
    }

    public function isEnableVoiceDeliveryInstructionsOrder()
    {
        global $ENABLE_DELIVERY_INSTRUCTIONS_ORDERS;

        return !empty($ENABLE_DELIVERY_INSTRUCTIONS_ORDERS) && 'YES' === strtoupper($ENABLE_DELIVERY_INSTRUCTIONS_ORDERS) ? true : false;
    }

    public function isEnableAutoAcceptStoreOrder()
    {
        global $ENABLE_AUTO_ACCEPT_STORE_ORDER;

        return !empty($ENABLE_AUTO_ACCEPT_STORE_ORDER) && 'YES' === strtoupper($ENABLE_AUTO_ACCEPT_STORE_ORDER) ? true : false;
    }

    public function isEnableManualAssignProvider()
    {
        global $ENABLE_ASSIGN_DRIVER_DELIVERALL_ADMIN;

        return !empty($ENABLE_ASSIGN_DRIVER_DELIVERALL_ADMIN) && 'YES' === strtoupper($ENABLE_ASSIGN_DRIVER_DELIVERALL_ADMIN) ? true : false;
    }

    public function isEnableAcceptingOrderFromWeb()
    {
        global $ENABLE_ACCEPT_ORDER_WEB_PLATFORM;

        return !empty($ENABLE_ACCEPT_ORDER_WEB_PLATFORM) && 'YES' === strtoupper($ENABLE_ACCEPT_ORDER_WEB_PLATFORM) ? true : false;
    }

    public function isEnableDeliveryHelper()
    {
        global $ENABLE_DELIVERY_HELPER;

        return !empty($ENABLE_DELIVERY_HELPER) && 'YES' === strtoupper($ENABLE_DELIVERY_HELPER) ? true : false;
    }

    public function isEnableStorePhotoUploadFacility()
    {
        global $ENABLE_STORE_WISE_BANNER;

        return !empty($ENABLE_STORE_WISE_BANNER) && 'YES' === strtoupper($ENABLE_STORE_WISE_BANNER) ? true : false;
    }

    public function isEnableCancelDriverOrder()
    {
        global $ENABLE_CANCEL_DRIVER_ORDER;

        return !empty($ENABLE_CANCEL_DRIVER_ORDER) && 'YES' === strtoupper($ENABLE_CANCEL_DRIVER_ORDER) ? true : false;
    }

    public function isEnableOrderInventoryStore()
    {
        global $ENABLE_ORDER_INVENTORY_STORE;

        return !empty($ENABLE_ORDER_INVENTORY_STORE) && 'YES' === strtoupper($ENABLE_ORDER_INVENTORY_STORE) ? true : false;
    }

    public function isEnableOTPVerificationRide()
    {
        global $ENABLE_OTP_RIDE;

        return !empty($ENABLE_OTP_RIDE) && 'YES' === strtoupper($ENABLE_OTP_RIDE) ? true : false;
    }

    public function isEnableOTPVerificationDelivery()
    {
        global $ENABLE_OTP_DELIVERY;

        return !empty($ENABLE_OTP_DELIVERY) && 'YES' === strtoupper($ENABLE_OTP_DELIVERY) ? true : false;
    }

    public function isEnableOTPVerificationUberX()
    {
        global $ENABLE_OTP_UFX;

        return !empty($ENABLE_OTP_UFX) && 'YES' === strtoupper($ENABLE_OTP_UFX) ? true : false;
    }

    public function isEnableOTPVerificationDeliverAll()
    {
        global $ENABLE_OTP_DELIVERALL;

        return !empty($ENABLE_OTP_DELIVERALL) && 'YES' === strtoupper($ENABLE_OTP_DELIVERALL) ? true : false;
    }

    public function isEnableCustomNotification()
    {
        global $ENABLE_CUSTOM_NOTIFICATION;

        return !empty($ENABLE_CUSTOM_NOTIFICATION) && 'YES' === strtoupper($ENABLE_CUSTOM_NOTIFICATION) ? true : false;
    }

    public function isEnableWalletWithdrawRequestRestriction()
    {
        global $ENABLE_WALLET_WITHDRAWAL_REQUEST_RESTRICTION;

        return !empty($ENABLE_WALLET_WITHDRAWAL_REQUEST_RESTRICTION) && 'YES' === strtoupper($ENABLE_WALLET_WITHDRAWAL_REQUEST_RESTRICTION) ? true : false;
    }

    public function isEnableAcceptMultipleOrders()
    {
        global $ENABLE_ACCEPT_MULTIPLE_ORDERS;

        return !empty($ENABLE_ACCEPT_MULTIPLE_ORDERS) && 'YES' === strtoupper($ENABLE_ACCEPT_MULTIPLE_ORDERS) ? true : false;
    }

    public function isEnableRideDeliveryV1()
    {
        global $ENABLE_RIDE_DELIVERY_NEW_FLOW;

        return !empty($ENABLE_RIDE_DELIVERY_NEW_FLOW) && 'YES' === strtoupper($ENABLE_RIDE_DELIVERY_NEW_FLOW) ? true : false;
    }

    public function isEnableAppHomeScreenLayoutV1()
    {
        global $ENABLE_NEW_HOME_SCREEN_LAYOUT_APP;

        return !empty($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP) && 'YES' === strtoupper($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP) ? true : false;
    }

    public function isEnableSearchUfxServices()
    {
        global $ENABLE_SEARCH_UFX_SERVICES;

        return !empty($ENABLE_SEARCH_UFX_SERVICES) && 'YES' === strtoupper($ENABLE_SEARCH_UFX_SERVICES) ? true : false;
    }

    public function isEnableMultiOptionsToppings()
    {
        global $ENABLE_MULTI_OPTIONS_ADDONS;

        return !empty($ENABLE_MULTI_OPTIONS_ADDONS) && 'YES' === strtoupper($ENABLE_MULTI_OPTIONS_ADDONS) ? true : false;
    }

    public function isEnableFoodRatingDetailFlow()
    {
        global $ENABLE_FOOD_RATING_DETAIL_FLOW;

        return !empty($ENABLE_FOOD_RATING_DETAIL_FLOW) && 'YES' === strtoupper($ENABLE_FOOD_RATING_DETAIL_FLOW) ? true : false;
    }

    public function isEnableCookieConsent()
    {
        global $ENABLE_COOKIE_CONSENT;

        return !empty($ENABLE_COOKIE_CONSENT) && 'YES' === strtoupper($ENABLE_COOKIE_CONSENT) ? true : false;
    }

    public function isEnableRequireMenuItemSKU()
    {
        global $ENABLE_REQUIRE_MENU_ITEM_SKU;

        return !empty($ENABLE_REQUIRE_MENU_ITEM_SKU) && 'YES' === strtoupper($ENABLE_REQUIRE_MENU_ITEM_SKU) ? true : false;
    }

    public function isEnableStoreMultiServiceCategories()
    {
        global $ENABLE_STORE_MULTI_SERVICE_CATEGORIES;

        return !empty($ENABLE_STORE_MULTI_SERVICE_CATEGORIES) && 'YES' === strtoupper($ENABLE_STORE_MULTI_SERVICE_CATEGORIES) ? true : false;
    }

    public function isEnableLocationwiseBanner()
    {
        global $ENABLE_LOCATION_WISE_BANNER;

        return !empty($ENABLE_LOCATION_WISE_BANNER) && 'YES' === strtoupper($ENABLE_LOCATION_WISE_BANNER) ? true : false;
    }

    public function isEnableFreeDeliveryOrStoreSpecificPromoCode()
    {
        global $ENABLE_FREE_DELIVERY_OR_STORE_SPECIFIC_PROMO_CODE;

        return !empty($ENABLE_FREE_DELIVERY_OR_STORE_SPECIFIC_PROMO_CODE) && 'YES' === strtoupper($ENABLE_FREE_DELIVERY_OR_STORE_SPECIFIC_PROMO_CODE) ? true : false;
    }

    public function isEnableCountrywiseNotification()
    {
        global $ENABLE_COUNTRY_WISE_PUSH_NOTIFICATION;

        return !empty($ENABLE_COUNTRY_WISE_PUSH_NOTIFICATION) && 'YES' === strtoupper($ENABLE_COUNTRY_WISE_PUSH_NOTIFICATION) ? true : false;
    }

    public function isEnableLocationWisePromoCode()
    {
        global $ENABLE_LOCATION_WISE_PROMO_CODE;

        return !empty($ENABLE_LOCATION_WISE_PROMO_CODE) && 'YES' === strtoupper($ENABLE_LOCATION_WISE_PROMO_CODE) ? true : false;
    }

    public function isEnableDriverArrivalDistance()
    {
        global $ENABLE_DRIVER_ARRIVAL_DISTANCE_TO_USER_PICKUP_ADDRESS;

        return !empty($ENABLE_DRIVER_ARRIVAL_DISTANCE_TO_USER_PICKUP_ADDRESS) && 'YES' === strtoupper($ENABLE_DRIVER_ARRIVAL_DISTANCE_TO_USER_PICKUP_ADDRESS) ? true : false;
    }

    public function isEnableAppleLoginForProvider()
    {
        global $ENABLE_APPLE_LOGIN_FOR_PROVIDER;

        return !empty($ENABLE_APPLE_LOGIN_FOR_PROVIDER) && 'YES' === strtoupper($ENABLE_APPLE_LOGIN_FOR_PROVIDER) ? true : false;
    }

    public function isEnableAppleLoginForUser()
    {
        global $ENABLE_APPLE_LOGIN_FOR_USER;

        return !empty($ENABLE_APPLE_LOGIN_FOR_USER) && 'YES' === strtoupper($ENABLE_APPLE_LOGIN_FOR_USER) ? true : false;
    }

    public function isEnableSmartLogin()
    {
        global $ENABLE_SMART_LOGIN;

        return !empty($ENABLE_SMART_LOGIN) && 'YES' === strtoupper($ENABLE_SMART_LOGIN) ? true : false;
    }

    public function isEnableMultiDeliveryInBooking()
    {
        if ('cubejekdev.bbcsproducts.net' === $_SERVER['HTTP_HOST']) {
            return false;
        }

        return false;
    }

    public function checkFavDriverModule()
    {
        global $ENABLE_FAVORITE_DRIVER_MODULE, $tconfig;
        $fav_driver_file_path = $tconfig['tpanel_path'].'include/features/include_fav_driver.php';
        if (file_exists($fav_driver_file_path) && !empty($ENABLE_FAVORITE_DRIVER_MODULE) && 'YES' === strtoupper($ENABLE_FAVORITE_DRIVER_MODULE) && 'NO' === strtoupper(ONLYDELIVERALL)) {
            return true;
        }

        return false;
    }

    public function checkDriverDestinationModule($adminfilepath = 0)
    {
        global $ENABLE_DRIVER_DESTINATIONS, $APP_TYPE, $tconfig;
        $driver_destination_file_path = $tconfig['tpanel_path'].'include/features/include_destinations_driver.php';
        if (file_exists($driver_destination_file_path) && !empty($ENABLE_DRIVER_DESTINATIONS) && 'YES' === strtoupper($ENABLE_DRIVER_DESTINATIONS) && (('Ride-Delivery' === $APP_TYPE) || ('Ride-Delivery-UberX' === $APP_TYPE) || ('Ride' === $APP_TYPE))) {
            return true;
        }

        return false;
    }

    public function checkSharkPackage()
    {
        global $tconfig, $type;
        if ('SHARK' !== strtoupper(PACKAGE_TYPE)) {
            return false;
        }
        $shark_file_path = $tconfig['tpanel_path'].'include/include_webservice_sharkfeatures.php';
        if (file_exists($shark_file_path)) {
            include_once $shark_file_path;

            return true;
        }

        return false;
    }

    public function checkStopOverPointModule()
    {
        global $ENABLE_STOPOVER_POINT, $APP_TYPE, $tconfig;
        $stop_over_point_file_path = $tconfig['tpanel_path'].'include/features/include_stop_over_point.php';
        if (file_exists($stop_over_point_file_path) && !empty($ENABLE_STOPOVER_POINT) && 'YES' === strtoupper($ENABLE_STOPOVER_POINT) && (('Ride-Delivery' === $APP_TYPE) || ('Ride-Delivery-UberX' === $APP_TYPE) || ('Ride' === $APP_TYPE))) {
            return true;
        }

        return false;
    }

    public function isDonationFeatureAvailable()
    {
        global $obj, $APP_TYPE, $DONATION_ENABLE, $CONFIG_OBJ, $tconfig;
        $DonationFilepath = $tconfig['tpanel_path'].'include/features/include_donation.php';
        if (empty($DONATION_ENABLE)) {
            $DONATION_ENABLE = $CONFIG_OBJ->getConfigurations('configurations', 'DONATION_ENABLE');
            $DONATION_ENABLE = $DRIVER_SUBSCRIPTION_ENABLE[0]['vValue'];
        }
        if (file_exists($DonationFilepath) && !empty($DONATION_ENABLE) && 'YES' === strtoupper($DONATION_ENABLE)) {
            return true;
        }

        return false;
    }

    public function isUfxFeatureAvailable()
    {
        global $obj, $ufx_data, $cacheKeysArr, $oCache;
        if ('NO' === strtoupper(ENABEL_SERVICE_PROVIDER_MODULE)) {
            return 'No';
        }
        if ('YES' === strtoupper(ONLYDELIVERALL)) {
            return 'No';
        }
        if (!empty(IS_CUBE_X_THEME) && IS_CUBE_X_THEME === 'Yes') {
            return 'No';
        }
        if ('RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE) || 'YES' === strtoupper(UFX_ENABLED)) {
            $ufx_data = $obj->MySQLSelect('SELECT COUNT(iVehicleCategoryId) AS Total FROM '.getVehicleCategoryTblName()." WHERE 1=1 AND eCatType = 'ServiceProvider'");
            if (!empty($ufx_data[0]['Total']) && $ufx_data[0]['Total'] > 0) {
                return 'Yes';
            }

            return 'No';
        }

        return 'No';
    }

    public function isEnableServiceTypeWiseProviderDocument()
    {
        global $APP_TYPE, $ENABLE_SERVICE_TYPE_WISE_PROVIDER_DOC;
        if ('YES' !== strtoupper($ENABLE_SERVICE_TYPE_WISE_PROVIDER_DOC)) {
            return 'No';
        }
        if ('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE || ONLYDELIVERALL === 'No') {
            return 'Yes';
        }

        return 'No';
    }

    public function isEnableDriverRewardModule()
    {
        global $ENABLE_DRIVER_REWARD_MODULE;

        return !empty($ENABLE_DRIVER_REWARD_MODULE) && 'YES' === strtoupper($ENABLE_DRIVER_REWARD_MODULE) ? true : false;
    }

    public function isEnableRiderRewardModule()
    {
        global $ENABLE_RIDER_REWARD_MODULE;

        return !empty($ENABLE_RIDER_REWARD_MODULE) && 'YES' === strtoupper($ENABLE_RIDER_REWARD_MODULE) ? true : false;
    }

    public function isEnableAppHomeScreenLayoutV2()
    {
        global $ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_22;

        return !empty($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_22) && 'YES' === strtoupper($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_22) ? true : false;
    }

    public function isSendRequestToDriverBeforFinishTripModule()
    {
        global $ENABLE_SEND_REQUEST_BEFORE_TRIP_END;

        return !empty($ENABLE_SEND_REQUEST_BEFORE_TRIP_END) && 'YES' === strtoupper($ENABLE_SEND_REQUEST_BEFORE_TRIP_END) ? true : false;
    }

    public function isEnableGoogleAds()
    {
        global $ENABLE_GOOGLE_ADS;

        return !empty($ENABLE_GOOGLE_ADS) && 'YES' === strtoupper($ENABLE_GOOGLE_ADS) ? true : false;
    }

    public function isEnableFacebookAds()
    {
        global $ENABLE_FACEBOOK_ADS;

        return !empty($ENABLE_FACEBOOK_ADS) && 'YES' === strtoupper($ENABLE_FACEBOOK_ADS) ? true : false;
    }

    public function isEnableBiddingServices($isFromConfig = 'No')
    {
        global $ENABLE_BIDDING_SERVICES, $master_service_category_tbl;
        $serviceBidModuleAvailable = (!empty($ENABLE_BIDDING_SERVICES) && 'YES' === strtoupper($ENABLE_BIDDING_SERVICES)) || 'YES' === strtoupper(BIDDING_ENABLED) ? true : false;
        if ($serviceBidModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && ('RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE))) {
            return isMasterServiceCategoryAvailable('Bidding') ? true : false;
        }

        return $serviceBidModuleAvailable;
    }

    public function isEnableVideoConsultingService($isFromConfig = 'No')
    {
        global $ENABLE_VIDEO_CONSULTING_SERVICE;
        $videoConsultModuleAvailable = (!empty($ENABLE_VIDEO_CONSULTING_SERVICE) && 'YES' === strtoupper($ENABLE_VIDEO_CONSULTING_SERVICE)) || 'YES' === strtoupper(VC_ENABLED) ? true : false;
        if ($videoConsultModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && ('RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE) || 'UBERX' === strtoupper(APP_TYPE))) {
            return isMasterServiceCategoryAvailable('VideoConsult') ? true : false;
        }

        return $videoConsultModuleAvailable;
    }

    public function isEnableItemMultipleImageVideoUpload()
    {
        global $ENABLE_ITEM_MULTIPLE_IMAGE_VIDEO_UPLOAD;

        return !empty($ENABLE_ITEM_MULTIPLE_IMAGE_VIDEO_UPLOAD) && 'YES' === strtoupper($ENABLE_ITEM_MULTIPLE_IMAGE_VIDEO_UPLOAD) ? true : false;
    }

    public function isEnableAccountDeletion()
    {
        global $ENABLE_ACCOUNT_DELETION;

        return !empty($ENABLE_ACCOUNT_DELETION) && 'YES' === strtoupper($ENABLE_ACCOUNT_DELETION) ? true : false;
    }

    public function isEnableAppHomeScreenLayout()
    {
        return $this->isEnableAppHomeScreenLayoutV1() || $this->isEnableAppHomeScreenLayoutV2() || $this->isEnableAppHomeScreenLayoutV3() ? true : false;
    }

    public function isEnableAdminPanelV2()
    {
        global $THEME_OBJ;
        if ('YES' === strtoupper($THEME_OBJ->isCubeJekXv3ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isProThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isServiceXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isCubeXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isRideCXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isDeliveryKingXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isDeliverallXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isDeliveryXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isRideDeliveryXv2ThemeActive()) || 'YES' === strtoupper($THEME_OBJ->isMedicalServicev2ThemeActive())) {
            return true;
        }

        return false;
    }

    public function isEnableMedicalServices($isFromConfig = 'No')
    {
        global $ENABLE_MEDICAL_SERVICES;
        $medicalServiceModuleAvailable = (!empty($ENABLE_MEDICAL_SERVICES) && 'YES' === strtoupper($ENABLE_MEDICAL_SERVICES)) || 'YES' === strtoupper(MED_UFX_ENABLED) ? true : false;
        if ($medicalServiceModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('MedicalServices') ? true : false;
        }

        return $medicalServiceModuleAvailable;
    }

    public function isEnableSkipRatingRide()
    {
        global $ENABLE_SKIP_RATING_RIDE;

        return !empty($ENABLE_SKIP_RATING_RIDE) && 'YES' === strtoupper($ENABLE_SKIP_RATING_RIDE) ? true : false;
    }

    public function isEnableRentItemService($isFromConfig = 'No')
    {
        global $ENABLE_RENT_ITEM_SERVICES;
        $rentItemModuleAvailable = (!empty($ENABLE_RENT_ITEM_SERVICES) && 'YES' === strtoupper($ENABLE_RENT_ITEM_SERVICES)) || 'YES' === strtoupper(RENT_ITEM_ENABLED) ? true : false;
        if ($rentItemModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('RentItem') ? true : false;
        }

        return $rentItemModuleAvailable;
    }

    public function isEnableRentEstateService($isFromConfig = 'No')
    {
        global $ENABLE_RENT_ESTATE_SERVICES;
        $rentEstateModuleAvailable = (!empty($ENABLE_RENT_ESTATE_SERVICES) && 'YES' === strtoupper($ENABLE_RENT_ESTATE_SERVICES)) || 'YES' === strtoupper(RENT_ESTATE_ENABLED) ? true : false;
        if ($rentEstateModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('RentEstate') ? true : false;
        }

        return $rentEstateModuleAvailable;
    }

    public function isEnableRentCarsService($isFromConfig = 'No')
    {
        global $ENABLE_RENT_CARS_SERVICES;
        $rentCarsModuleAvailable = (!empty($ENABLE_RENT_CARS_SERVICES) && 'YES' === strtoupper($ENABLE_RENT_CARS_SERVICES)) || 'YES' === strtoupper(RENT_CARS_ENABLED) ? true : false;
        if ($rentCarsModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('RentCars') ? true : false;
        }

        return $rentCarsModuleAvailable;
    }

    public function isEnableRating()
    {
        global $ENABLE_RATING;

        return !empty($ENABLE_RATING) && 'YES' === strtoupper($ENABLE_RATING) ? true : false;
    }

    public function isEnableTip()
    {
        global $ENABLE_TIP_MODULE;

        return !empty($ENABLE_TIP_MODULE) && 'YES' === strtoupper($ENABLE_TIP_MODULE) ? true : false;
    }

    public function isEnableBiddingWiseProviderDoc()
    {
        global $ENABLE_BIDDING_WISE_PROVIDER_DOC;

        return !empty($ENABLE_BIDDING_WISE_PROVIDER_DOC) && 'YES' === strtoupper($ENABLE_BIDDING_WISE_PROVIDER_DOC) ? true : false;
    }

    public function isEnableNearByService($isFromConfig = 'No')
    {
        global $ENABLE_NEARBY_SERVICES;
        $nearbyModuleAvailable = (!empty($ENABLE_NEARBY_SERVICES) && 'YES' === strtoupper($ENABLE_NEARBY_SERVICES)) || 'YES' === strtoupper(NEARBY_ENABLED) ? true : false;
        if ($nearbyModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('NearBy') ? true : false;
        }

        return $nearbyModuleAvailable;
    }

    public function isEnableTrackServiceFeature($isFromConfig = 'No')
    {
        global $ENABLE_TRACK_SERVICE;
        $trackServiceModuleAvailable = (!empty($ENABLE_TRACK_SERVICE) && 'YES' === strtoupper($ENABLE_TRACK_SERVICE)) || 'YES' === strtoupper(TRACK_SERVICE_ENABLED) ? true : false;
        if ($trackServiceModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('TrackService') ? true : false;
        }

        return $trackServiceModuleAvailable;
    }

    public function isEnableRideShareService($isFromConfig = 'No')
    {
        global $ENABLE_RIDE_SHARE_SERVICE;
        $rideShareModuleAvailable = (!empty($ENABLE_RIDE_SHARE_SERVICE) && 'YES' === strtoupper($ENABLE_RIDE_SHARE_SERVICE)) || 'YES' === strtoupper(RIDE_SHARE_ENABLED) ? true : false;
        if ($rideShareModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('RideShare') ? true : false;
        }

        return $rideShareModuleAvailable;
    }

    public function isEnableGiftCardFeature()
    {
        global $ENABLE_GIFT_CARD_FEATURE;

        return !empty($ENABLE_GIFT_CARD_FEATURE) && 'YES' === strtoupper($ENABLE_GIFT_CARD_FEATURE) ? true : false;
    }

    public function isEnableAppHomeScreenLayoutV3()
    {
        global $ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_23;

        return !empty($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_23) && 'YES' === strtoupper($ENABLE_NEW_HOME_SCREEN_LAYOUT_APP_23) ? true : false;
    }

    public function isEnableAdminPanelV3Pro()
    {
        global $APP_TYPE, $THEME_OBJ;
        if ('YES' === strtoupper($THEME_OBJ->isProThemeActive())) {
            return true;
        }

        return false;
    }

    public function isEnableTrackAnyServiceFeature($isFromConfig = 'No')
    {
        global $ENABLE_TRACK_ANY_SERVICE;
        $trackAnyServiceModuleAvailable = (!empty($ENABLE_TRACK_ANY_SERVICE) && 'YES' === strtoupper($ENABLE_TRACK_ANY_SERVICE)) || 'YES' === strtoupper(TRACK_ANY_SERVICE_ENABLED) ? true : false;
        if ($trackAnyServiceModuleAvailable && $this->isEnableAppHomeScreenLayout() && 'NO' === strtoupper($isFromConfig) && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isMasterServiceCategoryAvailable('TrackAnyService') ? true : false;
        }

        return $trackAnyServiceModuleAvailable;
    }

    public function isEnableGenieFeature($isFromConfig = 'No')
    {
        $GenieFeatureAvailable = ('YES' === strtoupper(GENIE_ENABLED)) ? true : false;
        if ($GenieFeatureAvailable && 'NO' === strtoupper($isFromConfig) && $this->isEnableAppHomeScreenLayout() && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isVehicleCategoryAvailable('Genie') ? true : false;
        }

        return $GenieFeatureAvailable;
    }

    public function isEnableRunnerFeature($isFromConfig = 'No')
    {
        $RunnerFeatureAvailable = ('YES' === strtoupper(RUNNER_ENABLED)) ? true : false;
        if ($RunnerFeatureAvailable && 'NO' === strtoupper($isFromConfig) && $this->isEnableAppHomeScreenLayout() && 'RIDE-DELIVERY-UBERX' === strtoupper(APP_TYPE)) {
            return isVehicleCategoryAvailable('Runner') ? true : false;
        }

        return $RunnerFeatureAvailable;
    }
}
