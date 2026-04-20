<?php
require_once 'inc/configuration.php';
require_once 'inc/init.php';

if (!$user -> LoggedIn()){
    header('Location: login.php');
    exit;
}

function hasBNAccess($db){
	$stmt = $db->prepare("SELECT botnet FROM users WHERE username=:login");
	$stmt->bindParam("login", $_SESSION['username'], PDO::PARAM_STR);
	$stmt->execute();
	$value = $stmt->fetchColumn(0);
	return $value;
}
$lastactive = $odb -> prepare("UPDATE `users` SET activity=UNIX_TIMESTAMP() WHERE username=:username");
$lastactive -> execute(array(':username' => $_SESSION['username']));

 $onedayago = time() - 86400;

 $twodaysago = time() - 172800;
 $twodaysago_after = $twodaysago + 86400;

 $threedaysago = time() - 259200;
 $threedaysago_after = $threedaysago + 86400;

 $fourdaysago = time() - 345600;
 $fourdaysago_after = $fourdaysago + 86400;

 $fivedaysago = time() - 432000;
 $fivedaysago_after = $fivedaysago + 86400;

 $sixdaysago = time() - 518400;
 $sixdaysago_after = $sixdaysago + 86400;

 $sevendaysago = time() - 604800;
 $sevendaysago_after = $sevendaysago + 86400;
 
 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` > :date");
 $SQL -> execute(array(":date" => $onedayago));
 $count_one = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $twodaysago, ":after" => $twodaysago_after));
 $count_two = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $threedaysago, ":after" => $threedaysago_after));
 $count_three = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $fourdaysago, ":after" => $fourdaysago_after));
 $count_four = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $fivedaysago, ":after" => $fivedaysago_after));
 $count_five = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $sixdaysago, ":after" => $sixdaysago_after));
 $count_six = $SQL->fetchColumn(0);

 $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
 $SQL -> execute(array(":before" => $sevendaysago, ":after" => $sevendaysago_after));
 $count_seven = $SQL->fetchColumn(0);
 
 $date_one = date('d/m/Y', $onedayago);
 $date_two = date('d/m/Y', $twodaysago);
 $date_three = date('d/m/Y', $threedaysago);
 $date_four = date('d/m/Y', $fourdaysago);
 $date_five = date('d/m/Y', $fivedaysago);
 $date_six = date('d/m/Y', $sixdaysago);
 $date_seven = date('d/m/Y', $sevendaysago);

     $plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
     $plansql -> execute(array(":id" => $_SESSION['ID']));
     $row = $plansql -> fetch(); 
     $date = date("m-d-Y, h:i:s a", $row['expire']);
     if (!$user->hasMembership($odb)){
         $row['mbt'] = 0;
         $row['concurrents'] = 0;
         $row['name'] = 'No membership';
         $date = '0-0-0';
     }
     
     $SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :usuario");
             $SQL -> execute(array(":usuario" => $_SESSION['username']));
             $balancebyripx = $SQL -> fetch();
             $balance = $balancebyripx['balance'];
     
 
?>


<!doctype html>
<html class="fixed dark" data-style-switcher-options="{'backgroundColor': 'dark'}">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title><?php echo $sitename; ?> | <?php echo $page; ?></title>
		<meta name="keywords" content="booter, stresser, ddos, layer4, layer7, hack, down, hit, offline, webstresser, webstresser.cc, stressthem.to, stressthem" />
		<meta name="description" content="WebStresser - Best & Powerful StressTesting tool">
		<meta name="author" content="WebStresser.cc">
<link rel="icon" href="https://media.discordapp.net/attachments/989138422297923665/1011705598083084518/unknown.png" type="image/x-icon"/>
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

	</head>
	<body>
		<section class="body">

			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="#" class="logo">
						<img src="https://media.discordapp.net/attachments/989138422297923665/1012392135913394357/Capture_decran_2022-08-25_174033_copie.png" width="220" height="35" alt="WebStresser Logo" />
					</a>

					<div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fas fa-bars" aria-label="Toggle sidebar"></i>
					</div>

				</div>

				<!-- start: search & user box -->
				<div class="header-right">
					<span class="separator"></span>

					<ul class="notifications">
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-bs-toggle="dropdown">
								<i class="bx bx-list-ol"></i>
								<span class="badge">3</span>
							</a>

							<div class="dropdown-menu notification-menu large ">
								<div class="notification-title bg-primary">
									<span class="float-end badge bg-danger">3</span>
									Check Network Stats
								</div>

								<div class="content">
									<ul>
										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Proxy List Used:</span>
												<span class="message float-end text-dark">60%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Apm List Used:</span>
												<span class="message float-end text-dark">98%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100" style="width: 98%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Power Used:</span>
												<span class="message float-end text-dark">33%</span>
											</p>
											<div class="progress progress-xs light mb-1">
												<div class="progress-bar bg-primary" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;"></div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</li>
						<li>
							<a href="https://t.me/webstressercc" class="dropdown-toggle notification-icon">
								<i class='bx bxl-telegram'></i>
								
							</a>
						</li>
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-bs-toggle="dropdown">
								<i class="bx bx-bell"></i>
								<span class="badge">1</span>
							</a>

							<div class="dropdown-menu notification-menu">
								<div class="notification-title bg-primary">
									<span class="float-end badge badge-default">1</span>
									Alerts
								</div>

								<div class="content">
									<ul>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="bx bxs-user-circle bg-primary text-light"></i>
												</div>
												<span class="title">Welcome message !</span>
												<span class="message">Welcome: <?php echo $_SESSION['username']; ?> !</span>
											</a>
										</li>
										
									</ul>
								</div>
							</div>
						</li>
					</ul>

					<span class="separator"></span>

					<div id="userbox" class="userbox">
						<a href="#" data-bs-toggle="dropdown">
							<figure class="profile-picture">
								<img src="https://media.discordapp.net/attachments/989138422297923665/1011705598083084518/unknown.png" alt="Joseph Doe" class="rounded-circle" />
							</figure>
							<div class="profile-info">
								<span class="name"><?php echo $_SESSION['username']; ?></span>
								<span class="role">Plan: <?php echo $row['name']; ?></span>
							</div>

							<i class="fa custom-caret"></i>
						</a>

						<div class="dropdown-menu">
							<ul class="list-unstyled mb-2">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="#"><i class="bx bx-user-circle"></i> My Profile</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="logout.php"><i class="bx bx-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">

				    <div class="sidebar-header">
				        <div class="sidebar-title">
				            Quick Navigation
				        </div>
				        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
				            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
				        </div>
				    </div>

				    <div class="nano">
				        <div class="nano-content">
				            <nav id="menu" class="nav-main" role="navigation">

				                <ul class="nav nav-main">
				                    <li class="<?php echo ($page === "Dashboard" ? 'nav-active' : ''); ?>">
				                        <a class="nav-link" href="home.php">
				                            <i class="fa fa-home" aria-hidden="true"></i>
				                            <span>Dashboard</span>
				                        </a>                        
				                    </li>
				                    	<li class="<?php echo ($page === "StresserHub" ? 'nav-active' : ''); ?>">
				                        <a href="stresshub.php">
				                            <i class="fa fa-globe"></i>
				                            <span>Stresser Panel</span>
				                        </a>                        
				                    </li>
                                                                                <li class="<?php echo ($page === "Api" ? 'nav-active' : ''); ?>">
				                        <a href="apidev.php">
                                     <span class="float-end badge bg-primary">New Features </span>
				                            <i class="fa fa-link"></i>
				                            <span>Api Developer</span>
				                        </a>                        
				                    </li>
                                                                                <li>
				                        <a href="#☻">
				                            <i class="fa fa-server"></i>
				                            <span>Power Proofs</span>
				                        </a>                        
				                    </li>
				              				    <div class="sidebar-header">
				        <div class="sidebar-title">
				            Payments & Packages
				        </div>
				    </div>
                                                                                                          <li class="<?php echo ($page === "Purchase" ? 'nav-active' : ''); ?>">
				                        <a href="purchase.php">
                                                                     <span class="float-end badge bg-primary">Cheap -10%</span>
				                            <i class="fa fa-cart-plus"></i>
				                            <span>Purchase</span>
				                        </a>                        
				                    </li>
                                                                                <li class="<?php echo ($page === "Add-Balance" ? 'nav-active' : ''); ?>">
				                        <a href="add.php">
                                                                     
				                            <i class="fa fa-plus"></i>
				                            <span>Add Balance</span>
				                        </a>                        
				                    </li>
                                                                                <li>
				                        <a href="#☻">
                                                                     
				                            <i class="fa fa-bug"></i>
				                            <span>Faq Page</span>
				                        </a>                        
				                    </li>

				              				    
				                </ul>
				            </nav>

				        

				    </div>

				</aside>
				<!-- end: sidebar -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2 style="border-bottom: 4px solid #2f3139;">WebStresser.cc</h2>

						<div class="right-wrapper text-end">
							<ol class="breadcrumbs">

								<li><span>Panel</span></li>

								<li><span><?php echo $page; ?></span></li>

							</ol>

							<a class="sidebar-right-toggle"><i class="fas fa-chevron-left"></i></a>
						</div>
					</header>