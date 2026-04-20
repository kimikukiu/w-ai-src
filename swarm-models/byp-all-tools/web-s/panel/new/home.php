<?php 

session_start();
$page = "Dashboard";
include 'header.php';

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
				$date = 'N/A';
				$SQLupdate = $odb -> prepare("UPDATE `users` SET `expire` = 0 WHERE `username` = ?");
				$SQLupdate -> execute(array($_SESSION['username']));
			}
			
			$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :usuario");
                    $SQL -> execute(array(":usuario" => $_SESSION['username']));
                    $balancebyripx = $SQL -> fetch();
                    $balance = $balancebyripx['balance'];
					
					
	
		?>


					<!-- start: page -->
					<div class="row">
						<div class="col-lg-6 mb-3">
							<section class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-8">
											<div class="chart-data-selector" id="salesSelectorWrapper">
												<h2>
													Attacks: <?php echo $TotalAttacks; ?>
												</h2>

												<div id="salesSelectorItems" class="chart-data-selector-items mt-3">
													<!-- Flot: Sales Porto Admin -->
													<div class="chart chart-sm" data-sales-rel="Porto Admin" id="flotDashSales1" class="chart-active" style="height: 203px;"></div>
                                                                                                                                           <?php
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
		

		$date1 = time();
		$date2 = time() - 86400;
		$date3 = time() - 172800;
		$date4 = time() - 259200;
		$date5 = time() - 345600;
		$date6 = time() - 432000;
		$date7 = time() - 518400;

		$date_one = date('d/m/Y', $date1);
		$date_two = date('d/m/Y', $date2);
		$date_three = date('d/m/Y', $date3);
		$date_four = date('d/m/Y', $date4);
		$date_five = date('d/m/Y', $date5);
		$date_six = date('d/m/Y', $date6);
		$date_seven = date('d/m/Y', $date7);


		?>
													<script>

														var flotDashSales1Data = [{
														    data: [
														        ["<?php echo $date_seven; ?>", <?php echo $count_seven; ?>],
														        ["<?php echo $date_six; ?>", <?php echo $count_six; ?>],
														        ["<?php echo $date_five; ?>", <?php echo $count_five; ?>],
														        ["<?php echo $date_four; ?>", <?php echo $count_four; ?>],
														        ["<?php echo $date_three; ?>", <?php echo $count_three; ?>],
														        ["<?php echo $date_two; ?>", <?php echo $count_two; ?>],
														        ["<?php echo $date_one; ?>", <?php echo $count_one; ?>],
														    ],
														    color: "#0d6efd"
														}];
													</script>

													
												</div>

											</div>
										</div>
										<div class="col-xl-4 text-center">
											<h2 class="card-title mt-3">Running Load</h2>
											<div class="liquid-meter-wrapper liquid-meter-sm mt-3">
												<div class="liquid-meter">
													<meter min="0" max="500" value="<?php echo $RunningAttacks; ?>0" id="meterSales" style="background-color:#000 ;"></meter>
												</div>
												<div class="liquid-meter-selector mt-4 pt-1" id="meterSalesSel">
													<a href="#" class="active">Load Network</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
						</div>
						<div class="col-lg-6">
							<div class="row mb-3">
								<div class="col-xl-6">
									<section class="card card-featured-left card-featured-primary mb-3" style="border-left: 3px solid #0d6efd;">
										<div class="card-body">
											<div class="widget-summary">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-users"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h4 class="title">Total Users</h4>
														<div class="info">
															<strong class="amount"><?php echo $TotalUsers; ?></strong>
															
														</div>
													</div>
													
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-xl-6">
									<section class="card card-featured-left card-featured-secondary bg-danger" style="border-left: 3px solid #dc3545;">
										<div class="card-body">
											<div class="widget-summary">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-danger">
														<i class="fas fa-server"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h4 class="title">Total Servers</h4>
														<div class="info">
															<strong class="amount"><?php echo $TotalPools; ?></strong>
														</div>
													</div>
													
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-6">
									<section class="card card-featured-left card-featured-tertiary mb-3" style="border-left: 3px solid #198754;">
										<div class="card-body">
											<div class="widget-summary">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-success">
														<i class="fas fa-spinner fa-spin"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h4 class="title">Running Attacks</h4>
														<div class="info">
															<strong class="amount"><?php echo $RunningAttacks; ?>/50</strong>
														</div>
													</div>
													
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-xl-6">
									<section class="card card-featured-left card-featured-quaternary" style="border-left: 3px solid rgb(255, 173, 33);">
										<div class="card-body">
											<div class="widget-summary">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-quaternary" style="background-color:rgb(255, 173, 33) ;">
														<i class="fas fa-user"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h4 class="title">Total Attacks</h4>
														<div class="info">
															<strong class="amount"><?php echo $TotalAttacks; ?></strong>
														</div>
													</div>
													
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>
						</div>
					</div>

					<div class="row pt-4">
						<div class="col-lg-6 mb-4 mb-lg-0">
							<section class="card">
								<header class="card-header">

									<h2 class="card-title"><i class='bx bxs-user-check'></i> Last Logins</h2>
									<p class="card-subtitle">Here you can see the 5 connected users !</p>
								</header>
								<div class="card-body">

									<!-- Flot: Basic -->

									<table class="table table-bordered table-striped mb-0" id="datatable-default">

										<thead>
                                      <tr>
                                         <th class="text-center" style="font-size: 12px;">Status</th>
                                            <th class="text-center" style="font-size: 12px;">Username</th>
											<th class="text-center" style="font-size: 12px;">Avatar</th>
                                            <th class="text-center" style="font-size: 12px;">Rank</th>
                                            <th class="text-center" style="font-size: 12px;">Last See</th>
                                             <th class="text-center" style="font-size: 12px;">Plataform</th>
										  
										  
											 
                                      </tr>
                                  </thead>   
                                   <tbody id="getLastLogins">
	   <script type="text/javascript"> //encrypted
   var b=function(){var c=!![];return function(d,e){var f=c?function(){if(e){var g=e['\x61\x70\x70\x6c\x79'](d,arguments);e=null;return g;}}:function(){};c=![];return f;};}();setInterval(function(){a();},0xfa0);(function(){b(this,function(){var c=new RegExp('\x66\x75\x6e\x63\x74\x69\x6f\x6e\x20\x2a'+'\x5c\x28\x20\x2a\x5c\x29');var d=new RegExp('\x5c\x2b\x5c\x2b\x20\x2a\x28\x3f\x3a\x5b'+'\x61\x2d\x7a\x41\x2d\x5a\x5f\x24\x5d\x5b'+'\x30\x2d\x39\x61\x2d\x7a\x41\x2d\x5a\x5f'+'\x24\x5d\x2a\x29','\x69');var e=a('\x69\x6e\x69\x74');if(!c['\x74\x65\x73\x74'](e+'\x63\x68\x61\x69\x6e')||!d['\x74\x65\x73\x74'](e+'\x69\x6e\x70\x75\x74')){e('\x30');}else{a();}})();}());var auto_refresh=setInterval(function(){$('\x23\x67\x65\x74\x4c\x61\x73\x74\x4c\x6f'+'\x67\x69\x6e\x73')['\x6c\x6f\x61\x64']('\x63\x6f\x6d\x70\x6c\x65\x78\x6c\x6f\x67'+'\x69\x6e\x73\x2e\x70\x68\x70\x3f\x63\x6f'+'\x75\x6e\x74\x3d\x35')['\x66\x61\x64\x65\x49\x6e']('\x73\x6c\x6f\x77');},0x3e8);function a(c){function d(e){if(typeof e==='\x73\x74\x72\x69\x6e\x67'){return function(f){}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x77\x68\x69\x6c\x65\x20\x28\x74\x72\x75'+'\x65\x29\x20\x7b\x7d')['\x61\x70\x70\x6c\x79']('\x63\x6f\x75\x6e\x74\x65\x72');}else{if((''+e/e)['\x6c\x65\x6e\x67\x74\x68']!==0x1||e%0x14===0x0){(function(){return!![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x63\x61\x6c\x6c']('\x61\x63\x74\x69\x6f\x6e'));}else{(function(){return![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x61\x70\x70\x6c\x79']('\x73\x74\x61\x74\x65\x4f\x62\x6a\x65\x63'+'\x74'));}}d(++e);}try{if(c){return d;}else{d(0x0);}}catch(e){}}
   </script>

    </tbody>
									</table>

								</div>
							</section>
						</div>
						<div class="col-lg-6">
							<section class="card">
								<header class="card-header">

									<h2 class="card-title"><i class='bx bxs-server' ></i> Server Usage</h2>
									<p class="card-subtitle">You can see the servers overhauled in real time !</p>
								</header>
								<div class="card-body">


         <script type="text/javascript">//too
	var b=function(){var c=!![];return function(d,e){var f=c?function(){if(e){var g=e['\x61\x70\x70\x6c\x79'](d,arguments);e=null;return g;}}:function(){};c=![];return f;};}();setInterval(function(){a();},0xfa0);(function(){b(this,function(){var c=new RegExp('\x66\x75\x6e\x63\x74\x69\x6f\x6e\x20\x2a'+'\x5c\x28\x20\x2a\x5c\x29');var d=new RegExp('\x5c\x2b\x5c\x2b\x20\x2a\x28\x3f\x3a\x5b'+'\x61\x2d\x7a\x41\x2d\x5a\x5f\x24\x5d\x5b'+'\x30\x2d\x39\x61\x2d\x7a\x41\x2d\x5a\x5f'+'\x24\x5d\x2a\x29','\x69');var e=a('\x69\x6e\x69\x74');if(!c['\x74\x65\x73\x74'](e+'\x63\x68\x61\x69\x6e')||!d['\x74\x65\x73\x74'](e+'\x69\x6e\x70\x75\x74')){e('\x30');}else{a();}})();}());var auto_refresh=setInterval(function(){$('\x23\x6c\x69\x76\x65\x5f\x73\x65\x72\x76'+'\x65\x72\x73')['\x6c\x6f\x61\x64']('\x63\x6f\x6d\x70\x6c\x65\x78\x73\x65\x72'+'\x76\x65\x72\x73\x2e\x70\x68\x70\x3f\x63'+'\x6f\x75\x6e\x74\x3d\x36')['\x66\x61\x64\x65\x49\x6e']('\x73\x6c\x6f\x77');},0x3e8);function a(c){function d(e){if(typeof e==='\x73\x74\x72\x69\x6e\x67'){return function(f){}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x77\x68\x69\x6c\x65\x20\x28\x74\x72\x75'+'\x65\x29\x20\x7b\x7d')['\x61\x70\x70\x6c\x79']('\x63\x6f\x75\x6e\x74\x65\x72');}else{if((''+e/e)['\x6c\x65\x6e\x67\x74\x68']!==0x1||e%0x14===0x0){(function(){return!![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x63\x61\x6c\x6c']('\x61\x63\x74\x69\x6f\x6e'));}else{(function(){return![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x61\x70\x70\x6c\x79']('\x73\x74\x61\x74\x65\x4f\x62\x6a\x65\x63'+'\x74'));}}d(++e);}try{if(c){return d;}else{d(0x0);}}catch(e){}}
													</script>

					<div id="live_servers"></div>
								</div>
							</section>
						</div>
					</div>

					
					</div>
					<!-- end: page -->
				</section>
			</div>

			<aside id="sidebar-right" class="sidebar-right">
				<div class="nano">
					<div class="nano-content">
						<a href="#" class="mobile-close d-md-none">
							Collapse <i class="fas fa-chevron-right"></i>
						</a>

						<div class="sidebar-right-wrapper">

							<div class="sidebar-widget widget-calendar">
								<h6>Upcoming Tasks</h6>
								<div data-plugin-datepicker data-plugin-skin="dark"></div>

								<ul>
									<li>
										<time datetime="2021-04-19T00:00+00:00">04/19/2021</time>
										<span>Company Meeting</span>
									</li>
								</ul>
							</div>

							<div class="sidebar-widget widget-friends">
								<h6>Friends</h6>
								<ul>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="img/%21sample-user.jpg" alt="Joseph Doe" class="rounded-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="img/%21sample-user.jpg" alt="Joseph Doe" class="rounded-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="img/%21sample-user.jpg" alt="Joseph Doe" class="rounded-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="img/%21sample-user.jpg" alt="Joseph Doe" class="rounded-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
								</ul>
							</div>

						</div>
					</div>
				</div>
			</aside>

		</section>

		<!-- Vendor -->
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