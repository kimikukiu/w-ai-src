<?php


	$GLOBALS['hmjhheo'] = 'mkbeoneo';
	$GLOBALS['ghuflzo'] = 'odb';
	$GLOBALS['qcdvprmpulg'] = 'txtbestand';
	$GLOBALS['mylkjibtdfh'] = 'licentiecode';
	$GLOBALS['melnodh'] = 'txtbestand';
	${$GLOBALS['hmjhheo']} = 'licentiecode';
	${$GLOBALS['mylkjibtdfh']} = 'mylicense';
	${$GLOBALS['melnodh']} = file_get_contents( '' );

	if ($${$GLOBALS['hmjhheo']}  = ${$GLOBALS['qcdvprmpulg']}) {
	} 
else {
		
	}





	if (!isset( $_SERVER['HTTP_REFERER'] )) {
		exit(  );
	}

	ob_start(  );
	require_once( 'inc/configuration.php' );
	require_once( 'inc/init.php' );

	if (( !$user->LoggedIn(  ) || !$user->notBanned( ${$GLOBALS['ghuflzo']} ) )) {
		exit(  );
	}

?>
	<div class="table-responsive">
<table class="table table-bordered table-striped mb-0">
	<thead>
        <tr>
			<th class="text-center" style="font-size: 12px;">Server Name</th>
            <th class="text-center" style="font-size: 12px;">Slots</th>
            <th class="text-center" style="font-size: 12px;">Server Status</th>
			
        </tr>
	</thead>
    <tbody>
	
<?php
		function ping($host, $port, $timeout) 
         { 
         $tB = microtime(true); 
         $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
         if (!$fP) { return "Down!"; } 
         $tA = microtime(true); 
         return round((($tA - $tB) * 1000), 0)." ms"; 
         }

		$SQLGetInfo = $odb->query("SELECT * FROM `api` ORDER BY `lastUsed` DESC LIMIT 6");
		while ($getInfo = $SQLGetInfo->fetch(PDO::FETCH_ASSOC)) {
		                             $name    = $getInfo['name'];
									 $vip = $getInfo['vip'];
									 $api = $getInfo['api'];
									 $status = $getInfo['status'];
									
									if($vip == "0")
									{
										$vip = '<b class=""> Normal</b>';
									}elseif($vip == "1")
									{
										$vip = '<b class=""> Private Network</b>';
									}elseif($vip == "3")
									{
										$vip = '<b class=""> AdminTester Network</b>';
									}

									if($status == "0")
									{
										$status = '<span class="text-danger"></span> <b class="text-danger">Disabled</b>';
									}elseif($status == "1")
									{
										$status = '<span class="text--success"></span> <b class="text-success">Enabled</b>';
									}elseif($status == "2")
									{
										$status = '<span class=" text--info"></span> <b class="text-info">Maintence</b>';
									}
									
									$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
		          $attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
		$load    = round($attacks / $getInfo['slots'] * 100, 2);
	
		 
		if ($load >= 0 and $load <= 30 )
			{
  $ripx = '<div class="progress progress-lg progress-half-rounded m-2">
										<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' . $load . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $load . '%;">
											' . $load . '%
										</div>
									</div>';
			}elseif ($load >= 30 and $load <= 50 )
			{
  $ripx = '<div class="progress progress-lg progress-half-rounded m-2">
										<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="' . $load . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $load . '%;">
											' . $load . '%
										</div>
									</div>';
			}elseif ($load >= 50 and $load <= 75 )
			{
$ripx = '<div class="progress progress-lg progress-half-rounded m-2">
										<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="' . $load . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $load . '%;">
											' . $load . '%
										</div>
									</div>';
			}elseif ($load >= 75 and $load <= 100 )
			{
$ripx = '<div class="progress progress-lg progress-half-rounded m-2">
										<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="' . $load . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $load . '%;">
											' . $load . '%
										</div>
									</div>';
			}
									
		$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
		$load    = round($attacks / $getInfo['slots'] * 100, 2);
		echo '<tr class="text-center" style="font-size: 12px;border-top: 2px solid #282d36;">
		<td><b class=""><i class="bx bx-server"></i> ' . $name . '</b></td>
				<td><center><b class="text-danger">'.$ripx.'</b></center></td>
				<td><b class="text-danger">'.$status.'</b</td>
			  </tr>';
	}
	
?>

    </tbody>
 </table>	
	</div>