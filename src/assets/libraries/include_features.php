<?php

require_once('include_header.php');

// if (!defined('ALLOWED_DOMAINS')) { exit; }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.driver_reward.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.driver_reward.php';
// }
// if(class_exists('DriverReward')) {
// 	$DRIVER_REWARD_OBJ = new DriverReward;
// }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.bidding.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.bidding.php';
// }
// if(class_exists('Bidding')) {
// 	$BIDDING_OBJ = new Bidding;
// }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.video_consultation.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.video_consultation.php';
// }
// if(class_exists('VideoConsultation')) {
// 	$VIDEO_CONSULT_OBJ = new VideoConsultation;
// }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.menu_item_media.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.menu_item_media.php';
// }
// if(class_exists('MenuItemMedia')) {
// 	$MENU_ITEM_MEDIA_OBJ = new MenuItemMedia;
// }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.delete_account.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.delete_account.php';
// }
// if(class_exists('Delete_account')) {
//     $DELETE_ACCOUNT_OBJ = new Delete_account;
// }

// if(file_exists($tconfig['tpanel_path'] . 'assets/libraries/features/class.rentitem.php')) {
// 	include_once $tconfig['tpanel_path'] . 'assets/libraries/features/class.rentitem.php';
// }
// if(class_exists('RentItem')) {
// 	$RENTITEM_OBJ = new RentItem;
// }

if(class_exists('DriverReward')) {
    $DRIVER_REWARD_OBJ = new Kesk\Web\Common\DriverReward;
}

if(class_exists('Delete_account')) {
    $DELETE_ACCOUNT_OBJ = new Kesk\Web\Common\Delete_account;
}
if(class_exists('MenuItemMedia')) {
    $MENU_ITEM_MEDIA_OBJ = new Kesk\Web\Common\MenuItemMedia;
}
if(class_exists('VideoConsultation')) {
    $VIDEO_CONSULT_OBJ = new Kesk\Web\Common\VideoConsultation;
}
if(class_exists('Bidding')) {
    $BIDDING_OBJ = new Kesk\Web\Common\Bidding;
}
if(class_exists('DriverReward')) {
    $DRIVER_REWARD_OBJ = new Kesk\Web\Common\DriverReward;
}
if(class_exists('RentItem')) {
    $RENTITEM_OBJ = new Kesk\Web\Common\RentItem;
}
if(class_exists('TrackService')) {
    $TRACK_SERVICE_OBJ = new Kesk\Web\Common\TrackService;
}
if(class_exists('TrackAnyService')) {
    $TRACK_ANY_SERVICE_OBJ = new Kesk\Web\Common\TrackAnyService;
}
if(class_exists('NearBy')) {
    $NEARBY_OBJ = new Kesk\Web\Common\NearBy;
}
if(class_exists('GiftCard')) {
    $GIFT_CARD_OBJ = new Kesk\Web\Common\GiftCard;
}
if(class_exists('RiderReward')) {
    $RIDER_REWARD_OBJ = new Kesk\Web\Common\RiderReward;
}
if(class_exists('Parking')) {
    $PARKING_OBJ = new Kesk\Web\Common\Parking;
}
if(class_exists('TaxiBid')) {
    $TAXIBID_OBJ = new Kesk\Web\Common\TaxiBid;
}
if(class_exists('RideShare')) {
    $RIDE_SHARE_OBJ = new Kesk\Web\Common\RideShare;
}
