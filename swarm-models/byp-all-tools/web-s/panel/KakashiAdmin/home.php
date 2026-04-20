<?php 

session_start();
$page = "Dashboard";
include 'header.php';


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
		
			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid p-4">
															<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$('#stats').load('../complexx/admin/stats.php').fadeIn("slow");
													}, 1000);
													</script>
<div class="col-12">
					<div id="stats"></div>


<div class="row">



		



									<div class="col-lg-12" id="div" style="display:none"></div>
						
						
									     <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 style="color: white;" class="card-title"><i class="fa fa-spinner fa-spin"></i> Running Attacks [ Live ]</h3>

                                    	   <div class="card">
                               <div class="block-content">
					
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$('#live_servers').load('../complexx/admin/view.php').fadeIn("slow");
													}, 1000);
													</script>

					<div id="live_servers"></div>

					</div>

                            </div>
						
                            </div>
                        </div>
                    </div>
                    </div>  
                    					           <?php 
		if (isset($_POST['clearBtn1']))
		{
			$SQL = $odb -> query("TRUNCATE `loginlogss`");
				echo '<p><strong>SUCCESS: </strong>Login Logs have been cleared</p>';		
}
		?>
					           <?php 
		if (isset($_POST['payment']))
		{
			$SQLKILLER = $odb -> query("DELETE FROM `addfunds` WHERE `status` = 'VOIDED'");
				echo '<p><strong>SUCCESS: </strong>Payment logs cleard</p>';		
}
		?>

		
						           <?php 
		if (isset($_POST['clearBtn2']))
		{
			$SQL = $odb -> query("TRUNCATE `logins_failed`");
				echo '<p><strong>SUCCESS: </strong>Login Logs have been cleared</p>';		
}
		?>
		
								           <?php 
		if (isset($_POST['clearBtn3']))
		{
			$SQL = $odb -> query("TRUNCATE `logs`");
				echo '<p><strong>SUCCESS: </strong>Login Logs have been cleared</p>';		
}
		?>
		
		
<div class="col-lg-12">
<div class="card">
<div class="card-header">
<h3 style="color: white;" class="card-title"><i class="fa fa-trash"></i> Delee Logs</h3>
</div>
<div class="card-body">
	
		        <form action="" method="POST" class="form-horizontal">
							<center><h4><i class="fa fa-trash"></i> Delete <i class="fa fa-trash"></i></h2></center>
<center>
		  <input type="submit" value="Delete Login Logs" onclick="disable" name="clearBtn1" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete?')">
		  <input type="submit" value="Delete attack Logs" name="clearBtn3" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete?')" >
<input type="submit" value="Delete payment Logs" name="payment" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete?')" >

	<input type="submit" value="Delete login fails Logs" name="clearBtn2" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete?')" >
		  </center>
</form>
	
	</div>
</div>
</div>
                    </div>
					<script>
		attacks();
	
		function attacks() {
			document.getElementById("attacksdiv").style.display = "none";
			document.getElementById("manage").style.display = "inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			}
			else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("attacksdiv").innerHTML = xmlhttp.responseText;
					document.getElementById("manage").style.display = "none";
					document.getElementById("attacksdiv").style.display = "inline-block";
					document.getElementById("attacksdiv").style.width = "100%";
					eval(document.getElementById("ajax").innerHTML);
				}
			}
			xmlhttp.open("GET","../complexx/admin/view.php",true);
			xmlhttp.send();
		}
		
		function stop(id) {
			document.getElementById("manage").style.display="inline"; 
			document.getElementById("div").style.display="none"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			}
			else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("div").innerHTML=xmlhttp.responseText;
					document.getElementById("div").style.display="inline";
					document.getElementById("manage").style.display="none";
					if (xmlhttp.responseText.search("success") != -1) {
						attacks();
						window.setInterval(ping(host),10000);
					}
				}
			}
			xmlhttp.open("GET","../complexx/admin/stop.php?id=" + id, true);
			xmlhttp.send();
		}
		
		</script>
    
    </div>
   
	  </div>


<!-- END Main Container -->
        </div>
    </main>
	
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

</div>
</div>
			<!-- Footer opened -->
			<div class="main-footer">
				<div class="container-fluid pt-0 ht-100p">
					 Copyright © 2022 <a href="javascript:void(0);" class="text-primary">boot-them</a>. Designed with <span class="fa fa-heart text-danger"></span> by <a href="javascript:void(0);"> Boot-ThemCEO </a> All rights reserved
				</div>
			</div>
			<!-- Footer closed -->
	

       		<!-- Internal Select2 js-->
		<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/js/vector-map.js"></script>
		<!--Internal Chartjs js -->
		<script src="assets/js/chart.chartjs.js"></script>

				<!-- Back-to-top -->
		<a href="#top" id="back-to-top"><i class="las la-arrow-up"></i></a>

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

	   <!--Internal  Vector-maps js -->
		<script src="../assets/plugins/jqvmap/jquery.vmap.min.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.world.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.canada.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.algeria.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.argentina.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.europe.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.germany.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.russia.js"></script>
		<script src="../assets/plugins/jqvmap/maps/jquery.vmap.france.js"></script>
		<script src="../assets/js/vector-map.js"></script>
		<!-- Internal Vector-sampledata js -->
		<script src="../assets/js/jquery.vmap.sampledata.js"></script>

		<!-- Sticky js -->
		<script src="../assets/js/sticky.js"></script>

		<!-- Right-sidebar js -->
		<script src="../assets/plugins/sidebar/sidebar.js"></script>
		<script src="../assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- Theme Color js -->
		<script src="../assets/js/themecolor.js"></script>

		<!-- custom js -->
		<script src="../assets/js/custom.js"></script>

		<!-- Switcher js -->
		<script src="../assets/switcher/js/switcher.js"></script>
	</body>
</html>