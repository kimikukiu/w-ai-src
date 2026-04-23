<?php
include("Class/header.php");
?>
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">
<div style="height: 502px;">
<div class="row g-5 g-xl-8 ">
<?php
						$newssql = $odb -> query("SELECT * FROM `api` LIMIT 0,30");
						while($row = $newssql ->fetch()){
							$name = $row['name'];
							$slots = $row['slots'];
                            $layer = $row['layer'];							
							$status = $row['status'];
							if($status == 0)
							{
								$status = "Online";
							}
							else 
							{
								$status = "Maintaince";
							}
							
							$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
							$totalserverAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND  `stopped` = 0")->fetchColumn(0);
							$load    = round($attacks / $slots * 100, 2);
					
							
							echo'
					 <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h5 class="text-center text-uppercase mt-3 mb-4">Server Name:  '.htmlspecialchars($name).'</h5>
                  <div class="d-flex align-items-center mb-2">
                    <i data-feather="monitor" class="icon-md text-light mr-2"></i>
                    <p>
					Running Attacks: <strong>'.htmlspecialchars($attacks).'</strong><br>
					Allowed Concurrents: <strong>'.htmlspecialchars($slots).'</strong><br>
					Total Attacks: <strong>'.htmlspecialchars($totalserverAttacks).'</strong><br>
					Network Load: <strong>'.htmlspecialchars($load).'%</strong><br>
					</p>
                  </div>
                    <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width:'.$load.'%; aria-valuenow="'.$load.'" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
              </div>
            </div>';
				  
						}
						?>
			</div>
            </div>
            </div>