<?php 
         ob_start();
	     require_once 'complex/configuration.php';
	     require_once 'complex/init.php';
		 
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
			
			// activate the account
//if ($type && preg_match('/activate/i', $type) && !empty($_REQUEST['actcode'])) {
        if (!empty($_REQUEST['actcode'])) {
            $activation = $odb->prepare("SELECT ID, activation FROM users WHERE activation_code = :actcode");
            $activation->execute(array(':actcode' => $_REQUEST['actcode']));
            $result    = $activation->fetch(PDO::FETCH_ASSOC);
            $uid       = $result['ID'];
            $act_value = $result['activation'];
            if (empty($uid)) {
        // the details needs to be logged
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
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Best Webstresser 2022">
		<meta name="Author" content="WebStresser">
		<meta name="Keywords" content="BOOTER, STRESSER, DDOS "/>

		<!-- Title -->
		<title> WebStresser | Login </title>

        		<!-- Favicon -->
		<link rel="icon" href="assets/img/brand/favicon.png" type="image/x-icon"/>
        <script src='https://www.google.com/recaptcha/api.js'></script>
		<!-- Icons css -->
		<link href="assets/css/icons.css" rel="stylesheet">

		<!--  bootstrap css-->
		<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" id="style" />

		<!--- Style css --->
		<link href="assets/css/style.css" rel="stylesheet">
		<link href="assets/css/style-dark.css" rel="stylesheet">
		<link href="assets/css/style-transparent.css" rel="stylesheet">

		<!---Skinmodes css-->
		<link href="assets/css/skin-modes.css" rel="stylesheet" />

		<!--- Animations css-->
		<link href="assets/css/animate.css" rel="stylesheet">

		
    
		<!-- INTERNAL Switcher css -->
		<link href="assets/switcher/css/switcher.css" rel="stylesheet"/>
		<link href="assets/switcher/demo.css" rel="stylesheet"/>
	</head>
	<body class="ltr error-page1 bg-primary">
	<script>
    var answer="<?php echo $x; ?>";
</script>
        		<!-- Switcher -->
		<div class="switcher-wrapper">
			<div class="demo_changer ">
				<div class="form_holder sidebar-right1">
					<div class="row">
						<div class="predefined_styles">
							<div class="swichermainleft text-center">
								<h4>LTR AND RTL VERSIONS</h4>
								<div class="skin-body">
									<div class="switch_section">
										<div class="switch-toggle d-flex mt-2">
											<span class="me-auto">LTR</span>
											<p class="onoffswitch2 my-0"><input type="radio" name="onoffswitch25" id="myonoffswitch54" class="onoffswitch2-checkbox" checked>
												<label for="myonoffswitch54" class="onoffswitch2-label"></label>
											</p>
										</div>
										<div class="switch-toggle d-flex mt-2">
											<span class="me-auto">RTL</span>
											<p class="onoffswitch2 my-0"><input type="radio" name="onoffswitch25" id="myonoffswitch55" class="onoffswitch2-checkbox">
												<label for="myonoffswitch55" class="onoffswitch2-label"></label>
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="swichermainleft">
								<h4>Light Theme Style</h4>
								<div class="skin-body">
									<div class="switch_section">
										<div class="switch-toggle d-flex">
											<span class="me-auto">Light Theme</span>
											<p class="onoffswitch2 my-0"><input type="radio" name="onoffswitch1" id="myonoffswitch1" class="onoffswitch2-checkbox" checked>
												<label for="myonoffswitch1" class="onoffswitch2-label"></label>
											</p>
										</div>
										<div class="switch-toggle d-flex mt-2">
											<span class="me-auto">Dark Theme</span>
											<p class="onoffswitch2 my-0"><input type="radio" name="onoffswitch1" id="myonoffswitch2" class="onoffswitch2-checkbox">
												<label for="myonoffswitch2" class="onoffswitch2-label"></label>
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="swichermainleft">
								<h4>Dark Theme Style</h4>
								<div class="skin-body">
									<div class="switch_section">
										<div class="switch-toggle d-flex">
											<span class="me-auto">Light Primary</span>
											<div class="">
												<input class="wd-25 ht-25 input-color-picker color-primary-light" value="#38cab3" id="colorID" oninput="changePrimaryColor()" type="color" data-id="bg-color" data-id1="bg-hover" data-id2="bg-border"  data-id7="transparentcolor"  name="lightPrimary">
											</div>
										</div>
										<div class="switch-toggle d-flex mt-2">
											<span class="me-auto">Dark Primary</span>
											<div class="">
												<input class="wd-25 ht-25 input-dark-color-picker color-primary-dark" value="#38cab3" id="darkPrimaryColorID" oninput="darkPrimaryColor()" type="color" data-id="bg-color" data-id1="bg-hover" data-id2="bg-border" data-id3="primary"  data-id8="transparentcolor" name="darkPrimary">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="swichermainleft">
								<h4>Reset All Styles</h4>
								<div class="skin-body">
									<div class="switch_section my-4">
										<button class="btn btn-danger btn-block"
											onclick="localStorage.clear();
											document.querySelector('html').style = '';
											names() ;
											resetData()"
											type="button">Reset All
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Switcher -->
    		<!-- Loader -->
		<div id="global-loader">
			<img src="assets/img/loader.svg" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->

        <div class="square-box">
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>
        <div class="page" >
            <div class="row">
                <div class="d-flex">
                    <a class="demo-icon new nav-link" href="javascript:void(0);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs fa-spin" width="24" height="24" viewBox="0 0 24 24"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"></path><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"></path></svg>
                    </a>
                </div>
            </div>

            

     <!-- /Loader -->
		<div>
			<div class="page">	
				<div class="page-single">
						<div class="container">
							<div class="row">
								<div class="col-xl-5 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main py-4 justify-content-center mx-auto">
									<div class="card-sigin">
										<!-- Demo content-->
										<div class="main-card-signin d-md-flex">
											<div class="wd-100p">
												<div class="d-flex mx-auto"> <a href="login.php" class="mx-auto d-flex"><img src="assets/img/brand/favicon.png" class="sign-favicon ht-40 mx-auto" alt="logo"><h1 class="main-logo1 ms-1 me-0 my-auto tx-28 text-dark ms-2">WEB<span>STRESSER</span>.CC</h1></a></div>
												<div class="main-card-signin d-md-flex bg-white">
													<div class="p-4 wd-100p">
														<div class="main-signin-header">
														<div id="alert" style="display:none"></div>
															
																<div class="form-group">
																	<input type="text" id="username" class="form-control" placeholder="Enter your Username">
																</div>
																<div class="form-group">
																	<input type="password" id="password" class="form-control" placeholder="Enter your Password">
																</div>
																													


<div id="hidebtn" >
			 <button type="button" class="btn btn-primary btn-block" id="login" onclick="login()">Sign In</button>
			</div>
			<div  id="loader" style="display:none" >
			 <button type="button" class="btn btn-primary btn-block" id="login" onclick="login()">Please Wait <i class="fa fa-spinner fa-spin"></i></button>
			</div>
														
            <div class="main-signin-footer text-center mt-3">
													
													<p>Don't have an account? <a href="register.php">Create an Account</a></p>
												</div>
														
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
    
        </div>
		<script>


function login()
{
swal({
  position: 'top-end',
  toast: true,
  type: 'info',
  title: 'Cheking Login Parameters..',
  showConfirmButton: false,
  timer: 2500
  
});
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
	if (xmlhttp.responseText.search("Login Successful") !== -1)
	{
				swal({
  position: 'top-end',
  toast: true,
  type: 'success',
  title: 'Signed in successfully!',
  showConfirmButton: false,
  timer: 2500
  
});

	setInterval(function(){window.location="home.php"},3000);
    }
    }
  }
xmlhttp.open("POST","complexx/login.php?type=login",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("user=" + user + "&password=" + password + "&hideme=" + hideme + "&question=" + question + "&answer=" + answer);
}
</script>

        		<!-- JQuery min js -->
		<script src="assets/plugins/jquery/jquery.min.js"></script>
        <script src="assets/js/jquery.min.js"></script>
		<!-- Bootstrap js -->
		<script src="assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		<!-- Moment js -->
		<script src="assets/plugins/moment/moment.js"></script>

		<!-- eva-icons js -->
		<script src="assets/js/eva-icons.min.js"></script>

		<!-- generate-otp js -->
		<script src="assets/js/generate-otp.js"></script>
		
		<!--Internal  Perfect-scrollbar js -->
		<script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

		
    
		<!-- Theme Color js -->
		<script src="assets/js/themecolor.js"></script>

		<!-- custom js -->
		<script src="assets/js/custom.js"></script>

		<!-- Switcher js -->
		<script src="assets/switcher/js/switcher.js"></script>

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

    
		<!-- Sticky js -->
		<script src="assets/js/sticky.js"></script>

		<!-- Right-sidebar js -->
		<script src="assets/plugins/sidebar/sidebar.js"></script>
		<script src="assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- Theme Color js -->
		<script src="assets/js/themecolor.js"></script>

		<!-- custom js -->
		<script src="assets/js/custom.js"></script>

		<!-- Switcher js -->
		<script src="assets/switcher/js/switcher.js"></script>
    </body>
</html>