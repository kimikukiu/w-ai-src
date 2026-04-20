<?php 

session_start();
$page = "Faq";
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


<!-- row -->
					<div class="row">
						<div class="col-xl-12">
							<div class="panel-group2" id="accordion11" role="tablist">
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour1" aria-expanded="false">1. What is "WebStresser"??</a>
									<div id="collapseFour1" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">WebStresser is an online tool, which allows you to do "ddos" attacks and test your product protection!</p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour2" aria-expanded="false">2. What type of payment does "WebStresser" accept?</a>
									<div id="collapseFour2" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">WebStresser takes as type of payment : BTC & LTC & ETH & PAYPAL & PAYSAFECARD (only in France or Belgium).</p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour3" aria-expanded="false">3. Can I get my money back after my payment?</a>
									<div id="collapseFour3" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">Yes, of course, but if the product (the plan) has already been used we are not able to make a refund, and if the plan code has not been used we refund 60% of the total amount!</p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour4" aria-expanded="false">4. Does WebStresser do Layer4 or only Layer7?</a>
									<div id="collapseFour4" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">For the moment we do only "Layer7", but before too many customers I can add "Layer4" !</p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour5" aria-expanded="false">5. How long does the payment take to be made?</a>
									<div id="collapseFour5" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">5-10Min, We just put 1confirmation on the crypto, So it's fast! </p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour6" aria-expanded="false">6. "WebStresser" Bypass le protection captcha ou uam ?</a>
									<div id="collapseFour6" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">Yes the method is available only in the VIP, for the normal methods it can put offline a basic site!</p>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<a class="accordion-toggle panel-heading2 collapsed font-weight-semibold tx-15" data-bs-toggle="collapse" data-bs-parent="#accordion11" href="#collapseFour7" aria-expanded="false">7. Your support team can be contacted on Telegram or Discord? or on the site itself!</a>
									<div id="collapseFour7" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
										<div class="panel-body">
											<p class="text-muted mb-0 tx-14">For the moment we prefer to use our own tool to talk with our customers!</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- row closed -->
					 </div>
				<!-- Container closed -->
			</div>
			<!-- main-content closed -->
			<!-- Footer opened -->
			<div class="main-footer">
				<div class="container-fluid pt-0 ht-100p">
					 Copyright © 2022 <a href="javascript:void(0);" class="text-primary">WebStresser</a>. Designed with <span class="fa fa-heart text-danger"></span> by <a href="javascript:void(0);"> WebStresserCEO </a> All rights reserved
				</div>
			</div>
			<!-- Footer closed -->

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