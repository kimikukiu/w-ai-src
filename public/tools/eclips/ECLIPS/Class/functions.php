<!-- Functions - Rank, API, Stats, membership -->
<?php
function getRealIpAddr(){
    if(!empty($_SERVER['HTTP_CF_CONNECTING_IP'])){
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
    $_SERVER['REMOTE_ADDR'] = getRealIpAddr();
	function checkSession($odb)
	{
		if ($_SERVER['REMOTE_ADDR'] != $odb->query("SELECT `ip` FROM `loginlogs` WHERE `username` = '{$_SESSION['username']}'")->fetchColumn(0))
		{
			unset($_SESSION['username']);
			unset($_SESSION['ID']);
			session_destroy();
			header('location: /cloud-login.php');
		}
	}

	$newssql = $odb -> query("SELECT * FROM `api` LIMIT 0,30");
	while($row = $newssql ->fetch()){
		$name = $row['name'];	
	}				

class user
{
	function isAdmin($odb)
	{
		$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$rank = $SQL -> fetchColumn(0);
		if ($rank == 102464)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function availableuser($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $user));
		$count = $SQL -> fetchColumn(0);
		if ($count == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function LoggedIn()
	{
		@session_start();
		if (isset($_SESSION['username'], $_SESSION['ID']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function hasVIP($odb)
	{
		$SQL = $odb -> prepare("SELECT `vip` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$vip  = $SQL -> fetchColumn(0);
		if ($vip == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function hasMembership($odb)
	{
		$SQL = $odb -> prepare("SELECT `expire` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$expire = $SQL -> fetchColumn(0);
		if (time() < $expire)
		{
			return true;
		}
		else
		{
			$SQLupdate = $odb -> prepare("UPDATE `users` SET `membership` = 0 WHERE `ID` = :id");
			$SQLupdate -> execute(array(':id' => $_SESSION['ID']));
			return false;
		}
	}
	function notBanned($odb)
	{
		$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$result = $SQL -> fetchColumn(0);
		if ($result == 0)
		{
			return true;
		}
		else
		{
			session_destroy();
			return false;
		}
	}
	
	
	function safeString($string)
	{
		$parameters = array("<script", "alert(", "<iframe", ".css", ".js", "<meta", ">", "*", ";", "<", "<frame", "<img", "<embed", "<xml", "<IMG", "<SCRIPT", "<IFRAME", "<META", "<FRAME", "<EMBED", "<XML");
		foreach ($parameters as $parameter)
		{
			if (strpos($string,$parameter) !== false)
			{
				return true;
			}
		}
	}
}
class stats
{
	function totalUsers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `users`");
		return $SQL->fetchColumn(0);
	}
	function activeUsers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `users` WHERE `expire` > UNIX_TIMESTAMP()");
		return $SQL->fetchColumn(0);
	}
	function totalBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs`");
		return $SQL->fetchColumn(0);
	}
	function totalmethods($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `methods`");
		return $SQL->fetchColumn(0);
	}
	function totalplans($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `plans`");
		return $SQL->fetchColumn(0);
	}
	function runningBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		return $SQL->fetchColumn(0);
	}
	function concurrents($odb)
	{
		$SQL = $odb -> prepare("SELECT `plans`.`concurrents` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		return $SQL->fetchColumn(0);
	}
	function countRunning($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username  AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$SQL -> execute(array(':username' => $user));
		return $SQL->fetchColumn(0);
	}
	function totalBootsForUser($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :user");
		$SQL -> execute(array(':user' => $user));
		return $SQL->fetchColumn(0);
	}
	function serversonline($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `api`");
		return $SQL->fetchColumn(0);
	}
	function usersforplan($odb, $plan)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `membership` = :membership");
		$SQL -> execute(array(":membership" => $plan));
		return $SQL->fetchColumn(0);
	}
}
?>