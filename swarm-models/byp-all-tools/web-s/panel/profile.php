<?php 

   //By Complex
session_start();
$page = "Profile";
include 'header.php';

$username = $_SESSION['username'];

if(isset($_POST['delete'])){
   
  $SQLKILLER = $odb -> query("DELETE FROM `logins_failed` WHERE `username` = '$username'");
  $SQLKILLER2 = $odb -> query("DELETE FROM `login_history` WHERE `username` = '$username'");
  $SQLKILLER3 = $odb -> query("DELETE FROM `logs` WHERE `user` = '$username'");
   $SQLKILLER4 = $odb -> query("DELETE FROM `loginlogss` WHERE `username` = '$username'");
$SQLCHECKER = $odb -> query("SELECT COUNT(*) FROM `loginlogss` WHERE `username` = '$username'");
$logsafter = $SQLCHECKER->fetchColumn(0);
if ($logsafter < 1) {
    	echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  type: "success",
  title: "Success",
  text: "Done, all logs cleared!",
  showConfirmButton: false,
  timer: 4500
  
});';
  echo ' }, 1000);</script>';
	
}
		

	
	}

if(!empty($_POST['update'])){
		
		if(empty($_POST['old']) || empty($_POST['new'])){
				$error = error('You need to enter both passwords.');
				
							echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: "true",
  type: "error",
  title: "You need to enter both passwords",
  showConfirmButton: false,
  timer: 4500
  
});';
  echo ' }, 1000);</script>';
		}

		$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password");
		$SQLCheckCurrent -> execute(array(':ID' => $_SESSION['ID'], ':password' => SHA1(md5($_POST['old']))));
		$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
	
		if ($countCurrent == 0){
			$error = error('Current password is incorrect.');
			echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: "true",
  type: "error",
  title: "Current password is incorrect.",
  showConfirmButton: false,
  timer: 4500
  
});';
  echo ' }, 1000);</script>';
		}
		
		$notify = error($error);
	
		if(empty($error)){
			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
			$SQLUpdate -> execute(array(':password' => SHA1(md5($_POST['new'])),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
			$error = success('Password has been successfully changed');
			echo '<script type="text/javascript">';
  echo 'setTimeout(function () { swal({
  position: "top-end",
  toast: "true",
  type: "success",
  title: "Password Updated",
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

  

      <div class="row">
        <div class="col-lg-4">
           <div class="card profile-card-2">
            <div class="card-body pt-5">
                 <center><img src="https://media.discordapp.net/attachments/989138422297923665/997550418550849688/avatar.jpg" alt="profile-image" class="drop-img  cover-image" style="height: 120px;
    width: 120px;"></center>
                
            </div>
			
		
<?php
		//fetching stats qloq	
			$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
			$plansql -> execute(array(":id" => $_SESSION['ID']));
			$row = $plansql -> fetch(); 
			$date = date("m-d-Y, h:i:s a", $row['expire']);
			if (!$user->hasMembership($odb)){
				$row['mbt'] = 0;
				$row['concurrents'] = 0;
				$row['name'] = 'No membership';
				$date = '0-0-0';
				$SQLupdate = $odb -> prepare("UPDATE `users` SET `expire` = 0 WHERE `username` = ?");
				$SQLupdate -> execute(array($_SESSION['username']));
				
				}
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
				
				
			?>
            <div class="card-body border-top border-light">
                 <div class="media align-items-center">
                   
                     <div class="media-body text-left ml-3">
                       <div class="progress-wrapper">
                         <p>Rank <span class="float-right"><?php echo $rank; ?></span></p>
                         <div class="progress" style="height: 5px;">
                          <div class="progress-bar gradient-blooker" style="width:100%"></div>
                         </div>
                        </div>                   
                    </div>
                  </div>
                  <hr>
                  <div class="media align-items-center">
                   
                     <div class="media-body text-left ml-3">
                       <div class="progress-wrapper">
                         <p>Memebership <span class="float-right badge badge-primary shadow-primary m-1"><?php echo $row['name']; ?></span></p>
                         <div class="progress" style="height: 5px;">
                          <div class="progress-bar gradient-purpink" style="width:100%"></div>
                         </div>
                        </div>                   
                    </div>
                  </div>
                   <hr>
                  <div class="media align-items-center">
                   
                     <div class="media-body text-left ml-3">
                       <div class="progress-wrapper">
                         <p>Max Boot time <span class="float-right badge badge-primary shadow-primary m-1"><?php echo $row['mbt']; ?></span></p>
                         <div class="progress" style="height: 5px;">
                          <div class="progress-bar gradient-ibiza" style="width:100%"></div>
                         </div>
                        </div>                   
                    </div>
                  </div>
                    <hr>
                  <div class="media align-items-center">
                   
                     <div class="media-body text-left ml-3">
                       <div class="progress-wrapper">
                         <p>Expire <span class="float-right badge badge-primary shadow-primary m-1"><?php echo $date; ?></span></p>
                         <div class="progress" style="height: 5px;">
                          <div class="progress-bar gradient-scooter" style="width:100%"></div>
                         </div>
                        </div>                   
                    </div>
                  </div>
				   <hr>
				  <div class="media align-items-center">
                   
                     <div class="media-body text-left ml-3">
                       <div class="progress-wrapper">
                         <p>Conncurrents <span class="float-right badge badge-primary shadow-primary m-1"><?php echo $row['concurrents']; ?></span></p>
                         <div class="progress" style="height: 5px;">
                          <div class="progress-bar gradient-blooker" style="width:100%"></div>
                         </div>
                        </div>                   
                    </div>
                  </div>
                  
              </div>
        </div>

        </div>

        <div class="col-lg-8">
           <div class="card">
            <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                <li class="nav-item">

<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#profile">Acc activity</a>
                </li>
                
                <li class="nav-item">

<a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#edit1">Edit Profile</a>
                </li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="profile">
                    <h5 class="mb-3">Account Activity</h5>
                    
				 <div class="table-responsive">
           <table class="table table-striped table-borderless table-vcenter">
							<thead>
								<tr>
									
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">IP</th>
									<th style="font-size: 12px;">Country</th>
									<th style="font-size: 12px;">date</th>
									<th style="font-size: 12px;">result</th>
									
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `loginlogss` WHERE `username`='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 5");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$username = $getInfo['username'];
								$ip = $getInfo['ip'];
								$date = date("m-d-Y, h:i:s a", $getInfo['date']);
								$country = $getInfo['country'];
								$result = $getInfo['results'];
								echo '<tr>
										<td>'.htmlspecialchars($username).'</td>
										<td> '. $ip .'</td>
										<td>'. $country .'</td>
										
										<td>'. $date .'</td>
										<td>'. $result .'</td>
										
									  </tr>';
							}
							?>	
							</tbody>
						</table>
						</div>
                    <!--/row-->
                </div>
               
                <div class="tab-pane" id="edit1">
                    <form method="post">
                        
                        
                        
                        
                       
                       
                        
                       
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Old Password</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" id="old" name="old" placeholder="Enter your old password..">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Password</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" id="new" name="new" placeholder="Enter your new password..">
                            </div>
                        </div>
                        
                        <div class="form-group">
                                            <div class="col-xs-12">                                             
												<button class="btn btn-success waves-effect waves-light m-1" name="update" value="change" type="submit">
													<i class="fa fa-plus push-5-r"></i> Update
												</button>
											</div>
                                        </div>
                    </form>
                </div>
                
                 <div class="tab-pane" id="redeem">
                   	<form class="form-horizontal" method="post" onsubmit="return false;"><div id="div"></div>
              <div class="form-group">
                <label for="GiftCode" class="col-sm-3 control-label">Gift Code</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" name="code" id="code" placeholder="PLACE YOUR CODE">
                </div>
				<br>
				 <div class="form-group m-b-0">
                <div class="col-sm-offset-12 col-sm-12">
                  <button  id="launch" onclick="redeemCode()" class="btn btn-primary waves-effect waves-light m-1">Redeem Code</button>
                </div>
              </div>
              </div>
           
            </form>
                </div>
            </div>
        </div>
      </div>
      </div>
        
    </div>

    </div>
    <!-- End container-fluid-->
   </div><!--End content-wrapper-->
	
	
	
	
   

		<script>
function redeemCode() {
	            launch.disabled = true;
				var code = $('#code').val();
Swal.fire({
		buttonsStyling: !1,
                confirmButtonClass: "btn btn-success waves-effect waves-light m-1",
                cancelButtonClass: "btn btn-danger waves-effect waves-light m-1",
                inputClass: "form-control",
                    title: "Are you sure?",
                    text: "Are you sure you want to check giftcard:  " + code + "?",
                    type: "warning",
				
                    showCancelButton: !0,
                    confirmButtonColor: "#d26a5c",
                    confirmButtonText: "Yes",
                    html: !1,
                    preConfirm: function() {
                        return new Promise(function(n) {
                            setTimeout(function() {
                                n()
                            }, 50)
                        })
                    }
                }).then(function(n) {
			//document.getElementById("icon").style.display="inline"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						launch.disabled = false;
					//document.getElementById("icon").style.display="none";
					     document.getElementById("div").innerHTML=xmlhttp.responseText;
						if (xmlhttp.responseText.search("SUCCESS") != -1) {
							inbox();
						}
					}
				}
				xmlhttp.open("GET","complexx/redeem2.php?user=<?php echo $_SESSION['ID']; ?>" + "&code=" + code,true);
				xmlhttp.send();
				}, function(n) {})
			}

			</script>
	

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
