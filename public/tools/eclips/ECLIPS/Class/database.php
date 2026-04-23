<!-- Database configuration -->
<?php
define('DB_HOST', 'localhost:3306');
define('DB_NAME', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('ERROR_MESSAGE', '"0x13" - "error":"yes","data":"There has been a error in the database configuration."}');

try {
$odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
}
catch( PDOException $Exception ) {
	error_log('ERROR: '.$Exception->getMessage().' - '.$_SERVER['REQUEST_URI'].' at '.date('l jS \of F, Y, h:i:s A')."\n", 3, 'exception_log');
	die(ERROR_MESSAGE);
}

function error($string)
{
  return '<div class="alert alert-danger border-2 d-flex align-items-center"></button><strong></strong> '.$string.'</div>';
}

function success($string)
{
return '<div class="alert alert-success border-2 d-flex align-items-center"></button><strong></strong> '.$string.'</div>';
}

function redeemerror($string)
{
  return '<!--begin::Alert-->
  <div class="alert alert-danger d-flex align-items-center p-5">
      <!--begin::Icon-->
      <i class="fas fa-bug m-3"></i>
      <!--end::Icon-->
  
      <!--begin::Wrapper-->
      <div class="d-flex flex-column">
          <!--begin::Title-->
          <h4 class="mb-1 text-white">Faulty Error</h4>
          <!--end::Title-->
          <!--begin::Content-->
          <span><strong>'.$string.'</strong></span>
          <!--end::Content-->
      </div>
      <!--end::Wrapper-->
  </div>
  <!--end::Alert-->';
}
function redeemsucess($string)
{
  return '<!--begin::Alert-->
  <div class="alert alert-success d-flex align-items-center p-5">
      <!--begin::Icon-->
      <i class="fas fa-sack-dollar m-3"></i>
      <!--end::Icon-->
  
      <!--begin::Wrapper-->
      <div class="d-flex flex-column">
          <!--begin::Title-->
          <h4 class="mb-1 text-white">Successful</h4>
          <!--end::Title-->
          <!--begin::Content-->
          <span><strong>'.$string.'</strong></span>
          <!--end::Content-->
      </div>
      <!--end::Wrapper-->
  </div>
  <!--end::Alert-->';
}
function attackalert($string)
{
  return '<div class="alert alert-success border-2 d-flex align-items-center" role="alert">
  <div class="bg-success me-3 icon-item"><span class="fas fa-wifi text-white fs-3"></span></div>
  <p class="mb-0 flex-1">'.$string.'</p>
</div>';
}
require('UIGuard.php');
$UIGuard = new UIGuard();
// Cloudflare Mode [Optional]
$UIGuard->useCloudflare();
$UIGuard->checkGET();
$UIGuard->checkPOST();
$UIGuard->checkCOOKIE();
?>

