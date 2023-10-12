<?php

$name = $_GET["name"];
$name = strtolower($name); 
$name = ucfirst($name);

$filename = 'fruit.txt';
$file = file_get_contents($filename);

if (stripos($file, $name) === FALSE) {
    $is_available = FALSE;
}
else{
    $is_available = TRUE;
}

header("Content-Type: application/json");

echo json_encode(["available" => $is_available]);