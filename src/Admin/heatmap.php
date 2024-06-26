<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-heatmap')) {
    $userObj->redirect();
}

// For Country
$sql = "SELECT vCountryCode,vCountry from country where eStatus = 'Active'";
$db_code = $obj->MySQLSelect($sql);

$sql = "select cn.vCountryCode,cn.vCountry,cn.vPhoneCode,cn.vTimeZone from country cn inner join
configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'";
$db_con = $obj->MySQLSelect($sql);

$vPhoneCode = clearPhone($db_con[0]['vPhoneCode']);
$vCountry = $db_con[0]['vCountryCode'];

$sqlcont = "select vCountry,tLatitude,tLongitude from country where vCountryCode='".$vCountry."'";
$db_contry = $obj->MySQLSelect($sqlcont);

$latitude = $db_contry[0]['tLatitude'];

$longitude = $db_contry[0]['tLongitude'];

// $address = $db_contry[0]['vCountry']; // Google HQ
// $prepAddr = str_replace(' ','+',$address);

// $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=true&key='. $GOOGLE_SEVER_API_KEY_WEB);
// $output= json_decode($geocode);

// $latLng = $output->results[0]->geometry->location;
// $latitude = $latLng->lat;
// $longitude = $latLng->lng;

if ('' === $latitude) {
    $latitude = '20.1849963';
}
if ('' === $longitude) {
    $longitude = '64.4125062';
}

$script = 'Heat Map';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
  <meta charset="UTF-8" />
  <title><?php echo $SITE_NAME; ?> | HeatMap</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />

  <!--[if IE]>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <![endif]-->
  <!-- GLOBAL STYLES -->
  <?php include_once 'global_files.php'; ?>

  <script type='text/javascript' src='../assets/map/gmaps.js'></script>
  <!--END GLOBAL STYLES -->

  <!-- PAGE LEVEL STYLES -->
  <!-- END PAGE LEVEL  STYLES -->
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
  <![endif]-->
  <style>
     /* html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
      #floating-panel {
        background-color: #fff;
        border: 1px solid #999;
        left: 25%;
        padding: 5px;
        position: absolute;
        top: 10px;
        z-index: 5;
      }*/
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 " >

<!-- MAIN WRAPPER -->
<div id="wrap">
  <?php include_once 'header.php'; ?>
  <?php include_once 'left_menu.php'; ?>
  <!--PAGE CONTENT -->
  <div id="content">

    <div class="inner" style="min-height: 900px;">
      <div class="row">
        <div class="col-lg-12">
          <h1> Heat View</h1>
        </div>
      </div>
      <hr />

      <!-- COMMENT AND NOTIFICATION  SECTION -->
      <div class="row">
        <div class="col-lg-12">
          <div class="chat-panel panel panel-default">
            <div class="panel-heading">
            <i class="icon-map-marker"></i>
            Locations
            </div>
            <div class="panel-heading" style="background:none;">
              <div class="google-map-wrap heatmap-but">
                 <div id="floating-panel">
                  <button onClick="toggleHeatmap()" class="toggle">Toggle Heatmap</button>
                  <button onClick="changeGradient()" class="gradient">Change Gradient</button>
                  <button onClick="changeRadius()" class="radius">Change Raduis</button>
                  <button onClick="changeOpacity()" class="opacity">Change Opacity</button>
                </div>
                <div id="map" style="width: 100%; height: 636.5px;margin: 20px 0 0 0;"></div>
                <!--<div id="google-map" class="google-map" style="width: 100%; height: 100%; position: absolute;">
                </div>--><!-- #google-map -->
              </div>
            </div>
          </div>
        </div>
      </div>
	  <div class="admin-notes">
			<h4>Notes:</h4>
			<?php if ('UberX' !== $APP_TYPE) {?>
				<ul>
					<li>
						Maximum <?php echo strtolower($langage_lbl_admin['LBL_TRIP_TXT_ADMIN']); ?> area showing in green color with gradients.
					</li>
					<li>
						Online <?php echo strtolower($langage_lbl_admin['LBL_RIDERS_ADMIN']); ?> with maximum 5 minutes are showing as green dots.
					</li>
					<li>
						Toggle will show or hide the gradients from the map.
					</li>
					<li>
						Opacity will show the gradients part less visible and vice versa.
					</li>
          <li>
            Online <?php echo strtolower($langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']); ?> are not considered in graph.
          </li>
				</ul>
			<?php } else { ?>
				<ul>
					<li>
						Maximum Jobs area showing in green color with gradients.
					</li>
					<li>
						Online users with maximum 5 minutes are showing as green dots.
					</li>
					<li>
						Toggle will show or hide the gradients from the map.
					</li>
					<li>
						Opacity will show the gradients part less visible and vice versa.
					</li>
          <li>
            Online Providers are not considered in graph.
          </li>
				</ul>
			<?php } ?>


		</div>

    </div>

    <div class="clear"></div>
    <!-- END COMMENT AND NOTIFICATION  SECTION -->
    <br><br><br><br>
  </div>


<!--END PAGE CONTENT -->
</div>

<?php include_once 'footer.php'; ?>
<?php

$str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));

// register_user table
$sql2 = "SELECT vLatitude,vLongitude  FROM `register_user`
			WHERE (vLatitude != '' AND vLongitude != '' AND eStatus='Active' AND tLastOnline > '{$str_date}')
			ORDER BY `register_user`.iUserId ASC";
$db_users = $obj->MySQLSelect($sql2);
// echo "<pre>";print_r($db_users);exit;
if (SITE_TYPE === 'Demo') {
    $sql = "SELECT iTripId,iUserId,iDriverId,tStartLat,tStartLong FROM trips WHERE tStartLat !='' AND tStartLong !='' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 2500 HOUR)";
} else {
    $sql = "SELECT iTripId,iUserId,iDriverId,tStartLat,tStartLong FROM trips WHERE tStartLat !='' AND tStartLong !='' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR)";
}

if ($MODULES_OBJ->isOnlyDeliverAllSystem()) {
    $db_users = [];
}
$db_records = $obj->MySQLSelect($sql);
// echo "<pre>";print_r($db_records);exit;
$str = '';
$str2 = '';
$newArr = [];
for ($i = 0; $i < count($db_records); ++$i) {
    $str .= 'new google.maps.LatLng('.$db_records[$i][tStartLat].', '.$db_records[$i][tStartLong].'),';
    $newArr[] = 'new google.maps.LatLng('.$db_records[$i][tStartLat].', '.$db_records[$i][tStartLong].')';
}

for ($i = 0; $i < count($db_users); ++$i) {
    $str2 .= 'new google.maps.LatLng('.$db_users[$i][vLatitude].', '.$db_users[$i][vLongitude].'),';
    $newArr[] = 'new google.maps.LatLng('.$db_users[$i][vLatitude].', '.$db_users[$i][vLongitude].')';
}

// echo "<pre>";
// print_r($newArr); die;

$map_area_lat = $db_records[0]['tStartLat'] ?? '';
$map_area_lng = $db_records[0]['tStartLong'] ?? '';
$str = substr($str, 0, -1);
// echo $str;exit;

?>
 <script>

      // This example requires the Visualization library. Include the libraries=visualization
      // parameter when you first load the API. For example:

	var map, heatmap, heatmap2;
	var marker, i;
	var markers = [];
	var bounds;
      function initMap() {
		bounds = new google.maps.LatLngBounds();
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 6,
         center: {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?>},
          //center: {lat: <?php echo $map_area_lat; ?>, lng: <?php echo $map_area_lng; ?>},
          mapTypeId: 'roadmap'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: getPoints(),
          map: map
        });
        changeRadius();
       // changeGradient();


        heatmap2 = new google.maps.visualization.HeatmapLayer({
          data: getPoints2(),
          map: map
        });
		changeGradient2();

		<?php if (!empty($newArr)) {
		    foreach ($newArr as $asd) { ?>
    		  var marker = new google.maps.Marker({
    			position: <?php echo $asd; ?>,
    			map: map
    		  });
    		  bounds.extend(marker.position);
    		  marker.setMap(null);
    <?php } ?>
      //bounds.extend(marker.position);
      map.fitBounds(bounds);
    <?php } ?>

	  }

      function toggleHeatmap() {
			$(".toggle").toggleClass('active');
			heatmap.setMap(heatmap.getMap() ? null : map);
			heatmap2.setMap(heatmap2.getMap() ? null : map);
      }

      function changeGradient() {
		$(".gradient").toggleClass('active');
        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
      }

	  function changeGradient2() {
        var gradient2 = [
          'rgba(0, 255, 100, 0)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)',
          'rgba(33, 99, 13, 1)'
        ]
        heatmap2.set('gradient', gradient2);
      }

      function changeRadius() {
		$(".radius").toggleClass('active');
        heatmap.set('radius', heatmap.get('radius') ? null : 20);
      }

      function changeRadius2() {
        heatmap2.set('radius', heatmap2.get('radius') ? null : 20);
      }

      function changeOpacity() {
		$(".opacity").toggleClass('active');
        heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
        heatmap2.set('opacity', heatmap2.get('opacity') ? null : 0.2);
      }

      function getPoints() {
        return [<?php echo $str; ?>];
      }

      function getPoints2() {
        return [<?php echo $str2; ?>];
      }

    </script>
 <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>&libraries=visualization&callback=initMap"></script>
</body>
<!-- END BODY-->
</html>