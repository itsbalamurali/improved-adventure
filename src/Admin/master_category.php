<?php



include_once '../common.php';
$FILEARRAY = ChangeFileCls::fileArray($SOURCE_FILE = 'SOURCE_FILE');
$eType = $_REQUEST['eType'] ?? '';
if ('Ride' === $eType) {
    include_once $FILEARRAY['MASTER_CATEGORY_RIDE'];
}

if ('VideoConsult' === $eType) {
    include_once $FILEARRAY['MASTER_CATEGORY_VIDEO-CONSULT'];
}

if ('UberX' === $eType) {
    include_once $FILEARRAY['MASTER_CATEGORY_UBERX'];
}

if ('DeliverAll' === $eType) {
    include_once $FILEARRAY['MASTER_CATEGORY_DELIVERALL'];
}

exit;
