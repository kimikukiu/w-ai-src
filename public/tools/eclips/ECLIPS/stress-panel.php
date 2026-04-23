<?php
include("Class/header.php");
?>
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">

<body oncontextmenu="return false">
    <script>
var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						eval(document.getElementById("success").innerHTML);
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=attacks",true);
					xmlhttp.send();

					function start()
					{
					launch.disabled = true;
					var host=$('#host').val();
					var port=$('#port').val();
					var time=$('#time').val();
					var method=$('#method').val();
					document.getElementById("div").style.display="none"; 
					
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						launch.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=start" + "&host=" + host + "&port=" + port + "&time=" + time + "&method=" + method,true);
					xmlhttp.send();
					}			

					function renew(id)
					{
					rere.disabled = true;
					document.getElementById("div").style.display="none";
					
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						rere.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("image").style.display="none";
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=renew" + "&id=" + id,true);
					xmlhttp.send();
					}

					function stop(id)
					{
					st.disabled = true;
					document.getElementById("div").style.display="none";
					
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						st.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("image").style.display="none";
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=stop" + "&id=" + id,true);
					xmlhttp.send();
					}

					function attacks()
					{
					document.getElementById("attacksdiv").style.display="none";
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						document.getElementById("attacksimage").style.display="none";
						document.getElementById("attacksdiv").style.display="inline-block";
						document.getElementById("attacksdiv").style.width="100%";
						eval(document.getElementById("Modules").innerHTML);
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=attacks",true);
					xmlhttp.send();
					}

					function adminattacks()
					{
					document.getElementById("attacksdiv").style.display="none";
					document.getElementById("attacksimage").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						document.getElementById("attacksimage").style.display="none";
						document.getElementById("attacksdiv").style.display="inline-block";
						document.getElementById("attacksdiv").style.width="100%";
						eval(document.getElementById("Modules").innerHTML);
						}
					  }
					xmlhttp.open("GET","Modules/hub.php?type=adminattacks",true);
					xmlhttp.send();
					}
					</script>

                    <!--begin::Main-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid">
							<!--begin::Toolbar-->
							<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
								<!--begin::Toolbar container-->
								<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
								</div>
								<!--end::Toolbar container-->
							</div>
							<!--end::Toolbar-->
                
    <div id="divall" style="display:inline"></div>
	<div id="div" style="display:inline"></div>
	<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
     <div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Attack Hub</h3>
    </div>
    <div class="card-body">
    <div class="fv-row mb-3">
    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
	<span class="required">IP Address</span>
	</label>
	<input type="text" id="host" placeholder="0.0.0.0" class="form-control form-control-solid">
	</div>
    <div class="fv-row mb-3">
    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
	<span class="required">Port</span>
	</label>
	<input  id="port" placeholder="80" class="form-control form-control-solid ">
	</div>
    <div class="fv-row mb-3">
    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
	<span class="required">Time</span>
	</label>
	<input  id="time" placeholder="Max time is: <?php echo $rowxd[mbt]; ?>" class="form-control form-control-solid">
	</div>
    <div class="fv-row mb-3">
    <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
	<span class="required">Method</span>
	</label>
    <div class="fv-row mb-3">
	<select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Methods" id="method">
    <optgroup label="Amplification" style="color:#BC395C";>
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'amp' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
                                        $type = $getInfo['type'];

											echo '<option value="' .htmlentities($name) . '">' . htmlentities($fullname) . '</option>';
									}
									?>
                                    </optgroup>
                                    <optgroup class="fw-bold" label="Raw Methods" style="color:#BC395C";>
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'raw' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
											echo '<option value="' .htmlentities($name) . '">' . htmlentities($fullname) . '</option>';
									}
									?>
									</optgroup>
                                    <optgroup label="Game Methods" style="color:#BC395C";>
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'game' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
											echo '<option value="' .htmlentities($name) . '">' . htmlentities($fullname) . '</option>';
									}
									?>
									</optgroup>
									<optgroup label="Bypass Methods" style="color:#BC395C";>
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'bypass' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];

	                                	echo '<option value="' .htmlentities($name) . '">' . htmlentities($fullname) . '</option>';		

	                                }
									?>					

	                                   <optgroup label="VIP Methods" style="color:#BC395C";>
	                 				<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'vip' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];

									if ($user -> HasVIP($odb)){ 

	                                	echo '<option value="' .htmlentities($name) . '">' . htmlentities($fullname) . '</option>';
	                                	}
									}
									  ?>
									</optgroup>
</select>
</div>
</div>
                <div class="form-group form-actions">
                    <div class="col-md-12">
<button type="button" id="launch" onclick="start()"  class="btn btn-block btn-light-primary waves-effect waves-light" >Launch Attack</button>




</div>
				   </div>
				    </div>
					 </div>
                     </div>
                     <div class="col-md-6 grid-margin stretch-card">
                     <div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Attack Logs</h3>
        <div class="card-toolbar">
        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_card">
                Action
            </button>
        </div>
    </div>
                 <div>
				</div>
				<div style="position: relative; width: auto" class="slimScrollDiv">
			   <div id="attacksdiv" style="display:inline-block;width:100%"></div>										
			    </div>
				  </div>
                    </div>
                       </div>
					    </div>
                        <div class="modal fade" id="kt_modal_new_card" tabindex="-1" aria-hidden="true">
			<!--begin::Modal dialog-->
			<div class="modal-dialog modal-dialog-centered mw-650px">
				<!--begin::Modal content-->
				<div class="modal-content">
					<!--begin::Modal header-->
					<div class="modal-header">
						<!--begin::Modal title-->
						<h2>Users Logs</h2>
						<!--end::Modal title-->
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
							<span class="svg-icon svg-icon-1">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
									<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Close-->
					</div>
					<!--end::Modal header-->
					<!--begin::Modal body-->
					<div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
						<!--begin::Form-->
							<!--begin::Input group-->
							<div class="d-flex flex-column mb-7 fv-row">
								<!--begin::Label-->
								<div class="col-lg-12  layout-spacing">
<div class="table-responsive">
    <table class="table">
    <thead>
   
        <tr>
  <br>
            <th class="text-center"> Target</th>
            <th class="text-center"> Port</th>
            <th class="text-center"> Method</th>                
            <th class="text-center"> Expires</th>
            <th class="text-center"> Handler</th>

        </tr>
    </thead>
    <tbody>
<?php 
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT 100");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $ip      = $show['ip'];
        $port    = $show['port'];
        $time    = $show['time'];
        $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID   = $show['id'];
        $date    = $show['date'];
        $handler = $show['handler'];
        $dios    = htmlspecialchars($ip);
        $expires = $date + $time - time();
        if ($expires < 0 || $show['stopped'] != 0) {
            $countdown = "Expired";
        } else { 
            $countdown = '<div id="a' . $rowID . '"></div>';
            echo '
<script id="ajax">
var count=' . $expires . ';
var counter=setInterval(a' . $rowID . ', 1000);
function a' . $rowID . '()
{
  count=count-1;
  if (count <= 0)
  {
     clearInterval(counter);
     attacks();
     return;
  }
 document.getElementById("a' . $rowID . '").innerHTML=count;
}
</script>
';
      } 
           ?>      
           
           <tr>
          
            <td><center><?php echo $dios ?></center></td>
            <td><center><?php echo $port ?></center></td>
            <td><center><?php echo $method ?></center></td>
            <td><center><?php echo $countdown ?></center></td>
            <td><center><?php echo $handler ?></center></td>
      
            
           </tr>
   <?php
   }
?> 
</tbody>
</table>
</div>
</div>
</div>
</div>
							</div>
							<!--end::Input group-->
						<!--end::Form-->
					</div>
					<!--end::Modal body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
		</div>
</div>		