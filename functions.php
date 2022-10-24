<?php

  include_once('db.php');
  include_once("config.php");
  include_once('vendor/autoload.php');

  use \Firebase\JWT\JWT;
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  //JWT Secret
  define('JWT_SECRET', $configs["jwt_secret"]);

  // Accent issue
  mysqli_set_charset($connection, 'utf8');

  // Global mysql
  $GLOBALS['connection'] = $connection;

  // Global config
  $GLOBALS['configs'] = $configs;

  /*
   * Function to run custom queries
   * useful for select queries whithout `WHERE` clause
   * usage -> runQuery("SELECT * FROM users");
  */
  function runQuery($query) {
    $connection = $GLOBALS['connection'];
    return mysqli_query($connection, $query);
  }

  /*
   * Function to run custom queries
   * usefull for any type of queries with arguments / paramaters
   * usage: -> prepareQuery('UPDATE users SET password = ? WHERE id = ?', "ss", ['some', 2]);
  */
  function prepareQuery($query, $types, $params, $customvariable = false) {
    $connection = $GLOBALS['connection'];
    $stmt = $connection->prepare($query);
    if (!empty($stmt)) {
      if (is_array($params)) {
        $arrayTP = [];
        $count = sizeof($params);
        for ($i = 0; $i <= $count; $i++) {
          if ($i == 0) {
            array_push($arrayTP, $types);
          } else {
            $j = $i - 1;
            $data = $params[$j];
            array_push($arrayTP, $data);
          }
        }
        $finalArr = [];
        foreach ($arrayTP as $k => $v) {
          $finalArr[$k] = &$arrayTP[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $finalArr);
        $queries = $stmt->execute();
        if (strpos($query, "INSERT") !== false || strpos($query, "UPDATE") !== false || strpos($query, "DELETE") !== false) {

          $result   = $stmt->affected_rows;

          if ($customvariable == true) {
            $insrtID  = $stmt->insert_id;
          }

          if (strpos($query, "INSERT") !== false) {
            $historytype = 'Add';
            if (strpos($query, "user_history") == false && strpos($query, "users") == true) {
              $logged = checkIfLogged(true);
              //userhistory($finalArr[2], $logged->id, $historytype);
            }
          } elseif (strpos($query, "UPDATE") !== false) {
            if (strpos($query, "user_history") == false && strpos($query, "users") == true) {
              $historytype = 'Edit';
              $logged = checkIfLogged(true);
              //userhistory($finalArr[3], $logged->id, $historytype);
            }
          }

          if ($result == 0) {
            $result = 1;
          }
        } else {
          $result = $stmt->get_result();
        }
        $stmt->close();

        if ($customvariable == true) {
          $final = ['response' => 1, 'id' => $insrtID];
          return $final;
        }

        return $result;
      }
      if (strpos($params, ',') !== false) {
        $tandparams = $types . "," . $params;
        $arrayTP = explode(",", $tandparams);
        $finalArr = [];
        foreach ($arrayTP as $k => $v) {
          $finalArr[$k] = &$arrayTP[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $finalArr);
        $queries = $stmt->execute();
        if (strpos($query, "INSERT") !== false || strpos($query, "UPDATE") !== false || strpos($query, "DELETE") !== false) {
          $result = $stmt->affected_rows;
          if ($result == 0) {
            $result = 1;
          }
        } else {
          $result = $stmt->get_result();
        }
        $stmt->close();

        return $result;
      } else {
        $stmt->bind_param($types, $params);
        $queries = $stmt->execute();
        if (strpos($query, "INSERT") !== false || strpos($query, "UPDATE") !== false || strpos($query, "DELETE") !== false) {
          $result = $stmt->affected_rows;
          if ($result == 0) {
            $result = 1;
          }
        } else {
          $result = $stmt->get_result();
        }
        $stmt->close();
        return $result;
      }
    } else {
      return false;
    }
  }

  /*
   * Cleane string to escape sqli and xss
   * usage --> createData('<script>alert('Hello')</script>');
  */
  function cleanData($data) {
    $datav3 = mysqli_real_escape_string($GLOBALS['connection'], strip_tags($data));
    return $datav3;
  }

  /*
   * Creates a token with any data
   * usage --> createToken(['data']);
  */
  function createToken($payload) {
    $payload['iat'] = time();
    //$payload['exp'] = time() + (60 * 60);
    $jwt = JWT::encode($payload, JWT_SECRET);
    return $jwt;
  }

  /*
   * Decodes a jwt token with secret key
   * usage -> decodeToken('ey........');
  */
  function decodeToken($token) {
    try {
      $decoded = JWT::decode($token, JWT_SECRET, array('HS256'));
      return $decoded;
    } catch (\Firebase\JWT\ExpiredException $e) {
      return false;
    }
  }

  function checkLogin($redirect = false) {
    if(!empty($_COOKIE['token']) && $_COOKIE['token'] != "null") {
      $token = $_COOKIE['token'];
      try {
        $decoded = JWT::decode($token, JWT_SECRET, array('HS256'));
        if (!$redirect) {
          return true;
        }
      } catch (\Firebase\JWT\ExpiredException $e) {
        if (!$redirect) {
          return false;
        }
        else {
          header('Location: login.php');
          die();
        }
      }
    }
    else {
      if($redirect) {
        header('Location: login.php');
        die();
      }
      else {
        return false;
      }
    }
  }


function checkIfLogged($returnDecoded = false)
{
  if (isset($_COOKIE['token'])) {
    $token = $_COOKIE['token'];
    if (!empty($token) && $token != "null") {
      try {
        $decoded = JWT::decode($token, JWT_SECRET, array('HS256'));
        if (!$returnDecoded) {
          return true; //logged in
        } else {
          return $decoded;
        }
      } catch (\Firebase\JWT\ExpiredException $e) {
        return false; // JWT expired, user logged out
      }
    }
  }   //End isset($_COOKIE['TOKEN'])

  else {
    return false; // not logged in
  }
}

function checkPass($old, $new) {
  $hash = password_verify($new, $old);
  return $hash;
}

function getInsertQuery($table, $valueArr) {
  $result = runQuery("SHOW COLUMNS FROM " . $table);
  $query = 'INSERT INTO ' . $table;
  $valuesQuery = '';
  $params = [];
  $types = '';
  $total = mysqli_num_rows($result);
  $i = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    $orig = $row['Field'];
    $add = false;
    $rowValue = '';
    if ($valueArr) {
      if (!empty($valueArr[$orig])) {
        $valueData =  $valueArr[$orig];
        if (gettype($valueData) == "array") {
          $valueData = implode(',', $valueData);
        } else {
          $valueData = cleanData($valueData);
        }
        array_push($params, $valueData);
        $types .= 's';
        $add = true;
      } else {
        $i--;
      }
    } else {
      $i--;
    }
    if ($i == 0) {
      if ($add) {
        $query .= "(";
        $valuesQuery .= "VALUES(";
        $query .= $orig;
        $valuesQuery .= "?";
      }
    } else {
      if ($add) {
        $query .= ", " . $orig;
        $valuesQuery .= ", ?";
      }
    }
    $i++;
  }
  $query .= ")";
  $valuesQuery .= ")";
  $finalQuery = $query . " " . $valuesQuery;
  return ['query' => $finalQuery, 'params' => $params, 'types' => $types];
}

function getUpdateQuery($table, $valueArr, $allowedEmpty=false, $overrides = [])
{
  $result = runQuery("SHOW COLUMNS FROM " . $table);
  $query = 'UPDATE ' . $table . ' SET';
  $valuesQuery = '';
  $params = [];
  $types = '';
  $total = mysqli_num_rows($result);
  $i = 0;
  while ($row = mysqli_fetch_assoc($result)) {
    $orig = $row['Field'];
    $add = false;
    $rowValue = '';
    if(!in_array($orig, $overrides)) {
      if ($valueArr) {
        if ($allowedEmpty)
        {
          $valueData =  (array_key_exists($orig,$valueArr)) ? $valueArr[$orig] : NULL ;
          if (gettype($valueData) == "array") {
            $valueData = implode(',', $valueData);
          }
          array_push($params, $valueData);
          $types .= 's';
          $add = true;
        }
        elseif (!empty($valueArr[$orig]))
        {
          $valueData =  $valueArr[$orig];
          if (gettype($valueData) == "array") {
            $valueData = implode(',', $valueData);
          } else {
            $valueData = cleanData($valueData);
          }
          array_push($params, $valueData);
          $types .= 's';
          $add = true;
        } else {
          $i--;
        }
      } else {
        $i--;
      }
      if ($i == 0) {
        if ($add) {
          $query .= ' ' . $orig . " = ?";
        }
      } else {
        if ($add) {
          $query .= ", " . $orig . " = ?";
        }
      }
      $i++;
    }
  }
  $valuesQuery .= ")";
  $finalQuery = $query;
  return ['query' => $finalQuery, 'params' => $params, 'types' => $types];
}

function getLatestQueryError() {
  $connection = $GLOBALS['connection'];
  return mysqli_error($connection);
}

function getAnythingFromtable($table, $appendQuery = "") {
  $result = runQuery("SELECT * FROM $table $appendQuery");
  if($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
  }
  else {
    return false;
  }
}

function getAnythingWithIdAndTable($id, $table, $appendQuery = '', $appendTypes = '', $appendParams = []) {
  $params = array_merge([$id], $appendParams);
  $types = 'i'.$appendTypes;
  $result = prepareQuery("SELECT * FROM $table WHERE id = ? $appendQuery", $types, $params);
  $row = mysqli_fetch_assoc($result);
  return $row;
}

function dbPass($pass) {
  $hash = password_hash($pass, PASSWORD_BCRYPT);
  return $hash;
}

function getEverythingFromTable($table, $appendQuery = "") {
  $result = runQuery("SELECT * FROM $table $appendQuery");
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
  return $rows;
}

function checkUserBeforeAdding($values) {
  $username = cleanData($values['username']);
  $result = prepareQuery("SELECT * FROM users WHERE username = ?", 's', [$username]);
  $num = mysqli_num_rows($result);
  if(!$num) {
    return true;
  }
  else {
    return false;
  }
}

function checkUserBeforeUpdating($userId, $values) {
  $editingUserData = getAnythingWithIdAndTable($userId, "users");
  $username = $editingUserData['username'];
  $result = prepareQuery("SELECT * FROM users WHERE username = ? AND id != ?", 'si', [$username, $userId]);
  $num = mysqli_num_rows($result);
  if(!$num) {
    return true;
  }
  else {
    return false;
  }
}


function getSpecificKeysFormAssociativeArray($array, $key) {
    $result = [];
    foreach ($array as $value) {
      if (is_array($key)) {
        $arr = [];
        foreach ($key as $keyR) {
          if (!empty($value[$keyR])) {
            $arr[$keyR] = $value[$keyR];
          } else {
            $arr[$keyR] = "";
          }
        }
        array_push($result, $arr);
      } else {
        array_push($result, $value[$key]);
      }
    }
    return $result;
  }
  
function getSpecificTypesOfUsers($userTypes, $selects = "*") {
    $query = "SELECT ";
    $types = "";
    $params = [];
    if(is_array($selects)) { // select specific values
        $querySelect = "";
        $i = 1;
        $max = count($selects);
        foreach($selects as $select) {
        if($i == $max) {
            $querySelect .= "u.$select";
        }
        else {
            $querySelect .= "u.$select, ";
        }
        $i++;
        }
        $query .= $querySelect;
    }
    else {
        $query .= $select;
    }
    $query .= " FROM users u JOIN userTypes ut WHERE ";
    if(is_array($userTypes)) {
        $i = 1;
        $max = count($userTypes);
        $queryCondition = "( ";
        foreach($userTypes as $userType) {
        if($i == $max) {
            $queryCondition .= "ut.short_name = ?";
            $types .= "s";
            array_push($params, $userType);
        }
        else {
            $queryCondition .= "ut.short_name = ? OR ";
            $types .= "s";
            array_push($params, $userType);
        }
        $i++;
        }
        $queryCondition .= ") AND ut.id = u.type";
        $query .= $queryCondition;
    }
    else {
        $query .= "ut.short_name = ? AND ut.id = u.type";
        $types .= "s";
        array_push($params, $userTypes);
    }
    //echo $query."<br>";
    //echo $types."<br>";
    //print_r($params);
    $result = prepareQuery($query, $types, $params);
    if($result) {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $rows;
    }
    else {
        return [];
    }
}
  
function getUserRole($userId) {
    $result = prepareQuery("SELECT ut.* FROM userTypes ut JOIN users u WHERE u.id = ? AND u.type = ut.id", "i", [$userId]);
    $row = mysqli_fetch_assoc($result);
    return $row;
}


function get_page($str_url){
  $curl = curl_init();
  //curl_setopt ($curl, CURLOPT_REFERER, strFOARD);
  curl_setopt ($curl, CURLOPT_URL, $str_url);
  curl_setopt ($curl, CURLOPT_TIMEOUT, 30);
  curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
  curl_setopt ($curl, CURLOPT_HEADER, 0);
  if(!empty($_COOKIE["token"])) {
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: token=".$_COOKIE["token"]));
  }
  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
  $html = curl_exec ($curl);
  curl_close ($curl);
  return $html;
}

function getTranslation($word, $brows = false) {
  if($brows){
    $lang = getBrowserLang();
  }else{
    $lang = 'fr';
  }
  
  $result = prepareQuery("SELECT * FROM translation WHERE word = ?", "s", [$word]);
  $len = mysqli_num_rows($result);
  if($len > 0) {
    $row = mysqli_fetch_assoc($result);
    return $row[$lang];
  }
  else {
    return $word;
  }
}


function insertIntoTable($table, $values, $customvariable = false) {
  $queryData = getInsertQuery($table, $values);
  $query = $queryData['query'];
  $params = $queryData['params'];
  $types = $queryData['types'];
  $result = prepareQuery($query, $types, $params, $customvariable);
  return $result;
}

function getBrowserLang(){
  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  if($lang == 'fr'){
    return 'fr';
  }else{
    return 'en';
  }
}

function getColumnsFromTable($table) {
  $result = runQuery("DESCRIBE $table");
  if($result){
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $columns = array();
    foreach($rows as &$row){
      array_push($columns, $row['Field']);
    }
    return $columns;
  }else{
    return false;
  }
}
  
?>