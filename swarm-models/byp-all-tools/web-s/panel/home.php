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
					<div class="row row-sm">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="card primary-custom-card1">
								<div class="card-body">
									<div class="row">
										<div class="col-xl-5 col-lg-6 col-md-12 col-sm-12">
											<div class="prime-card"><img class="img-fluid" src="assets/img/png/dashboard8.png" alt=""></div>
										</div>
										<div class="col-xl-7 col-lg-6 col-md-12 col-sm-12">
											<div class="text-justified align-items-center">
												<h2 class="text-dark font-weight-semibold mb-3 mt-2">Hi, Welcome Back <span class="text-primary"><?php echo $_SESSION['username']; ?>!</span></h2>
												<p class="text-dark tx-17 mb-2 lh-3"> Hi there, Before our website was with a free version and we had to stop due to too many attacks, And from now on it is only paying, We also added Layer4 with a very good power and very reliable.</p>
												<p class="font-weight-semibold tx-12 mb-4">All payments are automatic and start at $25 </p>
												
<button class="btn btn-primary mb-3 shadow p-2"><a href="https://t.me/webstressercc"><i class="fa fa-paper-plane"></i> Telegram Server</a></button>
											</div>
										</div>
									</div>
								</div>
							</div></div>

						<div class="col-sm-6 col-lg-6 col-xl-3">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col">
											<div class="">Total Attacks</div>
											<div class="h3 mt-2 mb-2"><b><?php echo $TotalAttacks; ?></b><span class="text-success tx-13 ms-2">(+25%)</span></div>
										</div>
										<div class="col-auto align-self-center ">
											<div class="feature mt-0 mb-0">
												<i class="fa fa-meteor" aria-hidden="true" style="font-size: 50px;"></i>											</div>
										</div>
									</div>
																	</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-6 col-xl-3">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col">
											<div class="">Total Server</div>
											<div class="h3 mt-2 mb-2"><b><?php echo $TotalPools; ?></b><span class="text-success tx-13 ms-2">(+15%)</span></div>
										</div>
										<div class="col-auto align-self-center ">
											<div class="feature mt-0 mb-0">
												<i class="fa fa-server" aria-hidden="true" style="font-size: 50px;"></i>
											</div>
										</div>
									</div>
																	</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-6 col-xl-3">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col">
											<div class="">Running Attacks</div>
											<div class="h3 mt-2 mb-2"><b><?php echo $RunningAttacks; ?> / 25</b><span class="text-success tx-13 ms-2">(+08%)</span></div>
										</div>
										<div class="col-auto align-self-center ">
											<div class="feature mt-0 mb-0">
												<i class="fa fa-spinner fa-spin" aria-hidden="true" style="font-size: 50px;"></i>
											</div>
										</div>
									</div>
																	</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-6 col-xl-3">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col">
											<div class="">Total Users</div>
											<div class="h3 mt-2 mb-2"><b><?php echo $TotalUsers; ?></b><span class="text-success tx-13 ms-2">(+35%)</span></div>
										</div>
										<div class="col-auto align-self-center ">
											<div class="feature mt-0 mb-0">
												<i class="fa fa-users" aria-hidden="true" style="font-size: 50px;"></i>
											</div>
										</div>
									</div>
																	</div>
							</div>
						</div>
						<div class="col-xl-4 col-lg-12 col-xl-9">
							<div class="card">
								<div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
									<div>
										<h3 class="card-title">Last Updates</h3>
									</div>
								</div>
								<div class="card-body">
									<div class="latest-timeline">
										<ul class="timeline mb-0">
										   <li>
												<div class="featured_icon danger">
													<i class="fas fa-circle"></i>
												</div>
											</li>
											<li class="mt-0 pb-3">
												<div><small class="fs-13 text-muted float-end">23/08/2022</small></div>
												<a class="font-weight-bold text-dark fs-16">WebStresser CEO</a>
												<p class="text-muted mt-0">As you already knew we are very new, But we are very powerful and with a service of 99% online without 0 worries, We have added +9 server with L4 and L7, With prices that change from our competition, We offer api where you can make your own panel and resell our power without the users of your panel know that your power comes from us, Please make us a support on the website we are not on telegram to talk or make a support.
<br><br>
Thank you and see you very soon.</p>
											</li>
											<li>
												<div class="featured_icon danger">
													<i class="fas fa-circle"></i>
												</div>
											</li>
											<li class="mt-0 pb-3">
												<div><small class="fs-13 text-muted float-end">23/08/2022</small></div>
												<a class="font-weight-bold text-dark fs-16">WebStresser CEO</a>
												<p class="text-muted mt-0">Hello everyone , So that you can make attack without paying we before put in place a free version or you can test your protection without paying any fee, We will soon open our free version with a power of 1gbps and with an attack time of 60 seconds , Of course it false not hoped to down all your test but some yes c for that it is called a free version .
<br><br>
See you soon on our website by WebStresser.</p>
											</li>
					
																						<li>
												<div class="featured_icon danger">
													<i class="fas fa-circle"></i>
												</div>
											</li>
											<li class="mt-0 pb-3">
												<div><small class="fs-13 text-muted float-end">23/08/2022</small></div>
												<a class="font-weight-bold text-dark fs-16">WebStresser CEO</a>
												<p class="text-muted mt-0">Hello our dear customer, In 10 days a new dedicated arrives on the site with a power of total network 150gbps AMP method.  
<br><br>
See you soon from Webstresser.net </p>
											</li>

									</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-xl-3">
							<div class="card">
								<div class="card-body">
									<div class="d-flex justify-content-between">
										<h4 class="card-title">Information Account</h4>
									</div>
									<div class="list d-flex align-items-center border-bottom py-3">
										<i class="fa fa-user" aria-hidden="true" style="font-size: 30px;"></i>
										<div class="wrapper w-100 ms-3">
											<p class="mb-0">
											<b>Username </b></p>
											<div class="d-sm-flex justify-content-between align-items-center">
												<div class="d-flex align-items-center">
													<i class="fa fa-user text-muted me-1 tx-11"></i>
													<p class="mb-0"><?php echo $_SESSION['username']; ?></p>
												</div>
											</div>
										</div>
									</div>
									<div class="list d-flex align-items-center border-bottom py-3">
										<i class="fa fa-server" aria-hidden="true" style="font-size: 30px;"></i>
										<div class="wrapper w-100 ms-3">
											<p class="mb-0">
											<b>Concurrent</b></p>
											<div class="d-sm-flex justify-content-between align-items-center">
												<div class="d-flex align-items-center">
													<i class="fa fa-server text-muted me-1 tx-11"></i>
													<p class="mb-0"><?php echo $row['concurrents']?></p>
												</div>
											</div>
										</div>
									</div>
									<div class="list d-flex align-items-center border-bottom py-3">
										<i class="fa fa-clock" aria-hidden="true" style="font-size: 30px;"></i>
										<div class="wrapper w-100 ms-3">
											<p class="mb-0">
											<b>Boot Time </b></p>
											<div class="d-sm-flex justify-content-between align-items-center">
												<div class="d-flex align-items-center">
													<i class="fa fa-clock text-muted me-1 tx-11"></i>
													<p class="mb-0"><?php echo $row['mbt']?></p>
												</div>
											</div>
										</div>
									</div>
									<div class="list d-flex align-items-center border-bottom py-3">
										<i class="fa fa-calendar" aria-hidden="true" style="font-size: 30px;"></i>
										<div class="wrapper w-100 ms-3">
											<p class="mb-0">
											<b>Expire Plan</b></p>
											<div class="d-sm-flex justify-content-between align-items-center">
												<div class="d-flex align-items-center">
													<i class="fa fa-clock text-muted me-1 tx-11"></i>
													<p class="mb-0"><?php echo $date ?></p>
												</div>
											</div>
										</div>
									</div>
									<div class="list d-flex align-items-center border-bottom py-3">
										<i class="fa fa-plus" aria-hidden="true" style="font-size: 30px;"></i>

										<div class="wrapper w-100 ms-3">
											<p class="mb-0">
											<b>Your Balance</b></p>
											<div class="d-sm-flex justify-content-between align-items-center">
												<div class="d-flex align-items-center">
													<i class="fa fa-rocket text-muted me-1 tx-11"></i>
													<p class="mb-0"><?php echo number_format((float)$balance, 2, '.', ''); ?> $</p>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>

						<div class="col-xl-6">
							<div class="card">
								<div class="card-header pb-0">
									<div class="d-flex justify-content-between">
										<h4 class="card-title mg-b-0">Last 7 days tests</h4>
									</div>
								</div>
								<div class="tab-pane active" id="logins">
								<div class="card-body">
<script src="char/jquery.min.js"></script>
        <script src="char/Chart.min.js"></script>
									<div style="height: 480px;"><canvas id="myChart"></canvas></div>								</div>
							</div>
						</div>
						<!--/div-->

					</div>
					<!-- /row -->
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
		
		
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    loadchart();

}, false);

// Loading the attack graph
function loadchart() {
	var ctx = $("#myChart").get(0).getContext("2d");
	 
	var data = {
		labels: ["<?php echo $date_seven; ?>", "<?php echo $date_six; ?>", "<?php echo $date_five; ?>", "<?php echo $date_four; ?>", "<?php echo $date_three; ?>", "<?php echo $date_two; ?>", "<?php echo $date_one; ?>"],
		datasets: [
			{
				label: 'Last 7 days tests',
				fillColor: '#cb3a3a',
				strokeColor: '#000',
				pointColor: '#fff',
				pointStrokeColor: '#000',
				pointHighlightFill: '#000',
				pointHighlightStroke: '#000',
				data: [<?php echo $count_seven; ?>, <?php echo $count_six; ?>, <?php echo $count_five; ?>, <?php echo $count_four; ?>, <?php echo $count_three; ?>, <?php echo $count_two; ?>, <?php echo $count_one; ?>]
			}
		]
	}

	var myNewChart = new Chart(ctx).Line(data, {
		scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		scaleFontColor: '#999',
		scaleFontStyle: '600',
		tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		tooltipCornerRadius: 3,
		maintainAspectRatio: false,
		tooltipTemplate: "<%if (label){%><%=label%> - <%}%><%= value %> tests",
		responsive: true
	});
}
</script>  

						<div class="col-lg-6">
							<div class="card mg-b-20" id="map">
								<div class="card-body">
		   									<div class="main-content-label mg-b-5">
										Best 8 Methods									</div>
									
									<table class="table table-striped table-bordered table-vcenter">
	<thead>
        <tr>
			<th class="text-center" style="font-size: 12px;">Method</th>
			<th class="text-center" style="font-size: 12px;">Attacks</th>
			<th class="text-center" style="font-size: 12px;">Porcentaje</th>
			<th class="text-center" style="font-size: 12px;">Network</th>
        </tr>
	</thead>
    <tbody>
	
       <?php
	   
        $SQLGetMethods = $odb -> query("SELECT * FROM `logs`");
        $metodos_limit = 0;
        while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
         if (!(isset($Metodos[$getInfo['method']]))) {
          $Metodos[$getInfo['method']] = 0;
         }
         $Metodos[$getInfo['method']] = $Metodos[$getInfo['method']] + 1;
        }
        asort($Metodos);
        $Metodos = array_reverse($Metodos);
        $Metodos = array_slice($Metodos, 0, 8, true);

        $Total = 0;


        foreach ($Metodos as $key => $value) {
         $Total = $Total + $value;
        }

        function get_percentage($total, $number)
        {
          if ( $total > 0 ) {
           return round($number / ($total / 100),2);
          } else {
            return 0;
          }
        }



        foreach ($Metodos as $key => $value) {
         $Porcentaje = get_percentage($Total, $value);
		 
		 	if ($Porcentaje >= 0 and $Porcentaje <= 10 )
			{
  $ripx = '<div class="progress progress-lg m-b-5" style="margin-bottom:0px;"><div class="progress-bar bg-success" role="progressbar" aria-valuenow="'. $Porcentaje .'" aria-valuemin="0" aria-valuemax="100" style="width:'. $Porcentaje .'%; visibility: visible; animation-name: animationProgress;"><center>' . $Porcentaje . '%</center></div></div>';
			}elseif ($Porcentaje >= 10 and $Porcentaje <= 25 )
			{
  $ripx = '<div class="progress progress-lg m-b-5" style="margin-bottom:0px;"><div class="progress-bar bg-info" role="progressbar" aria-valuenow="'. $Porcentaje .'" aria-valuemin="0" aria-valuemax="100" style="width: '. $Porcentaje .'%; visibility: visible; animation-name: animationProgress;"><center>' . $Porcentaje . '%</center></div></div>';
			}elseif ($Porcentaje >= 25 and $Porcentaje <= 30 )
			{
  $ripx = '<div class="progress progress-lg m-b-5" style="margin-bottom:0px;"><div class="progress-bar bg-warning" role="progressbar" aria-valuenow="'. $Porcentaje .'" aria-valuemin="0" aria-valuemax="100" style="width: '. $Porcentaje .'%; visibility: visible; animation-name: animationProgress;"><center>' . $Porcentaje . '%</center></div></div>';
			}elseif ($Porcentaje >= 30 and $Porcentaje <= 100 )
			{
  $ripx = '<div class="progress progress-lg m-b-5" style="margin-bottom:0px;"><div class="progress-bar bg-danger" role="progressbar" aria-valuenow="'. $Porcentaje .'" aria-valuemin="0" aria-valuemax="100" style="width: '. $Porcentaje .'%; visibility: visible; animation-name: animationProgress;"><center>' . $Porcentaje . '%</center></div></div>';
			}
			
         echo '<tr class="text-center" style="font-size: 12px;">
		<td><b class="text-info">'.htmlspecialchars($key).'</b></td>
		<td><b class="text-info"><span class="badge" style="background: linear-gradient(135deg, #262f38 0, #42a5f5 100%)!important;">'.$value.'</span></b></td>	
		<td><center><b class="text-danger">'.$ripx.'</b></center></td>	
		<td><span class="badge badge-info"><i class="fa fa-bolt"></i></span> <b class="text-info">ViP Network</b></td>	
			</tr>';
                  


         echo '</tr>';
        }
       ?>
    </tbody>
 </table>									</div>
							</div>
						</div>    
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

<script>
	SendPop = setTimeout(function(){
		document.getElementById('modal-popout').click();
		clearTimeout(SendPop);
	}, 2500);
</script>
<script>
	SendPop = setTimeout(function(){
		document.getElementById('modal-popGift').click();
		clearTimeout(SendPop);
	}, 5000);
</script>
<div class="modal fade" id="modal-popout" tabindex="-1" role="dialog" aria-labelledby="modal-popout" style="display: none;" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout" role="document" style="box-shadow: 0 -5px 25px -5px #fbfbfc, 0 1px 5px 0 #fbfbfc, 0 0 0 0 #fbfbfc;">
<div class="modal-content">
<div class="block block-themed block-transparent mb-0">
<div class="block-header bg-primary-dark">
<h3 class="block-title"><i class="fa fa-exclamation-triangle"></i> ALERT</h3>
<div class="block-options">
<button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
<i class="si si-close"></i>
</button>
</div>
</div>
<div class="block-content">
Dear <strong><?php echo ucfirst($_SESSION['username']); ?></strong><br><br><p>You can see many systems has been added!<br></p><hr>UserProfile, ApiAccess, Last Logins + Users online,Servers Per Attack, Graph 7 Days Attacks <br>
<span class="badge badge-danger">HOT</span> <bb class="text-warning">Bot System </bb>(<bb class="text-danger">ON!</bb>)<br><br>
<bb class="text-success">Now you can pay to plans with your account balance!</bb><br><hr><span class="badge badge-danger">HUB</span> <bb class="text-warning">Stresser Hub </bb>(<bb class="text-success">ON!!</bb>)<br><p></p></div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
       		<!-- Internal Select2 js-->
		<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/js/vector-map.js"></script>
		<!--Internal Chartjs js -->
		<script src="assets/js/chart.chartjs.js"></script>

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

	   <!--Internal  Vector-maps js -->
		<script src="assets/plugins/jqvmap/jquery.vmap.min.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.world.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.canada.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.algeria.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.argentina.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.europe.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.germany.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.russia.js"></script>
		<script src="assets/plugins/jqvmap/maps/jquery.vmap.france.js"></script>
		<script src="assets/js/vector-map.js"></script>
		<!-- Internal Vector-sampledata js -->
		<script src="assets/js/jquery.vmap.sampledata.js"></script>

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