<?php
session_start();
$language = $_SESSION['sess_lang'];
$searching_string = $_REQUEST['query_text'];
$session_token = 'Passenger_'.random_int(10, 100).'_'.random_int(1_000_000_000_000, 9_999_999_999_999);
$array = [
    'language_code' => $language,
    'search_query' => $searching_string,
    'session_token' => $session_token,
];
echo json_encode($array);
?>


