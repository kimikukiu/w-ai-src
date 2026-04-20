<?php 
         ob_start();
	     require_once 'inc/configuration.php';
	     require_once 'inc/init.php';
		 
         if ($user -> LoggedIn()){
		 header('Location: home.php');
		 exit;
	     }
		 

$publickey = '6LfcbIEhAAAAAFhmeojXecHcPoVncW54QgutlU-2';

?>

<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
		<title>WebStresser | Register</title>
		<meta name="keywords" content="booter, stresser, ddos, layer4, layer7, hack, down, hit, offline, webstresser, webstresser.cc, stressthem.to, stressthem" />
		<meta name="description" content="WebStresser - Best & Powerful StressTesting tool">
		<meta name="author" content="WebStresser.cc">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="vendor/animate/animate.compat.css">
		<link rel="stylesheet" href="vendor/font-awesome/css/all.min.css" />
		<link rel="stylesheet" href="vendor/boxicons/css/boxicons.min.css" />
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

		<!-- Specific Page Vendor CSS -->
		<link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.css" />
		<link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.theme.css" />
		<link rel="stylesheet" href="vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />
		<link rel="stylesheet" href="vendor/morris/morris.css" />
<script src='https://www.google.com/recaptcha/api.js'></script>
		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Head Libs -->
		<script src="vendor/modernizr/modernizr.js"></script>

		<script src="master/style-switcher/style.switcher.localstorage.js"></script>
  
<style>
  html, body {
    background: #1d2127;
    width: 100%;
}
.card-body {
    background: #2e353e;
    box-shadow: 0 1px 1pxrgba(0,0,0,.05);
    border-radius: 5px;
}
.form-control:not(.form-control-sm):not(.form-control-lg) {
    font-size: 13.6px;
    font-size: .85rem;
    line-height: 1.85;
    min-height: 38.4px;
    min-height: 2.4rem;
    background-color: #1d2127;
}
.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #1d2127;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.form-group+.form-group {
    border-top: 1px solid #1d212700;
    padding-top: 16px;
    padding-top: 1rem;
}
.btn-primary {
    background-color: #2b87fd;
    border-color: #2b87fd #2b87fd #2b87fd;
    color: #fff;
    width: 100%;
}
.card-footer {
    border-radius: 0 0 5px 5px;
    margin-top: -5px;
    background: #2e353e;
    color: #fff;
}
.rounded-circle {
    border-radius: 50%;
    height: 100px;
}

.card-body {
    background: #2e353e;
    box-shadow: 0 1px 1pxrgba(0,0,0,.05);
    border-radius: 5px;
    width: 450px;
}
.form-control:not(.form-control-sm):not(.form-control-lg) {
    font-size: 13.6px;
    font-size: .85rem;
    line-height: 1.85;
    min-height: 38.4px;
    min-height: 2.4rem;
    background-color: #1d2127;
    color: #fff;
}
  </style>
</head>
  
</head>
<body>



<!-- Start wrapper-->
 <div id="wrapper">
<br> <br> <br>
 <div class="height-100v d-flex align-items-center justify-content-center">
	<div class="card card-authentication1 mb-0">
		<div class="card-body">
		 <div class="card-content p-2">
<div class="card-title text-uppercase text-center py-3"><img id="registerimage" style="display:none" /></div>
<form class="form-horizontal form-material" id="regsiterform">
<div id="alert" style="display:none"></div>
<div class="form-group">
<div class="form-material floating">

<input type="text" id="username" class="form-control" placeholder="Username">
</div>
</div>
<div class="form-group">
<div class="form-material floating">

<input type="text" id="email" class="form-control" placeholder="Email">
</div>
</div>
<div class="form-group">
<div class="form-material floating">

 <input type="password" id="password" class="form-control" placeholder="Password">
</div>
</div>
<div class="form-group">
<div class="form-material floating">

 <input type="password" id="rpassword" class="form-control" placeholder="Verify Password">
</div>
</div>
<div class="form-group">
	<center><div class="g-recaptcha" id="captcha" data-sitekey="<?php echo $publickey; ?>"></div></center>
</div>


</form>
<br>
<div class="form-group">
<div id="hidebtn" >
<button class="btn btn-primary btn-block" id="register" onclick="register()">
<i class="si si-login mr-10"></i> Create account
</button>
</div>
	<div class="col-12 mb-10">
<div id="loader" style="display:none">
<button class="btn btn-primary btn-block" id="login2" onclick="register2()">
<i class="si si-login mr-10"></i> please wait...<i class="fa fa-spinner fa-spin "></i>
</button>
</div>
</div>
</div>
</div>
</div>
<div class="card-footer text-center py-3">
<p class="text-warning mb-0">Already have an account? <a href="login.php"> Sign In here</a></p>
</div>
</div>

	
</div><!--wrapper-->
	
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
	
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  
  <script>
function register()
{
var username=$('#username').val();
var email=$('#email').val();
var password=$('#password').val();
var rpassword=$('#rpassword').val();
var captcha = $('#g-recaptcha-response').val();
document.getElementById("alert").style.display="none";
document.getElementById("loader").style.display="inline";
document.getElementById("hidebtn").style.display="none";
var xmlhttp;
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("alert").innerHTML=xmlhttp.responseText;
  document.getElementById("loader").style.display="none";
  document.getElementById("alert").style.display="inline";
		document.getElementById("hidebtn").style.display="inline";
  if (xmlhttp.responseText.search("") != -1)
  {
	  				

    }
    }
  }
xmlhttp.open("POST","ajax/login2.php?type=register",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("username=" + username + "&email=" + email + "&password=" + password + "&rpassword=" + rpassword + "&captcha=" + grecaptcha.getResponse());
}
</script>
<script src="assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
        <link rel="stylesheet" href="assets/node_modules/sweetalert2/dist/sweetalert2.min.css">
   <script src="assets/toastr/toastr.min.js"></script>
		 
  
</body>
</html>
