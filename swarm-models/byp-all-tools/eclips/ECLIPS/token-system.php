<?php
include("Class/header.php");
?>
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">

<div class="row g-5 g-xl-8 ">
<div class="col-xl-12">

<div class="card bg-light-primary card-xl-stretch mb-xl-8">

<div class="card-body p-0">
<div class="d-flex flex-stack card-p flex-grow-1">
<span class="symbol symbol-50px me-2">
<span class="symbol-label bg-primary">
<!--begin::FontAwesome Icons-->
<i class="fas fa-book text-white"></i>
</span>
</span>
<div class="d-flex flex-column text-end">
<span class="text-dark fw-bolder fs-2" data-kt-countup="true" data-kt-countup-value="<?php echo htmlspecialchars($total_user_tokens); ?>">0</span>
<span class="text-primary fw-bold mt-1">Total Tokens Used</span>
</div>
</div>
</div>

</div>

</div>
</div>
<div class="row g-5 g-xl-8 mb-5 mb-xl-0">

<div class="col-xl-6">

<div style="height: 502px; overflow-y: scroll;">

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
                    <div class="col-md-12 grid-margin stretch-card">
                    <div id="redeemdiv" style="display:none"></div>
     <div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">Token System</h3>
    </div>
    <div class="card-body">
                <div class="table-responsive">
                <div class="col-sm-12">
                  <input type="text" class="input form-control" name="code" id="code" maxlength="32" placeholder="XXXXXXX">
                  <br>
                </div>
              </div>
              <div class="form-group m-b-0">
                <div class="col-sm-offset-3 col-sm-9">
                  <button onclick="redeemCode()" class="btn btn-block btn-bg-light btn-color-primary">Redeem Token</button><i style="display: none;" id="icon"></i>

                      </div>

                </div>
                

              </div>
          </div>

</div>


</div>
</div>
</div>
<div class="col-xl-6">
												<!--begin::Body-->
												<div class="card-body p-0">
																									<!--begin::Items-->
																									<div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1" style="margin-top: -9px">
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-45px w-40px me-5">
																<span class="symbol-label bg-lighten">
																	<!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M18.4 5.59998C21.9 9.09998 21.9 14.8 18.4 18.3C14.9 21.8 9.2 21.8 5.7 18.3L18.4 5.59998Z" fill="currentColor" />
																			<path d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2ZM19.9 11H13V8.8999C14.9 8.6999 16.7 8.00005 18.1 6.80005C19.1 8.00005 19.7 9.4 19.9 11ZM11 19.8999C9.7 19.6999 8.39999 19.2 7.39999 18.5C8.49999 17.7 9.7 17.2001 11 17.1001V19.8999ZM5.89999 6.90002C7.39999 8.10002 9.2 8.8 11 9V11.1001H4.10001C4.30001 9.4001 4.89999 8.00002 5.89999 6.90002ZM7.39999 5.5C8.49999 4.7 9.7 4.19998 11 4.09998V7C9.7 6.8 8.39999 6.3 7.39999 5.5ZM13 17.1001C14.3 17.3001 15.6 17.8 16.6 18.5C15.5 19.3 14.3 19.7999 13 19.8999V17.1001ZM13 4.09998C14.3 4.29998 15.6 4.8 16.6 5.5C15.5 6.3 14.3 6.80002 13 6.90002V4.09998ZM4.10001 13H11V15.1001C9.1 15.3001 7.29999 16 5.89999 17.2C4.89999 16 4.30001 14.6 4.10001 13ZM18.1 17.1001C16.6 15.9001 14.8 15.2 13 15V12.8999H19.9C19.7 14.5999 19.1 16.0001 18.1 17.1001Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Description-->
															<div class="d-flex align-items-center flex-wrap w-100">
																<!--begin::Title-->
																<div class="mb-1 pe-3 flex-grow-1">
																	<a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Max Bootime:</a>
																</div>
																<!--end::Title-->
																<!--begin::Label-->
																<div class="d-flex align-items-center">
																	<div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $rowxd['mbt']; ?></div>
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
																	<!--end::Svg Icon-->
																</div>
																<!--end::Label-->
															</div>
															<!--end::Description-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-45px w-40px me-5">
																<span class="symbol-label bg-lighten">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Description-->
															<div class="d-flex align-items-center flex-wrap w-100">
																<!--begin::Title-->
																<div class="mb-1 pe-3 flex-grow-1">
																	<a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Max Concurrents</a>
																</div>
																<!--end::Title-->
																<!--begin::Label-->
																<div class="d-flex align-items-center">
																	<div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $rowxd['concurrents']; ?></div>
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
																	<!--end::Svg Icon-->
																</div>
																<!--end::Label-->
															</div>
															<!--end::Description-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-45px w-40px me-5">
																<span class="symbol-label bg-lighten">
																	<!--begin::Svg Icon | path: icons/duotune/electronics/elc005.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M15 19H7C5.9 19 5 18.1 5 17V7C5 5.9 5.9 5 7 5H15C16.1 5 17 5.9 17 7V17C17 18.1 16.1 19 15 19Z" fill="currentColor" />
																			<path d="M8.5 2H13.4C14 2 14.5 2.4 14.6 3L14.9 5H6.89999L7.2 3C7.4 2.4 7.9 2 8.5 2ZM7.3 21C7.4 21.6 7.9 22 8.5 22H13.4C14 22 14.5 21.6 14.6 21L14.9 19H6.89999L7.3 21ZM18.3 10.2C18.5 9.39995 18.5 8.49995 18.3 7.69995C18.2 7.29995 17.8 6.90002 17.3 6.90002H17V10.9H17.3C17.8 11 18.2 10.7 18.3 10.2Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Description-->
															<div class="d-flex align-items-center flex-wrap w-100">
																<!--begin::Title-->
																<div class="mb-1 pe-3 flex-grow-1">
																	<a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Expiration</a>
																</div>
																<!--end::Title-->
																<!--begin::Label-->
																<div class="d-flex align-items-center">
																	<div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $date = date("m/d/y", $rowxd['expire']); ?></div>
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
																	<!--end::Svg Icon-->
																</div>
																<!--end::Label-->
															</div>
															<!--end::Description-->
														</div>
														<!--end::Item-->
																												<!--begin::Item-->
																												<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-45px w-40px me-5">
																<span class="symbol-label bg-lighten">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
																			<rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Description-->
															<div class="d-flex align-items-center flex-wrap w-100">
																<!--begin::Title-->
																<div class="mb-1 pe-3 flex-grow-1">
																	<a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Max Concurrents</a>
																</div>
																<!--end::Title-->
																<!--begin::Label-->
																<div class="d-flex align-items-center">
																	<div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $rowxd['concurrents']; ?></div>
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
																	<!--end::Svg Icon-->
																</div>
																<!--end::Label-->
															</div>
															<!--end::Description-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-6">
															<!--begin::Symbol-->
															<div class="symbol symbol-45px w-40px me-5">
																<span class="symbol-label bg-lighten">
																	<!--begin::Svg Icon | path: icons/duotune/electronics/elc005.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M15 19H7C5.9 19 5 18.1 5 17V7C5 5.9 5.9 5 7 5H15C16.1 5 17 5.9 17 7V17C17 18.1 16.1 19 15 19Z" fill="currentColor" />
																			<path d="M8.5 2H13.4C14 2 14.5 2.4 14.6 3L14.9 5H6.89999L7.2 3C7.4 2.4 7.9 2 8.5 2ZM7.3 21C7.4 21.6 7.9 22 8.5 22H13.4C14 22 14.5 21.6 14.6 21L14.9 19H6.89999L7.3 21ZM18.3 10.2C18.5 9.39995 18.5 8.49995 18.3 7.69995C18.2 7.29995 17.8 6.90002 17.3 6.90002H17V10.9H17.3C17.8 11 18.2 10.7 18.3 10.2Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Description-->
															<div class="d-flex align-items-center flex-wrap w-100">
																<!--begin::Title-->
																<div class="mb-1 pe-3 flex-grow-1">
																	<a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Has VIP</a>
																</div>
																<!--end::Title-->
																<!--begin::Label-->
																<div class="d-flex align-items-center">
																	<div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo htmlspecialchars($rank); ?></div>
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
																	<!--end::Svg Icon-->
																</div>
																<!--end::Label-->
															</div>
															<!--end::Description-->
														</div>
														<!--end::Item-->
													</div>
													<!--end::Items-->
												</div>
												<!--end::Body-->


<div class="card-body">

<div style="height: 350px; min-height: 365px;">

</div>

</div>

</div>

</div>
</div>


<div class="card-body">

<div style="height: 350px; min-height: 365px;">

</div>

</div>

</div>

</div>
</div>
<script>
			function redeemCode() {
				var code = $('#code').val();
				document.getElementById("icon").style.display="inline"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {

                        document.getElementById("redeemdiv").innerHTML=xmlhttp.responseText;
			            document.getElementById("redeemdiv").style.display="inline";
						if (xmlhttp.responseText.search("You have redeemed a Token your plan has been added!") != -1) {

							Swal.fire({
                            text: "Token Has Been redeemed!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
							inbox();
						}
						else if (xmlhttp.responseText.search("Please enter a vaild Token") != -1) {

Swal.fire({
text: "Please enter a vaild Token",
icon: "error",
buttonsStyling: false,
confirmButtonText: "Ok, got it!",
customClass: {
	confirmButton: "btn btn-primary"
}
});
inbox();
}
else if (xmlhttp.responseText.search("Seems like this Token has been redeemed") != -1) {

Swal.fire({
text: "Seems like this Token has been redeemed",
icon: "error",
buttonsStyling: false,
confirmButtonText: "Ok, got it!",
customClass: {
	confirmButton: "btn btn-primary"
}
});
inbox();
}
else if (xmlhttp.responseText.search("Sorry this Token does not exist") != -1) {

Swal.fire({
text: "Sorry this Token does not exist",
icon: "error",
buttonsStyling: false,
confirmButtonText: "Ok, got it!",
customClass: {
	confirmButton: "btn btn-primary"
}
});
inbox();
}
					}
				}
				xmlhttp.open("GET","Modules/token.php?user=<?php echo htmlspecialchars($_SESSION['ID']); ?>" + "&code=" + code,true);
				xmlhttp.send();
			}
			</script>