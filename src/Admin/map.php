<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-gods-view')) {
    $userObj->redirect();
}

$script = 'LiveMap';
?>
<!DOCTYPE html>
<html lang="en">

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
	<title><?php echo $SITE_NAME; ?> | Live Map</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<!-- GLOBAL STYLES -->
	<?php include_once 'global_files.php'; ?>
	<link rel="stylesheet" href="css/style.css" />

	<script src="https://maps.google.com/maps/api/js?sensor=true&key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>" type="text/javascript"></script>
	<script type='text/javascript' src='../assets/map/gmaps.js'></script>
	<script type='text/javascript' src='../assets/js/jquery-ui.min.js'></script>
	<!--END GLOBAL STYLES -->

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

		<div class="inner" style="min-height: 700px;">
			<div class="row">
				<div class="col-lg-12">
					<h1> God's View</h1>
				</div>
			</div>
			<hr />

		 <div class="map-color-code">
			<ul>
			  <li> <a href="javascript:void(0);" onClick="setNewDriverLocations('Active','set1')"><img src="../assets/img/red.png">
				<h2>
				  <button role="button" class="btn btn-default setclass enroute" id="set1"><?php echo $langage_lbl['LBL_ENROUTE_TO']; ?></button>
				</h2>
				</a> </li>
			  <li> <a  href="javascript:void(0);" onClick="setNewDriverLocations('Arrived','set2')"><img src="../assets/img/blue.png">
				<h2>
				  <button role="button" class="btn btn-default setclass reached" id="set2"><?php echo $langage_lbl['LBL_REACHED_PICKUP']; ?></button>
				</h2>
				</a> </li>
			  <li> <a href="javascript:void(0);" onClick="setNewDriverLocations('On Going Trip','set3')"><img src="../assets/img/yellow.png">
				<h2>
				  <button role="button" class="btn btn-default setclass tripstart" id="set3"><?php echo $langage_lbl['LBL_JOURNEY_STARTED']; ?></button>
				</h2>
				</a> </li>
			  <li> <a href="javascript:void(0);" onClick="setNewDriverLocations('Available','set4')"><img src="../assets/img/green.png">
				<h2>
				  <button role="button" class="btn btn-default setclass available"  id="set4">
				  <?php echo $langage_lbl['LBL_AVAILABLE']; ?>
				  </button>
				</h2>
				</a> </li>
			  <li> <a href="javascript:void(0);" onClick="setNewDriverLocations('','set5')">
				<h2>
				  <button role="button" class="btn raised setclass active all" id="set5"><?php echo $langage_lbl['LBL_ALL']; ?></button>
				</h2>
				</a> </li>
			</ul>
		</div>
	  <div class="map-main-page-inner">
        <div class="map-main-page-inner-tab">
          <h3 class="list_title"><a href="javascript:void(0);" class="active">All</a></h3>
          <span>
          <input name="" type="text" id="finddriver" placeholder="Search <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>" onKeyUp="get_drivers_list(this.value);">
          </span>
          <ul id="driver_main_list"  style="display:none">
          </ul>
        </div>
        <div class="map-page">
          <!-- <div class="panel-heading location-heading">
							<i class="icon-map-marker"></i>
							Locations
						</div> -->
          <div class="panel-heading location-map" style="background:none;">
			  <div class="google-map-wrap">
			  <div id="google-map" class="google-map map001"> </div>
				  <!-- #google-map -->
			  </div>
        </div>
        </div>
        <!-- popup -->
        <div class="map-popup" style="display:none" id="driver_popup"></div>
        <!-- popup end -->
      </div>
      <input type="hidden" name="newType" id="newType" value="">
      <input type="hidden" name="drivename" id="drivename" value="">
      <div style="clear:both;"></div>
      <?php if (SITE_TYPE !== 'Demo') { ?>
		<div class="admin-notes">
		<h4>Notes:</h4>
		<ul>
		    <li>
		            IMPORTANT: Please note that the <?php echo $langage_lbl_admin['LBL_DRIVERS_TXT_ADMIN']; ?> shown as Available/Online ( Green ) are only those <?php echo $langage_lbl_admin['LBL_DRIVERS_TXT_ADMIN']; ?> which are available in real with <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> application running properly in their phone. If <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> has put his application in background since long time then there is major possibility of application being killed in background, which makes them unavailable for <?php echo strtolower($langage_lbl_admin['LBL_RIDERS_ADMIN']); ?> and also shown offline in map despite of they are online in application.
		    </li>
		</ul>
		</div>
	<?php } ?>
	</div>

<!--END PAGE CONTENT -->
</div>

<?php include_once 'footer.php'; ?>
<div style="clear:both;"></div>
	<script>
	//var is_touch_device = 'ontouchstart' in document.documentElement;
	var newName; var newAddr; var newOnlineSt; var newLat; var newLong; var newImg; var map;
	var bounds = [];
	var markers = [];
	var latlng;
	var newImg;
	var newLocations;
	jQuery( document ).ready( function($) {
		/* Do not drag on mobile. */
		var ajaxData = {
		    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_map_drivers_list.php',
		    'AJAX_DATA': {type: ''},
		    'REQUEST_DATA_TYPE': 'json'
		};
		getDataFromAjaxCall(ajaxData, function(response) {
		    if(response.action == "1") {
		        var dataHtml = response.result;
		        newLocations = dataHtml.locations;
				if(newLocations == "") {
					map = new GMaps({
						el: '#google-map',
						lat: '',
						lng: '',
						//scrollwheel: false,
						//draggable: ! is_touch_device
					});
				}else {
					map = new GMaps({
						el: '#google-map',
						lat: newLocations[0].google_map.lat,
						lng: newLocations[0].google_map.lng,
						//scrollwheel: false,
						//draggable: ! is_touch_device
					});
				}

				for (var i = 0; i < newLocations.length; i++) {
					newName = newLocations[i].location_name;
					newAddr = newLocations[i].location_address;
					newOnlineSt = newLocations[i].location_online_status;
					newLat = newLocations[i].google_map.lat;
					newLong = newLocations[i].google_map.lng;
					newDriverImg = newLocations[i].location_image;
					newMobile = newLocations[i].location_mobile;
					newDriverID = newLocations[i].location_ID;
					newImg = newLocations[i].location_icon;

					latlng = new google.maps.LatLng(newLat, newLong);
					bounds.push(latlng);

					// if(newOnlineSt == 'Available') { newImg = '../webimages/upload/mapmarker/available.png'; } else if(newOnlineSt == 'Active') { newImg = '../webimages/upload/mapmarker/enroute.png'; }else if(newOnlineSt == 'Arrived') { newImg = '../webimages/upload/mapmarker/reached.png'; }else { newImg = '../webimages/upload/mapmarker/started.png'; }
					var marker = map.addMarker({
						lat: newLat,
						lng: newLong,
						icon: newImg,
						infoWindow: {
							content: '<table><tr><td rowspan="4"><img src="'+newDriverImg+'" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Name: </td><td><b>'+newName+'</b></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>'+newDriverID+'</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+'+newMobile+'</b></td></tr></table>'
						}
					});
					markers.push(marker);
				}
				map.fitLatLngBounds(bounds);

				var ajaxData = {
				    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_in_godsview.php',
				    'AJAX_DATA': {type: ''},
				    'REQUEST_DATA_TYPE': 'html'
				};
				getDataFromAjaxCall(ajaxData, function(response) {
				    if(response.action == "1") {
				        var dataHtml2 = response.result;
				        $('#driver_main_list').show();
						$('#driver_main_list').html(dataHtml2);
				    }
				    else {
				        console.log(response.result);
				    }
				});
		    }
		    else {
		        console.log(response.result);
		        var map = new GMaps({
					el: '#google-map',
					lat: '',
					lng: '',
					// scrollwheel: false,
					// draggable: ! is_touch_device
				});
		    }
		});

		var $window = $(window);
		function mapWidth() {
			var size = $('.google-map-wrap').width();
			$('.google-map').css({width: size + 'px', height: (size/2) + 'px'});
		}
		setTimeout(function(){
		mapWidth();
		}, 2000)
		$(window).resize(mapWidth);
	});


	/* Map Reload after a minute */
	setInterval(function() {
		newType = $("#newType").val();

		var ajaxData = {
		    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_map_drivers_list.php',
		    'AJAX_DATA': {type: newType},
		    'REQUEST_DATA_TYPE': 'json'
		};
		getDataFromAjaxCall(ajaxData, function(response) {
		    if(response.action == "1") {
		        var dataHtml = response.result;
				newLocations = dataHtml.locations;
				if(newLocations == "") {
					map = new GMaps({
						el: '#google-map',
						lat: '',
						lng: '',
						//scrollwheel: false,
						//draggable: ! is_touch_device
					});
				}else {
					map = new GMaps({
						el: '#google-map',
						lat: newLocations[0].google_map.lat,
						lng: newLocations[0].google_map.lng,
						//scrollwheel: false,
						//draggable: ! is_touch_device
					});
				}
		        for (var i = 0; i < markers.length; i++) {
				  markers[i].setMap(null);
				}
				newLocations = dataHtml.locations;
				for (var i = 0; i < newLocations.length; i++) {
					if(newType == newLocations[i].location_type || newType == "") {
						newName = newLocations[i].location_name;
						newAddr = newLocations[i].location_address;
						newOnlineSt = newLocations[i].location_online_status;
						newLat = newLocations[i].google_map.lat;
						newLong = newLocations[i].google_map.lng;
						newDriverImg = newLocations[i].location_image;
						newMobile = newLocations[i].location_mobile;
						newDriverID = newLocations[i].location_ID;
						newImg = newLocations[i].location_icon;

						latlng = new google.maps.LatLng(newLat, newLong);
						bounds.push(latlng);

						// if(newOnlineSt == 'Available') { newImg = '../webimages/upload/mapmarker/available.png'; } else if(newOnlineSt == 'Active') { newImg = '../webimages/upload/mapmarker/enroute.png'; }else if(newOnlineSt == 'Arrived') { newImg = '../webimages/upload/mapmarker/reached.png'; }else { newImg = '../webimages/upload/mapmarker/started.png'; }
						var marker = map.addMarker({
							lat: newLat,
							lng: newLong,
							icon: newImg,
							infoWindow: {
								content: '<table><tr><td rowspan="4"><img src="'+newDriverImg+'" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Name: </td><td><b>'+newName+'</b></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>'+newDriverID+'</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+'+newMobile+'</b></td></tr></table>'
							}
						});
						markers.push(marker);
					}
				}

				if($("#finddriver").val() == '') {
					var ajaxData = {
				    	'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_in_godsview.php',
					    'AJAX_DATA': {type: newType},
					    'REQUEST_DATA_TYPE': 'html'
					};
					getDataFromAjaxCall(ajaxData, function(response) {
					    if(response.action == "1") {
					        var dataHtml2 = response.result;
					        $('#driver_main_list').show();
							$('#driver_main_list').html(dataHtml2);
					    }
					    else {
					        console.log(response.result);
					    }
					});
				}
		    }
		    else {
		        console.log(response.result);
		        var map = new GMaps({
					el: '#google-map',
					lat: '',
					lng: '',
					// scrollwheel: false,
					// draggable: ! is_touch_device
				});
		    }
		});

	},10000);
	/* Map Reload after a minute */

	function setNewDriverLocations(type, set) {
		if(set != "") {
			$("#newType").val(type);
			$('.setclass').removeClass('active');
			$("#"+set).addClass('active');
			if(type == 'Active'){
				title = 'Enroute to Pickup';
				classname = 'enroute';
			} else if(type == 'Arrived'){
				title = 'Reached Pickup';
				classname = 'reached';
			} else if(type == 'On Going Trip'){
				title = 'Journey Started';
				classname = 'tripstart';
			} else if(type == 'Available'){
				title = 'Available';
				classname = 'available';
			} else {
				title = 'All';
				classname = 'all';
			}

		}

		for (var i = 0; i < markers.length; i++) {
		  markers[i].setMap(null);
		}


		var ajaxData = {
		    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_map_drivers_list.php',
		    'AJAX_DATA': {type: type},
		    'REQUEST_DATA_TYPE': 'json'
		};
		getDataFromAjaxCall(ajaxData, function(response) {
		    if(response.action == "1") {
		        var dataHtml = response.result;
		        newLocations = dataHtml.locations;
				if(newLocations == "") {
					map = new GMaps({
						el: '#google-map',
						lat: '',
						lng: '',
					});
				}else {
					map = new GMaps({
						el: '#google-map',
						lat: newLocations[0].google_map.lat,
						lng: newLocations[0].google_map.lng,
					});
				}

				for (var i = 0; i < newLocations.length; i++) {
					if(type == newLocations[i].location_type || type == "") {
						newName = newLocations[i].location_name;
						newAddr = newLocations[i].location_address;
						newOnlineSt = newLocations[i].location_online_status;
						newLat = newLocations[i].google_map.lat;
						newLong = newLocations[i].google_map.lng;
						newDriverImg = newLocations[i].location_image;
						newMobile = newLocations[i].location_mobile;
						newDriverID = newLocations[i].location_ID;
						newImg = newLocations[i].location_icon;
						latlng = new google.maps.LatLng(newLat, newLong);
						bounds.push(latlng);

						// if(newOnlineSt == 'Available') { newImg = '../webimages/upload/mapmarker/available.png'; } else if(newOnlineSt == 'Active') { newImg = '../webimages/upload/mapmarker/enroute.png'; }else if(newOnlineSt == 'Arrived') { newImg = '../webimages/upload/mapmarker/reached.png'; }else { newImg = '../webimages/upload/mapmarker/started.png'; }
						var marker = map.addMarker({
							lat: newLat,
							lng: newLong,
							icon: newImg,
							infoWindow: {
								content: '<table><tr><td rowspan="4"><img src="'+newDriverImg+'" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Name: </td><td><b>'+newName+'</b></td></tr><tr><td>&nbsp;&nbsp;ID: </td><td><b>'+newDriverID+'</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+'+newMobile+'</b></td></tr></table>'
							}
						});
						markers.push(marker);
					}
				}
				map.fitLatLngBounds(bounds);

				var ajaxData = {
			    	'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_in_godsview.php',
				    'AJAX_DATA': {type: type},
				    'REQUEST_DATA_TYPE': 'html'
				};
				getDataFromAjaxCall(ajaxData, function(response) {
				    if(response.action == "1") {
				        var dataHtml2 = response.result;
				        $('#driver_main_list').show();
						$('#driver_main_list').html(dataHtml2);
				    }
				    else {
				        console.log(response.result);
				    }
				});
		    }
		    else {
		        console.log(response.result);
		        var map = new GMaps({
					el: '#google-map',
					lat: '',
					lng: '',
					// scrollwheel: false,
					// draggable: ! is_touch_device
				});
		    }
		});

	}

	function get_drivers_list(keyword) {
		newType = $("#newType").val();

		var ajaxData = {
		    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_in_godsview.php',
		    'AJAX_DATA': {keyword: keyword,type: newType},
		    'REQUEST_DATA_TYPE': 'html'
		};
		getDataFromAjaxCall(ajaxData, function(response) {
		    if(response.action == "1") {
		        var dataHtml2 = response.result;
		        $('#driver_main_list').show();
				$('#driver_main_list').html(dataHtml2);
		    }
		    else {
		        console.log(response.result);
		    }
		});
	}

	function showPopupDriver(driverId) {
		if($("#driver_popup").is(":visible") && $('#driver_popup ul').attr('class') == driverId) {
			$("#driver_popup").hide( "slide", { direction: "right"  }, 700 );
		}else {

			$("#driver_popup").hide();

			var ajaxData = {
			    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_driver_detail_popup.php',
			    'AJAX_DATA': {driverId: driverId},
			    'REQUEST_DATA_TYPE': 'html'
			};
			getDataFromAjaxCall(ajaxData, function(response) {
			    if(response.action == "1") {
			        var dataHtml2 = response.result;
			        $('#driver_popup').html(dataHtml2);
					$("#driver_popup").show( "slide", { direction: "right"  }, 700 );
			    }
			    else {
			        console.log(response.result);
			    }
			});
		}
	}


	$(document).mouseup(function (e)
	{
		var container = $("#driver_popup");
		var container1 = $("#driver_main_list");

		if (!container.is(e.target) && !container1.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0 && container1.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.hide( "slide", { direction: "right"  }, 700 );
		}
	});

</script>
</body>
<!-- END BODY-->
</html>