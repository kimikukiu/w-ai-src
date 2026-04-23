<?php
include("Class/header.php");
if (!($user -> hasMembership($odb)))
{
    header('location: home.php');
}

if(!isset($_POST['keyBtn']))

{

    $SQLGetKey = $odb -> prepare("SELECT `apikey` FROM `users` WHERE `ID` = :id");

    $SQLGetKey -> execute(array(':id' => $_SESSION['ID']));

    $userKey = $SQLGetKey -> fetchColumn(0);

    if(isset($_POST['disable_key'])){
        if(isset($_SESSION['username'])){
            disableKey($_SESSION['username'], $odb);
            header('Location: reseller-panel.php');
        }
    }
}



else

{

    function generateRandomKey($length = 15) 

    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, strlen($characters) - 1)];

        }

        return $randomString;

    }

    

    $userKey = generateRandomKey();

    
    $SQLNewKey = $odb -> prepare("UPDATE `users` SET `apikey` = :newkey WHERE `ID` = :id");

    $SQLNewKey -> execute(array(':newkey' => $userKey, ':id' => $_SESSION['ID']));

}
//function to disable API key
    function disableKey($username, $odb){
    $stmt2 = $odb->query("UPDATE users SET apikey='0' WHERE username='$username'");
}
?> 
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">

<div class="row g-5 g-xl-8 ">
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-cloud text-white"></i>
</span>
</span>
<div class="d-flex flex-column text-end">
<span class="text-dark fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo $stats -> totalBoots($odb); ?>">0</span>
<span class="text-primary fw-bold mt-1">Total (API) Request</span>
</div>
</div>
</div>

</div>

</div>
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-running text-white"></i>
</span>
</span>
<div class="d-flex flex-column text-end">
<span class="text-dark fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo $rowxd['concurrents']; ?>"></span>
<span class="text-primary fw-bold mt-1">Total Concurrents</span>
</div>
</div>
</div>

</div>

</div>
<div class="col-xl-4">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-bolt text-white"></i>
</span>
</span>

<div class="d-flex flex-column text-end">
<span class="text-white fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo htmlspecialchars($my_running_attacks); ?>"></span>
<span class="text-primary fw-bold mt-1">Active Attacks</span>
</div>
</div>
</div>

</div>

</div>
</div>
<div class="row g-5 g-xl-8 mb-5 mb-xl-0">

<div class="col-xl-12">

<div style="height: 502px; overflow-y: scroll;">

							<!--begin::Card-->
							<div class="card">
								<!--begin::Card body-->
								<div class="card-body">
									<!--begin::Heading-->
									<div class="card-px text-center pt-15 pb-15">
										<!--begin::Title-->
										<h2 class="fs-2x fw-bold mb-0">View API Panel</h2>
										<!--end::Title-->
										<!--begin::Description-->
										<p class="text-gray-400 fs-4 fw-semibold py-7">Click on the below button to launch 
										<br />the Reseller Panel</p>
										<!--end::Description-->
										<!--begin::Action-->
										<a href="#" class="btn btn-light-primary er fs-6 px-8 py-4" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">View Panel</a>
										<!--end::Action-->
									</div>
									<!--end::Heading-->
									<!--begin::Illustration-->
									<div class="text-center pb-15 px-5">
										<img src="assets/API.png" alt="" class="mw-90 h-100px h-sm-125px" />
									</div>
									<!--end::Illustration-->
								</div>
								<!--end::Card body-->
							</div>
							<!--end::Card-->
						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->


</div>
</div>
</div>
<!--begin::Modal - View Users-->
<div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
			<!--begin::Modal dialog-->
			<div class="modal-dialog mw-650px">
				<!--begin::Modal content-->
				<div class="modal-content">
					<!--begin::Modal header-->
					<div class="modal-header pb-0 border-0 justify-content-end">
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
							<span class="svg-icon svg-icon-1">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
									<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Close-->
					</div>
					<!--begin::Modal header-->
					<!--begin::Modal body-->
					<div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
						<!--begin::Heading-->
						<div class="text-center mb-13">
							<!--begin::Title-->
							<h1 class="mb-3">Reseller/API Panel</h1>
							<!--end::Title-->
							<!--begin::Description-->
							<div class="text-muted fw-semibold fs-5">If you need more info, please check out our 
							<a href="#" class="link-primary fw-bold">API Document</a>.</div>
							<!--end::Description-->
						</div>
						<!--end::Heading-->
						<!--begin::Users-->
						<form action="" method="POST">
						<div class="mb-15">
						<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
<div class="col-md-20 grid-margin stretch-card">
  <div class="card">
  <div class="card-body">
        <h3 class="card-title m-5 fw-bold">API Link</h3>
        <!--begin::Input group-->
        <div class="input-group">
            <!--begin::Input-->
            <input id="kt_clipboard_1" type="text" class="form-control" readonly="" value="https://websitehereModules/Modules/api.php?key=<?php echo htmlspecialchars ($userKey);?>&host=[host]&port=[port]&time=[time]&method=[method]&username=[username]" placeholder="https://websitehere/Endpoints/apistart.php?key=<?php echo htmlspecialchars ($userKey);?>&host=[host]&port=[port]&time=[time]&method=[method]&username=[username]" />
            <!--end::Input-->

                        </div>

						</div>
									<!--end::API Link-->
        <p class="card-text">
          <button type="submit" class=" mb-3 btn btn-block bg-gradient bg-info "name="keyBtn">Generate new (API) key</button>
           <button type="submit" class="mb-3  btn btn-block bg-gradient bg-danger "name="disable_key">Disable current (API) key</button>
			</p>

					
					</div>
					<!--end::Modal body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
		</div>
		<!--end::Modal - View Users-->
