<?php

if (empty($_POST["name"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$name = $_POST["name"];
$email = $_POST["email"];
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM user WHERE email = '$email' ";

$result = mysqli_query($mysqli, $sql);

if(mysqli_num_rows($result)) {
    exit('This email is already being used');
}
else {
    
    $sql2 = "INSERT INTO user (name, email, password_hash, added_fruit, points)
        VALUES ('$name', '$email', '$password_hash', 0, 0)";

    if ($mysqli->multi_query($sql2) === TRUE) {
        header("Location: signup-success.html");
        exit;
    } else {
        if ($mysqli->errno === 1062) {
                        die("email already taken");
                    } else {
                        die($mysqli->error . " " . $mysqli->errno);
                    }
    }
}










