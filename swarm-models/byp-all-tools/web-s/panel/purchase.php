<?php 

   //By Complex
	
session_start();
$page = "Purchase";
include 'header.php';
    $runningrip = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$slotsx = $odb->query("SELECT COUNT(*) FROM `api` WHERE `slots`")->fetchColumn(0);

	


		
?>

			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid p-4">
					


   <div class="row animated flipInX">
  
					<div class="pricing-tabs">
							<div class="tab-content">
									<div class="tab-pane pb-0 active show" id="year">
										<div class="row d-flex align-items-center justify-content-center">


			  <?php
												$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `ID` ASC");
												while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC))
												{
									$id = $getInfo['ID'];
									$name = $getInfo['name'];
									$price = $getInfo['price'];
									$length = $getInfo['length'];
									$unit = $getInfo['unit'];
									$concurrents = $getInfo['concurrents'];
									$mbt = $getInfo['mbt'];
									$network = $getInfo['vip'];
									$api = $getInfo['api'];
									$totalservers = $getInfo['totalservers'];
$power = $getInfo['power'];
$powerl7 = $getInfo['powerl7'];
															
									if($network == "0")
									{
										$network = '<b class="text-primary"><i class="fa fa-feed text-warning"></i> Normal</b>';
										$colorx = 'bg-body-light';
										$l4= '<strong>Layer 4 </strong><span class="text-success font-w700"></i>  &#10004; </span>';
										$l7= '<strong>Layer 7 </strong><span class="text-success font-w700"></i> &#10004; </span>';
										$b4= '<strong>Bypass Layer 4 </strong><span class="text-danger font-w700"></i>  &#10006; </span>';
										$b7= '<i class="fa fa-times"></i>';
										
										
										
									}elseif($network == "1")
									{
										$network = '<span class="text-danger font-w700"></i> VIP <i class="si si-fire text-warning"></i></span>';
										$colorx = '';
										$l4= '<strong>Layer 4 </strong><span class="text-success font-w700"></i>  &#10004; </span>';
										$l7= '<strong>Layer 7 </strong><span class="text-success font-w700"></i> &#10004; </span>';
										$b4= '<strong>Bypass Layer 4 </strong><span class="text-success font-w700"></i>  &#10004; </span>';
										$b7= '<i class="fa fa-check"></i>';
									}
									if($api == "0")
									{
										$api = '<i class="fa fa-times"></i>';
									}elseif($api == "1")
									{
										$api = '<i class="fa fa-check"></i>';
									}
									
					
										
echo '
											<div class="col-lg-6 col-xl-3 col-md-6 col-sm-12">
												<div class="card p-3 pricing-card">
													<div class="card-header text-justified pt-2">
														<p class="tx-18 font-weight-semibold mb-1">'.htmlspecialchars($name).' <i class="fa fa-cart-arrow-down" aria-hidden="true"></i></p>
														<p class="text-justify font-weight-semibold mb-1"> <span class="tx-30 me-2">$</span><span class="tx-30 me-1">'.htmlentities($price).'</span><span class="tx-24"><span class="op-5 text-muted tx-20">/</span> '.htmlentities($length).' '.htmlspecialchars($unit).'</span></p>
														<p class="tx-13 mb-1">All this plan uses a dedicated server power, so know quite normal that it is so expensive! Each attack is sent with a server speed port power at 10Gbps.</p>
														<p class="tx-13 mb-1 text-primary font-weight-">Make your best choice.</p>
													</div>
													<div class="card-body pt-2">
														<ul class="text-justify pricing-body text-muted ps-0">
															<li class="mb-4"><span class="text-primary me-2 p-1 bg-primary-transparent rounded-pill tx-8"><i class="fa fa-check"></i></span>  <strong> '.htmlentities($mbt).'</strong> Boot Time</li>
															<li class="mb-4"><span class="text-primary me-2 p-1 bg-primary-transparent  rounded-pill tx-8"><i class="fa fa-check"></i></span> <strong>'.$concurrents.' </strong> Concurrent</li>

<li class="mb-4"><span class="text-primary me-2 p-1 bg-primary-transparent  rounded-pill tx-8">'.$api.'</span> <strong>Api Access</strong></li> 




														</ul>
													</div>
													<div class="card-footer text-center border-top-0 pt-1">
														<a href="invoice.php?id='. $id .'"><button  class="btn btn-lg btn-primary text-white btn-block" data-bs-target="#modaldemo5" data-bs-toggle="modal">
															<span class="ms-4 me-4"><i class="fa fa-cart-plus" aria-hidden="true"></i> Subscribe</span></button></a>


													</div>
												</div>
											</div>';
                                                    
												
												?>
											
											
											
											
											
											
											
											
																					
<?php
									} 
								?>



										</div>
									</div>
						                      
					</div>
					<!--/row-->
                    </div></div>
				<!-- Container closed -->

			</div></div>
			<!-- main-content closed -->
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
		<!-- Internal Select2.min js -->
		<script src="assets/js/modal.js"></script>
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
<script>
function CopyToClipboard(id)
{
var r = document.createRange();
r.selectNode(document.getElementById(id));
window.getSelection().removeAllRanges();
window.getSelection().addRange(r);
document.execCommand('copy');
window.getSelection().removeAllRanges();
}
</script>
	</body>
</html>