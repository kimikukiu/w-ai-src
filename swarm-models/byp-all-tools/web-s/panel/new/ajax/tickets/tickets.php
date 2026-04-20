<?php


	

	ob_start();

	require_once '../../complex/configuration.php';

	require_once '../../complex/init.php';


    $id = anti_injection($_GET['id']);

	

	if(is_numeric($id) == false) {

		header('Location: support.php');

		exit;

	}



	if (empty($id)){

		die(error('You need to enter a reply'));

	}

	

	if ($user -> safeString($id)){

		die(error('Unsafe characters were set'));

        $error = "xd";

	}

    if(empty($error)){

	$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");

	$SQLGetMessages -> execute(array(':ticketid' => $id));



	while ($show = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC)){

		$class = "";

		if($show['sender'] == "Admin"){



			$class = 'class="blockquote-reverse"';



			$username = 'Administrator';



		}

		echo '



			<blockquote '. $class .'>



				<h5>'. $show['content'] .'</h5>



				<footer>'. $show['sender'] .' [ '. date('d-m-Y h:i:s a', $show['date']) .' ]</footer>



			</blockquote>



		';



	}

	}



	



?>