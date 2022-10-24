<?php
//DB details
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'template_php_dashboard';
//Create connection and select DB
$connection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if($connection->connect_error) {
   die("Unable to connect databases: " . $connection->connect_error);
}
?>
