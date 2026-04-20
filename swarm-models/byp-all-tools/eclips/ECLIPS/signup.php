<?php
require 'Class/database.php';
require 'Class/init.php';
session_start();
unset($_SESSION['captcha']);
$_SESSION['captcha'] = rand(1, 100);
$x1 = rand(2,10);
$x2 = rand(1,10);
$x = SHA1(($x1 + $x2).$_SESSION['captcha']); 
?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<title><?php echo htmlspecialchars($sitename); ?></title>
		<meta charset="utf-8" />
		<link rel="shortcut icon" href="assets/ProfileHeader.png" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--begin::Theme mode setup on page load-->
   <!--end::Theme mode setup on page load-->
	<!--end::Head-->
	<!--begin::Body-->
	<body data-theme="dark" id="kt_body" class="app-blank app-blank">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
					<!--begin::Logo-->
					<a href="landing.html" class="mb-12">
					<img alt="Logo" src="assets/ProfileHeader.png" class="h-75px" />
					</a>
					<!--end::Logo-->
					<!--begin::Wrapper-->
					<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
						<!--begin::Form-->
							<!--begin::Heading-->
							<div class="text-center mb-10">
								<!--begin::Title-->
								<h1 class="text-dark mb-3">Sign In to <?php echo htmlspecialchars($sitename); ?></h1>
								<!--end::Title-->
								<!--begin::Link-->
								<div class="text-gray-400 fw-bold fs-4">Already have an account?
								<a href="login.php" class="link-primary fw-bolder">Sign In</a></div>
								<!--end::Link-->
							</div>
							<!--begin::Heading-->
                            <div id="registerdiv" style="display:none"></div>
							<!--begin::Input group-->
                            <div class="fv-row mb-3">
									<!--begin::Email-->
									<input type="text" id="username" placeholder="Username" name="Username" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Email-->
								</div>
								<!--end::Input group=-->
								<div class="fv-row mb-3">
									<!--begin::Password-->
									<input type="password" id="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Password-->
								</div>
                                <div class="fv-row mb-3">
									<!--begin::Password-->
									<input type="password" id="rpassword" placeholder="Confirm Password" name="password" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Password-->
								</div>
                                <div class="fv-row mb-3">
									<!--begin::Email-->
									<input type="email" id="email" placeholder="Email" name="Email" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Email-->
								</div>
                                <div class="fv-row mb-3">
									<!--begin::Email-->
									<input type="text" id="scode" placeholder="1234" name="text" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Email-->
								</div>
                                <div class="fv-row mb-3">
									<!--begin::Email-->
									<input type="text" id="question" placeholder="<?php echo ''.$x1.'+'.$x2.'?'; ?>" name="text" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Email-->
								</div>
							<!--end::Input group-->
							<!--begin::Actions-->
							<div class="text-center">
								<!--begin::Submit button-->
								<button onclick="register()" type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
									<span class="indicator-label">Sign up</span>
									<span class="indicator-progress">Please wait...
									<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
								</button>
								<!--end::Submit button-->
								
							<!--end::Actions-->
						<!--end::Form-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
				<!--begin::Footer-->
			</div>
			<!--end::Authentication - Sign-Up-->
		</div>
		<!--end::Root-->
		<!--end::Main-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="assets/js/custom/authentication/sign-in/general.js"></script>
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	</body>
	<!--end::Body-->
	
    <script>
		function register()
		{
		var username=$('#username').val();
		var password=$('#password').val();
		var rpassword=$('#rpassword').val();
		var email=$('#email').val();
		var scode=$('#scode').val();
        var question=$('#question').val();
		var answer="<?php echo $x; ?>";
		document.getElementById("registerdiv").style.display="none";
		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("registerdiv").innerHTML=xmlhttp.responseText;
			document.getElementById("registerdiv").style.display="inline";
			if (xmlhttp.responseText.search("Redirecting") != -1)
			{
			setInterval(function(){window.location="cloud-login.php"},3000);
			}
			}
		  }
		xmlhttp.open("POST","Modules/login.php?type=register",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("username=" + username + "&password=" + password + "&rpassword=" + rpassword + "&scode=" + scode + "&email=" + email + "&question=" + question + "&answer=" + answer);
		}
		</script> 
</html>