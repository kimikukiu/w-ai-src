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

  <!-- Page Content -->
		
			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid p-4">

      <!-- .row -->

      <!--/.row -->
      <!-- .row -->
	  <div class="widget-content">

</div> 
						<div class="col-md-12 col-xl-12">
							<div class="card">
								<div class="card-body">

		  	<h3 class="box-title">Api URL</h3>
		  	<form method="POST">
		  		<?php if($key == '0'){?>
	            <input class="form-control" type="text" value="API is unavailable or api-key is disabled! Click 'Generate new api-key'." readonly="">
	            <?php }else{?>
				<?php if($user->api($odb)){?>
				<?php if($user->isVip($odb)){?>
	            <input class="form-control" type="text" value="https://webstresser.cc/panel/api/api.php?key=<?php echo $key;?>&host=[host]&port=[port]&time=[time]&method=[method]&vip=[0]" readonly="">
	            <?php }else{?>
				<input class="form-control" type="text" value="https://webstresser.cc/panel/api/api.php?key=<?php echo $key;?>&host=[ip]&port=[port]&time=[Seconds]&method=[Method/stop]&vip=0" readonly="">
				<?php }?>
				<?php }else{?>
				<input class="form-control" type="text" value="You need api access for use this!" readonly="" style="color:black;">
				<?php }?>
				<?php }?>
	            <br><button type="submit" class="btn btn-primary" name="gen_key">Generate new api-key</button> <button type="submit" class="btn btn-danger" name="disable_key">Disable api-key</button>
	        </form>
          </div></div></div>
<!--div-->
						<div class="col-xl-12">
							<div class="card">
								<div class="card-header pb-0">
									<div class="d-flex justify-content-between">
										<h4 class="card-title mg-b-0">Method LIST</h4>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
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
        $Metodos = array_slice($Metodos, 0, 100, true);

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
									</div>
									<!-- bd -->
								</div>
								<!-- bd -->
							</div>
							<!-- bd -->
						</div>
						<!--/div-->

</div></div>
			<!-- Footer opened -->
			<div class="main-footer">
				<div class="container-fluid pt-0 ht-100p">
					 					 Copyright © 2022 <a href="javascript:void(0);" class="text-primary">Webstresser</a>. Designed with <span class="fa fa-heart text-danger"></span> by <a href="javascript:void(0);"> WebstresserCEO </a> All rights reserved
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
