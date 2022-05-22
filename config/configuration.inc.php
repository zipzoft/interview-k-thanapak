<?php
require 'vendor/autoload.php';
use MongoDB\Client;
$con = new Client("mongodb://localhost:27017");  
$db = $con->mydb;
?>