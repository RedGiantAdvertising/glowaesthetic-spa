<?php
/*
 * Used to send JSON response
 * usage $response = new Response();
 * $response->sendResponse($message, $statusCode)
 * params
  * $message (array or string)
  * $statusCode (integer) -> default is 200
 * Code will not be executed after sendResponse method
*/
class Response {
  function __construct() {
    header('Content-Type: application/json');
  }
  function sendResponse($message, $code = 200, $customvariable=null) {
    if(is_array($message)) {
        $keys = array_keys($message);
        foreach($keys as $key) {
          $final[$key] = $message[$key];
        }
    }
    else {
      $final = ['message' => $message];
    }
    $success = true;
    if($code != 200 || $code === false) {
      $success = false;
    }
    if($success) {
      $final['success'] = true;

      if($customvariable) {
        $final['id'] = $customvariable;
      }
    }
    else {
      $final['error'] = true;
    }
    http_response_code($code);
    echo json_encode($final);
    die();
  }
}
