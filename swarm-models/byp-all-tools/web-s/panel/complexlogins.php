

<?php
ob_start();
require_once "complex/configuration.php";
require_once "complex/init.php";
if (!($user->LoggedIn()) || !($user->notBanned($odb))) {
    die();
}
function hiddenString($str, $start = 1, $end = 1)
{
    $aquvgiwuit    = "len";
    $lswpeoo       = "len";
    ${$aquvgiwuit} = strlen($str);
    return substr($str, 0, $start) . str_repeat("*", $len - ($start + $end)) . substr($str, ${$lswpeoo} - $end, $end);
}
date_default_timezone_set("Europe/London");
$checkOnlines = $odb->query("SELECT * FROM `login_history` WHERE `status` = 'success' ORDER BY `id` DESC LIMIT 10;");
while ($ripx = $checkOnlines->fetch(PDO::FETCH_BOTH)) {
    $userInfoData = $odb->query("SELECT * FROM `users` WHERE `username` = '" . $ripx["username"] . "'");
    $lrsmpcghx    = "planInfoData";
    $userInfo     = $userInfoData->fetch(PDO::FETCH_BOTH);
    $diffOnline   = time() - $userInfo["activity"];
    $countOnline  = $odb->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username  AND {$diffOnline} < 60");
    $countOnline->execute(array(
        ":username" => $ripx["username"]
    ));
    $onlineCount = $countOnline->fetchColumn(0);
    $djmfxrrkjyw = "tonutukdce";
    $wnvafklx    = "rank";
    if ($onlineCount == "1") {
        $status = "<td><span class=\"badge badge-pill badge-success ml-auto\" style=\"color: #fff;box-shadow: 1px 1px 15px #03c10d ;background-color:#03c10d;\">Online</span></td>";
    } else {
       $status = "<td><span class=\"badge badge-pill badge-danger ml-auto\" style=\"color: #fff;box-shadow: 1px 1px 15px #f5365c ;background-color:#f5365c;\">Offline</span></td>";
    }
    $userInfoDatao  = $odb->query("SELECT * FROM `users` WHERE `username` = '" . $userInfo["username"] . "'");
    $userInfoo      = $userInfoDatao->fetch(PDO::FETCH_BOTH);
    $avhhlwovqnqh   = "ip2";
    ${$djmfxrrkjyw} = "custom";
    $pexpire        = $userInfoo["expire"];
    ${$lrsmpcghx}   = $odb->query("SELECT * FROM `plans` WHERE `id` = '" . $userInfo["membership"] . "'");
    $planInfo       = $planInfoData->fetch(PDO::FETCH_BOTH);
    
	if ($userInfo["rank"] == 69) {
        $rank = "<bb class=\"text-danger\"><strong> 😼 Admin</strong></bb>";
        $fotox = "<img src=\"https://img.icons8.com/flat_round/2x/crown.png\" alt=\"user-img\" class=\"img-circle\" width=\"30\" height=\"30\"/>";
    } elseif ($userInfo["rank"] == 15) {
        $rank  = "<bb style=\"color: #aa65cc!important;\"><strong>💎Support</strong></bb>";
        $fotox = "<img src=\"https://img.icons8.com/flat_round/2x/crown.png\" alt=\"user-img\" class=\"img-circle\" width=\"40\" height=\"40\"/>";
    } elseif ($planInfo["vip"] == "1") {
        $gxxbmeop        = "rank";
        $scrnlursi       = "gxxbmeop";
        ${${$scrnlursi}} = "<bb style=\"color:#0284db;\"><strong>💀 VIP User</strong></bb>";
        $fotox           = "<img src=\"https://img.icons8.com/bubbles/2x/user-male.png\" alt=\"user-img\"\x20\x63\x6c\x61\x73\x73\x3d\x22\x69\x6d\x67-c\x69\x72\x63\x6c\x65\x22\x20\x77\x69\x64t\x68\x3d\"40\"\x20\x68\x65\x69\x67\x68\x74\x3d\x22\x34\x30\x22/>";
    } elseif ($userInfo["expire"] > time()) {
        $rank  = "<bb class=\"text-success\">&#9889; Paid User</bb>";
        $fotox = "<img src=\"https://img.icons8.com/dusk/2x/launchpad.png\" alt=\"\x75\x73\x65\x72-i\x6d\x67\x22\x20\x63\x6c\x61\x73\x73\x3d\x22\x69\x6d\x67-\x63\x69\x72\x63\x6c\x65\x22\x20\x77\x69\x64\x74\x68\x3d\x22\x34\x30\" height=\"40\"/>";
    } else {
        $rank  = "<bb style=\"text-primary;\">&#9785; Free User</bb>";
        $fotox = "<img src=\"https://img.icons8.com/fluent/2x/user-male-circle.png\" alt=\"\x75\x73\x65\x72-i\x6d\x67\x22\x20\x63\x6c\x61\x73\x73\x3d\x22\x69\x6d\x67-\x63\x69\x72\x63\x6c\x65\x22\x20\x77\x69\x64\x74\x68\x3d\x22\x34\x30\" height=\"40\"/>";
    }
	
	

    $realIP          = "&#215;Encrypted&#215;";
    if ($userInfoo["rank"] == 69) {
        $realIP = "B.O.S.S";
    }
    if ($userInfoo["rank"] == 15) {
        $realIP = "1.3.3.7";
    }
    if ($ripx["platform"] == "Iphone") {
        $platform = "<td><img src=\"https://img.icons8.com/fluent/2x/mac-os.png\" title=\"Apple\" width=\"30\" height=\"30\"/></td>";
    } elseif ($ripx["platform"] == "PC") {
        $platform = "<td><img src=\"https://img.icons8.com/fluent/2x/windows-10.png\" title=\"Windows\" width=\"30\" height=\"30\"/></td>";
    } else {
        $platform = "<td><img src=\"https://img.icons8.com/doodle/2x/android.png\" title=\"Android\" width=\"30\" height=\"30\"/></td>";
    }
	

$country = "<td><img src=\"https://www.flaticon.com/svg/vstatic/svg/518/518707.svg\" title=\"Opera\" width=\"30\" height=\"30\"/></td>";

if ($ripx["hide"] == "off"){
$username = substr_replace($ripx["username"], "***", -5, -2);    
}
if ($ripx["hide"] == "on"){
$username = "👻👻👻👻";
$realIP = "👻👻👻👻";    
}
$system = $ripx["method"];

    echo "\n    <tr class=\"text-center\"\x20s\x74\x79\x6c\x65\x3d\"font-size: 12px;\">" . $status . "<td><span>" . $username . "</span></td><td> ". $fotox ." </td><td>" . ${$wnvafklx} . "</td><td><span>" . _ago($ripx["date"]) . " ago</span></td>" . $platform .  "</tr>\n";
}
?>