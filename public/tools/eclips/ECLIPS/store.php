<?php
include("Class/header.php");
?>
<!--begin::Container-->
<div class="content d-flex flex-column flex-column-fluid">

<div class="container-xxl">

                        <body oncontextmenu="return false">
                        <div style="height: 502px;">
<div class="row">
    <div class="col-12">
        <div class="card">
        <div class="card-header">
                    <h4 class="card-title"><?php echo htmlspecialchars($sitename); ?> Store</h4>
                    </div>
            <div class="card-body">


                <table id="datatable" class="table  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                    <tr>
                    <th class="text-center">Name</th>
                      <th class="text-center">Price</th>
                      <th class="text-center">Attack Time</th>
                      <th class="text-center">Concurrent</th>
                    <th class="text-center">Length</th>
                    <th class="text-center">Duration</th>
                    <th class="text-center">API Access</th>
                      <th class="text-center">Actions</th>
                    </tr>
                    </thead>

 <tbody class="text-center">
<?php 
    $SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `ID` ASC");
    while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC))
    {
        $id = $getInfo['ID'];
        $name = $getInfo['name'];
        $price = $getInfo['price'];
        $length = $getInfo['length'];
        $unit = $getInfo['unit'];
        $concurrents = $getInfo['concurrents'];
        $mbt = $getInfo['mbt'];
        $apiaccess = $getInfo['apiaccess'];
        if($apiaccess == 0)
{
$apiaccess = "No";
}
else 
{
$apiaccess = "Yes";
}
        echo'
        <tr>
            <td>'.htmlspecialchars($name).'</td>
            <td id="totalDue">$'.htmlentities($price).'<small></td>
            <td>'.$mbt.' Seconds</td>
            <td>'.htmlentities($concurrents).'</td>
            <td>'.htmlentities($length).'</td>
            <td>'.htmlspecialchars($unit).'</td>
            <td>'.htmlentities($apiaccess).'</td>
            <td>
            <form method="POST"><a href="error.html" class="btn btn-primary btn-block "><i class="fa fa-store" style="color: #FFFFFF" aria-hidden="true"></i> Buy</button></form>
            </td>
        </tr>
        <div class="modal fade" id="defaultsizemodal'.$id.'">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post"></form>
                    <div class="modal-header">
                        <h5 class="modal-title">Plan Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times" aria-hidden="true"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->