	<?php 
	
	 require '../complex/configuration.php';
 require '../complex/init.php';
								$checkOnlines = $odb->query("SELECT * FROM `login_history` WHERE `status` = 'success' AND `hideme` = '0' AND `method` = 'System_Login' ORDER BY `date` DESC LIMIT 10;");
									while($row = $checkOnlines->fetch(PDO::FETCH_BOTH)){
										$count = '';
										$users = $users . strtolower($row['username']) . ",";
										$userInfoData = $odb->prepare("SELECT * FROM `users` WHERE `username` = :username");
										$userInfoData->execute(array(':username' => htmlspecialchars(htmlentities($row['username']))));
										$userInfo = $userInfoData->fetch(PDO::FETCH_BOTH);
										
										$diffOnline = time() - $userInfo['activity'];
										$countOnline = $odb->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username  AND {$diffOnline} < 30");
										$countOnline->execute(array(':username' => htmlspecialchars(htmlentities($row['username']))));
										$onlineCount = $countOnline->fetchColumn(0);
										if($onlineCount == "1")  { 
										 $avatar = '<td class="text-center" style="width: 50px; padding: 5px;"><div class="avatars">
                        <div class="avatar"><img class="img-40 rounded-circle" src="'. $userInfo['avatar'] .'" alt="" height="40px" title="Online">
                          <div class="status status-60 bg-success"> </div></div></td>';
										$status = '<td class="text-center" style="width: 50px; padding: 5px;"><bb class="text-success">⬤  </bb></td>';
										} else { 
										 $avatar = '<td class="text-center" style="width: 50px; padding: 5px;"><div class="avatars">
                        <div class="avatar"><img class="img-40 rounded-circle" src="'. $userInfo['avatar'] .'" alt="" height="40px" title="Offline">
                          <div class="status status-60 bg-danger"> </div></div></td>';
										$status =  '<td class="text-center" style="width: 50px; padding: 5px;"><bb class="text-danger">⬤  </bb></td>';
										}
									
									$planInfoData = $odb->prepare("SELECT * FROM `plans` WHERE `id` = :planid");
									$planInfoData->execute(array(':planid' => htmlspecialchars(htmlentities($userInfo['plan']))));
									$planInfo = $planInfoData->fetch(PDO::FETCH_BOTH);
									if($planInfo['network'] == 'vip') {
										$rank = '<span class="badge badge-warning"><bb style="color: #806512 !important">VIP</bb></span>';
									} else if($planInfo['network'] == 'normal') {
										$rank = '<span class="badge badge-success" style="color: #3f5328">Member</span>';
									} else {
										$rank = '<span class="badge" style="background: #767676">User</span>';
									}
									
								$custom = explode(".",$row['ip']);
								$ip1 = $custom[0];
								$ip2 = $custom[1];
								$realIP = $ip1 . '.' . $ip2 . '.' . 'xxx.xxx';
							$usernamecount = strlen($row['username']);
							$usernamecount = $usernamecount / 2;
							
							  for($i=0;$i<=$usernamecount;$i++) {
								  $count = $count . "*";
							  }
								 
							$hideusername = substr($row['username'],0,$usernamecount) . $count;
							if($row['platform'] == 'Iphone') {
								$platform = '<td class="text-center" style="width: 50px; padding: 5px;"><img src="assets/img/devices/apple.png" title="Apple"></td>';
							} elseif($row['platform'] == 'PC') {
								$platform = '<td class="text-center" style="width: 50px; padding: 5px;"><img src="assets/img/devices/windows.png" title="Windows"></td>';
							} else { 
							$platform = '<td class="text-center" style="width: 50px; padding: 5px;"><img src="assets/img/devices/android.png" title="Android"></td>';
							}
							$usernamecount = substr_count($users, strtolower($row['username']) . ",");
						if($usernamecount > 0 && $usernamecount < 2) {
							echo '
						<tr>' . $avatar . '
						<td class="text-center" style="padding: 5px;">' . $rank . '</td>
						<td class="text-center" style="width: 50px; padding: 5px;">' . htmlentities($hideusername) . '</td>
						
						<td class="text-center" style="width: 50px; padding: 5px;"><i class="flag-icon flag-icon-' . strtolower(htmlspecialchars(htmlentities($row['country']))) . '"></i></td>
						' . $platform . '
						<td class="text-center" style="width: 50px; padding: 5px;"><span class="badge badge-primary text-white">' . htmlspecialchars(htmlentities(time_elapsed_string1($row['date']))) . '</span></td></tr>
					';
						}
									}
									?>