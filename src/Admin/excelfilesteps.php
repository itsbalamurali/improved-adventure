<?php
include_once '../common.php';
$fav_icon_image = 'favicon.ico';
if (file_exists($tconfig['tpanel_path'].$logogpath.$fav_icon_image)) {
    $fav_icon_image = $tconfig['tsite_url'].$logogpath.$fav_icon_image;
} else {
    $fav_icon_image = $tconfig['tsite_url'].''.ADMIN_URL_CLIENT.'/'.'images/'.$fav_icon_image;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Excel File</title>
	<link rel="icon" href="<?php echo $fav_icon_image; ?>" type="image/x-icon">
</head>
<style>
.imgclass {
	width: 1020px;
	margin: 0 auto;
}
.imgclass img{
	width: 100%;
	border: 1px dashed darkblue;
    padding: 5px;
}
</style>
<body>
	<h3>Follow Below Steps</h3>
	<div class="imgclass">
		<div>
			<h3>Step 1:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/1.png">
		</div>
		<div>
			<h3>Step 2:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/2.png">
		</div>
		<div>
			<h3>Step 3:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/3.png">
		</div>
		<div>
			<h3>Step 4:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/4.png">
		</div>
		<div>
			<h3>Step 5:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/5.png">
		</div>
		<div>
			<h3>Step 6:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/6.png">
		</div>
		<div>
			<h3>Step 7:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/7.png">
		</div>
		<div>
			<h3>Step 8:</h3>
			<hr/>
			<img src="images/csv-utf8-steps/8.png">
		</div>
	</div>
</body>
</html>