<?php
class ChangeFileCls
{
    function __construct()
    {

    }

    public static function fileArray($FILE_TYPE)
    {
        global $APP_TYPE,$MODULES_OBJ,$service_categories_ids_arr;

        $cubeDeliverallOnly = $MODULES_OBJ->isOnlyDeliverAllSystem();
        $onlyDeliverallModule = strtoupper(ONLYDELIVERALL);

        $APPTYPE  = $APP_TYPE;

        $ARR = [];
        switch ($APPTYPE) {
            case 'UberX':
                $ARR = self::uberxFile();
                break;

            case 'Delivery':
                $ARR = self::deliveryFile();
                break;

        }

        if(empty($ARR)){
            $ARR = self::mainArr();
        }

        if($onlyDeliverallModule == "YES" && count($service_categories_ids_arr) == 1 && $service_categories_ids_arr[0] == 1){
            $ARR = self::FoodFiles();
        }

        if($onlyDeliverallModule == "YES" && count($service_categories_ids_arr) > 1){
            $ARR = self::DeliverAllFile();
        }


        $F_ARR = [];
        if($FILE_TYPE == "SOURCE_FILE"){
            foreach ($ARR as $KEY => $A){
                $F_ARR[$KEY] = $A['SOURCE_FILE'];
            }
        }
        if($FILE_TYPE == "LOCATION_FILE"){
            foreach ($ARR as $KEY => $A){
                $F_ARR[$KEY] = $A['LOCATION_FILE'];
            }
        }

        return $F_ARR;
    }


    private static function uberxFile()
    {
        $ARR = [];
        $mainArr = self::mainArr();
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
        $mainArr['MASTER_CATEGORY_VIDEO-CONSULT']['LOCATION_FILE'] = 'master_category.php?eType=VideoConsult';
        $mainArr['MASTER_CATEGORY_UBERX']['LOCATION_FILE'] = 'master_category.php?eType=UberX';
        $mainArr['LATER_BOOKING']['LOCATION_FILE'] = 'job_booking.php';
        $mainArr['TRIP']['LOCATION_FILE'] = 'job.php';
        $mainArr['BLOCKED_RIDER']['LOCATION_FILE'] = 'blocked_user.php';
        $mainArr['BLOCKED_DRIVER']['LOCATION_FILE'] = 'blocked_provider.php';
        $mainArr['CANCELLED_TRIP']['LOCATION_FILE'] = 'cancelled_job.php';
        $mainArr['TRIP_HELP_DETAILS']['LOCATION_FILE'] = 'job_help_details.php';
        $mainArr['MASTER_CATEGORY']['LOCATION_FILE'] = 'master_category.php';
        $mainArr['APP_LAUNCH_INFO_PASSENGER']['LOCATION_FILE'] = 'app_launch_info.php?option=User';
        $mainArr['APP_LAUNCH_INFO_DRIVER']['LOCATION_FILE'] = 'app_launch_info.php?option=Provider';
        $mainArr['RIDER_ACTION']['LOCATION_FILE'] = 'user_action.php';
        $mainArr['DRIVER.PHP']['LOCATION_FILE'] = 'provider.php';
        $mainArr['DRIVER_ACTION']['LOCATION_FILE'] = 'provider_action.php';
        $mainArr['TOTAL_TRIP_DETAIL']['LOCATION_FILE'] = 'total_job_detail.php';
        $mainArr['DRIVER_DOCUMENT_ACTION']['LOCATION_FILE'] = 'provider_document_action.php';
        

        return $mainArr;
    }

    private static function deliveryFile()
    {
        $ARR = [];
        $mainArr = self::mainArr();
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
        $mainArr['MASTER_CATEGORY_DELIVER']['LOCATION_FILE'] = 'master_category.php?eType=Deliver';
        $mainArr['LATER_BOOKING']['LOCATION_FILE'] = 'job_booking.php';
        $mainArr['TRIP']['LOCATION_FILE'] = 'job.php';
        $mainArr['BLOCKED_RIDER']['LOCATION_FILE'] = 'blocked_user.php';
        $mainArr['CANCELLED_TRIP']['LOCATION_FILE'] = 'cancelled_job.php';
        $mainArr['TRIP_HELP_DETAILS']['LOCATION_FILE'] = 'job_help_details.php';
        $mainArr['MASTER_CATEGORY']['LOCATION_FILE'] = 'master_category.php';
        $mainArr['APP_LAUNCH_INFO_PASSENGER']['LOCATION_FILE'] = 'app_launch_info.php?option=User';
        $mainArr['RIDER_ACTION']['LOCATION_FILE'] = 'user_action.php';
        $mainArr['TOTAL_TRIP_DETAIL']['LOCATION_FILE'] = 'total_job_detail.php';
        

        return $mainArr;
    }

    private static function DeliverAllFile()
    {
        $ARR = [];
        $mainArr = self::mainArr();
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
        $mainArr['RIDER_ACTION']['LOCATION_FILE'] = 'user_action.php';
        $mainArr['BLOCKED_RIDER']['LOCATION_FILE'] = 'blocked_user.php';
        $mainArr['APP_LAUNCH_INFO_PASSENGER']['LOCATION_FILE'] = 'app_launch_info.php?option=User';
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
        $mainArr['FOOD_MENU.PHP']['LOCATION_FILE'] = 'menuitem_category.php';
        $mainArr['FOOD_MENU_ACTION']['LOCATION_FILE'] = 'menuitem_category_action.php';
        $mainArr['MASTER_CATEGORY_DELIVERALL']['LOCATION_FILE'] = 'master_category.php?eType=DeliverAll';
        $mainArr['RESTAURANTS_PAY_REPORT.PHP']['LOCATION_FILE'] = 'store_pay_report.php';
        return $mainArr;
    }

     private static function FoodFiles()
    {
        $ARR = [];
        $mainArr = self::mainArr();
        $mainArr['STORE.PHP']['LOCATION_FILE'] = 'restaurant.php';
        $mainArr['STORE_ACTION']['LOCATION_FILE'] = 'restaurant_action.php';
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
        $mainArr['RIDER_ACTION']['LOCATION_FILE'] = 'user_action.php';
        $mainArr['BLOCKED_RIDER']['LOCATION_FILE'] = 'blocked_user.php';
        $mainArr['APP_LAUNCH_INFO_PASSENGER']['LOCATION_FILE'] = 'app_launch_info.php?option=User';
        $mainArr['RIDER.PHP']['LOCATION_FILE'] = 'user.php';
       /* $mainArr['FOOD_MENU.PHP']['LOCATION_FILE'] = 'menuitem_category.php';
        $mainArr['FOOD_MENU_ACTION']['LOCATION_FILE'] = 'menuitem_category_action.php';
        $mainArr['MASTER_CATEGORY_DELIVERALL']['LOCATION_FILE'] = 'master_category.php?eType=DeliverAll';*/
        $mainArr['STORE_DOCUMENT_ACTION']['LOCATION_FILE'] = 'restaurant_document_action.php';
        $mainArr['STORE_VEHICLE_TYPE.PHP']['LOCATION_FILE'] = 'restaurant_vehicle_type.php';
        $mainArr['STORE_VEHICLE_TYPE_ACTION']['LOCATION_FILE'] = 'restaurant_vehicle_type_action.php';
        return $mainArr;
    }

    private static function mainArr()
    {

        $ARR = [];
        $ARR['RIDER.PHP']['LOCATION_FILE'] = 'rider.php';
        $ARR['RIDER.PHP']['SOURCE_FILE'] = 'rider.php';

        $ARR['RIDER_ACTION']['LOCATION_FILE'] = 'rider_action.php';
        $ARR['RIDER_ACTION']['SOURCE_FILE'] = 'rider_action.php';

        $ARR['DRIVER.PHP']['LOCATION_FILE'] = 'driver.php';
        $ARR['DRIVER.PHP']['SOURCE_FILE'] = 'driver.php';

        $ARR['DRIVER_ACTION']['LOCATION_FILE'] = 'driver_action.php';
        $ARR['DRIVER_ACTION']['SOURCE_FILE'] = 'driver_action.php';

        $ARR['MASTER_CATEGORY']['LOCATION_FILE'] = 'vehicle_category.php';
        $ARR['MASTER_CATEGORY']['SOURCE_FILE'] = 'vehicle_category.php';

        $ARR['MASTER_CATEGORY_RIDE']['LOCATION_FILE'] = 'vehicle_category.php?eType=Ride';
        $ARR['MASTER_CATEGORY_RIDE']['SOURCE_FILE'] = 'vehicle_category.php';

        $ARR['MASTER_CATEGORY_VIDEO-CONSULT']['LOCATION_FILE'] = 'vehicle_category.php?eType=VideoConsult';
        $ARR['MASTER_CATEGORY_VIDEO-CONSULT']['SOURCE_FILE'] = 'vehicle_category.php';

        $ARR['MASTER_CATEGORY_UBERX']['LOCATION_FILE'] = 'vehicle_category.php?eType=UberX';
        $ARR['MASTER_CATEGORY_UBERX']['SOURCE_FILE'] = 'vehicle_category.php';

        $ARR['LATER_BOOKING']['LOCATION_FILE'] = 'cab_booking.php';
        $ARR['LATER_BOOKING']['SOURCE_FILE'] = 'cab_booking.php';

        $ARR['TRIP']['LOCATION_FILE'] = 'trip.php';
        $ARR['TRIP']['SOURCE_FILE'] = 'trip.php';

        $ARR['TRIP_HELP_DETAILS']['LOCATION_FILE'] = 'trip_help_details.php';
        $ARR['TRIP_HELP_DETAILS']['SOURCE_FILE'] = 'trip_help_details.php';

        $ARR['BLOCKED_RIDER']['LOCATION_FILE'] = 'blocked_rider.php';
        $ARR['BLOCKED_RIDER']['SOURCE_FILE'] = 'blocked_rider.php';

        $ARR['BLOCKED_DRIVER']['LOCATION_FILE'] = 'blocked_driver.php';
        $ARR['BLOCKED_DRIVER']['SOURCE_FILE'] = 'blocked_driver.php';

        $ARR['CANCELLED_TRIP']['LOCATION_FILE'] = 'cancelled_trip.php';
        $ARR['CANCELLED_TRIP']['SOURCE_FILE'] = 'cancelled_trip.php';

        $ARR['APP_LAUNCH_INFO_PASSENGER']['LOCATION_FILE'] = 'app_launch_info.php?option=Passenger';
        $ARR['APP_LAUNCH_INFO_PASSENGER']['SOURCE_FILE'] = 'app_launch_info.php?option=Passenger';

        $ARR['APP_LAUNCH_INFO_DRIVER']['LOCATION_FILE'] = 'app_launch_info.php?option=Driver';
        $ARR['APP_LAUNCH_INFO_DRIVER']['SOURCE_FILE'] = 'app_launch_info.php?option=Driver';

        $ARR['FOOD_MENU.PHP']['LOCATION_FILE'] = 'food_menu.php';
        $ARR['FOOD_MENU.PHP']['SOURCE_FILE'] = 'food_menu.php';

        $ARR['FOOD_MENU_ACTION']['LOCATION_FILE'] = 'food_menu_action.php';
        $ARR['FOOD_MENU_ACTION']['SOURCE_FILE'] = 'food_menu_action.php';

        $ARR['MASTER_CATEGORY_DELIVERALL']['LOCATION_FILE'] = 'vehicle_category.php?eType=DeliverAll';
        $ARR['MASTER_CATEGORY_DELIVERALL']['SOURCE_FILE'] = 'vehicle_category.php';

        $ARR['RESTAURANTS_PAY_REPORT.PHP']['LOCATION_FILE'] = 'restaurants_pay_report.php';
        $ARR['RESTAURANTS_PAY_REPORT.PHP']['SOURCE_FILE'] = 'restaurants_pay_report.php';

        $ARR['TOTAL_TRIP_DETAIL']['LOCATION_FILE'] = 'total_trip_detail.php';
        $ARR['TOTAL_TRIP_DETAIL']['SOURCE_FILE'] = 'total_trip_detail.php';

        $ARR['DRIVER_DOCUMENT_ACTION']['LOCATION_FILE'] = 'driver_document_action.php';
        $ARR['DRIVER_DOCUMENT_ACTION']['SOURCE_FILE'] = 'driver_document_action.php';

        $ARR['STORE.PHP']['LOCATION_FILE'] = 'store.php';
        $ARR['STORE.PHP']['SOURCE_FILE'] = 'store.php';

        $ARR['STORE_ACTION']['LOCATION_FILE'] = 'store_action.php';
        $ARR['STORE_ACTION']['SOURCE_FILE'] = 'store_action.php';

        $ARR['STORE_VEHICLE_TYPE.PHP']['LOCATION_FILE'] = 'store_vehicle_type.php';
        $ARR['STORE_VEHICLE_TYPE.PHP']['SOURCE_FILE'] = 'store_vehicle_type.php';

        $ARR['STORE_VEHICLE_TYPE_ACTION']['LOCATION_FILE'] = 'store_vehicle_type_action.php';
        $ARR['STORE_VEHICLE_TYPE_ACTION']['SOURCE_FILE'] = 'store_vehicle_type_action.php';

        return $ARR;
    }
}

?>