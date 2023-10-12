<?php

$host = "sql311.epizy.com";
$dbname = "epiz_34295265_fruit";
$username = "epiz_34295265";
$password = "pT7olk3yr8nHKA8";

$mysqli = new mysqli(hostname: $host,
                     username: $username,
                     password: $password,
                     database: $dbname);
                     
if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;