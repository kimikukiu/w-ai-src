<?php
include("header.php");
include("fuctions/pinit.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../home.php');
	die();
}
?>
<!DOCTYPE html>
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
			<div class="card-body">
					
<table id="datatable" class="table dt-responsive  nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Rank</th>
                                                <th>Membership</th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
{
	$id = $getInfo['ID'];
	$user = $getInfo['username'];
	$email = $getInfo['email'];
	if ($getInfo['expire']>time()) {$plan = $odb -> query("SELECT `plans`.`name` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = '$id'") -> fetchColumn(0);} else {$plan='No membership';}
	$rank = $getInfo['rank'];
		if ($rank == 102464)
		{
			$rank = 'Admin';
		}
		else
		{
			$rank = 'Member';
		}
	echo '<tr><td>'.htmlspecialchars($id).'</td><td><a href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td><td>'.htmlspecialchars($email).'</td><td>'.htmlspecialchars($rank).'</td><td>'.htmlspecialchars($plan).'</td></tr>';
}
?>											
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>