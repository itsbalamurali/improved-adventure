<?php



namespace Kesk\Web\Common;

class RefactorApp
{
    public function __construct() {}

    public static function addTimeslotToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['STORE_INDIVIDUALDAY_AVAILABILITY_MODULE']) && ('Yes' === $_REQUEST['STORE_INDIVIDUALDAY_AVAILABILITY_MODULE'] || 'YES' === $_REQUEST['STORE_INDIVIDUALDAY_AVAILABILITY_MODULE'])) {
            return $deleteAppFilesArr['Timeslot Feature'] = $_REQUEST['STORE_INDIVIDUALDAY_AVAILABILITY_MODULE_FILES'];
        }
    }

    public static function addSearchByItemNameToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['STORE_SEARCH_BY_ITEM_NAME_MODULE']) && ('Yes' === $_REQUEST['STORE_SEARCH_BY_ITEM_NAME_MODULE'] || 'YES' === $_REQUEST['STORE_SEARCH_BY_ITEM_NAME_MODULE'])) {
            return $deleteAppFilesArr['Item Search For Store Order Feature'] = $_REQUEST['STORE_SEARCH_BY_ITEM_NAME_MODULE_FILES'];
        }
    }

    public static function addTollFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['MANUAL_TOLL_FEATURE_MODULE']) && ('Yes' === $_REQUEST['MANUAL_TOLL_FEATURE_MODULE'] || 'YES' === $_REQUEST['MANUAL_TOLL_FEATURE_MODULE'])) {
            return $deleteAppFilesArr['Manual Toll Feature'] = $_REQUEST['MANUAL_TOLL_FEATURE_MODULE_FILES'];
        }
    }

    public static function addSafetyToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['SAFETY_MODULE']) && ('Yes' === $_REQUEST['SAFETY_MODULE'] || 'YES' === $_REQUEST['SAFETY_MODULE'])) {
            return $deleteAppFilesArr['Safety Feature'] = $_REQUEST['SAFETY_MODULE_FILES'];
        }
    }

    public static function addSafetyRatingToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['SAFETY_RATING_MODULE']) && ('Yes' === $_REQUEST['SAFETY_RATING_MODULE'] || 'YES' === $_REQUEST['SAFETY_RATING_MODULE'])) {
            return $deleteAppFilesArr['Safety Rating Feature'] = $_REQUEST['SAFETY_RATING_MODULE_FILES'];
        }
    }

    public static function addSafetyCheckListToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['SAFETY_CHECK_LIST_MODULE']) && ('Yes' === $_REQUEST['SAFETY_CHECK_LIST_MODULE'] || 'YES' === $_REQUEST['SAFETY_CHECK_LIST_MODULE'])) {
            return $deleteAppFilesArr['Safety Check List Feature'] = $_REQUEST['SAFETY_CHECK_LIST_MODULE_FILES'];
        }
    }

    public static function addSafetyFacemaskVerificationToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['SAFETY_FACEMASK_VERIFICATION_MODULE']) && ('Yes' === $_REQUEST['SAFETY_FACEMASK_VERIFICATION_MODULE'] || 'YES' === $_REQUEST['SAFETY_FACEMASK_VERIFICATION_MODULE'])) {
            return $deleteAppFilesArr['Safety Facemask Verification Feature'] = $_REQUEST['SAFETY_FACEMASK_VERIFICATION_MODULE_FILES'];
        }
    }

    public static function addEighteenPlusToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['EIGHTEEN_PLUS_FEATURE_MODULE']) && ('Yes' === $_REQUEST['EIGHTEEN_PLUS_FEATURE_MODULE'] || 'YES' === $_REQUEST['EIGHTEEN_PLUS_FEATURE_MODULE'])) {
            return $deleteAppFilesArr['Eighteen Plus Feature'] = $_REQUEST['EIGHTEEN_PLUS_FEATURE_MODULE_FILES'];
        }
    }

    public static function addGenieFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['GENIE_FEATURE_MODULE']) && ('Yes' === $_REQUEST['GENIE_FEATURE_MODULE'] || 'YES' === $_REQUEST['GENIE_FEATURE_MODULE'])) {
            return $deleteAppFilesArr['Genie Feature'] = $_REQUEST['GENIE_FEATURE_MODULE_FILES'];
        }
    }

    public static function addContactlessFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['CONTACTLESS_DELIVERY_MODULE']) && ('Yes' === $_REQUEST['CONTACTLESS_DELIVERY_MODULE'] || 'YES' === $_REQUEST['CONTACTLESS_DELIVERY_MODULE'])) {
            return $deleteAppFilesArr['Contactless Feature'] = $_REQUEST['CONTACTLESS_DELIVERY_MODULE_FILES'];
        }
    }

    public static function addMultiSingleStoreToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['MULTI_SINGLE_STORE_MODULE']) && ('Yes' === $_REQUEST['MULTI_SINGLE_STORE_MODULE'] || 'YES' === $_REQUEST['MULTI_SINGLE_STORE_MODULE'])) {
            return $deleteAppFilesArr['Multi Single Store Feature'] = $_REQUEST['MULTI_SINGLE_STORE_MODULE_FILES'];
        }
    }

    public static function addCategorywiseStoeToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['CATEGORY_WISE_STORE_MODULE']) && ('Yes' === $_REQUEST['CATEGORY_WISE_STORE_MODULE'] || 'YES' === $_REQUEST['CATEGORY_WISE_STORE_MODULE'])) {
            return $deleteAppFilesArr['Category Wise Store Feature'] = $_REQUEST['CATEGORY_WISE_STORE_MODULE_FILES'];
        }
    }

    public static function addWayBillToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['WAYBILL_MODULE']) && ('Yes' === $_REQUEST['WAYBILL_MODULE'] || 'YES' === $_REQUEST['WAYBILL_MODULE'])) {
            return $deleteAppFilesArr['WayBill Feature'] = $_REQUEST['WAYBILL_MODULE_FILES'];
        }
    }

    public static function addDeliverAllToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['DELIVER_ALL']) && ('Yes' === $_REQUEST['DELIVER_ALL'] || 'YES' === $_REQUEST['DELIVER_ALL'])) {
            return $deleteAppFilesArr['DeliverAll Feature (Food/Grocery/DeliverAll etc.)'] = $_REQUEST['DELIVER_ALL_FILES'];
        }
    }

    public static function addMultiDeliveryToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['MULTI_DELIVERY']) && ('Yes' === $_REQUEST['MULTI_DELIVERY'] || 'YES' === $_REQUEST['MULTI_DELIVERY'])) {
            return $deleteAppFilesArr['Multi Delivery Feature'] = $_REQUEST['MULTI_DELIVERY_FILES'];
        }
    }

    public static function addSingleDeliveryToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['DELIVERY_MODULE']) && ('Yes' === $_REQUEST['DELIVERY_MODULE'] || 'YES' === $_REQUEST['DELIVERY_MODULE'])) {
            return $deleteAppFilesArr['Single Delivery Feature'] = $_REQUEST['DELIVERY_MODULE_FILES'];
        }
    }

    public static function addUberXServicesToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['UBERX_SERVICE']) && ('Yes' === $_REQUEST['UBERX_SERVICE'] || 'YES' === $_REQUEST['UBERX_SERVICE'])) {
            return $deleteAppFilesArr['UberX (Other Services like - Carwash etc) Feature'] = $_REQUEST['UBERX_FILES'];
        }
    }

    public static function addOnGoingJobsToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['ON_GOING_JOB_SECTION']) && ('Yes' === $_REQUEST['ON_GOING_JOB_SECTION'] || 'YES' === $_REQUEST['ON_GOING_JOB_SECTION'])) {
            return $deleteAppFilesArr['OnGoing Job Section (UberX/Multi)'] = $_REQUEST['ON_GOING_JOB_SECTION_FILES'];
        }
    }

    public static function addCommonDeliveryTypesToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['COMMON_DELIVERY_TYPE_SECTION']) && ('Yes' === $_REQUEST['COMMON_DELIVERY_TYPE_SECTION'] || 'YES' === $_REQUEST['COMMON_DELIVERY_TYPE_SECTION'])) {
            return $deleteAppFilesArr['Common Delivery Type Section (For Ride-Delivery/Delivery/MultiDelivery/Cubejek)'] = $_REQUEST['COMMON_DELIVERY_TYPE_SECTION_FILES'];
        }
    }

    public static function addRentalFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['RENTAL_FEATURE']) && ('Yes' === $_REQUEST['RENTAL_FEATURE'] || 'YES' === $_REQUEST['RENTAL_FEATURE'])) {
            return $deleteAppFilesArr['Rental Feature'] = $_REQUEST['RENTAL_SERVICE_FILES'];
        }
    }

    public static function addRideSectionToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['RIDE_SECTION']) && ('Yes' === $_REQUEST['RIDE_SECTION'] || 'YES' === $_REQUEST['RIDE_SECTION'])) {
            return $deleteAppFilesArr['Ride Section'] = $_REQUEST['RIDE_SECTION_FILES'];
        }
    }

    public static function addPoolFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['POOL_MODULE']) && ('Yes' === $_REQUEST['POOL_MODULE'] || 'YES' === $_REQUEST['POOL_MODULE'])) {
            return $deleteAppFilesArr['Pool Feature'] = $_REQUEST['POOL_MODULE_FILES'];
        }
    }

    public static function addFlyFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['FLY_MODULE']) && ('Yes' === $_REQUEST['FLY_MODULE'] || 'YES' === $_REQUEST['FLY_MODULE'])) {
            return $deleteAppFilesArr['Fly Feature'] = $_REQUEST['FLY_MODULE_FILES'];
        }
    }

    public static function addBusinessProfileFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['BUSINESS_PROFILE_FEATURE']) && ('Yes' === $_REQUEST['BUSINESS_PROFILE_FEATURE'] || 'YES' === $_REQUEST['BUSINESS_PROFILE_FEATURE'])) {
            return $deleteAppFilesArr['Business Profile Feature'] = $_REQUEST['BUSINESS_PROFILE_FILES'];
        }
    }

    public static function addRDUToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['RDU_SECTION']) && ('Yes' === $_REQUEST['RDU_SECTION'] || 'YES' === $_REQUEST['RDU_SECTION'])) {
            return $deleteAppFilesArr['RDU Section Files'] = $_REQUEST['RDU_SECTION_FILES'];
        }
    }

    public static function addVOIPToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['VOIP_SERVICE']) && ('Yes' === $_REQUEST['VOIP_SERVICE'] || 'YES' === $_REQUEST['VOIP_SERVICE'])) {
            return $deleteAppFilesArr['VOIP Feature'] = $_REQUEST['VOIP_SERVICE_FILES'];
        }
    }

    public static function addFavDriverToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['FAV_DRIVER_SECTION']) && ('Yes' === $_REQUEST['FAV_DRIVER_SECTION'] || 'YES' === $_REQUEST['FAV_DRIVER_SECTION'])) {
            return $deleteAppFilesArr['Favourite Driver Feature'] = $_REQUEST['FAV_DRIVER_SECTION_FILES'];
        }
    }

    public static function addBookForElseToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['BOOK_FOR_ELSE_SECTION']) && ('Yes' === $_REQUEST['BOOK_FOR_ELSE_SECTION'] || 'YES' === $_REQUEST['BOOK_FOR_ELSE_SECTION'])) {
            return $deleteAppFilesArr['Book for someone else Feature'] = $_REQUEST['BOOK_FOR_ELSE_SECTION_FILES'];
        }
    }

    public static function addEndOfDayTripToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['END_OF_DAY_TRIP_SECTION']) && ('Yes' === $_REQUEST['END_OF_DAY_TRIP_SECTION'] || 'YES' === $_REQUEST['END_OF_DAY_TRIP_SECTION'])) {
            return $deleteAppFilesArr['EndOfDayTrip Feature'] = $_REQUEST['END_OF_DAY_TRIP_SECTION_FILES'];
        }
    }

    public static function addMultiStopOverPointsToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['STOP_OVER_POINT_SECTION']) && ('Yes' === $_REQUEST['STOP_OVER_POINT_SECTION'] || 'YES' === $_REQUEST['STOP_OVER_POINT_SECTION'])) {
            return $deleteAppFilesArr['Multi StopOverPoint Feature'] = $_REQUEST['STOP_OVER_POINT_SECTION_FILES'];
        }
    }

    public static function addDriverSubscriptionToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['DRIVER_SUBSCRIPTION_SECTION']) && ('Yes' === $_REQUEST['DRIVER_SUBSCRIPTION_SECTION'] || 'YES' === $_REQUEST['DRIVER_SUBSCRIPTION_SECTION'])) {
            return $deleteAppFilesArr['Driver Subscription Feature'] = $_REQUEST['DRIVER_SUBSCRIPTION_SECTION_FILES'];
        }
    }

    public static function addDonationToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['DONATION_SECTION']) && ('Yes' === $_REQUEST['DONATION_SECTION'] || 'YES' === $_REQUEST['DONATION_SECTION'])) {
            return $deleteAppFilesArr['Donation Feature'] = $_REQUEST['DONATION_SECTION_FILES'];
        }
    }

    public static function addGoJekGoPayToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['GO_PAY_SECTION']) && ('Yes' === $_REQUEST['GO_PAY_SECTION'] || 'YES' === $_REQUEST['GO_PAY_SECTION'])) {
            return $deleteAppFilesArr['Wallet to Wallet money Feature'] = $_REQUEST['GO_PAY_SECTION_FILES'];
        }
    }

    public static function addThermalPrintToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['THERMAL_PRINT_MODULE']) && ('Yes' === $_REQUEST['THERMAL_PRINT_MODULE'] || 'YES' === $_REQUEST['THERMAL_PRINT_MODULE'])) {
            return $deleteAppFilesArr['Thermal Print Feature'] = $_REQUEST['THERMAL_PRINT_MODULE_FILES'];
        }
    }

    public static function addAppleWatchAppToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['APPLE_WATCH_MODULE']) && ('Yes' === $_REQUEST['APPLE_WATCH_MODULE'] || 'YES' === $_REQUEST['APPLE_WATCH_MODULE'])) {
            $_REQUEST['APPLE_WATCH_FILES'] = 'WatchApp,WatchApp Extension, '.$_REQUEST['APPLE_WATCH_FILES'];

            return $deleteAppFilesArr['Apple Watch Feature'] = $_REQUEST['APPLE_WATCH_FILES'];
        }
    }

    public static function addBidFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['BID_SERVICE']) && ('Yes' === $_REQUEST['BID_SERVICE'] || 'YES' === $_REQUEST['BID_SERVICE'])) {
            return $deleteAppFilesArr['Service Bidding Feature'] = $_REQUEST['BID_SERVICE_FILES'];
        }
    }

    public static function addLiveActivityFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['LIVE_ACTIVITY_MODULE']) && ('Yes' === $_REQUEST['LIVE_ACTIVITY_MODULE'] || 'YES' === $_REQUEST['LIVE_ACTIVITY_MODULE'])) {
            $_REQUEST['LIVE_ACTIVITY_FILES'] = 'LiveActivityData, '.$_REQUEST['LIVE_ACTIVITY_FILES'];

            return $deleteAppFilesArr['Live Activity Feature'] = $_REQUEST['LIVE_ACTIVITY_FILES'];
        }
    }

    public static function addBuySellRentFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['RENT_ITEM_SERVICE']) && ('Yes' === $_REQUEST['RENT_ITEM_SERVICE'] || 'YES' === $_REQUEST['RENT_ITEM_SERVICE'])) {
            return $deleteAppFilesArr['Buy,Sell,Rent Feature'] = $_REQUEST['RENT_ITEM_SERVICE_FILES'];
        }
    }

    public static function addTrackingFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['TRACKING_MODULE']) && ('Yes' === $_REQUEST['TRACKING_MODULE'] || 'YES' === $_REQUEST['TRACKING_MODULE'])) {
            return $deleteAppFilesArr['Tracking Feature'] = $_REQUEST['TRACKING_MODULE_FILES'];
        }
    }

    public static function addRideShareFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['RIDESHARE_MODULE']) && ('Yes' === $_REQUEST['RIDESHARE_MODULE'] || 'YES' === $_REQUEST['RIDESHARE_MODULE'])) {
            return $deleteAppFilesArr['Ride Sharing Feature'] = $_REQUEST['RIDESHARE_MODULE_FILES'];
        }
    }

    public static function addNearByFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['NEARBY_MODULE']) && ('Yes' === $_REQUEST['NEARBY_MODULE'] || 'YES' === $_REQUEST['NEARBY_MODULE'])) {
            return $deleteAppFilesArr['Nearby Feature'] = $_REQUEST['NEARBY_MODULE_FILES'];
        }
    }

    public static function addGiftCardFeatureToDeliteList()
    {
        global $_REQUEST, $deleteAppFilesArr;
        if (!empty($_REQUEST['GIFTCARD_MODULE']) && ('Yes' === $_REQUEST['GIFTCARD_MODULE'] || 'YES' === $_REQUEST['GIFTCARD_MODULE'])) {
            return $deleteAppFilesArr['Gift Card Feature'] = $_REQUEST['GIFTCARD_MODULE_FILES'];
        }
    }
}
