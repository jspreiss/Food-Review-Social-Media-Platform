<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $name = $_POST["name"];
    $rating = $_POST["rating"];

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM user_rating 
            WHERE user_id = $_SESSION[user_id] AND fruit_id = $name";

    $result = mysqli_query($mysqli, $sql);

    $value = $result->fetch_assoc();

    if(mysqli_num_rows($result) && $_SESSION["user_id"] != 1) { // 1 = admin
        $_SESSION["already_rated"] = "Already rated this fruit. You rated it a {$value["rating"]}.";
        header("Location: home.php");
    }
    else {

        $sql1 = "SELECT avg_rating, num_rates, added_by FROM fruit
                WHERE id = $name;";
                
        $result = $mysqli->query($sql1);
        $values = $result->fetch_assoc();

        $newNumRates = $values["num_rates"] + 1;
        
        $newRating = ($values["avg_rating"] * $values["num_rates"] + $rating)
                    / $newNumRates;
        
        $added_by = $values["added_by"];


        $sql2 = "UPDATE fruit 
                SET avg_rating = $newRating, num_rates = $newNumRates
                WHERE id = $name;

                UPDATE user 
                SET points = points + 2
                WHERE id = $_SESSION[user_id];

                UPDATE user 
                SET points = points + $rating
                WHERE id = $added_by;
                
                INSERT INTO user_rating (user_id, fruit_id, rating)
                VALUES ($_SESSION[user_id], $name, $rating);";

       
        if ($mysqli->multi_query($sql2) === TRUE) {
            $_SESSION["already_rated"] = NULL;
            header("Location: home.php");
        } else {
            echo "Error: " . $sql2 . "<br>" . $mysqli->error;
        }
    }
}
