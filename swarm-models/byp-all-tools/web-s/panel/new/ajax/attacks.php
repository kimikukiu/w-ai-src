<?php

    // Bt Complex
  
	ob_start(); 
	require '../inc/configuration.php';
     require '../inc/init.php';

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}
	
	$username = $_SESSION['username'];

?>
<div class="table-responsive">	
<table class="table table-striped table-bordered table-vcenter">
	<thead>
   
        <tr>
  
		         <th class="text-center"><i class='bx bxs-purchase-tag-alt' style="font-size: 15px;"></i> ID</th>
            <th class="text-center"><i class='bx bx-sitemap' style="font-size: 15px;"></i> Target</th>
            <th class="text-center"><i class='bx bxs-meteor' style="font-size: 15px;"></i> Method</th>				
            <th class="text-center"><i class='bx bxs-time' style="font-size: 15px;"></i> Expires</th>

        </tr>
    </thead>
    <tbody style="border-top: 2px solid #282d36;">
<?php

    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT 7");

    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {

        $ip = $show['ip'];
        $port = $show['port'];
        $time = $show['time'];
        $method = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 10")->fetchColumn(0);
        $rowID = $show['id'];
        $date = $show['date'];
		$totalservers = $show['totalservers'];
		$vip = $show['vip']; 
		if($vip == 0)
		{
		$vip = "Normal";
		}elseif($vip == 1)
		{
		$vip = "ViP";
		}else
		{
		 $vip = "Error!"; 
		}
		 
        $expires = $date + $time - time();

        if ($expires < 0 || $show['stopped'] != 0) {
            $countdown = '<span class="text-danger"><i class="fa fa-ban"></i> Expired</span>';
        }
		else {
            $countdown = '<div id="a' . $rowID . '"></div>';
            echo "
				<script id='ajax'>
					var count={$expires};
					var counter=setInterval(a{$rowID}, 1000);
					function a{$rowID}(){
						count=count-1;
						if (count <= 0){
							clearInterval(counter);
							attacks();
							return;

						}
					document.getElementById('a{$rowID}').innerHTML=count;
					}
				</script>
			";
        }
		
		
        if ($show['time'] + $show['date'] > time() and $show['stopped'] != 1) {
            $action = '<button type="button" onmousedown="bleep2.play()" onclick="stop(' . $rowID . ')" id="st" class="btn btn-warning btn-trans waves-effect w-md waves-danger m-b-5"><i class="fa fa-power-off"></i> Stop</button>';
        } else {
            $action = '<button type="button" id="rere" onmousedown="bleep4.play()" onclick="renew(' . $rowID . ')" class="btn btn-primary btn-trans waves-effect w-md waves-info m-b-5"><i class="fa fa-refresh"></i> Renew</button>';
        }
		
        echo '<tr class="text-center">
	    <td><span class="badge" style="background: linear-gradient(135deg, #eab000 0, rgba(255, 202, 40, 0.37) 100%)!important;">' . $rowID . '</span></td>
	    <td><span class="badge" style="background: linear-gradient(135deg, #262f38 0, #42a5f5 100%)!important;">' . htmlspecialchars($ip) . ' : ' . $port . ' </span></td>		
		<td><span class="badge badge-danger" >' . $method . '</span> </td>
		<td style="font-size: 15px;" class="text-center">' . $countdown . '</td>
	
		</tr>
		';

    }
?>
	</tbody>
	
</table>
</div>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
</html>
	<!-- JQuery min js -->
		<script src="../assets/plugins/jquery/jquery.min.js"></script>
		
		<!-- Bootstrap js -->
		<script src="../assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		<!-- Moment js -->
		<script src="../assets/plugins/moment/moment.js"></script>

		<!-- P-scroll js -->
		<script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="../assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- eva-icons js -->
		<script src="../assets/js/eva-icons.min.js"></script>

		<!-- Sidebar js -->
		<script src="../assets/plugins/side-menu/sidemenu.js"></script>

			    <!-- Internal Select2 js-->
		<script src="../assets/plugins/select2/js/select2.min.js"></script>

		<!--Internal  Sweet-Alert js-->
		<script src="../assets/plugins/sweet-alert/sweetalert.min.js"></script>
		<script src="../assets/plugins/sweet-alert/jquery.sweet-alert.js"></script>