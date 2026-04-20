<?php
	$page = "Api";
	require_once 'header.php'; 
?>


<?php
	if(isset($_POST['gen_key'])){
		if(isset($_SESSION['username'])){
			genKey($_SESSION['username'], $odb);
			header('Location: api.php');
		}
	}
	if(isset($_POST['disable_key'])){
		if(isset($_SESSION['username'])){
			disableKey($_SESSION['username'], $odb);
			header('Location: api.php');
		}
	}

	function genKey($username, $odb){
		$newkey = generateRandomString(16);
		$stmt2 = $odb->query("UPDATE users SET apikey='$newkey' WHERE username='$username'");
	}
	function disableKey($username, $odb){
		$stmt2 = $odb->query("UPDATE users SET apikey='0' WHERE username='$username'");
	}
	function generateRandomString($length = 10){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i=0;$i<$length;$i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	$stmt = $odb->prepare("SELECT apikey FROM users WHERE username=:login");
	$stmt->bindParam("login", $_SESSION['username'], PDO::PARAM_STR);
	$stmt->execute();
	$key = $stmt->fetchColumn(0);
?>


					<!-- start: page -->


					<div class="row">
						<div class="col-lg-8 mb-4 mb-lg-0">
							<section class="card">
								<header class="card-header">
<form method="POST">
									<h2 class="card-title"><i class='bx bx-link'></i> Api Link</h2>
									<p class="card-subtitle">Here you can see your api link !</p>
								</header>
								<div class="card-body">

<?php if($key == '0'){?>
	            <div class="form-group row pb-2">
											<label class="col-sm-3 control-label text-sm-end pt-2">Generate Key <span class="required">*</span></label>
											<div class="col-sm-9">
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-link"></i>
													</span>
													<input type="text" class="form-control" value="Api do not activate yet click on the button to get access." required/>
												</div>
											</div>
											<div class="col-sm-12">
                                                                                            <br>
<center><button type="submit" class="btn bg-primary" name="gen_key">Generate Key</button><center>
											</div>
										</div>
                                                                  
	            <?php }else{?>
									<!-- Flot: Basic -->
                                      <?php if($user->api($odb)){?>
                                      <?php if($user->api($odb)){?>

                         <div class="form-group row pb-2">
											<label class="col-sm-3 control-label text-sm-end pt-2">Api Link <span class="required">*</span></label>
											<div class="col-sm-9">
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-link"></i>
													</span>
													<input type="text" class="form-control" value="https://webstresser/panel/api/api.php?key=<?php echo $key;?>&host=[host]&port=[port]&time=[time]&method=[method]&vip=0" readonly="" required/>
												</div>
											</div>
											<div class="col-sm-9">

											</div>
										</div>
                                                                  
	           <?php }else{?>
				
        
				<?php }?>
        
        
				<?php }else{?>
			
      
      
				<?php }?>

                                                                  <div class="form-group row pb-2" style="border-top: 1px solid #1d2127;">
											<label class="col-sm-3 control-label text-sm-end pt-2">Format Start <span class="required">*</span></label>
											<div class="col-sm-9">
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-link"></i>
													</span>
													<input type="text" class="form-control" value="https://webstresser/panel/api/api.php?key=<?php echo $key;?>&host=1.1.1.1&port=80&time=30&method=LDAP&vip=0" readonly="" required//>
												</div>
											</div>
											<div class="col-sm-12">
                                                                                            <br>
<center><button type="submit" class="btn bg-primary" name="gen_key">Generate Key</button> <button type="submit" class="btn btn-danger bg-danger" name="disable_key">Disable Api</button><center>
											</div>
										</div>
                                                                 
							</form>

								</div>
							</section>
						</div>
						<div class="col-lg-4">
							<section class="card">
								<header class="card-header">

									<h2 class="card-title"><i class='bx bx-list-ul' ></i> Methods List</h2>
									<p class="card-subtitle">You can see the methods list !</p>
								</header>
								<div class="card-body">


<table class="table table-striped table-bordered table-vcenter">
	<thead>
        <tr>
			<th class="text-center" style="font-size: 12px;">Method</th>
			<th class="text-center" style="font-size: 12px;">Attacks</th>
			<th class="text-center" style="font-size: 12px;">Progress</th>
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
        $Metodos = array_slice($Metodos, 0, 80, true);

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
 </table>

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
<?php }?>
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