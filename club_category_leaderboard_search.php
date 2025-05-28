<!-- improvements to make next time: change header file depending on if user is logged in or not -->

<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Club Category Leaderboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>

  <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body>

  <header> 
    <!-- php for menu -->
    <?php 
      include 'commonPhp/headerLogIn.php';
    ?>
  </header>

<div class="container">

  <?php
    include 'config/database_pdo.php';

    $query = '';
    if (isset($_GET['clubCategoryLeaderboard'])) {
      $query = $_GET['clubCategoryLeaderboard'];
    }

    $sql = "SELECT users.username, 
      users.firstName, users.lastName, 
      SUM(memberships.score) AS totalCategoryScore
    FROM users
    JOIN memberships on users.userID=memberships.userID
    JOIN clubs on memberships.clubID=clubs.clubID
    WHERE category = :query
    AND users.deleted = false
    AND memberships.activityStatus = true
    GROUP BY users.userID, users.username, users.firstName, users.lastName
    ORDER BY totalCategoryScore DESC";

    $stmt = $con->prepare($sql);
    $userInput = $query;
    $stmt->bindParam(':query', $userInput);

    $stmt->execute();

    $data = $stmt->fetchAll();

    echo "<h1 class='display-3'><strong>Search Results for the Leaderboard of the Club Category: " . $query . "<strong></h1>";
    
    echo '<table class="table">';

    if ($data) {
      echo '<tr><th>Rank</th><th>Username</th><th>Name</th><th>Score</th></tr>';
      $count = 1;
      foreach ($data as $info) {
        echo "<tr>" .
        "<td>" . $count . "</td>" .
        "<td>" . $info["username"] . "</td>" .
        "<td>" . $info["firstName"] . " " . $info["lastName"] . "</td>"  .
        "<td>" . $info["totalCategoryScore"] ."</td>"  .
        '</tr>';
        $count++;
      }
    } else {
      echo '<tr><td>No clubs categories found matching your search.</td></tr>';
    } 
    echo "</table>";
  ?>

</div>

</body>
</html>
