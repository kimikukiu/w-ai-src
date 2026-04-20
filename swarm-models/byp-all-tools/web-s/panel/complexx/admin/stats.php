					<?php


	session_start();
	require '../../complex/configuration.php';
    require '../../complex/init.php';

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !($user -> isAdmin($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

?>
	
<div class="col-12">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col text-center">
											<label class="tx-12">Total Atacks</label>
											<p class="font-weight-bold tx-20"><?php echo $TotalAttacks; ?></p>
										</div>
										<!-- col -->
										<div class="col border-start text-center">
											<label class="tx-12">Running Attacks</label>
											<p class="font-weight-bold tx-20"><?php echo $RunningAttacks; ?></p>
										</div>
										<!-- col -->
										<div class="col border-start text-center">
											<label class="tx-12">Total Users</label>
											<p class="font-weight-bold tx-20"><?php echo $TotalUsers; ?></p>
										</div>
										<!-- col -->
									</div>
									<!-- row -->
									<div class="progress ht-20 mt-4">
										<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary ht-20 w-<?php echo $RunningAttacks; ?>00"><?php echo $RunningAttacks; ?>0%</div>
									</div>
								</div>
							</div>
						</div>
