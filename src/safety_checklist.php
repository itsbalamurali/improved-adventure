<?php
include 'common.php';

$lang = $_REQUEST['vLang'] ?? '';
$iPageId = $_REQUEST['iPageId'] ?? '55';
if (empty($lang)) {
    $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
}
$rideSafetyGuidelines = $obj->MySQLSelect('SELECT tPageDesc_'.$lang.' as tPageDesc FROM `pages` WHERE `iPageId` = '.$iPageId);
if (empty($rideSafetyGuidelines[0]['tPageDesc'])) {
    $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
    $rideSafetyGuidelines = $obj->MySQLSelect('SELECT tPageDesc_'.$lang.' as tPageDesc FROM `pages` WHERE `iPageId` = '.$iPageId);
}

$rideSafetyGuidelines[0]['tPageDesc'] = str_replace('#ffffff', 'white', $rideSafetyGuidelines[0]['tPageDesc']);
if ('Yes' === $THEME_OBJ->isCubeJekXv3ThemeActive()) {
    $rideSafetyGuidelines[0]['tPageDesc'] = preg_replace('/#[0-9A-Fa-f]{6}/i', APP_THEME_COLOR, $rideSafetyGuidelines[0]['tPageDesc']);
}

?>
<!DOCTYPE html>
<html>
<body>
	<?php echo str_replace('src="../assets/img/', 'src="'.$tconfig['tsite_url'].'assets/img/', html_entity_decode($rideSafetyGuidelines[0]['tPageDesc'])); ?>
</body>
</html>