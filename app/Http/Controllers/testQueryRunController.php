<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbName = "dbName";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 $query = "SELECT * FROM `application_list`";
 $result = $conn->query($query);

 return $result;

?>
