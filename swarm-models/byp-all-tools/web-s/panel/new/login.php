<?php 
         ob_start();
	     require_once 'inc/configuration.php';
	     require_once 'inc/init.php';
		 
         if ($user -> LoggedIn()){
		 header('Location: home.php');
		 exit;
	     }

	
	unset($_SESSION['captcha']);
$_SESSION['captcha'] = rand(1, 100);
$x1 = rand(2,10);
$x2 = rand(1,5);
$x = SHA1(($x1 + $x2).$_SESSION['captcha']);

?>

<?php
       
         if (isset($_GET['session'])){
if($_GET['session'] == "rip"){

    echo '
	<div class="alert alert-icon-warning alert-dismissible mb-0" role="alert">
		   <button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="alert-icon icon-part-warning">
			 <i class="fa fa-exclamation-triangle"></i>
			</div>
			<div class="alert-message">
			  <span><strong>Warning:</strong> Session Token Expired!</span>
			</div>
		  </div>
	';

                }
            }

			
            ?>
			 <?php
			

        if (!empty($_REQUEST['actcode'])) {
            $activation = $odb->prepare("SELECT ID, activation FROM users WHERE activation_code = :actcode");
            $activation->execute(array(':actcode' => $_REQUEST['actcode']));
            $result    = $activation->fetch(PDO::FETCH_ASSOC);
            $uid       = $result['ID'];
            $act_value = $result['activation'];
            if (empty($uid)) {
                die(error('We encounter an error. Please try again later.'));
            } else if ($act_value == 0) {
                $updatesql = $odb->prepare("UPDATE users SET activation = ? WHERE ID = ? AND activation_code = ?");
                $updatesql->execute(array(1, $uid, $_REQUEST['actcode']));
                $mesg = 'Your account hase been activated!!. Please continue to login';
				echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: true,
  type: "success",
  title: "Success, Account activated!",
  showConfirmButton: false,
  timer: 4500
})';
  echo ' }, 1000);</script>';
        
            } else {
				echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: true,
  type: "info",
  title: "Your account hase been activated!! already",
  showConfirmButton: false,
  timer: 4500
})';
  echo ' }, 1000);</script>';
               
            }

        }
		
			if(empty($_COOKIE['theme']))
    {
		$SQLC = $odb->prepare("SELECT `theme` FROM `settings` LIMIT 1");
					$SQLC -> execute();
                    $themee = $SQLC -> fetchColumn(0);
            $theme = $themee;
    }
else
{
            $theme = $_COOKIE['theme'];;
    }	
	
	

        ?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
		<title>WebStresser | Login</title>
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

<body>
<script>
    var answer="<?php echo $x; ?>";
</script>

<!-- Start wrapper-->
 <div id="wrapper">
<br> <br> <br>
 <div class="height-100v d-flex align-items-center justify-content-center">
	<div class="card card-authentication1 mb-0">
		<div class="card-body">

		 <div class="card-content p-2">
      
   							<figure class="profile-picture text-center">
								<img src="https://media.discordapp.net/attachments/989138422297923665/1011705598083084518/unknown.png" alt="Joseph Doe" class="rounded-circle" />
							</figure>
		    <div id="alert" style="display:none"></div>
			  <div class="form-group">
			  <label for="exampleInputUsername" class="sr-only">Username</label>
			   <div class="position-relative has-icon-right">
				  <input type="text" id="username" class="form-control input-shadow" placeholder="Enter your Username">
				  <div class="form-control-position">
					  <i class="icon-user"></i>
				  </div>
			   </div>
			  </div>
			  <div class="form-group">
			  <label for="exampleInputPassword" class="sr-only">Password</label>
			   <div class="position-relative has-icon-right">
				  <input type="password" id="password" class="form-control input-shadow"  placeholder="Enter Your Password">
				  <div class="form-control-position">
					  <i class="icon-lock"></i>
				  </div>
			   </div>
			  </div>
<br>
			<div id="hidebtn" >
			 <button type="button" class="btn btn-primary btn-block" id="login" onclick="login()">Sign In</button>
			</div>
			<div  id="loader" style="display:none" >
			 <button type="button" class="btn btn-primary btn-block bg-primary" id="login" onclick="login()">Please Wait <i class="fa fa-spinner fa-spin"></i></button>
			</div>
			
			 
			
		   </div>
		  </div>
		  <div class="card-footer text-center py-3">
		    <p class="text-warning mb-0">Do not have an account? <a href="register.php" style="color=blue;"> Sign Up here</a></p>
		  </div>
	     </div>
	 </div>
    
	
	
	
	</div><!--wrapper-->
	
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css"/>
	
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  <script>


function login()
{
var user=$('#username').val();
var password=$('#password').val();
 let hideme = $('#hideme:checked').val();
 var question=$('#question').val();

            if (hideme === undefined) {
                hideme = 'off';
            }
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
	if (xmlhttp.responseText.search("") !== -1)
	{
				

	setInterval(function(){window.location="home.php"},3000);
    }
    }
  }
xmlhttp.open("POST","ajax/login.php?type=login",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("user=" + user + "&password=" + password + "&hideme=" + hideme + "&question=" + question + "&answer=" + answer);
}
</script>
	<!-- Vendor -->
   		<!-- JQuery min js -->
		<script src="assets/plugins/jquery/jquery.min.js"></script>
		
		<!-- Bootstrap js -->
		<script src="assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		<!-- Moment js -->
		<script src="assets/plugins/moment/moment.js"></script>

		<!-- P-scroll js -->
		<script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- eva-icons js -->
		<script src="assets/js/eva-icons.min.js"></script>

		<!-- Sidebar js -->
		<script src="assets/plugins/side-menu/sidemenu.js"></script>

			    <!-- Internal Select2 js-->
		<script src="assets/plugins/select2/js/select2.min.js"></script>

		<!--Internal  Sweet-Alert js-->
		<script src="assets/plugins/sweet-alert/sweetalert.min.js"></script>
		<script src="assets/plugins/sweet-alert/jquery.sweet-alert.js"></script>

		<!-- Sweet-alert js  -->
		<script src="assets/plugins/sweet-alert/sweetalert.min.js"></script>
		<script src="assets/js/sweet-alert.js"></script>
		<script src="vendor/jquery/jquery.js"></script>
		<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
		<script src="master/style-switcher/style.switcher.js"></script>
		<script src="vendor/popper/umd/popper.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="vendor/common/common.js"></script>
		<script src="vendor/nanoscroller/nanoscroller.js"></script>
		<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
		<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
		<script src="vendor/jquery-ui/jquery-ui.js"></script>
		<script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
		<script src="vendor/jquery-appear/jquery.appear.js"></script>
		<script src="vendor/bootstrapv5-multiselect/js/bootstrap-multiselect.js"></script>
		<script src="vendor/jquery.easy-pie-chart/jquery.easypiechart.js"></script>
		<script src="vendor/flot/jquery.flot.js"></script>
		<script src="vendor/flot.tooltip/jquery.flot.tooltip.js"></script>
		<script src="vendor/flot/jquery.flot.pie.js"></script>
		<script src="vendor/flot/jquery.flot.categories.js"></script>
		<script src="vendor/flot/jquery.flot.resize.js"></script>
		<script src="vendor/jquery-sparkline/jquery.sparkline.js"></script>
		<script src="vendor/raphael/raphael.js"></script>
		<script src="vendor/morris/morris.js"></script>
		<script src="vendor/gauge/gauge.js"></script>
		<script src="vendor/snap.svg/snap.svg.js"></script>
		<script src="vendor/liquid-meter/liquid.meter.js"></script>
		<script src="vendor/jqvmap/jquery.vmap.js"></script>
		<script src="vendor/jqvmap/data/jquery.vmap.sampledata.js"></script>
		<script src="vendor/jqvmap/maps/jquery.vmap.world.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.africa.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.asia.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.australia.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.europe.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.north-america.js"></script>
		<script src="vendor/jqvmap/maps/continents/jquery.vmap.south-america.js"></script>



		<!-- Sweet-alert js  -->
		<script src="plugins/sweet-alert/sweetalert.min.js"></script>
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>

		<!-- Theme Custom -->
		<script src="js/custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

		<!-- Analytics to Track Preview Website -->
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','../../../../www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-42715764-8', 'auto');
		  ga('send', 'pageview');
		</script>
		<!-- Examples -->
		<script src="js/examples/examples.dashboard.js"></script>

	<script defer src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194" integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw==" data-cf-beacon='{"rayId":"73f5bf22683d2e50","version":"2022.8.0","r":1,"token":"03fa3b9eb60b49789931c4694c153f9b","si":100}' crossorigin="anonymous"></script>
</body>
</html>