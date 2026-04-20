<?php
include("header.php");

if (!($user -> isAdmin($odb)))
{
	header('location: ../home.php');
	die();
}
	
	if(isset($_POST['createnewCard']))
	{
		$plan = $_POST['plan'];

		if(empty($plan))
		{
			$notify = error('Plan input was empty!');
		}

		if(empty($notify))
		{
			 /// Generate Gift Code
			$code = bin2hex(random_bytes(16));
			
			 /// Input to database
			$SQLinsert = $odb -> prepare("INSERT INTO `tokens` VALUES(NULL, :code, :planID, 0, 0, UNIX_TIMESTAMP())");
			$SQLinsert -> execute(array(':code' => $code, ':planID' => $plan));	

			$notify = ' <div class="alert alert-success border-2 d-flex align-items-center"></button><strong></strong>New Token has been generated '.htmlentities($code).'</p>
		  </div>';
		}	
	}
?>


    <body>	
    <div class="main-content">

<div class="page-content">
    <div class="container-fluid">


                            <!-- start page title -->
                            <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><?php echo htmlspecialchars($sitename); ?></h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
                <!-- START PAGE CONTENT -->
				<?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
                <div class="card-body">
                <div class="table-responsive">
   					   <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 style="color: white;" class="card-title">Token Generator</h3>
                                <div>
		          	                        		<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="plan">Plan</label>
										<select class="form-control" id="plan" name="plan">
											<?php
											$SQLGetMethods = $odb -> query("SELECT * FROM `plans`");
											while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
												$ID = $getInfo['ID'];
												$name = $getInfo['name'];
												echo '<option value="'.htmlentities($ID).'">'.htmlentities($name).'</option>';
											}
											?>
										</select>
										
									</div>
								</div>
							</div>
							<br>
							<div class="form-group">
								<div class="col-sm-12">
									<button name="createnewCard" value="do" class="btn btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
                                </div>
                            </div>
                        </div>
                    </div>
					<br>
										   <div class="col-lg-12">
                       <div class="card"> 
                           <div class="card-body">
                                <h3 style="color: white;" class="card-title"></i>Eclipse Tokens</h3>
                                <div>
								<div class="table-responsive">
			          	<table class="table">
						<tr>
							<th style="font-size: 12px;">Code</th>
							<th class="text-center" style="font-size: 12px;">Plan</th>
							<th class="text-center" style="font-size: 12px;">Claimed By</th>
							<th class="text-center" style="font-size: 12px;">Date Claimed</th>
							<th class="text-center" style="font-size: 12px;">Date Created</th>
						</tr>
						<tr>
						<?php
							$SQLSelect = $odb -> query("SELECT * FROM `tokens` ORDER BY `ID` ASC LIMIT 200");
							while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
							{
								$ID = $show['unit'];
								$code = $show['code'];
								$planID = $show['planID'];
								$claimedby = $show['claimedby'];
								$status = $show['status'];
								$dateClaimed = $show['dateClaimed'];
								$date = $show['date'];
								if(!($dateClaimed == "0"))
								{
									$dateClaimed = date("m-d-Y, h:i:s a" , $dateClaimed);
								}
								if($claimedby == "0") { $claimedby = "Unclaimed"; }
								if($dateClaimed == "0") { $dateClaimed = "Unclaimed"; }
								$date = date("m-d-Y, h:i:s a" , $date);
								$plan = $odb->query("SELECT `name` FROM `plans` WHERE `ID` = '$planID'")->fetchColumn(0);
								$usernName = $odb->query("SELECT `username` FROM `users` WHERE `ID` = '$claimedby'")->fetchColumn(0);
								echo '<tr">
										<td class="text-center" style="font-size: 12px;">'.htmlentities($code).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($plan).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($usernName).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($dateClaimed).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($date).'</td>
									</tr>';
							
							} 
							?>
									</tr>                                       
					</table>
                            </div>
                        </div>
                           
				<!-- end container -->
					<?php include htmlspecialchars('../footer.php'); ?>

                </div>
                <!-- End #page-right-content -->

                <div class="clearfix"></div>

    </body>
</html>