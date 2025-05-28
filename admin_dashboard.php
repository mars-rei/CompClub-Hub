<!-- feature missed:
- reactivating and deactivating club member memberships

<?php
  session_start();

  if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
    header('Location: login.php'); 
    exit();
  }

  // redirecting to the page for creating a competition
  if (isset($_POST["createComp"])) {
    header("Location: create_competition.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">    
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>
  <link rel="stylesheet" href="css/styles.css"> 

</head>
<body>

<header>
  <!-- php for menu -->
  <?php 
    include 'commonPhp/headerLogOut.php';
  ?>
</header>

<div class="contdash">
  <h1><Strong>My Admin Dashboard</Strong></h1>

  <form action="admin_dashboard.php" method="post">
    <button id="adminDashButton" name="createComp" type="submit" class="btn btn-dark">Create Competition</button>
  </form>
</div>

<div class="container-fluid">
  <div class="row">

  <!-- account details -->
    <div class="col-sm-6">
      <?php
        include 'config/database_pdo.php';

        $sql = "SELECT admins.username, admins.password FROM admins
        WHERE adminID = :query";

        $stmt = $con->prepare($sql);
        $adminID = $_SESSION["id"];
        $stmt->bindParam(':query', $adminID);
        $stmt->execute();
        $data = $stmt->fetchAll();

        echo "<h1 class='display-6'><strong>Account Details</strong></h1>"; 
             
        echo "<table class='table table-striped'><tbody>";
        if ($data) {
          foreach ($data as $info) {
            echo "<tr><td><strong>Username:</strong></td><td>" . $info["username"] . "</td></tr>";
            $adminUsername = $info["username"];
            echo "<tr><td><strong>Password:</strong></td><td>" . $info["password"] . "</td></tr>";
            $adminPassword = $info["password"];
          }
        } 
        echo "</tbody></table>";
        
      ?>

      <div class="container">
        <div class="row">
          <div class="col text-center">
            <form id="editAdminForm" action="edit_admin_details.php" method="post">
                <button id="editAdminDetailsButton" name="updateAdminDetails" type="submit" class="btn btn-secondary">Update Details</button>
            </form>
          </div>
        </div>
      </div>

    
    </div> 

    <!-- club details -->
    <div class="col-sm-6">
      <?php
        include 'config/database_pdo.php';

        $sql = "SELECT * FROM clubs
        JOIN admins on clubs.clubID=admins.club
        WHERE admins.adminID = :query";

        $stmt = $con->prepare($sql);
        $adminID = $_SESSION["id"];
        $stmt->bindParam(':query', $adminID);
        $stmt->execute();
        $data = $stmt->fetchAll();

        echo "<h1 class='display-6'><strong>Club Details</strong></h1>";
        echo "<table class='table table-striped'><tbody>";
        if ($data) {
          foreach ($data as $info) {
            echo "<tr><td><strong>Name:</strong></td><td> <a href='club_page.php?clubID=" . 
            $info["clubID"] .  "' class='link-light'>" . 
            $info["clubName"] . "</a></td></tr>";
            echo "<tr><td><strong>Email:</strong></td><td>" . $info["clubEmail"] . "</td></tr>";
            echo "<tr><td><strong>Category:</strong></td><td>" . $info["category"] . "</td></tr>";
            echo "<tr><td><strong>Description:</strong></td><td>" . $info["description"] . "</td></tr>";
            echo "<tr><td><strong>Address:</strong></td><td>" . $info["address"] . "</td></tr>";
            if ($info["activityStatus"] == 1) {
              echo "<tr><td><strong>Activity status:</strong></td><td>Active</td></tr>";
            } else {
              echo "<tr><td><strong>Activity status:</strong></td><td>Inactive</td></tr>";
            }
          }
        } 
        echo "</tbody></table>";  
      ?> 
      
      <div class="container">
        <div class="row">
          <div class="col text-center">
            <form action="edit_club_details.php" method="post">
              <button id="editClubDetailsButton" name="updateClubDetails" type="submit" class="btn btn-secondary">Update Details</button>
            </form>
          </div>
        </div>
      </div>
      
    </div> 
</div> 

<div class="container-fluid">
  <!-- members  -->
  <div class="row">
    <?php
      include 'config/database_pdo.php';

      // find club the admin is managing
      $sql = "SELECT club FROM admins
        WHERE admins.adminID = :query";

      $stmt = $con->prepare($sql);
      $adminID = $_SESSION["id"];
      $stmt->bindParam(':query', $adminID);
      $stmt->execute();
      $club = $stmt->fetchColumn();

      // find club members
      $sql = "SELECT users.firstName, users.lastName, users.username, 
        users.email, memberships.score, memberships.clubRank, memberships.activityStatus FROM users
        JOIN memberships ON users.userID=memberships.userID
        JOIN clubs ON memberships.clubID=clubs.clubID
        WHERE clubs.clubID = :query
        ORDER BY memberships.clubRank, memberships.score DESC";

      $stmt = $con->prepare($sql);
      $stmt->bindParam(':query', $club);
      $stmt->execute();
      $data = $stmt->fetchAll();

      echo "<h1 class='display-6'><strong>Club Members</strong></h1>";
      echo "<table class='table table-striped'>";
      
      if ($data) {
        echo "<thead>";
        echo "<tr><th>Name</th><th>Username</th><th>Email</th><th>Score</th><th>Rank</th><th>Membership Status</th></tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($data as $info) {
          echo "<tr>" .
            "<td>" . $info["firstName"] . " " . $info["lastName"] . "</td>" .
            "<td>" . $info["username"] . "</td>" .     
            "<td>" . $info["email"] . "</td>" .     
            "<td>" . $info["score"] . "</td>" .     
            "<td>" . $info["clubRank"] . "</td>";
            
          if ($info["activityStatus"]) {
            $status = "Active";
          } else {
            $status = "Inactive";
          }
          echo "<td>" . $status . "</td>" . "</tr>";
        }
      } else {
        echo "<tbody>";
        echo "<tr><td>No members in this club yet.</td></tr>";
      }
      echo "</tbody></table>";  
    ?> 
  </div>
</div>  

<div class="container-fluid">
  <div class="row">
    <!-- ongoing competitions held by the admin's club -->
    <?php
      include 'config/database_pdo.php';

      // find club the admin is managing
      $sql = "SELECT club FROM admins
        WHERE admins.adminID = :query";
  
      $stmt = $con->prepare($sql);
      $adminID = $_SESSION["id"];
      $stmt->bindParam(':query', $adminID);
      $stmt->execute();
      $club = $stmt->fetchColumn();
  
      // find competitions
      $sql = "SELECT * FROM competitions
        JOIN clubs ON competitions.club=clubs.clubID
        WHERE clubs.clubID = :query";
  
      $stmt = $con->prepare($sql);
      $stmt->bindParam(':query', $club);
      $stmt->execute();
      $data = $stmt->fetchAll();

      // find current round of competitions
      $sql = "SELECT matches.roundNum, competitions.start, competitions.end FROM competitions
        JOIN matches ON competitions.competitionID = matches.competition
        WHERE matches.roundNum = (
            SELECT MAX(roundNum) 
            FROM matches 
            WHERE competition = competitions.competitionID
          )
          AND competitions.finished = false
        GROUP BY competitions.competitionID, matches.roundNum, matches.schedule";

      $stmt = $con->prepare($sql);
      $stmt->execute();
      $currentRounds = $stmt->fetchAll();

      echo "<h1 class='display-6'><strong>Ongoing Competitions</strong></h1>";
      echo "<table class='table table-striped'>";
      if ($data && $currentRounds) {
        echo "<thead>";
        echo "<tr><th>Competition</th><th>Participants</th><th>Current Round</th><th>Total Rounds</th></tr>";
        echo "</thead><tbody>";
        for ($i = 0; $i < count($data); $i++) {
          if (date("Y-m-d") < $data[$i]["end"]) {
            echo "<tr><td><a href='competition_page.php?competitionID=" . 
            $data[$i]["competitionID"] .  "' class='link-light'>" . 
            $data[$i]["name"] . "</a></td>" .

            "<td>" . $data[$i]["numParticipants"] . "</td>" .
            "<td>" . $currentRounds[$i]["roundNum"] . "</td>" .
            "<td>" . $data[$i]["numRounds"] . "</td>" .
            "</tr>";
          }
        }
      } else {
        echo "<tbody>";
        echo "<tr><td>No ongoing competitions.</td></tr>";
      }
      echo "</tbody></table>";
    ?>

    <!-- past competitions held by the admin's club -->
    <?php
      include 'config/database_pdo.php';

      // find club the admin is managing
      $sql = "SELECT club FROM admins
        WHERE admins.adminID = :query";
  
      $stmt = $con->prepare($sql);
      $adminID = $_SESSION["id"];
      $stmt->bindParam(':query', $adminID);
      $stmt->execute();
      $club = $stmt->fetchColumn();
  
      // find competitions
      $sql = "SELECT competitions.*, 
        clubs.clubName, 
        u1.firstName AS firstName1, u1.lastName AS lastName1, u1.username AS username1,
        u2.firstName AS firstName2, u2.lastName AS lastName2, u2.username AS username2,
        u3.firstName AS firstName3, u3.lastName AS lastName3, u3.username AS username3
      FROM competitions
      JOIN clubs ON competitions.club = clubs.clubID
      JOIN users AS u1 ON competitions.1stPlace = u1.userID
      JOIN users AS u2 ON competitions.2ndPlace = u2.userID
      JOIN users AS u3 ON competitions.3rdPlace = u3.userID
      WHERE clubs.clubID = :query
        AND competitions.finished = true
      ";

      $stmt = $con->prepare($sql);
      $stmt->bindParam(':query', $club);
      $stmt->execute();
      $data = $stmt->fetchAll();

      if ($data) {
        echo "<h1 class='display-6'><strong>Past Competitions</strong></h1>";
        echo "<table class='table table-striped'><thead>";
        echo "<tr><th>Competition</th><th>Participants</th><th>Rounds</th><th>Duration</th>
        <th>1st Place</th><th>2nd Place</th><th>3rd Place</th></tr>";
        echo "</thead><tbody>";
        foreach ($data as $info) {
          $startDate = date_create($info["start"]);
          $endDate = date_create($info["end"]);
          echo "<tr><td><a href='competition_page.php?competitionID=" . 
            $info["competitionID"] .  "' class='link-light'>" . 
            $info["name"] . "</a></td>" .

          "<td>" . $info["numParticipants"] . "</td>" .
          "<td>" . $info["numRounds"] . "</td>" .
          "<td>" . date_format($startDate,"d F") . " to " . date_format($endDate,"d F Y") . "</td>" .
          "<td>" . $info["firstName1"] . " " . $info["lastName1"] . " (" . $info["username1"] . ")</td>" .
          "<td>" . $info["firstName2"] . " " . $info["lastName2"] . " (" . $info["username2"] . ")</td>" .
          "<td>" . $info["firstName3"] . " " . $info["lastName3"] . " (" . $info["username3"] . ")</td>" .
          "</tr>";
        }
        echo "</tbody></table>";
      }
    ?>
  </div>
</div>

</body>
</html>
