<?php
  session_start();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1"> 

  <title>Club Search</title>

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
    if (isset($_GET['clubSearch'])) {
      $query = $_GET['clubSearch'];
    }

    $sql = "SELECT * FROM clubs WHERE clubName LIKE :query";

    $stmt = $con->prepare($sql);

    $userInput = "%" . $query . "%";
    $stmt->bindParam(':query', $userInput);


    $stmt->execute();
    $clubs = $stmt->fetchAll();


    $sql = "SELECT COUNT(DISTINCT(userID)) FROM memberships JOIN clubs on memberships.clubID=clubs.clubID WHERE clubs.clubName LIKE :query";

    $stmt = $con->prepare($sql);

    $userInput = "%" . $query . "%";
    $stmt->bindParam(':query', $userInput);


    $stmt->execute();
    $clubMembers = $stmt->fetch(mode: PDO::FETCH_ASSOC);

    echo "<h1 class='display-3'><strong>Search Results for the Club: " . $query . "<strong></h1>";

    echo '<table class="table">';
    if ($clubs) {
      echo '<tr><th>Club Name</th><th>Contact</th><th>Category</th><th>Description</th><th>Address</th><th>No. of Members</th></tr>';
      foreach ($clubs as $club) {
        foreach ($clubMembers as $clubMembersCount) {
          echo "<tr>" .
          "<td><a href='club_page.php?clubID=" . $club["clubID"] .  
            "' class='link-light'>" . 
            $club["clubName"] . "</a></td>" .
          "<td>" . $club["clubEmail"] ."</td>"  .
          "<td>" . $club["category"] . "</td>" .
          "<td>" . $club["description"] ."</td>"  .
          "<td>" . $club["address"] ."</td>"  .
          "<td>" . $clubMembersCount . "</td>" .
          '</tr>';
        }
      }
    } else {
      echo '<tr><td>No clubs found matching your search.</td></tr>';
    }
    echo "</table>";
  ?>
</div>

</body>
</html>
