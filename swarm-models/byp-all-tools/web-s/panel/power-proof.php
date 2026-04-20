<?php 

session_start();
$page = "Power";
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
						
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/971542241925550181/984015660348739605/unknown.png"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/971542241925550181/984015660348739605/unknown.png?width=2038&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "Captcha" protection</h4></a>
									<p class="mb-2">This attack has been launched with 6 sumultanese attacks, with the method "CF-BYPASS", This method bypass any type of UAM or CAPTCHA very well but you fake a "VIP" pack which costs 120$, It's up to you to make the best choice !</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/971542241925550181/984018343445033010/unknown.png"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/971542241925550181/984018343445033010/unknown.png" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "UAM + Bot Fight Mode" protection</h4></a>
									<p class="mb-2">This attack has been launched with 6 sumultanese attacks, with the method "CF-BYPASS", This method bypass any type of UAM or CAPTCHA very well but you fake a "VIP" pack which costs 120$, It's up to you to make the best choice !</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/971542241925550181/984022339522031616/unknown.png?width=2903&height=1159"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/971542241925550181/984022339522031616/unknown.png?width=2903&height=1159" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a home wifi </h4></a>
									<p class="mb-2">This type of attack can be done with any type of method in our L4 have to see which one is better for you, we ask you to put the method "NTP" it is the best posible for the moment.</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/971542241925550181/984021336747806740/unknown.png?width=2625&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/971542241925550181/984021336747806740/unknown.png?width=2625&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" site but without protection!</h4></a>
									<p class="mb-2">For unprotected sites you can use the method "HTTP-RAW" & "HTTP-DOWN" The 2 most used methods that work very well.</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/971542241925550181/984023280434417664/unknown.png?width=1198&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/971542241925550181/984023280434417664/unknown.png?width=1198&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a target that supports 20Gbps of ddos!</h4></a>
									<p class="mb-2">The dstat is down with 3Concurrent which is +2.5m pps and +20Gbps, With a very stable power.</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/982019744246947860/985667027295412264/unknown.png?width=1682&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/982019744246947860/985667027295412264/unknown.png?width=1682&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" with "UAM" & "CF PRO" protection!</h4></a>
									<p class="mb-2">We  have used 6concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/947283260210294844/985863735161786378/unknown.png?width=2903&height=1288"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/947283260210294844/985863735161786378/unknown.png?width=2903&height=1288" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" with "UAM" & "CUSTOM RULES" protection!</h4></a>
									<p class="mb-2">We  have used 5concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>

						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/982019744246947860/985865206007738488/unknown.png"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/982019744246947860/985865206007738488/unknown.png" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" with "UAM" & "CUSTOM RULES" protection!</h4></a>
									<p class="mb-2">We  have used 5concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>

						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/951306340419137546/986031801761923122/unknown.png?width=2510&height=1329"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/951306340419137546/986031801761923122/unknown.png?width=2510&height=1329" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" with "UAM" & "CUSTOM RULES" protection!</h4></a>
									<p class="mb-2">We  have used 4concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/951306340419137546/986034435302506516/unknown.png?width=2503&height=1329"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/951306340419137546/986034435302506516/unknown.png?width=2503&height=1329" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on a "CF" with "UAM" & "CUSTOM RULES" protection!</h4></a>
									<p class="mb-2">We  have used 4concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/986357077976961054/unknown.png?width=2540&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/986357077976961054/unknown.png?width=2540&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on basic protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/986359241260556348/unknown.png?width=2638&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/986359241260556348/unknown.png?width=2638&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on basic protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/986360404861128724/unknown.png?width=2495&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/986360404861128724/unknown.png?width=2495&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on basic protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>

						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/986361704227172452/unknown.png?width=2535&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/986361704227172452/unknown.png?width=2535&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on basic protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/987116668322975784/unknown.png?width=2533&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/987116668322975784/unknown.png?width=2533&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Path protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988523961736364042/unknown.png"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988523961736364042/unknown.png" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on CF UAM protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988523862708846702/unknown.png?width=2503&height=1325"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988523862708846702/unknown.png?width=2503&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on CF UAM protection!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>

						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988527040493932715/unknown.png"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988527040493932715/unknown.png?width=1690&height=1325" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Contabo server!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988602340712009798/unknown.png?width=2490&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988602340712009798/unknown.png?width=2490&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Uam server!</h4></a>
									<p class="mb-2">We  have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988621016022540288/unknown.png?width=2503&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988621016022540288/unknown.png?width=2503&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 6concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>

						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988905483115057242/unknown.png?width=2533&height=1326"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988905483115057242/unknown.png?width=2533&height=1326" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 7concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/988937524472856607/unknown.png?width=2570&height=1324"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/988937524472856607/unknown.png?width=2570&height=1324" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 10concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/989234629255004230/unknown.png?width=2552&height=1342"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/989234629255004230/unknown.png?width=2552&height=1342" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 2concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/989235531734978610/unknown.png?width=2566&height=1341"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/989235531734978610/unknown.png?width=2566&height=1341" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 3concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/989304121762926592/unknown.png?width=2560&height=1343"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/989304121762926592/unknown.png?width=2560&height=1343" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 2concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/989670893577961472/unknown.png?width=2536&height=1341"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/989670893577961472/unknown.png?width=2536&height=1341" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/989994185350328400/unknown.png?width=2566&height=1341"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/989994185350328400/unknown.png?width=2566&height=1341" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 3concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/995045417542766663/unknown.png?width=2522&height=1342"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/995045417542766663/unknown.png?width=2522&height=1342" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 1concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/995075952616935534/unknown.png?width=2522&height=1341"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/995075952616935534/unknown.png?width=2522&height=1341" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 2concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>
						<div class="col-xxl-3 col-xl-6 col-lg-6  col-sm-6">
							<div class="card custom-card card-img-top-1">
								<a href="https://media.discordapp.net/attachments/986357028744220712/995123643384340581/unknown.png?width=2532&height=1341"><img class="card-img-top w-100 w-100" src="https://media.discordapp.net/attachments/986357028744220712/995123643384340581/unknown.png?width=2532&height=1341" alt=""></a>
								<div class="card-body pb-0">
									<a><h4 class="card-title">An attack on Cf-Captcha server!</h4></a>
									<p class="mb-2">We have used 2concurrents to drop this site .</p>
								</div>
								<div class="item7-card-desc d-flex p-3 pt-0 align-items-center justify-content-center border-top">
									<div class="main-img-user online">
									<img alt="avatar" src="https://media.discordapp.net/attachments/947969023914700860/979888417959727144/profile_2.png">
									</div>
									<div class="main-contact-body">
										<h6>Boot-Them | CEO</h6>
									</div>
									<div class="ms-auto">
										<a class="me-0 d-flex" href="javascript:void(0);"><span class="phone me-3 font-weight-semibold text-muted"><span class="fe fe-calendar text-muted me-2 tx-16"></span>June 08,2022</span></a>
									</div>
								</div>
							</div>
					    </div>


					</div>
					<!-- /row -->



    
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