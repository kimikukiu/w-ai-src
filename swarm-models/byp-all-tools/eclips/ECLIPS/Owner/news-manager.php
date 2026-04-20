<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />								
<div class="main-content">

<div class="page-content">
    <div class="container-fluid">


                            <!-- start page title -->
                            <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><?php echo htmlspecialchars($sitename); ?></h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
                <!-- START PAGE CONTENT -->
        </div>
        <div class="content-body">
<?php if (isset($_POST['addnews']))
{
if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['author']))
{
$error = 'Please verify all fields';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL, :title, :content, UNIX_TIMESTAMP(), :author)");
$SQLinsert -> execute(array(':title' => $_POST['title'], ':content' => $_POST['content'], ':author' => $_POST['author']));
echo success2('News has been added <meta http-equiv="refresh" content="1;url=news.php">');
}
else
{
echo error2($error);
}
}?>		
    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
							<div class="card-header">
      <h4 class="card-title">Server Manager</h4>
    </div>                                 
                                    <div class="block-content tab-content">
                                        <div class="tab-pane active" id="tab3">
<p>
                                    <table class="table zero-configuration">
                                        <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
				<form method="post">
			<?php 
			$SQLGetNews = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC");
			while ($getInfo = $SQLGetNews -> fetch(PDO::FETCH_ASSOC))
			{
				$id = $getInfo['ID'];
				$title = $getInfo['title'];
				$content = $getInfo['content'];
				$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
				echo '<tr><td>'.htmlspecialchars($title).'</td><td>'.htmlspecialchars($content).'</td><td><button type="submit" title="Delete FAQ" name="deletenews" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-icon"><i data-feather="delete"></i></button></td></tr>';
			}
if (isset($_POST['deletenews']))
{
$delete = $_POST['deletenews'];
$SQL = $odb -> query("DELETE FROM `news` WHERE `ID` = '$delete'");
echo success2('News has been removed <meta http-equiv="refresh" content="1;url=news.php">');
}
			?>
</form>
                                        </tr>                                       
                                    </table>
</p>
                                        </div>
<p>
                        <div class="col-md-12">
                            <div class="card">
							<div class="card-header">
      <h4 class="card-title">News Manager</h4>
    </div>
                                <div class="card-body">
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Title:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="title"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Content:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="content"/></div>
                                    </div>
                                    <br>
							 <div class="row-form">
                                        <div class="col-md-12"><input type="hidden" class="form-control" name="author" value="<?php echo $_SESSION['username']; ?>" /></div>
                                    </div>
									 <div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="addnews" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
									</form>
                                </div>
</p>
                                        </div>                    
                                    </div>
                                </div>