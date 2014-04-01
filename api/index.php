<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$config = require("./config.php");
if(isset($config["development"])) {
  $_ENV["REMOTE_USER"] = $config["development"]["username"];
}

//Make sure user is logged in
if(empty($_ENV["REMOTE_USER"])) {
  echo '{"error" : "not logged in"}'; 
  return;
}
$GLOBALS["USERNAME"] = $_ENV["REMOTE_USER"];

//Instatiate the SLIM framework
require 'vendor/autoload.php';
$app = new \Slim\Slim(array("debug"=>false));
$GLOBALS["APP"] = $app;
//Change the http Content-type header to json
$response = $app->response();
$response['Content-Type'] = "application/json; charset=utf-8";
//Internal string encoding is utf-8
mb_internal_encoding("UTF-8"); 

//Load only the needed controllers 
spl_autoload_register(function ($class_name) {
    $file = 'routes/' . $class_name. '.php';
    if (file_exists($file)) {
        require_once($file);
    }
});

//Binds our routes
require "./router.php";
//Get any fatal errors
register_shutdown_function(function() {
  $error = error_get_last();
  if(isset($error)) {
    $GLOBLAS["APP"]->response()->status(404);
    $error =  $error["type"] . ' - '. $error["message"];
    echo '{ "error" : "' . $error .'" }'; die();
  }
});
//Capture slim errors
$app->error(function (\Exception $e) use ($app) {
  $app->response()->status(404);
  echo '{ "error" : ' . json_encode($e->getMessage()) . ' }';
});
//Run app
$app->run();

?>
