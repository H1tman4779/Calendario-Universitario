<?php

$host = 'localhost';
$username = 'root';
$password = 'MySql';
$dbname = 'calendario_universitario_db';

$conn = new mysqli($host, $username, $password, $dbname);
if (!$conn) {
    die("Cannot connect to the database." . $conn->error);
}