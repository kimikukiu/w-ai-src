<?php




	ob_start();

	require_once '../../complex/configuration.php';

	require_once '../../complex/init.php';



	if (!empty($maintaince)){

		die();

	}

	

	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){

		die();

	}



	$id = anti_injection(htmlspecialchars($_GET['id']));

	

	if(is_numeric($id) == false) {

		header('Location: support.php');

		exit;

	}

	

	if ($user -> safeString($id)){

		die(error('Unsafe characters were set'));

        $error = "xd";

	}

	

	if(empty($error)){

	 $SQLFind = $odb -> prepare("SELECT `status` FROM `tickets` WHERE `id` = :id");

	 $SQLFind -> execute(array(':id' => $id));

	

	 if($SQLFind->fetchColumn(0) == "Closed"){

		die(error('Ticket is already closed'));

	 }

	

	 $SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");

	 $SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));

	 die(success('Ticket has been closed successfuly'));

	}

	

?>