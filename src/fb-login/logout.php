<?php





session_start();
session_unset();
$_SESSION['FBID'] = null;
$_SESSION['FULLNAME'] = null;
$_SESSION['EMAIL'] = null;
header('Location: index.php');        // you can enter home page here ( Eg : header("Location: " ."http://www.krizna.com");
