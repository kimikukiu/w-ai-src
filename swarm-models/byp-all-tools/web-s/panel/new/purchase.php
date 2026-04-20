<?php 


	
session_start();
$page = "Purchase";
include 'header.php';
    $runningrip = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$slotsx = $odb->query("SELECT COUNT(*) FROM `api` WHERE `slots`")->fetchColumn(0);

	


		
?>
					



  
<div class="row">

								
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
										$api = 'No';
									}elseif($api == "1")
									{
										$api = 'Yes';
									}
									
					
										
echo '<div class="col-lg-4 col-xl-3">
								<section class="card mt-4">
									<header class="card-header bg-white">
										<div class="card-header-icon bg-primary">
											<i class="fas fa-rocket"></i>
										</div>
									</header>
									<div class="card-body">
										<h3 class="mt-0 font-weight-semibold mt-0 text-center">'.htmlspecialchars($name).' | '.htmlentities($price).'$</h3>
										<hr>
<center><h5><i class="bx bxs-time" ></i> Attack Time : '.htmlentities($mbt).'</h5></center>
<center><h5><i class="bx bxs-meteor" ></i> Concurrent : '.$concurrents.'</h5></center>
<center><h5><i class="bx bx-link" ></i> ApiSystem : '.$api.'</h5></center>
<hr>
<center><a href="invoice.php?id='. $id .'"<button type="button" class="mb-1 mt-1 me-1 btn bg-primary">Subscribe</button></a></center>
									</div>
								</section>
							</div>';
                                                    
												
												?>
<?php
									} 
								?>
		                      
					</div>
					<!--/row-->
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
		<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>		<!-- Specific Page Vendor -->
		<script src="vendor/select2/js/select2.js"></script>
		<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
		<script src="vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
		<script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>		<!-- Theme Custom -->
		<script src="js/custom.js"></script>		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>		<!-- Analytics to Track Preview Website -->
<script src="js/examples/examples.datatables.default.js"></script>
		<script src="js/examples/examples.datatables.row.with.details.js"></script>
		<script src="js/examples/examples.datatables.tabletools.js"></script>

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