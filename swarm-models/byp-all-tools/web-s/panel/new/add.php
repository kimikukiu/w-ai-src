<?php 
session_start();
$page = "Add-Balance";
include 'header.php';


	if (isset($_POST["add"])) {
		
        if (!isset($_POST['amount'])) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
         
         <div class="alert-icon">
          <i class="fa fa-info"></i>
         </div>
         
         <div class="alert-message">
           <span><strong>WRNING!</strong> champ amount vide</span>
         </div></div>';

        }


        if (!isset($_POST['type'])) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> champ type vide</span>
           </div></div>';
  
        }

        if (empty($_POST['type'])) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> champ type vide</span>
           </div></div>';
      
        }

        if (empty($_POST['amount'])) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> champ amount vide</span>
           </div></div>';

        }
        $type = ($_POST["type"]);
        $amount = ($_POST["amount"]);

        $allowedtype = array('BITCOIN', 'LITECOIN', 'MONERO');
        if ($amount > 10000) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> money max 1000</span>
           </div></div>';

 
        }
        if ($amount < 1) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> money min 10</span>
           </div></div>';

 
        }
        if (!in_array($type, $allowedtype)) {
            $error = '						<div class="alert alert-outline-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
           
           <div class="alert-icon">
            <i class="fa fa-info"></i>
           </div>
           
           <div class="alert-message">
             <span><strong>WRNING!</strong> money no valide</span>
           </div></div>';

 
        }
	

   
    else{
            $url = "https://dev.sellix.io/v1/payments";
    
            $data = array(
    
                "product_id" => "629bc4f18b133",
    
                "gateway" => $type,
    
                "quantity" => $amount,
    
                "confirmations" => 2,
    
                "email" => "dropthem@protonmail.com",
    
                "custom_fields" =>  array(
    
                    "ses_id" => '5155151'
    
                ),
    
                "webhook" => "https://boot-them.wtf/",
    
                "white_label" => "true",
    
                "return_url" => "https://boot-them.wtf/"
    
            );
    
            $content = json_encode($data);
    
            $curl = curl_init($url);
    
            curl_setopt($curl, CURLOPT_HEADER, false);
    
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json", "Authorization: Bearer Nm7tQXthU1T2tmW2jEhpxKhFBaat3lI8dQSTgXTufDG5hI7dDfGMNplTVMTr2OFS"));
    
            curl_setopt($curl, CURLOPT_POST, true);
    
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    
            $json_response = curl_exec($curl);
    
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
            curl_close($curl);
    
            $response = json_decode($json_response, true);
    
            if ($response == null) {
                $error = '<div class="alert alert-outline-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
               
               <div class="alert-icon">
                <i class="fa fa-info"></i>
               </div>
               
               <div class="alert-message">
                 <span><strong>WRNING!</strong> error</span>
               </div></div>';

            }


	//insert in db table
			$SQLCheckRegister = $odb->prepare("INSERT INTO `addfunds`(`ID`, `username`, `transaction_id`, `amount`, `status`, `transaction_date`) VALUES (NULL, :user, :btcid, :amount, :status, UNIX_TIMESTAMP(NOW()))");
        $SQLCheckRegister->execute(array(
            ":user" => $_SESSION["username"],
			":btcid" => $response["data"]["invoice"]["uniqid"],	
			":amount" => $amount,
            ":status" => $response["data"]["invoice"]["status"],
        ));
		
		//redirect o pay link
        $success = '						<div class="alert alert-outline-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
       
       <div class="alert-icon">
        <i class="fa fa-info"></i>
       </div>
       
       <div class="alert-message">
         <span><strong>WRNING!</strong>Success</span>
       </div></div>';


    
    }
	 }
		


		?>
	
   <?php if (isset($error)) { echo $error; }elseif(isset($success)) { echo $success; } ?>
<div class="row">
<div class="col-12">
		  
			
						
			<div class="alert alert-success" role="alert">
													<span class="alert-inner--icon"><i class="fe fe-thumbs-up"></i></span>
													<span class="alert-inner--text"><strong>Confirm !</strong> Wait 2 confirmations for your balance</span>
												
			<br>
			</div></div>
	<div class="col-lg-5">
	<div class="card">
<div class="card-body">
<h3 class="card-title"> Add Credit (BTC ONLY!)</h3>

		
	<form method="POST">
	<div class="form-group">
               <input class="form-range" step="1" min="1" max="1000" value="0" oninput="this.form.amount.value=this.value" type="range">
               <input class="form-control" name="amount" type="number" placeholder="Enter Amount $">
             </div>
             <div class="form-group">
             <label class="form-label">Payment Method <i class="text-danger">*</i>
               </label>
               <select class="form-select" name="type">

                 <option value="BITCOIN">Bitcoin</option>
                 <option value="LITECOIN">Litecoin</option>
<option value="MONERO">Monero</option>

 
               </select>
               </label>
               </div>
              <div class="form-group m-b-0">
                <div class="col-xs-12 ">
                 <button  type="submit" id="add" name="add" class="btn btn-primary waves-effect waves-light btn-block">Add Balance</button>
                </div>
              </div>
		
		
		
		
			
     </form>

		</div>
		</div>
		</div>
				<div class="col-md-7">
  <div class="card">

<div class="card-body">
<div class="table-responsive">
                                <table class="table">
                                <tbody>
                                    <?php
									
                                        $SQLGetMessages = $odb -> prepare("SELECT * FROM `addfunds` WHERE `username` = :id ORDER BY transaction_date LIMIT 5");
                                        $SQLGetMessages -> execute(array(':id' => $_SESSION['username']));
                                        while ($show = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC)){
                                        $user = $usernamee;
                                        $ID = $show['ID'];
                                        $amount = $show['amount'];
                                        $trans = $show['transaction_id'];
                                        $date =  $show['transaction_date'];  
										$statusPayment =  $show['status'];
  $curl = curl_init('https://dev.sellix.io/v1/orders/' . $trans);
  curl_setopt($curl, CURLOPT_USERAGENT, 'Sellix WooCommerce (PHP ' . PHP_VERSION . ')');
  curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer Nm7tQXthU1T2tmW2jEhpxKhFBaat3lI8dQSTgXTufDG5hI7dDfGMNplTVMTr2OFS']);
  curl_setopt($curl, CURLOPT_TIMEOUT, 10);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);

  curl_close($curl);
  $body = json_decode($response, true);
					                                        if ($show["status"] != 'VALIDE') {
                                            if ($show["status"] != $body["data"]["order"]["status"]) {
                                             
                                                $updateServer = $odb -> prepare("UPDATE `addfunds` SET `status` = :status WHERE `transaction_id` = :transaction_id AND `username` = :username;");
                                                $updateServer -> execute(array(':status' => $body["data"]["order"]["status"], ':transaction_id' => $body["data"]["order"]["uniqid"], ':username' => $_SESSION["username"]));

                                            }
                                          }

                                          if ($statusPayment == 'COMPLETED') {
                                           $point = $balance + $show['amount'];
                                            $updateServer = $odb -> prepare("UPDATE `users` SET `balance` = :balance WHERE `username` = :username;");
                                            $updateServer -> execute(array(':balance' => $point, ':username' => $_SESSION["username"]));

                                            $updateServer = $odb -> prepare("UPDATE `addfunds` SET `status` = :status WHERE `transaction_id` = :transaction_id AND `username` = :username;");
                                            $updateServer -> execute(array(':status' => 'VALIDE', ':transaction_id' => $body["data"]["order"]["uniqid"], ':username' => $_SESSION["username"]));

                                                $success = "Success Your Balance Added";
                                               
                                          }						
	  if ($statusPayment == 'VOIDED') {
    $echopay = ' <div class="col-lg-12 text-center"><span class="badge bg-danger h6">Your payment has been cancelled</span>  </div>';
  } else {
    $echopay = ' 
    <div class="row">   <div class="col-4">
      <div style="margin-left:auto;margin-right:auto;display:block">
      <img class="btcQR" src="https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl=' . $body["data"]["order"]["crypto_uri"] . '">
      </div>
    <br>
    <p class="fs--1">After 1-2 Bitcoin confirmations of the transaction, the deposited amount will be automatically added to your account. Please notice: 1. complete the payment within 15 minutes 2. send the EXACT amount. Otherwise you might have to contact our support to manually apply the credits to your account.
    </p>

  </div>

    <div class="col-8">
      <div class="col-lg-12 mt-3">
        <label class="form-label">Currency</label>
        <input type="text" class="form-control" style="text-transform:uppercase" readonly="" value="' . $body["data"]["order"]["gateway"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Status</label>
        <input type="text" class="form-control" style="text-transform:uppercase" readonly="" value="' . $body["data"]["order"]["status"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Payment Address</label>
        <input type="text" class="form-control" onclick="this.select();document.execCommand(\'copy\');Toastify({text:\' Successfully copied!\', duration: 3000, backgroundColor: \'bleu\' }).showToast();" value="' . $body["data"]["order"]["crypto_address"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Crypto Amount</label>
        <input type="text" class="form-control" onclick="this.select();document.execCommand(\'copy\');Toastify({text:\' Successfully copied!\', duration: 3000, backgroundColor: \'bleu\' }).showToast();" value="' . $body["data"]["order"]["crypto_amount"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Crypto recevin</label>
        <input type="text" class="form-control" onclick="this.select();document.execCommand(\'copy\');Toastify({text:\' Successfully copied!\', duration: 3000, backgroundColor: \'bleu\' }).showToast();" value="' . $body["data"]["order"]["crypto_received"] . '">
      </div>
      <br>
    </div>
 ';
  }

                                       echo '                                     
                                     

								<div class="modal-body tx-center pd-y-20 pd-x-20">
                                                 <div class="rounded-top-lg py-3 ps-4 pe-6">
                                                   <h4 class="mb-1"><span class="fab fa-btc"></span> Payment </h4>
                                                 </div>
                                                 <div class="p-4 pb-0">
                                                   '.$echopay.'
                                                 </div>
                                        
                                  
                                  
                            
                                         <td><span ></span></td>
                                         <td><span ></span></td>
										 <td></td>
                                         <td></td>
                                         </tr>


                                         ';

                                        }

                                    ?>
                                </tbody>
                            </table>
	




</div>
<!-- Back-to-top -->

		<a href="#top" id="back-to-top"><i class="las la-arrow-up"></i></a>

		<!-- JQuery min js -->
		<script src="assets/plugins/jquery/jquery.min.js"></script>
		
		<!-- Bootstrap js -->
		<script src="assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		<!-- Moment js -->
		<script src="assets/plugins/moment/moment.js"></script>

		<!-- P-scroll js -->
		<script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="assets/plugins/perfect-scrollbar/p-scroll.js"></script>

		<!-- eva-icons js -->
		<script src="assets/js/eva-icons.min.js"></script>

		<!-- Sidebar js -->
		<script src="assets/plugins/side-menu/sidemenu.js"></script>

		
    
		<!-- Sticky js -->
		<script src="assets/js/sticky.js"></script>

		<!-- Right-sidebar js -->
		<script src="assets/plugins/sidebar/sidebar.js"></script>
		<script src="assets/plugins/sidebar/sidebar-custom.js"></script>

		<!-- Theme Color js -->
		<script src="assets/js/themecolor.js"></script>

		<!-- custom js -->
		<script src="assets/js/custom.js"></script>

		<!-- Switcher js -->
		<script src="assets/switcher/js/switcher.js"></script>
		<!-- Internal Select2.min js -->
		<script src="assets/js/modal.js"></script>

	</body>
</html>