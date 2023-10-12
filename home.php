<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql1 = "SELECT * FROM user
            WHERE id = {$_SESSION["user_id"]};";   
    $result1 = $mysqli->query($sql1);
    $user = $result1->fetch_assoc();


    $sql2 = "SELECT id, name FROM fruit";
    $result2 = mysqli_query($mysqli, $sql2);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="/fruit/validation_addnew.js" defer></script>
</head>
<body>
    
    <h1>Home</h1>
    
    <?php if (isset($user)): ?>
        
        <a href="logout.php">Log out</a></p>
        
 
        <p>Welcome to Rate My Fruit, <?= htmlspecialchars($user["name"]) ?>! You are free to rate fruits on a scale (0-10) based on how much 
            you like them, or add your own favorite fruit if you don't see it!</p>
   
        <p><i>*Limited to one rate per fruit, and one added fruit per account.</i></p>
        <div style="display: inline-block">
            <form action="process-rating.php" method="post">
            
                <label for="name">Select a Fruit</label>
                <select name="name">
                    <?php
                        while ($rows = mysqli_fetch_array(
                                $result2)){
                    ?>
                        <option value="<?php echo $rows["id"];?>">
                            <?php echo $rows["name"];?>
                        </option>
                    <?php
                        }
                    ?>
                </select>

                <label for="rating">Rate</label>
                <div>
                    <div style="display: inline-block;">
                    <input type="range" id="rating" name="rating"
                    min="0" max="10" oninput="this.nextElementSibling.value = this.value">
                    
                    <output>5</output>
                    </div>
                </div>
                

                <button>Send</button>

            </form>
        
            <?php if (isset($_SESSION["already_rated"])): ?>

                <p> <?= htmlspecialchars($_SESSION["already_rated"]) ?></p>

            <?php $_SESSION["already_rated"] = NULL;
                endif; ?>
        </div>

        <div style="display: inline-block; padding-left: 30%">

            <form action="process-addnew.php" method="post" id="addnew" novalidate>
                <div>
                    <label for="name">Add New Fruit</label>
                    <input type="text" id="name" name="name">
                </div>
                <button>Send</button>
            </form>

            <?php if (isset($_SESSION["already_added"])): ?>

                <p> <?= htmlspecialchars($_SESSION["already_added"]) ?></p>

            <?php $_SESSION["already_added"] = NULL;
                endif; ?>
        </div>
       
                
        <h2>Fruit Leaderboard</h2>

        <p>List of all added fruits and their ratings.</p>

        <table>
        <?php
        if (isset($_GET['orderfruit'])) {
            $orderfruit = trim(strip_tags($_GET['orderfruit']));
            if ($orderfruit == 'DESC'){
                $orderfruit = 'ASC';
            } else {
                $orderfruit = 'DESC';
            }  
        } else {
            $orderfruit = 'DESC';
        }
        ?>
        
        <tr>
            <th><a href="home.php?sortfruit=name&&orderfruit=<?php echo $orderfruit;?>">Name</th>
            <th><a href="home.php?sortfruit=avg_rating&&orderfruit=<?php echo $orderfruit;?>">Average Rating</th>
            <th><a href="home.php?sortfruit=num_rates&&orderfruit=<?php echo $orderfruit;?>">Number of Rates</th>
            <th><a href="home.php?sortfruit=added_by&&orderfruit=<?php echo $orderfruit;?>">Added By</th>
        </tr>
        
        <?php
        if (isset($_GET['sortfruit']) && isset($_GET['orderfruit'])) {

            $columnfruit = trim(strip_tags($_GET['sortfruit']));
            $orderfruit = trim(strip_tags($_GET['orderfruit']));
            $orderby = "ORDER BY $columnfruit $orderfruit";
        } else {
            $orderby = "ORDER BY fruit.avg_rating DESC";
        }
        
        $sql = "SELECT fruit.name, fruit.avg_rating, fruit.num_rates, user.name AS username
                FROM fruit, user
                WHERE fruit.added_by = user.id 
                $orderby";

        $result = $mysqli->query($sql);
        if ($result-> num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if (!isset($row["avg_rating"])){
                    $row["avg_rating"] = '-';
                } else {
                    $row["avg_rating"] = round($row["avg_rating"], 2);
                }
                echo "<tr><td>" . $row["name"]. "</td><td>" . $row["avg_rating"] . "</td><td>"
                . $row["num_rates"]. "</td><td>". $row["username"] . "</td></tr>";
            }
            echo "</table>";
        }
        
        ?>
        </table>

        <h2>User Leaderboard</h2>

        <p>Rating a fruit gives +2 points each. You also earn points based on the rating
            that others rate your fruit.</p>

        <?php
        if (isset($_GET['orderuser'])) {
            $orderuser = trim(strip_tags($_GET['orderuser']));
            if ($orderuser == 'DESC'){
                $orderuser = 'ASC';
            } else {
                $orderuser = 'DESC';
            }  
        } else {
            $orderuser = 'DESC';
        }
        ?>
        <table>
        <tr>
            <th><a href="home.php?sortuser=name&&orderuser=<?php echo $orderuser;?>">Name</th>
            <th><a href="home.php?sortuser=name&&orderuser=<?php echo $orderuser;?>">Points</th>
        </tr>

        <?php
        if (isset($_GET['sortuser']) && isset($_GET['orderuser'])) {

            $columnuser = trim(strip_tags($_GET['sortuser']));
            $orderuser = trim(strip_tags($_GET['orderuser']));
            $orderby = "ORDER BY $columnuser $orderuser";
        } else {
            $orderby = "ORDER BY points DESC";
        }
        $sql = "SELECT name, points FROM user
                WHERE NOT id = 1
                $orderby";

        $result = $mysqli->query($sql);
        if ($result-> num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["name"]. "</td><td>" . $row["points"] . "</td></tr>";
            }
            echo "</table>";
        }
        
        $mysqli->close();
        
        ?>
        </table>
        
    <?php else: ?>

        <h3>The World's Premier Fruit-Rating Platform.</h3>

        <img src="fruits.jpg" alt="fruits" />
        
        <p><a href="login.php">Log in</a> or <a href="signup.html">Sign up</a></p>
        
    <?php endif; ?>
    
</body>
</html>
    
    
    
    
    
    
    
    
    
    
    