<?php
 // By netsource.pw
session_start();
$page = "Compose";
include 'header.php';



$status = "Waiting for user response";
$SQLGetTickets = $odb -> prepare("SELECT COUNT(*) FROM `tickets` WHERE `username` = :username AND `status` = :status");
        $SQLGetTickets -> execute(array(':username' => $_SESSION['username'], ':status' => $status));
        $active = $SQLGetTickets -> fetchColumn(0);
		
		$status = "Waiting for admin response";
$SQLGetTickets = $odb -> prepare("SELECT COUNT(*) FROM `tickets` WHERE `username` = :username AND `status` = :status");
        $SQLGetTickets -> execute(array(':username' => $_SESSION['username'], ':status' => $status));
        $sended = $SQLGetTickets -> fetchColumn(0);
		
				$status = "Waiting for admin response";
$SQLGetTickets = $odb -> prepare("SELECT COUNT(*) FROM `tickets` WHERE `username` = :username");
        $SQLGetTickets -> execute(array(':username' => $_SESSION['username']));
        $total = $SQLGetTickets -> fetchColumn(0);
			?>

			<div class="main-content app-content">

				<!-- container -->
				<div class="main-container container-fluid p-4">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
           <div class="card-body">

        <div class="row">
        
            <!-- Left sidebar -->
            <div class="col-lg-3 col-md-4">
                <a href="support.php" class="btn btn-white btn-block">Return to inbox</a>
                <div class="card mt-3 shadow-none">
                        <div class="list-group shadow-none">
                          <a href="javascript:void();" class="list-group-item bg-light-dark"><i class="fa fa-inbox mr-2"></i>Answered<b>(<?php echo $active ?>)</b> <b></b></a>
                         
                          <a href="javascript:void();" class="list-group-item"><i class="fa fa-paper-plane-o mr-2"></i>Sended<b>(<?php echo $sended ?>)</b></a>
                          <a href="javascript:void();" class="list-group-item"><i class="fa fa-trash-o mr-2"></i>Total tickets <b>(<?php echo $total ?>)</b></a>
                         
                         
                        </div>
                </div>

                
            </div>
            <!-- End Left sidebar -->
                    
        <!-- Right Sidebar -->
        <div class="col-lg-9 col-md-8">
            
            <div class="card mt-3 shadow-none">
                <div class="card-body">
				<div id="newticketalert" style="display:none"></div>
                    <form>
                        <div class="form-group">
                            <input class="form-control" type="text" id="subject">
                        </div>
                        <div class="form-group">
                            <div class="row">
                                
                              	
                                <div class="col-md-6">
                                    <div>
                                        <label for="department"><i class="si si-energy text-danger"></i> department</label>
                                        <select class="form-control" id="department" name="department">
                                                <option value="1">Choose a Department</option>
                                                <option value="Billing">Billing</option>
                                                <option value="General">General</option>
                                                <option value="Tech">Tech</option>
                                                <option value="Other">Other</option> 
                                        </select>
                                    </div>
                                </div>
                            
                            
                              
                                <div class="col-md-6">
                                    <div>
                                        <label for="priority"><i class="si si-energy text-danger"></i> priority</label>
                                        <select class="form-control" id="priority" name="priority">
                                               <option value="2">Choose a Priority</option>
                                               <option value="Low">Low</option>
                                               <option value="Medium">Medium</option>
                                               <option value="High">High</option>
                                        </select>
                                   
                                </div>
                            </div>
                            </div>
                        </div>
                       
                        <div class="form-group">
						
                            <textarea class="form-control" id="message" name="message" placeholder="Enter your message.." style="height: 200px"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-light btn-block waves-effect waves-light m-1" onclick="submitTicket()"><i class="fa fa-floppy-o mr-1"></i> Send</button>
                           
                        </div>
                    </form>
                </div> <!-- card body -->
             </div> <!-- card -->
           </div> <!-- end Col-9 -->
         </div><!-- End row -->
      </div>
    </div>
  </div>
 </div><!-- End row -->

    </div>
    <!-- End container-fluid-->
    
   </div><!--End content-wrapper-->
  
  
  
  
  <script>
function updateTickets()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("ticketsdiv").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","complexx/support.php?type=update",true);
xmlhttp.send();
}

window.setInterval(function(){updateTickets();}, 30000);
updateTickets();


function submitTicket()
{
var subject=$('#subject').val();
var message=$('#message').val();
var department=$('#department').val();
var ppp=$('#priority').val();

document.getElementById("newticketalert").style.display="none";
//document.getElementById("newticketloader").style.display="inline";
var xmlhttp;
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("newticketalert").innerHTML=xmlhttp.responseText;
	//document.getElementById("newticketloader").style.display="none";
	document.getElementById("newticketalert").style.display="inline";
	if (xmlhttp.responseText.search("Ticket has been created.") != -1)
	{
	updateTickets();
    }
    }
  }
xmlhttp.open("POST","complexx/support.php?type=submit",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("message=" + message + "&subject=" + subject + "&department=" + department + "&ppp=" + ppp);
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