<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// This file is generated by Composer
require_once 'vendor/autoload.php';

require_once 'github_auth.php';

require_once 'mturk.php';

class TurkTools{
   function getFileFromRepo($file, $client){
      return $client->api('repo')->contents()->download(Config::GH_REPO_OWNER, Config::GH_REPO, $file, null);
   }

   function init(){
      $db = new PDO('mysql:host='.Config::DB_HOST.';dbname='.Config::DB_NAME.';charset=utf8', Config::DB_USER, Config::DB_PASS);
      $mturk = new MechanicalTurk(Config::MT_KEY, Config::MT_SECRET);
      $client = new \Github\Client();
      $client->authenticate($_SESSION['gh_token'], null, Github\Client::AUTH_HTTP_TOKEN);
      $template = json_decode($this->getFileFromRepo($_POST['template'].'.json', $client), true);
      $mturk->setSandbox($_POST['sandbox']);

      try {
         $st = $db->prepare($template['query']);
         $st->bindParam(':video', $_POST['video']);
         $st->execute();
         $result = $st->fetchAll(PDO::FETCH_ASSOC);
      }
      catch(PDOException $ex) {
         echo "An Error occured!";
         echo $ex->getMessage();
         die();
      }

      $rows = $template["rows"];
      $collapsed = array();
      for($i = 0; $i < count($result); $i++){ //each row in the DB result
         if($i % $rows == 0){ //each ith row
            $collapsed[$i/$rows] = $result[$i];
            for($j = $i + 1; $j < $i + $rows && $j < count($result); $j++){ // $rows rows after ith row
               foreach($result[$j] as $col => $val){
                  $collapsed[$i/$rows][$col.($j % $rows)] = $val;
               }
            }
         }
      }

      $body = $this->getFileFromRepo(Config::GH_REPO_FILE, $client);

      $result = $collapsed;

      foreach($result as $res){
         $html = $body;
         foreach($res as $k => $v){
            $html = str_replace('${'.$k.'}', $v, $html);
         }
         $response = $mturk->createHitFromTemplate($template['title'], $template['desc'], $template['duration'], $template['lifetime'], $template['reward'], '<?xml version="1.0"?><HTMLQuestion
         xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2011-11-11/HTMLQuestion.xsd"
         ><HTMLContent><![CDATA[
      		<!DOCTYPE html>
        		<html>
         		<head>
          			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
          			<script type="text/javascript" src="https://s3.amazonaws.com/mturk-public/externalHIT_v1.js"></script>
         		</head>
         		<body>
         			<form name="mturk_form" method="post" id="mturk_form" action="https://' . ($_POST['sandbox'] ? 'workersandbox' : 'www') . '.mturk.com/mturk/externalSubmit">
         				<input type="hidden" value="" name="assignmentId" id="assignmentId"/>' . 
         					$html .
         				'<p><input type="submit" id="submitButton" value="Submit" /></p>
        				</form>
        				<script language="Javascript">turkSetAssignmentID();</script>
        			</body>
        		</html>
        ]]></HTMLContent><FrameHeight>600</FrameHeight></HTMLQuestion>');

      }

      $resp = json_decode(json_encode(simplexml_load_string($response, null, LIBXML_NOCDATA)), true);
      echo "Successful: " . json_encode(simplexml_load_string($response, null, LIBXML_NOCDATA)); //$resp['HIT']['Request']['IsValid'];
   }
}

$tools = new TurkTools();
$tools->init();
?>