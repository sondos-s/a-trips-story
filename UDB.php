<?php
    $host="localhost";
    $dbname="a-trips-story-db";
    $username="root";
    $password="";

    $mysqli=new mysqli(hostname:$host,
    username:$username,
    password:$password,
    database:$dbname);

    // Create a connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check the connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Set the character set for proper encoding
    $mysqli->set_charset("utf8mb4");

    return $mysqli;
?>


