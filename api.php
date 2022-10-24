<?php

include_once('functions.php');
include_once('response.php');

if(isset($_POST['login'])) {
  $username = cleanData($_POST['username']);
  $password = cleanData($_POST['password']);
  $result = prepareQuery("SELECT * FROM users WHERE username = ? AND is_deleted = ?", 'si', [$username, 0]);
  $response = new Response();
  if(!mysqli_num_rows($result)) {
    $response->sendResponse("Invalid credentials", 406);
    die();
  }
  $userData = mysqli_fetch_assoc($result);
  $check = checkPass($userData['password'], $password);
  if(!$check) {
    $response->sendResponse("Invalid credentials", 406);
    die();
  }
  $userDataCopy = $userData;
  unset($userDataCopy['password']);
  $token = createToken($userDataCopy);
  $response->sendResponse(["message" => "Successfully logged in user", "token" => $token]);
}

if(isset($_POST['addUser'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }

  $values = $_POST;
  $values['password'] = dbPass($values['password']);
  if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $values['username'])){
    $response = new Response();
    $response->sendResponse("Username should only contain characters and numbers.", 406);
    die();
  }
  $checkUser = checkUserBeforeAdding($values);
  if(!$checkUser) {
    $response = new Response();
    $response->sendResponse("Ce nom d'utilisateur existe déjà.", 406);
    die();
  }
  $queryData = getInsertQuery('users', $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $result = prepareQuery($query, $types, $params);
  if ($result) { // Success query
    $userId = $result['id'];
    $response = new Response();
    $response->sendResponse("Cet utilisateur a bien été ajouté.", 200, $userId);
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Failed due to query error: ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }
}

if(isset($_POST['updateUser'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }
  $values = $_POST;
  if(!empty($values['password'])) { // Remove password
    unset($values['password']);
  }
  $checkUser = checkUserBeforeUpdating($values['id'], $values);
  if(!$checkUser) {
    $response = new Response();
    $response->sendResponse("Ce nom d'utilisateur existe déjà.", 406);
    die();
  }
  $queryData = getUpdateQuery('users', $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $query .= " WHERE id = ?";
  $types .= 'i';
  array_push($params, $_POST['id']);
  $result = prepareQuery($query, $types, $params);
  if ($result) { // Success query
    $response = new Response();
    $response->sendResponse("Cet utilisateur a bien été modifié.");
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Une erreur est survenue : ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }
}

if(isset($_POST['deleteUser'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }

  $userId = $_POST['id'];
  $values = [ 'is_deleted' => 1 ];
  $queryData = getUpdateQuery('users', $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $query .= " WHERE id = ?";
  $types .= 'i';
  array_push($params, $_POST['id']);
  $result = prepareQuery($query, $types, $params);
  if ($result) { // Success query
    $response = new Response();
    $response->sendResponse("Cet utilisateur a bien été supprimé.");
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Une erreur est survenue : ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }
}

/* Client here*/

// AddClient
if(isset($_POST['addClient'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }

  $values = $_POST;
  $clientFirstname = cleanData($_POST['clientFirstname']);
  $clientLastname  = cleanData($_POST['clientLastname']);
  $clientPhone     = cleanData($_POST['clientPhone']);

  $sql = "INSERT INTO clients(firstname,lastname,numberphone) VALUES('".$clientFirstname."', '".$clientLastname."', '".$clientPhone."')";
    $result = mysqli_query($connection, $sql);
  if ($result) { // Success query
    $clientId = mysqli_insert_id($connection);
    $response = new Response();
    $response->sendResponse("Cet client a bien été ajouté.", 200, $clientId);
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Failed due to query error: ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }   
}

// Update Client

if(isset($_POST['updateClient'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }
  $values = $_POST;
  /*if(!empty($values['password'])) { // Remove password
    unset($values['password']);
  }
  $checkUser = checkUserBeforeUpdating($values['id'], $values);
  if(!$checkUser) {
    $response = new Response();
    $response->sendResponse("Ce nom d'utilisateur existe déjà.", 406);
    die();
  }*/
  /*$queryData = getUpdateQuery('clients', $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $query .= " WHERE id = ?";
  $types .= 'i';
  array_push($params, $_POST['id']);
  $result = prepareQuery($query, $types, $params);*/
  
  $client_id     = cleanData($_POST['client_id']);
  $clientFirstname = cleanData($_POST['clientFirstname']);
  $clientLastname  = cleanData($_POST['clientLastname']);
  $clientPhone     = cleanData($_POST['clientPhone']);

  $sql = "UPDATE clients SET firstname = '".$clientFirstname."', lastname = '".$clientLastname."', numberphone = '".$clientPhone."' WHERE id = '".$client_id."' ";
    $result = mysqli_query($connection, $sql);
  if ($result) { // Success query
    $response = new Response();
    $response->sendResponse("Cet client a bien été modifié.");
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Une erreur est survenue : ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }
}

// Delete Client

if(isset($_POST['deleteClient'])) {
  $logged = checkIfLogged(true);
  if (!$logged) {
    $response = new Response();
    $response->sendResponse('404 Not Found', 400);
  }

  $clientId = $_POST['id'];
  $values = [ 'is_deleted' => 1 ];
  $queryData = getUpdateQuery('clients', $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $query .= " WHERE id = ?";
  $types .= 'i';
  array_push($params, $_POST['id']);
  $result = prepareQuery($query, $types, $params);
  if ($result) { // Success query
    $response = new Response();
    $response->sendResponse("Cet utilisateur a bien été supprimé.");
  } else { // Query failed
    $response = new Response();
    if($configs['env'] == "dev") {
      $error = "Une erreur est survenue : ".getLatestQueryError();
      $response->sendResponse($error, 500);
      die();
    }
    $response->sendResponse('Internal error', 500);
  }
}
?>