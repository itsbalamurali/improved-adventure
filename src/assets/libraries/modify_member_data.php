<?php


if ('dev.bbcsproducts.net' === $_SERVER['HTTP_HOST']) {
    if ('getDetail' === $_REQUEST['type']) {
        unset($userDataArr['message']['TripDetails']['PetDetails'], $userDataArr['message']['TripDetails']['DriverCarDetails'], $userDataArr['message']['TripDetails']['DriverDetails'], $userDataArr['message']['TripDetails']['PassengerDetails'], $userDataArr['message']['TripDetails']['FareSubTotal'], $userDataArr['message']['TripDetails']['FareDetailsNewArr'], $userDataArr['message']['TripDetails']['FareDetailsArr'], $userDataArr['message']['TripDetails']['HistoryFareDetailsNewArr'], $userDataArr['message']['TripDetails']['HistoryFareDetailsArr']);

        return $userDataArr;
    }
}
