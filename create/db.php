<?php

/* NOTE: Only used once to create database */

// Connection req. fields
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
else {
	echo "Connected Successfully!\n";
}

//Creating a Database through php
$sql= "CREATE DATABASE eimvs_db";

// for dropping db
//$sql= "DROP DATABASE eimvs_db";

/* eimvs_db abbr: Electronic Items Market Viewer System DataBase */

// Check query
if (mysqli_query($conn, $sql)) {
	echo "Database created successfully";
}
else {
	echo "Error: " . mysqli_error($conn);
}

?>