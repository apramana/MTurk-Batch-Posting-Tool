<?php
/*
 * GitHub Auth Check
 * Aaron Pramana <aaron@sociotopia.com> 2015
 */

require_once 'config.php';

session_start();

if(isset($_GET["code"])){
   if(!isset($_GET["state"]) || $_SESSION['gh_state'] != $_GET['state']){
      die("Invalid state.");
   }
   $ch = curl_init("https://github.com/login/oauth/access_token");

   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=".Config::GH_KEY."&client_secret=".Config::GH_SECRET."&code=" . $_GET['code']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $result = curl_exec($ch);
   curl_close($ch);

   $resp = array();
   parse_str($result, $resp);

   $_SESSION['gh_token'] = $resp['access_token'];
   header("Location: /~apramana?done=1");
   die();
}
else if(!isset($_SESSION['gh_token'])){
   $rand = rand(1, 99999999);
   $_SESSION["gh_state"] = $rand;
   header("Location: https://github.com/login/oauth/authorize?client_id=".Config::GH_KEY."&scope=user,repo&state=" . $rand);
   die();
}

try{
   $client = new \Github\Client();
   $client->authenticate(Config::GH_KEY, Config::GH_SECRET, Github\Client::AUTH_HTTP_PASSWORD);
   $client->api('authorizations')->check(Config::GH_KEY, $_SESSION['gh_token']);
}
catch(Exception $e){
   unset($_SESSION['gh_token']);
   header("Location: /~apramana");
}
?>