<?php
include_once '../common.php';

$AUTH_OBJ->AuthAdminRedirect();

if ('cubetaxiplus' === $host_system) {
    $logo = 'logo.png';
} elseif ('ufxforall' === $host_system) {
    $logo = 'ufxforall-logo.png';
} elseif ('uberridedelivery4' === $host_system) {
    $logo = 'ride-delivery-logo.png';
} elseif ('uberdelivery4' === $host_system) {
    $logo = 'delivery-logo-only.png';
} else {
    $logo = 'logo.png';
}

$userType = $_REQUEST['userType'] ?? '';
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Login Page</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link rel="icon" href="<?php echo $tconfig['tsite_url']; ?>favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url_main_admin']; ?>css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url_main_admin']; ?>css/login.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url_main_admin']; ?>css/style.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url']; ?>assets/css/animate/animate.min.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url']; ?>assets/plugins/magic/magic.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url_main_admin']; ?>css/font-awesome.css" />
		<link rel="stylesheet" href="<?php echo $tconfig['tsite_url']; ?>assets/plugins/font-awesome-4.6.3/css/font-awesome.min.css" />

	</head>
	<!-- END HEAD -->
	<!-- BEGIN BODY -->
	<body class="nobg loginPage">
		<input type="hidden" name="hdf_class" id="hdf_class" value="<?php echo $_SESSION['edita']; ?>">
		<div class="topNav">
			<div class="userNav">
				<ul>
					<li><a href="<?php echo $tconfig['tsite_url']; ?>index.php" title=""><i class="icon-reply"></i><span>Main website</span></a></li>
					<li><a href="<?php echo $tconfig['tsite_url']; ?>rider" title=""><i class="icon-user"></i><span><?php echo $langage_lbl_admin['LBL_RIDER']; ?> Login</span></a></li>
					<li><a href="<?php echo $tconfig['tsite_url']; ?>driver" title=""><i class="icon-comments"></i><span><?php echo $langage_lbl_admin['LBL_DRIVER']; ?> Login</span></a></li>
				</ul>
			</div>
		</div>
		<!-- PAGE CONTENT -->
		<div class="container animated fadeInDown">
			<div class="text-center"> <img src="<?php echo $tconfig['tsite_url']; ?>assets/img/<?php echo $logo; ?>" id="Admin" alt=" Admin" /> </div>
			<?php if ('hotel' === $userType) { ?>
				<div id="login">
					<p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-success" id="success" ></p>
					<p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-danger text-muted text-center" id="errmsg"></p>
					<div class="admin-home-tab">
						<div class="tab-content clearfix custom-tab">
							<h4>Hotel Administrator</h4>
						  	<div>
			          			<form action="" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid();" style="margin:0 auto;border:0;">
									<br>
									<b><label for="email">Hotel Administrator E-mail</label>
									<input type="text" placeholder="Email Address" class="form-control" name="vEmail" id="vEmail" required Value="<?php echo (SITE_TYPE === 'Demo') ? 'hoteladmin@gmail.com' : ''; ?>"/>
									</b>
									<b><label for="password">Password</label>
									<input type="password" placeholder="Password" class="form-control" name="vPassword" id="vPassword" required Value="<?php echo (SITE_TYPE === 'Demo') ? '123456' : ''; ?>"/>
									</b>
									<input type="hidden" name="group_id" id="group_id" value="4"/>
									<input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>
									<br>
								</form>
							</div>
						</div>
						<?php if (SITE_TYPE === 'Demo') { ?>
							<div class="tab-content">
								<div id="super001" class="tab-pane active">
									<h3> Use below Detail for Demo Version</h3>
									<p><b>User Name:</b> hoteladmin@gmail.com</p>
									<p><b>Password:</b> 123456 </p>
									<p>Hotel Administrator can book taxi.</p>
								</div>
							</div>
						<?php } ?>
						<div style="clear:both;"></div>
					</div>
				</div>
			<?php } else {?>
				<div class="tab-content">
					<div id="login" class="tab-pane active">
						<p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-success" id="success" ></p>
						<p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-danger text-muted text-center" id="errmsg"></p>
						<!--
							<form action="" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid();">
							<p class="head_login_005">Login</p>
							<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
							<input type="text" placeholder="Email Address" class="form-control" name="vEmail" id="vEmail" required />
							<span class="glyphicon glyphicon-lock form-control-feedback"></span>
							<input type="password" placeholder="Password" class="form-control" name="vPassword" id="vPassword" required />
							<input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>
							<br>
						</form>-->
						<div class="admin-home-tab">
							<ul class="nav nav-tabs">
								<li class="active" onClick="setCredentials('1', '<?php echo SITE_TYPE; ?>');passLoginid('super001','1');"><a data-toggle="tab" href="#super001">Super Administrator</a></li>
								<li onClick="setCredentials('2', '<?php echo SITE_TYPE; ?>');passLoginid('dispatch001','2');"><a data-toggle="tab" href="#dispatch001">Dispatcher Administrator</a></li>
								<li onClick="setCredentials('3', '<?php echo SITE_TYPE; ?>');passLoginid('billing001','3');"><a data-toggle="tab" href="#billing001">Billing Administrator</a></li>

							</ul>
							<div class="tab-content clearfix custom-tab">
							  	<div class="tab-pane active" id="super001">
				          			<form action="" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid();" style="margin:0 auto;border:0;">
										<br>
										<b><label for="email">Super Administrator E-mail</label>
											<input type="text" placeholder="Email Address" class="form-control" name="vEmail" id="vEmail" required Value="<?php echo (SITE_TYPE === 'Demo') ? 'demo@demo.com' : ''; ?>"/>
										</b>
										<b><label for="password">Password</label>
											<input type="password" placeholder="Password" class="form-control" name="vPassword" id="vPassword" required Value="<?php echo (SITE_TYPE === 'Demo') ? '123456' : ''; ?>"/>
										</b>
										<input type="hidden" name="group_id" id="group_id" value="1"/>
										<input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>
										<br>
									</form>
								</div>
							</div>
							<?php if (SITE_TYPE === 'Demo') { ?>
								<div class="tab-content">
									<div id="super001" class="tab-pane active">
										<h3> Use below Detail for Demo Version</h3>

										<p><b>User Name:</b> demo@demo.com</p>
										<p><b>Password:</b> 123456 </p>
										<p>Super Administrator can manage whole system and other <?php echo strtolower($langage_lbl_admin['LBL_RIDER']); ?>'s rights too.</p>
									</div>
									<div id="dispatch001" class="tab-pane">
										<h3> Use below Detail for Demo Version</h3>

										<p><b>User Name:</b> demo2@demo.com</p>
										<p><b>Password:</b> 123456 </p>
										<p>Call Center Panel / Administrator Dispatcher Panel / Manual Taxi Booking Panel. This panel allows one to see all taxi's on map using God's View. And book taxi's for customer's who would call to book a taxi.</p>
									</div>
									<div id="billing001" class="tab-pane">
										<h3> Use below Detail for Demo Version</h3>

										<p><b>User Name:</b> demo3@demo.com</p>
										<p><b>Password:</b> 123456 </p>
										<p>This use will have access to reports only. Will be used by Accounts Team to manage finances and see profits/revenue.</p>
									</div>
								</div>
							<?php } ?>
							<div style="clear:both;"></div>
						</div>

					</div>
					<div id="forgot" class="tab-pane">
						<form  class="form-signin" method="post" id="frmforget">
							<input type="email"  required="required" placeholder="Your E-mail"  class="form-control" id="femail"/>
							<br />
							<button class="btn text-muted text-center btn-success" type="submit" onClick="forgotPass();">Recover Password</button>
						</form>
					</div>
				</div>
			<?php } ?>
		</div>
		<!--END PAGE CONTENT -->
		<!-- PAGE LEVEL SCRIPTS -->
		<script src="<?php echo $tconfig['tsite_url']; ?>assets/plugins/jquery-2.0.3.min.js"></script>
		<script src="<?php echo $tconfig['tsite_url']; ?>assets/plugins/bootstrap/js/bootstrap.js"></script>
		<script src="<?php echo $tconfig['tsite_url']; ?>assets/js/login.js"></script>
		<script>
			var testLink = '<?php echo $_SESSION['current_link']; ?>';
			function setCredentials(tpd, site_type) {
				if(site_type == "Demo")
				{
					if(tpd == 2){
						$("#vEmail").val('demo2@demo.com');
						$("#vPassword").val('123456');
					}
					else if(tpd == 3)
					{
						$("#vEmail").val('demo3@demo.com');
						$("#vPassword").val('123456');
					}
					else
					{
						$("#vEmail").val('demo@demo.com');
						$("#vPassword").val('123456');
					}
				}
			}

			function passLoginid(tabid,login_group_id) {
				$(".custom-tab .tab-pane").attr('id',tabid);
				$("#group_id").val(login_group_id);
				if(tabid == "dispatch001") {
					$("label[for = email]").text("Dispatcher Administrator E-mail");
				} else if(tabid == "billing001") {
					$("label[for = email]").text("Billing Administrator E-mail");
				} else {
					$("label[for = email]").text("Super Administrator E-mail");
				}
			}

			$('input').keyup(function(){
				$this = $(this);
				if($this.val().length == 1)
				{
					var x =  new RegExp("[\x00-\x80]+"); // is ascii

					var isAscii = x.test($this.val());
					if(isAscii)
					{
						$this.attr("dir", "ltr");
					}
					else
					{
						$this.attr("dir", "rtl");
					}
				}

			});
			function change_heading(heading, addClass, removeClass)
			{
				document.getElementById("login").innerHTML= heading;
				document.getElementById(addClass).className = "tab-pane";
				document.getElementById(removeClass).className = "tab-pane active";
			}
			function chkValid()
			{
				var id = document.getElementById("vEmail").value;
				var pass = document.getElementById("vPassword").value;
				if(id == '' || pass == '')
				{
					document.getElementById("errmsg").style.display = '';
					setTimeout(function() {document.getElementById('errmsg').style.display='none';},2000);
				}
				else
				{
					// var request = $.ajax({
					// 	type: "POST",
					// 	url: '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_login_action.php',
					// 	data: $("#login_box").serialize(),

					// 	success: function(dataHTml)
					// 	{// alert(data);
					// 		dataHTml = dataHTml.trim();
					// 		if(dataHTml == 1){
					// 			document.getElementById("errmsg").innerHTML = 'You are not active.Please contact administrator to activate your account.';
					// 			document.getElementById("errmsg").style.display = '';
					// 			return false;
					// 		}
					// 		else if(dataHTml == 2){

					// 			document.getElementById("errmsg").style.display = 'none';
					// 			var hdf_class=$("#hdf_class").val();
					// 			if(hdf_class!="")
					// 			{
					// 				window.location = "<?php echo $tconfig['tsite_url_main_admin']; ?>languages.php";
					// 			}
					// 			else
					// 			{
					// 				if(testLink == "") {
					// 					testLink = "<?php echo $tconfig['tsite_url_main_admin']; ?>dashboard.php";
					// 				}
					// 				window.location = testLink;
					// 			}
					// 			return true; // success registration
					// 		}
					// 		else if(dataHTml == 3) {
					// 			document.getElementById("errmsg").innerHTML = 'Invalid combination of username & Password';
					// 			document.getElementById("errmsg").style.display = '';
					// 			return false;

					// 		}
					// 		else {
					// 			document.getElementById("errmsg").innerHTML = 'Invalid Email or Password';
					// 			document.getElementById("errmsg").style.display = '';
					// 			//setTimeout(function() {document.getElementById('errmsg1').style.display='none';},2000);
					// 			return false;
					// 		}
					// 	}
					// });

					// request.fail(function(jqXHR, textStatus) {
					// 	alert( "Request failed: " + textStatus );
					// });

					var ajaxData = {
					    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_login_action.php',
					    'AJAX_DATA': $("#login_box").serialize(),
					};
					getDataFromAjaxCall(ajaxData, function(response) {
					    if(response.action == "1") {
					        var dataHTml = response.result;
					        dataHTml = dataHTml.trim();
							if(dataHTml == 1){
								document.getElementById("errmsg").innerHTML = 'You are not active.Please contact administrator to activate your account.';
								document.getElementById("errmsg").style.display = '';
								return false;
							}
							else if(dataHTml == 2){

								document.getElementById("errmsg").style.display = 'none';
								var hdf_class=$("#hdf_class").val();
								if(hdf_class!="")
								{
									window.location = "<?php echo $tconfig['tsite_url_main_admin']; ?>languages.php";
								}
								else
								{
									if(testLink == "") {
										testLink = "<?php echo $tconfig['tsite_url_main_admin']; ?>dashboard.php";
									}
									window.location = testLink;
								}
								return true; // success registration
							}
							else if(dataHTml == 3) {
								document.getElementById("errmsg").innerHTML = 'Invalid combination of username & Password';
								document.getElementById("errmsg").style.display = '';
								return false;

							}
							else {
								document.getElementById("errmsg").innerHTML = 'Invalid Email or Password';
								document.getElementById("errmsg").style.display = '';
								//setTimeout(function() {document.getElementById('errmsg1').style.display='none';},2000);
								return false;
							}
					    }
					    else {
					        console.log(response.result);
					    }
					});

				}
				return false;
			}
			function forgotPass()
			{
				var id = document.getElementById("femail").value;
				if(id == '')
				{

					document.getElementById("errmsg_email").style.display = '';
					document.getElementById("errmsg_email").innerHTML = 'Please enter Email Address';
					return false;
				}
				else {

					// var request = $.ajax({
					// 	type: "POST",
					// 	url: 'ajax_fpass_action.php',
					// 	data: $("#frmforget").serialize(),
					// 	beforeSend:function()
					// 	{
					// 		alert(data);
					// 	},
					// 	success: function(data)
					// 	{
					// 		if(data == 1)
					// 		{
					// 			document.getElementById("page_title").innerHTML= "Login";
					// 			document.getElementById("forgot").className = "tab-pane";
					// 			document.getElementById("login").className = "tab-pane active";
					// 			document.getElementById("success").innerHTML = 'Your Password has been sent Successfully.';
					// 			document.getElementById("success").style.display = '';
					// 			return false;
					// 		}
					// 		else if(data == 0)
					// 		{
					// 			document.getElementById("errmsg_email").innerHTML = 'Error in Sending Password.';
					// 			document.getElementById("errmsg_email").style.display = '';
					// 			return false;

					// 		}
					// 		else if(data == 3)
					// 		{
					// 			document.getElementById("errmsg_email").innerHTML = 'Sorry ! The Email address you have entered is not found.';
					// 			document.getElementById("errmsg_email").style.display = '';
					// 			return false;
					// 		}
					// 		return false;
					// 	}
					// });
					// request.fail(function(jqXHR, textStatus) {
					// 	alert( "Request failed: " + textStatus );
					// 	return false;
					// });

					var ajaxData = {
					    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_fpass_action.php',
					    'AJAX_DATA': $("#frmforget").serialize(),
					};
					getDataFromAjaxCall(ajaxData, function(response) {
					    if(response.action == "1") {
					        var data = response.result;
					        if(data == 1)
							{
								document.getElementById("page_title").innerHTML= "Login";
								document.getElementById("forgot").className = "tab-pane";
								document.getElementById("login").className = "tab-pane active";
								document.getElementById("success").innerHTML = 'Your Password has been sent Successfully.';
								document.getElementById("success").style.display = '';
								return false;
							}
							else if(data == 0)
							{
								document.getElementById("errmsg_email").innerHTML = 'Error in Sending Password.';
								document.getElementById("errmsg_email").style.display = '';
								return false;

							}
							else if(data == 3)
							{
								document.getElementById("errmsg_email").innerHTML = 'Sorry ! The Email address you have entered is not found.';
								document.getElementById("errmsg_email").style.display = '';
								return false;
							}
							return false;
					    }
					    else {
					        console.log(response.result);
					    }
					});
					return false;
				}
				return false;
			}

		</script>
		<!--END PAGE LEVEL SCRIPTS -->
	</body>
	<!-- END BODY -->
</html>