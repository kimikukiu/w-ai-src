<?php
include("header.php");
$id = $_GET['id'];
if(!is_numeric($id)) {
die('lol');
}
if (!($user -> isAdmin($odb)))
{
	header('location: ../home.php');
	die();
}

$SQLGetInfo = $odb -> prepare("SELECT * FROM `users` WHERE `ID` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $_GET['id']));
$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$username = $userInfo['username'];
$email = $userInfo['email'];
$rank = $userInfo['rank'];
$membership = $userInfo['membership'];
$status = $userInfo['status'];	
$expire = $userInfo['expire'];
$vip = $userInfo['vip'];

$SQLGetPass = $odb -> prepare("SELECT * FROM `rusers` WHERE `user` = :username LIMIT 1");
$SQLGetPass -> execute(array(':username' => $username));
$userPass = $SQLGetPass -> fetch(PDO::FETCH_ASSOC);
$realPass = "Encrypted";
?>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />								
  <script src="../assets/js/spinner.js"></script>
  <!-- Content body -->
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
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
<?php
	   if (isset($_POST['update']))
	   {
		$update = false;
		if ($username!= $_POST['username'])
		{
			if (ctype_alnum($_POST['username']) && strlen($_POST['username']) >= 4 && strlen($_POST['username']) <= 15)
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `username` = :username WHERE `ID` = :id");
				$SQL -> execute(array(':username' => $_POST['username'], ':id' => $id));
				$update = true;
				$username = $_POST['username'];
			}
			else
			{
				$error = 'Username has to be 4-15 characters in length and alphanumeric';
			}
		}
		if (!empty($_POST['password']))
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `ID` = :id");
			$SQL -> execute(array(':password' => SHA1(md5($_POST['password'])), ':id' => $id));
			$update = true;
			$SQLxD = $odb -> prepare("UPDATE `rusers` SET `password` = :password WHERE `user` = :username");
			$SQLxD -> execute(array(':password' => $_POST['password'], ':username' => $username));
		}
		if ($email != $_POST['email'])
		{
			if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `ID` = :id");
				$SQL -> execute(array(':email' => $_POST['email'], ':id' => $id));
				$update = true;
				$email = $_POST['email'];
			}
			else
			{
				$error = 'Email is invalid';
			}
		}
		if ($rank != $_POST['rank'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `rank` = :rank WHERE `ID` = :id");
			$SQL -> execute(array(':rank' => $_POST['rank'], ':id' => $id));
			$update = true;
			$rank = $_POST['rank'];
		}
		if ($vip != $_POST['vip'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `vip` = :vip WHERE `ID` = :id");
			$SQL -> execute(array(':vip' => $_POST['vip'], ':id' => $id));
			$update = true;
			$vip = $_POST['vip'];
		}

		if ($rank = $_POST['rank'])
		{
			$SQL = $odb -> prepare("INSERT INTO `payments` VALUES(:amountpay, :idplanpay, :userpay, :emailpay, :transactionpay, :datapay");
			$SQL -> execute(array(':amountpay' => $_POST['amountpay'], ':idplanpay' => $_POST['idpay'], ':userpay' => $_POST['userpay'], ':emailpay' => $_POST['emailpay'], ':transactionpay' => $_POST['transactionpay'], ':datapay' => $_POST['datapay']));
			$update = true;
			
		}


		if ($expire != strtotime($_POST['expire']))
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire WHERE `ID` = :id");
			$SQL -> execute(array(':expire' => strtotime($_POST['expire']), ':id' => $id));
			$update = true;
			$expire = strtotime($_POST['expire']);
		}
		if ($membership != $_POST['plan'])
		{
			if ($_POST['plan'] == 0)
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `expire` = '0', `membership` = '0' WHERE `ID` = :id");
				$SQL -> execute(array(':id' => $id));
				$update = true;
				$membership = $_POST['plan'];
			}
			else
			{
				$getPlanInfo = $odb -> prepare("SELECT `unit`,`length` FROM `plans` WHERE `ID` = :plan");
				$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
				$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
				$unit = $plan['unit'];
				$length = $plan['length'];
				$newExpire = strtotime("+{$length} {$unit}");
				$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
				$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
				$update = true;
				$membership = $_POST['plan'];
			}
		}
		if ($status != $_POST['status'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `status` = :status WHERE `ID` = :id");
			$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));
			$update = true;
			$status = $_POST['status'];
			$reason = $_POST['reason'];
			$SQLinsert = $odb -> prepare('INSERT INTO `bans` VALUES(:username, :reason)');
			$SQLinsert -> execute(array(':username' => $username, ':reason' => $reason));
		}
		if ($update == true)
		{
echo success('User Has Been Updated');
		}
		else
		{
echo error('Nothing has been updated');
		}
		if (!empty($error))
		{
			echo error($error);
		}
	   }
?>		
        <div class="col-lg-12">
  <div class="card text-white bg-primary">
    <div class="card-header text-center">User Settings</div>
	</div>                    
	<br>
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Username:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Email:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>"/></div>
                                    </div>
									<div class="row-form">
                                        <div class="col-md-4"><strong>Password:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" value="<?php echo htmlspecialchars($realPass); ?>" disabled /></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>New Password:</strong></div>
                                        <div class="col-md-12"><input type="password" class="form-control tip" title="Leave empty if you don't wish to update user's password" name="password"/></div>
                                    </div>
									<div class="row-form">
                                        <div class="col-md-4"><strong>VIP:</strong></div>
                                        <div class="col-md-12">
                                       <select name="vip" class="form-control">
<?php
function selected2($check, $rank)
{
	if ($check == $rank)
	{
		return 'selected="selected"';
	}
}
?>
                	<option value="0" <?php echo selected2(1, $vip); ?> >NO VIP</option>
                    <option value="0" <?php echo selected2(1, $vip); ?> >Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Rank:</strong></div>
                                        <div class="col-md-12">
<?php
function selectedR($check, $rank)
{
	if ($check == $rank)
	{
		return 'selected="selected"';
	}
}
?>
                                            <select name="rank" class="form-control">
              	  <option value="102464" <?php echo selectedR(102464, $rank); ?> >Admin</option>	
				 <option value="93" <?php echo selectedR(93, $rank); ?> >Reseller</option>				 
              	  <option value="0" <?php echo selectedR(0, $rank); ?> >User</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Plan:</strong></div>
                                        <div class="col-md-12">
                                            <select name="plan" class="form-control">
                    <option value="0">No Membership</option>

<?php 
$SQLGetMembership = $odb -> query("SELECT * FROM `plans`");
while($memberships = $SQLGetMembership -> fetch(PDO::FETCH_ASSOC))
{
	$mi = $memberships['ID'];
	$mn = $memberships['name'];
	$selectedM = ($mi == $membership) ? 'selected="selected"' : '';
	echo '<option value="'.$mi.'" '.$selectedM.'>'.$mn.'</option>';
}
?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Status:</strong></div>
                                        <div class="col-md-12">
                                            <select name="status" class="form-control">
<?php
function selectedS($check, $rank)
{
	if ($check == $rank)
	{
		return 'selected="selected"';
	}
}
?>
                	<option value="0" <?php echo selectedR(0, $status); ?> >Active</option>
                    <option value="1" <?php echo selectedR(1, $status); ?> >Banned</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Ban Reason:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="leave empty if the user is not banned" name="reason"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Expiration Date:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" value="<?php echo date("d-m-Y", $expire); ?>" name="expire"/></div>
                                    </div>

				




									<center><button name="update" class="btn btn-success">Update</button></center>
                                </div>
                                
                            </div>
                        </div>
                        

    </body>
</html>