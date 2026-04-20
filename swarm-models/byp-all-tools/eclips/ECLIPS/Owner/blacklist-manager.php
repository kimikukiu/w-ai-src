<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
if (isset($_POST['deleteblacklist'])){
    $delete = $_POST['deleteblacklist'];
    $SQL = $odb -> query("DELETE FROM `blacklist` WHERE `ID` = '$delete'");
    success('Blacklist has been removed');
}

if (isset($_POST['addblacklist'])){

    if (empty($_POST['value'])){
        error('Please verify all fields');
    }

    $value = $_POST['value'];
    $type = $_POST['type'];

    if (empty($error)){	
        $SQLinsert = $odb -> prepare("INSERT INTO `blacklist` VALUES(NULL, :value, :type)");
        $SQLinsert -> execute(array(':value' => $value, ':type' => $type));
        success('Blacklist has been added');
    }
    else{
        error($error);
    }
}
  ?>
<div class="clearfix"></div>
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
<?php if (isset($error)) { echo $error; }elseif(isset($notify)) { echo htmlspecialchars($notify); } ?>
<div class="row">
<div class="col-lg-5">
<div class="card ">
<div class="card-header ">
<h3 style="color: white;" class="card-title"><i class="fa fa-ban"></i> Blacklist</h3>
</div>
<div class="card-body">
<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="name">Host</label>
										<input class="form-control" type="text" id="name" name="value">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="type">Type</label>
										<select class="form-control" id="type" name="type" size="1">
											<option value="victim">Host</option>
										</select>
										
									</div>
								</div>
							</div>
                            <br>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addblacklist" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
						</div></div></div>
						<div class="col-lg-7">
<div class="card">
<div class="card-header">
<h3 style="color: white;" class="card-title"><i class="fa fa-ban"></i> Blacklist Logs</h3>
</div>
<div class="card-body">
<div class="table-responsive">
		<table class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer">
						<tr>
							<th style="font-size: 12px;">Value</th>
							<th style="font-size: 12px;">Type</th>
							<th style="font-size: 12px;">Delete</th>
						</tr>
						<tr>
							<form method="post">
								<?php
								$SQLGetMethods = $odb -> query("SELECT * FROM `blacklist`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['ID'];
									$value = $getInfo['data'];
									$type = $getInfo['type'];
									echo '<tr>
											<td style="font-size: 16px;"><strong><span class="badge badge-primary">'.htmlspecialchars($value).'</span></strong></td>
											<td style="font-size: 16px;"><strong><span class="badge badge-danger">'.htmlspecialchars($type).'</span></strong></td>
											<td style="font-size: 16px;"><button name="deleteblacklist" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></td>
										</tr>';
								}
								if(empty($SQLGetMethods)){
									echo error('No Blacklists');
								}
								?>
							</form>
						</tr>                                       
					</table>
						</div></div></div></div>
        </div>
    </div>