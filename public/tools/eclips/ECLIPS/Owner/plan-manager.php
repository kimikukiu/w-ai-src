<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-12" />								
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
</div>
			  <?php if (isset($_POST['addplan']))
{
			$name = $_POST['name'];
			$unit = $_POST['unit'];
			$length = $_POST['length'];
			$mbt = intval($_POST['mbt']);
			$price = floatval($_POST['price']);
			$concurrents = $_POST['concurrents'];
			$private = $_POST['private'];
            $apiaccess = $_POST['apiaccess'];
			$errors = array();
			
			if (empty($price) || empty($name) || empty($unit) || empty($length) || empty($mbt) || empty($concurrents))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :mbt, :unit, :length, :price, :concurrents, :private, :apiaccess)");
				$SQLinsert -> execute(array(':name' => $name, ':mbt' => $mbt, ':unit' => $unit, ':length' => $length, ':price' => $price, ':concurrents' => $concurrents, ':private' => $private, ':apiaccess' => $apiaccess));
				echo success('Plan has been added');
			}
			else
			{
				echo error($error);
			}
} ?>			  
              <div class="card">
			       <div class="card">
                                <div class="card-header card-title">Available plans</div>
	                            </div>
                 <table class="table zero-configuration">
                <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Max Boot Time</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Length</th>
                        <th class="text-center">Concurrents</th>
                        <th class="text-center">Private</th>
                        <th class="text-center">Apiaccess</th>
                  </tr
                </thead>
                <tbody>
				<form method="post">
<?php
$SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
{
	$unit = $show['unit'];
	$price = $show['price'];
    $length = $show['length'];
	$concurrents = $show['concurrents'];
	$planName = $show['name'];
	$mbtShow = $show['mbt'];
	$id = $show['ID'];
	if ($show['private'] == 0) { $private = 'No'; } else { $private = 'Yes'; }
 if ($show['apiaccess'] == 0) { $apiaccess = 'No'; } else { $apiaccess = 'Yes'; }
	echo '<tr><td><a href="plan.php?id='.$id.'"><center>'.htmlspecialchars($planName).'</center></a></td><td><center>'.$mbtShow.' Seconds</center></td><td><center>$'.htmlentities($price).'</center></td><td><center>'.htmlentities($length).' '.htmlentities($unit).'</center></td><td><center>'.htmlentities($concurrents).'</center></td><td><center>'.htmlentities($private).'</center></td><td><center>'.htmlentities($apiaccess).'</center></td></tr>';
}

?>
</form>
   </table>
                                        </div>
</div>	
<br>										
<div class="row">
                        <div class="col-md-12">
                            <div class="card">
							<div class="card-header">
      <h4 class="card-title">Plan Manager</h4>
    </div>
                                <div class="card-body">
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="name"/></div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Duration:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="mbt"/></div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Concurrents:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="concurrents"/></div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="price"/></div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Length:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="length"/></div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Unit:</strong></div>
                                        <div class="col-md-12">
                                            <select name="unit" class="form-control">
                                                <option value="days">Days</option>
												<option value="weeks">Weeks</option>
                                                <option value="months">Months</option>
												<option value="years">Years</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Private:</strong></div>
                                        <div class="col-md-12">
                                            <select name="private" class="form-control">
                                                <option value="1">Yes</option>
												<option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>API Access:</strong></div>
                                        <div class="col-md-12">
                                            <select name="apiaccess" class="form-control">
                                                <option value="1">Yes</option>
												<option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    
									<div class="card-header">
									<div  class="col-xs-4 text-center" >
                                     <button name="addplan" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
									</form>
                                </div>