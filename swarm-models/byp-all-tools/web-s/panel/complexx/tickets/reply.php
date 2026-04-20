<?php


		if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}

	ob_start();

	require_once '../../complex/configuration.php';

	require_once '../../complex/init.php';



	if (!empty($maintaince)){

		die();

	}

	

	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){

		die();

	}



	$updatecontent = anti_injection(htmlspecialchars($_GET['message']));

	$id = anti_injection(htmlspecialchars($_GET['id']));

	

	if(is_numeric($id) == false) {

		header('Location: ../../support.php');

		exit;

	}



	if (empty($updatecontent) || empty($id)){

		die(error('You need to enter a reply'));

	}

	

	if ($user -> safeString($updatecontent) || $user -> safeString($id)){

		die(error('Unsafe characters were set'));

        $error = "xd";

	}

	

	$SQLClosed = $odb -> query("SELECT `status` FROM `tickets` WHERE `id` = '$id'");

	if($SQLClosed->fetchColumn(0) == "Closed"){

		die(error('The ticket has been closed'));

	}

	

	$i = 0;

	$SQLGetMessages = $odb -> query("SELECT * FROM `messages` WHERE `ticketid` = '$id' ORDER BY `messageid` DESC LIMIT 1");

	

	while ($getInfo = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC)){

		if ($getInfo['sender'] == 'Client'){

			$i++;

		}

	}

	

	if ($i >= 1){

		die(error('Please wait for an admin to respond until you send a new message'));

	}

	

	$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender, UNIX_TIMESTAMP())");

	$SQLinsert -> execute(array(':sender' => 'Client', ':content' => $updatecontent, ':ticketid' => $id));

	

	$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");

	$SQLUpdate -> execute(array(':status' => 'Waiting for admin response', ':id' => $id));

	die(success('Message has been sent'));

	

?>