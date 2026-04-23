<?php

	ob_start(); 
	require '../Class/database.php';
	require '../Class/init.php';

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$userID = (is_numeric($_GET['user']) ? htmlspecialchars($_GET['user']) : die(redeemerror('There Was An Error, Please Try Again Later!')));
	$code = htmlspecialchars($_GET['code']);
	
	if (empty($code) || empty($userID)){
        die(redeemerror('Please enter a vaild Token'));
		return false;
	}

	if ($user -> safeString($userID) || $user -> safeString($code)){
        die(redeemerror('Unsafe characters were set'));
	}
	
		$SQL = $odb -> prepare("SELECT `claimedby` FROM `tokens` WHERE `code` = :code");
		$SQL -> execute(array(':code' => $code));
		$status = $SQL -> fetchColumn(0);
		if (!($status == 0)){
            die(redeemerror('Seems like this Token has been redeemed'));
		}

		$SQL2 = $odb -> prepare("SELECT * FROM `tokens` WHERE `code` = :code");
		$SQL2-> execute(array(':code' => $code));
		$check = $SQL2 -> fetch();
		$checkcode =$check['code'];

		if (!($checkcode == $code)){
			die(redeemerror('Sorry this Token does not exist'));
		}

	// Update Status of Token System
	$SQLUpdate = $odb -> prepare("UPDATE `tokens` SET `claimedBy` = :userID, `dateClaimed` = UNIX_TIMESTAMP() WHERE `code` = :code");
	$SQLUpdate -> execute(array(':userID' => $userID, ':code' => $code));
	
	// Update User Account with new Plan
	$SQL = $odb -> prepare("SELECT `planID` FROM `tokens` WHERE `code` = :code");
	$SQL -> execute(array(':code' => $code));
	$planID = $SQL -> fetchColumn(0);
	
	$SQL = $odb -> prepare("SELECT * FROM `plans` WHERE `ID` = :id");
	$SQL -> execute(array(':id' => $planID));
	$plan = $SQL -> fetch();
	
	$planName = $plan['name'];
	$unit = $plan['unit'];
	$length = $plan['length'];
	
	$newExpire = strtotime("+{$length} {$unit}");
	$updateSQL = $odb -> prepare("UPDATE `users` SET `membership` = :plan, `expire` = :expire WHERE `ID` = :id");
	$updateSQL -> execute(array(':plan' => (int)$planID, ':expire' => $newExpire, ':id' => (int)$userID));
	die(redeemsucess('You have redeemed a Token your plan has been added! <meta http-equiv="refresh" content="3;url=token-system.php">'));
?>