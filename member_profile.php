<!-- error to do with login -->

<?php
  session_start();
  include 'config/database_pdo.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>Club Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
  </head>

  <body>
    <header> 
      <!-- PHP for menu -->
      <?php 
        include 'commonPhp/headerLogIn.php';
      ?>
    </header>

    <div class="contdash">
      <?php
     
      // Check if user is logged in
      if (isset($_SESSION["userID"])) {
        $userID = $_SESSION["userID"];

        try {
          // Get the user's details
          $stmt = $con->prepare("SELECT u.Username, CONCAT(u.FirstName, ' ', u.LastName) AS FullName, 
                                (SELECT COUNT(*) FROM competitions c JOIN memberships m ON c.clubID = m.clubID WHERE m.userID = u.UserID) AS CompetitionsAttended,
                                (SELECT SUM(m.score) FROM memberships m WHERE m.userID = u.UserID) AS TotalScore
                                FROM tbl_user u WHERE u.UserID = ?");
          $stmt->execute([$userID]);
          $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
         echo "Error " .$e->getMessage();
        }

        if ($userDetails) {
          echo '<div class="welcome-message">';
          echo '<h2>Welcome, ' . htmlspecialchars($userDetails['Username']) . '!</h2>';
          echo '<p>Full Name: ' . htmlspecialchars($userDetails['FullName']) . '</p>';
          echo '<p>Number of Competitions Participated: ' . htmlspecialchars($userDetails['CompetitionsAttended']) . '</p>';
          echo '<p>Total Score: ' . htmlspecialchars($userDetails['TotalScore']) . '</p>';
          echo '</div>';
        } else {
        echo '<p> No user found! </p>';
      } 
    } else {
        echo "<p> Please log in to see your details.</p>";
    }
      ?>
    </div>

    <div class="container">
      <?php
      // Get the club details
      $clubID = isset($_GET['clubID']) ? $_GET['clubID'] : 1;
      try {
        $stmt = $con->prepare("SELECT clubName FROM clubs WHERE clubID = ?");
        $stmt->execute([$clubID]);
        $club = $stmt->fetch(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
      }

      if ($club) {
        echo '<div class="club-info">';
        echo '<h1>' . htmlspecialchars($club['clubName']) . '</h1>';
        echo '</div>';
      } else {
        echo '<p>Club not found.</p>';
      }
      ?>
    </div>
  </body>
</html>
