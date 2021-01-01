<?php
/**
 * This file Holds database connection details
 */

$host = "localhost";
$userName = "root";
$password = "";
$dbName = "restaurant";
// Create connection
$conn = new mysqli($host, $userName, $password, $dbName);
// Check if connection is successful
if ($conn->connect_error) die("! Connection failed: " . $conn->connect_error);
?>