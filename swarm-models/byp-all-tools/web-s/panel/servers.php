<?php 

session_start();
$page = "Servers";
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
					
					
					if ($user -> isAdmin($odb)){ 
				
				$rank =' <span class="badge badge-primary shadow-primary m-1"> Owner</span>';
				 
				}
				
				else if ($user -> isVip($odb)){ 
				
					$rank =' <span class="badge badge-primary shadow-primary m-1"> Advance User</span>';
				}
				else if ($user -> hasMembership($odb)){ 
				
					$rank =' <span class="badge badge-primary shadow-primary m-1"> Paid User</span>';
				}
				else if ($user -> isSupport($odb)){ 
				
					$rank =' <span class="badge badge-primary shadow-primary m-1"> Staff</span>';
				}
				else { 
				
					$rank =' <span class="badge badge-primary shadow-primary m-1"> Visitor</span>';
				}
				
			
		
			if (isset($_GET['wel']))
		{
				
				{
					
					echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: "true",
  type: "info",
  title: "Welcome back '. $_SESSION['username'] .' to Stressing.eu!",
  showConfirmButton: false,
  timer: 4500
  
});';
  echo ' }, 1000);</script>';
  
				}
				
			}
			
			
			
		?>
			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid p-4">
<!--div-->
						<div class="col-xl-12">
							<div class="card">
								<div class="card-header pb-0">
									<div class="d-flex justify-content-between">
										<h4 class="card-title mg-b-0">Servers Status</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-striped mg-b-0 text-md-nowrap">
											<tbody>
<div class="table-responsive"> 
         <script type="text/javascript">//too
	var b=function(){var c=!![];return function(d,e){var f=c?function(){if(e){var g=e['\x61\x70\x70\x6c\x79'](d,arguments);e=null;return g;}}:function(){};c=![];return f;};}();setInterval(function(){a();},0xfa0);(function(){b(this,function(){var c=new RegExp('\x66\x75\x6e\x63\x74\x69\x6f\x6e\x20\x2a'+'\x5c\x28\x20\x2a\x5c\x29');var d=new RegExp('\x5c\x2b\x5c\x2b\x20\x2a\x28\x3f\x3a\x5b'+'\x61\x2d\x7a\x41\x2d\x5a\x5f\x24\x5d\x5b'+'\x30\x2d\x39\x61\x2d\x7a\x41\x2d\x5a\x5f'+'\x24\x5d\x2a\x29','\x69');var e=a('\x69\x6e\x69\x74');if(!c['\x74\x65\x73\x74'](e+'\x63\x68\x61\x69\x6e')||!d['\x74\x65\x73\x74'](e+'\x69\x6e\x70\x75\x74')){e('\x30');}else{a();}})();}());var auto_refresh=setInterval(function(){$('\x23\x6c\x69\x76\x65\x5f\x73\x65\x72\x76'+'\x65\x72\x73')['\x6c\x6f\x61\x64']('\x63\x6f\x6d\x70\x6c\x65\x78\x73\x65\x72'+'\x76\x65\x72\x73\x2e\x70\x68\x70\x3f\x63'+'\x6f\x75\x6e\x74\x3d\x36')['\x66\x61\x64\x65\x49\x6e']('\x73\x6c\x6f\x77');},0x3e8);function a(c){function d(e){if(typeof e==='\x73\x74\x72\x69\x6e\x67'){return function(f){}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x77\x68\x69\x6c\x65\x20\x28\x74\x72\x75'+'\x65\x29\x20\x7b\x7d')['\x61\x70\x70\x6c\x79']('\x63\x6f\x75\x6e\x74\x65\x72');}else{if((''+e/e)['\x6c\x65\x6e\x67\x74\x68']!==0x1||e%0x14===0x0){(function(){return!![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x63\x61\x6c\x6c']('\x61\x63\x74\x69\x6f\x6e'));}else{(function(){return![];}['\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f'+'\x72']('\x64\x65\x62\x75'+'\x67\x67\x65\x72')['\x61\x70\x70\x6c\x79']('\x73\x74\x61\x74\x65\x4f\x62\x6a\x65\x63'+'\x74'));}}d(++e);}try{if(c){return d;}else{d(0x0);}}catch(e){}}
													</script>

					<div id="live_servers"></div>											</tbody>
										</table>
									</div>
									<!-- bd -->
								</div>
								<!-- bd -->
							</div>
							<!-- bd -->
						</div>
						<!--/div-->
 </div>
				<!-- Container closed -->
			</div>
			<!-- main-content closed -->
		
			<!-- Footer opened -->
			<div class="main-footer">
				<div class="container-fluid pt-0 ht-100p">
					 Copyright © 2022 <a href="javascript:void(0);" class="text-primary">Webstresser</a>. Designed with <span class="fa fa-heart text-danger"></span> by <a href="javascript:void(0);"> WebstresserCEO </a> All rights reserved
				</div>
			</div>
			<!-- Footer closed -->
		</div>
		<!-- End Page -->

				<!-- Back-to-top -->
		<a href="#top" id="back-to-top"><i class="las la-arrow-up"></i></a>

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
