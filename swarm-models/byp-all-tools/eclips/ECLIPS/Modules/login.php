<?php
if (!isset($_SERVER['HTTP_REFERER'])) {die;}
//Get the includes
require '../Class/database.php';
require '../Class/init.php';

//Set ip (are you using cloudflare?)
if ($cloudflare == 1)
{
$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
else
{
$ip = $_SERVER['REMOTE_ADDR'];
}

//Are you already logged in?
if ($user -> LoggedIn())
{
echo success(' You are already logged in!');
echo "<meta http-equiv=\"refresh\" content=\"3;url=home.php\">";
die();
}

//Safe get
$type = $_GET['type'];


//Login case
if ($type == 'login')
{
$username =  htmlspecialchars(htmlentities($_POST['username']));
$password =  htmlspecialchars(htmlentities($_POST['password']));
$date     =  htmlspecialchars(htmlentities(strtotime('-1 min', time())));

//Check fields
if (empty($username) || empty($password) || !ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
{
die(error('Password or username not found.'));
}

//Check login details
$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1(md5($password))));
$countLogin = $SQLCheckLogin -> fetchColumn(0);
if (!($countLogin == 1))
{
die(error('Username or password are invalid.'));
}

//Check if the user is banned
$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$status = $SQL -> fetchColumn(0);
if ($status == 1)
{
$ban = $odb -> query("SELECT `reason` FROM `bans` WHERE `username` = '$username'") -> fetchColumn(0);
die(error('You are banned. Reason: '.htmlspecialchars($ban)));
}

//Insert login log and log in
$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$userInfo = $SQL -> fetch();
$_SESSION['username'] = $userInfo['username'];
$_SESSION['ID'] = $userInfo['ID'];
echo success(' Login Successful!<meta http-equiv="refresh" content="3;URL=home.php">');
}
//Register case
if ($type == 'register')
{
//Check captcha
if (!($_POST['answer'] == SHA1($_POST['question'].$_SESSION['captcha']))) {
die(error(' All fields seem to be empty.'));
}
//Set variables
$username = htmlspecialchars(htmlentities($_POST['username']));
$password = htmlspecialchars(htmlentities($_POST['password']));
$rpassword = htmlspecialchars(htmlentities($_POST['rpassword']));
$email = htmlspecialchars(htmlentities($_POST['email']));
$scode = htmlspecialchars(htmlentities($_POST['scode']));
//Validate fields
if (empty($username) || empty($password) || empty($rpassword) || empty($email))
{
die(error(' Please fill in all fields'));
}
//Check if the username is legit
if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
{
die(error(' Username Must Be  Alphanumberic And 4-15 characters in length.'));
}
//Check if the code have 4 digits
if (strlen($scode) < 4 || strlen($scode) > 4)
{
die(error(' Code must be 4 characters in length.'));
}
//vip access
$vip ='0';
//api access
//Check if user is available
$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$countUser = $SQL -> fetchColumn(0);
if ($countUser > 0)
{
die(error(' Username is already taken'));
}
//Compare first to second password
if ($password != $rpassword)
{
die(error(' Passwords do not match'));
}
//Make registeration
$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$userInfo = $SQL -> fetch();

$insertUser = $odb -> prepare("INSERT INTO `users` VALUES(NULL, :username, :password, :email, :scode, 0, 0, 0, 0, :vip, 0, 0, 0, 0)");
$insertUser -> execute(array(':username' => $username, ':password' => SHA1(md5($password)), ':email' => $email, ':scode' => $scode, ':vip' => $vip,));

echo success(' Account has been successfully registered!<meta http-equiv="refresh" content="3;url=login.php">');
}