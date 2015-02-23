<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

require_once 'vendor/autoload.php';

require_once 'github_auth.php';
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
      <style>
      #container {
         width: 300px;
         margin-left: auto;
         margin-right: auto;
         text-align: center;
      }
      </style>

      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

      <title>MTurk Tools</title>
   </head>

   

   <body>
      <div id="container">
         <div class="starter-template">
            <h1>MTurk Tools</h1>
            <h2>Post Batches</h2>
            <div id="controls">
            <label for="video">Video ID: </label><input type="text" id="video" />
            <label for="sandbox">Use sandbox:</label><input type="checkbox" id="sandbox" checked="checked" />
            <p><button data-template="transcribe_1">Post singular transcription tasks</button></p>
            <p><button data-template="verify_1">Post singular verification tasks</button></p>
            <p><button data-template="transcribe_3">Post multi transcription tasks</button></p>
            <p><button data-template="verify_3">Post multi verification tasks</button></p>
         </div>
         </div>
      </div>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
      
      <script type="text/javascript">
      $( document ).ready(function(){
         $("#controls button").click(function(){
            $.post(
               "process.php",
               {
                  video: $("#video").val(),
                  template: $(this).attr("data-template"),
                  sandbox: $("#sandbox").is(':checked') ? 1 : 0
               },
               function(data){
                  alert(data);
               }
            );
         });
      });
      </script>
   </body>
</html>