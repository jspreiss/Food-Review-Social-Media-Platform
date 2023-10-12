<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $name = $_POST["name"];
    // "Fruit"
    $name = strtolower($name); 
    $name = ucfirst($name);

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT added_fruit FROM user 
            WHERE id = $_SESSION[user_id]";

    $result = mysqli_query($mysqli, $sql);

    $value = $result->fetch_assoc();
    
    if($value["added_fruit"] > 0 && $_SESSION["user_id"] != 1) { // 1 = admin
        $_SESSION["already_added"] = "Already added a fruit. Limited to 1 per account.";
        header("Location: home.php");
    }
    else {
    
        $sql2 = "INSERT INTO fruit (name, avg_rating, num_rates, added_by)
            VALUES ('$name', NULL, 0, $_SESSION[user_id]);

            UPDATE user 
            SET added_fruit = 1
            WHERE id = $_SESSION[user_id];";

        if ($mysqli->multi_query($sql2) === TRUE) {
            header("Location: home.php");
            exit;
        } else {
            if ($mysqli->errno === 1062) {
                            die("fruit already exists");
                        } else {
                            die($mysqli->error . " " . $mysqli->errno);
                        }
        }
    }
}
