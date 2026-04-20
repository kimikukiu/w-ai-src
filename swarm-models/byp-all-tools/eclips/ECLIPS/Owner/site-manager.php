<?php
include("header.php");
include("fuctions/init.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../home.php');
	die();
}
?>

<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />								
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
              <div class="row">
<div class="col-lg-12">
  <div class="card">
  <div class="card text-white bg-primary">
    <div class="card-header text-center">General Settings</div>
</div>
</div>

			                              <div class="card-body">
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="sitename" value="<?php echo htmlspecialchars($sitename); ?>"/></div>
                                    </div>
									<div class="card-header">
									<div  class="col-xs-4 text-center" >
                                     <button name="update" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>					                         
                            </div>
                          </div>  
                           <br>	
                           <div class="col-lg-12">
  <div class="card">
  <div class="card text-white bg-primary">
    <div class="card-header text-center">HUB/API Settings</div>
</div>
</div>
<br>
                                <div class="block-content controls">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Attack Slots:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="insert 0 to disable" name="maxattacks" value="<?php echo htmlspecialchars($maxattacks); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Attack System:</strong></div>
                                        <div class="col-md-12">
                                            <select name="system" class="form-control">
                                                <option value="api" <?php if ($system == 'api') { echo 'selected'; } ?>>API</option>
                                                <option value="servers" <?php if ($system == 'servers') { echo 'selected'; } ?>>Servers</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Rotation:</strong></div>
                                        <div class="col-md-12"><input type="checkbox" name="rotation" <?php if ($rotation == 1) { echo 'checked'; } ?> /></div>
                                    </div>
                                  <div class="card-body">
                                    <div  class="col-xs-4 text-center" >
                                     <button name="update" class="btn btn-primary" >Update</button>
                                </div> 
                              </div>
                                </div>                               
                            </div>
                             </div>                                  
                               </div>
                                 </div>                                                             
                                   </body>
</div>

</html>