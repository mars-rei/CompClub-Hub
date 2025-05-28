<!-- to improve for next time:
 - for ongoing competitions, only the 1st round of each ongoing competition is shown,
 unsure how to let later rounds also be shown
 - totalScore is not showing the correct number
-->

<?php
  session_start();

  if (!ISSET($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
    header("Location: login.php"); 
    exit();
  }

  if (isset($_POST["createClubAdmin"])) {
    header("Location: create_club_and_admin.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">

  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>User Dashboard</title>
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
  <h1><Strong>My User Dashboard</Strong></h1>

  <form action="user_dashboard.php" method="post">
    <button id="userDashButton" name="createClubAdmin" type="submit" class="btn btn-dark">Create Club & Admin Account</button>
  </form>
</div>


<div class="container-fluid">

  <div class="row">

    <!-- if user is participating in competitions currently, display them here -->
    <h1 class='display-6'><strong>Ongoing Competitions</strong></h1> 
    <?php
      include 'config/database_pdo.php';

      // if user is in the competitor1 column of matches  
      $stmt = $con->prepare(
        "SELECT clubs.clubID, clubs.clubName, competitions.name, 
        competitions.numRounds AS totalRounds, matches.roundNum,
         matches.schedule, matches.competitor2,
         competitions.competitionID
        FROM clubs
        JOIN competitions ON clubs.clubID = competitions.club
        JOIN matches ON competitions.competitionID = matches.competition
        WHERE matches.competitor1 = ?
          AND competitions.finished = false
          AND matches.roundNum IN (
            SELECT DISTINCT roundNum 
            FROM matches 
            WHERE competition = competitions.competitionID
            AND roundNum <= competitions.numRounds
          )
        ORDER BY matches.roundNum DESC"
      );
    
    
      $stmt->execute([$_SESSION["id"]]);
      $data = $stmt->fetchAll();

      // get opponent's username
      $stmt2 = $con->prepare(
          "SELECT username FROM users
          JOIN matches ON users.userID=matches.competitor2
          WHERE matches.competitor1 = ?"
      );
      
      $stmt2->execute([$_SESSION["id"]]);
      $dataOpp = $stmt2->fetchAll();


      // if user is in the competitor2 column of matches
      $stmt3 = $con->prepare(
        "SELECT clubs.clubID, clubs.clubName, competitions.name, 
        competitions.numRounds AS totalRounds, matches.roundNum, 
        matches.schedule, matches.competitor2, 
        competitions.competitionID
        FROM clubs
        JOIN competitions ON clubs.clubID = competitions.club
        JOIN matches ON competitions.competitionID = matches.competition
        WHERE matches.competitor2 = ?
          AND competitions.finished = false
          AND matches.roundNum IN (
            SELECT DISTINCT roundNum 
            FROM matches 
            WHERE competition = competitions.competitionID
            AND roundNum <= competitions.numRounds
          )
        ORDER BY matches.roundNum DESC"
      );
      
      $stmt3->execute([$_SESSION["id"]]);
      $data2 = $stmt3->fetchAll();

      // get opponent's username
      $stmt4 = $con->prepare(
        "SELECT username FROM users
        JOIN matches ON users.userID=matches.competitor1
        WHERE matches.competitor2 = ?"
      );
      
      $stmt4->execute([$_SESSION["id"]]);
      $data2Opp = $stmt4->fetchAll();


      // display table of ongoing competitions
      echo "<table class='table table-striped'>";

      if ($data || $data2) {
        echo "<thead>";
        echo "<tr><th>Club</th><th>Competition</th><th>Round</th><th>Schedule</th><th>Opponent</th></tr>";
        echo "</thead>";
        echo "<tbody>";

        // if user is competitor 1
        if ($data && $dataOpp) {
          for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]["totalRounds"] > $data[$i]["roundNum"]) {
              echo "<tr><td><a href='club_page.php?clubID=" . $club["clubID"] .  
              "' class='link-light'>" . 
              $club["clubName"] . "</a></td>" .

              "<td><a href='competition_page.php?competitionID=" . 
                $data[$i]["competitionID"] .  "' class='link-light'>" . 
                $data[$i]["name"] . "</a></td>" .
              
              "</td><td>" . $data[$i]["roundNum"] . 
              "</td><td>" . $data[$i]["schedule"] . "</td><td>" . $dataOpp[$i]["username"] . "</td></tr>";
            }
          }
        }

        // if user is competitor 2
        if ($data2 && $data2Opp) {
            for ($i = 0; $i < count($data2); $i++) {
              if ($data2[$i]["totalRounds"] > $data2[$i]["roundNum"]) {
                echo "<tr><td><a href='club_page.php?clubID=" . $club["clubID"] .  
                "' class='link-light'>" . 

                "<td><a href='competition_page.php?competitionID=" . 
                $data2[$i]["competitionID"] .  "' class='link-light'>" . 
                $data2[$i]["name"] . "</a></td>" .

                "<td>" . $data2[$i]["name"] . "</td><td>" . $data2[$i]["roundNum"] . 
                "</td><td>" . $data2[$i]["schedule"] . "</td><td>" . $data2Opp[$i]["username"] . "</td></tr>";
              }
            }
        } 

      } else {
        echo "<tbody>";
        echo "<tr><td>No ongoing competitions.</td></tr>";
      }

      echo "</tbody></table>"; 
    ?>

    <!-- account details -->
    <div class="col-sm-6">
      <?php
        include 'config/database_pdo.php';

        $sql = "SELECT * FROM users
        WHERE userID = :query";

        $stmt = $con->prepare($sql);
        $userID = $_SESSION["id"];
        $stmt->bindParam(':query', $userID);

        $stmt->execute();

        $data = $stmt->fetchAll();

        echo "<h1 class='display-6'><strong>Account Details</strong></h1>";
        echo "<table class='table table-striped'><tbody>";
        if ($data) {
          foreach ($data as $info) {
            echo "<tr><td><strong>Username:</strong></td><td>" . $info["username"] . "</td></tr>";
            echo "<tr><td><strong>Password:</strong></td><td>" . $info["password"] . "</td></tr>";
            echo "<tr><td><strong>First name:</strong></td><td>" . $info["firstName"] . "</td></tr>";
            echo "<tr><td><strong>Last name:</strong></td><td>" . $info["lastName"] . "</td></tr>";
            echo "<tr><td><strong>Email:</strong></td><td>" . $info["email"] . "</td></tr>";
            echo "<tr><td><strong>Total score:</strong></td><td>" . $info["totalScore"] . "</td></tr>";
            echo "<tr><td><strong>Number of active memberships:</strong></td><td>" . $info["numActiveMemberships"] . "</td></tr>";
          }
        } 
        echo "</tbody></table>"; 
      ?>

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-auto">
            <div class="d-flex justify-content-between gap-4">
              <form action="edit_user_details.php" method="post">
                <button id="editUserDetailsButton" name="updateUserDetails" type="submit" class="btn btn-secondary">Update Details</button>
              </form>
              <form action="delete_user.php">
                <button id="deleteUserButton" name="deleteUser" type="submit" class="btn btn-secondary">Delete Account</button>
              </form>
            </div>
          </div>
        </div>
      </div>


    </div>

    <!-- membership details -->
    <div class="col-sm-6">
      <?php
      include 'config/database_pdo.php';

      $sql = "SELECT clubs.clubID, clubName, memberships.score, memberships.activityStatus, memberships.clubRank FROM clubs
      JOIN memberships on clubs.clubID=memberships.clubID
      JOIN users on memberships.userID=users.userID
      WHERE users.userID = :query";

      $stmt = $con->prepare($sql);
      $userID = $_SESSION["id"];
      $stmt->bindParam(':query', $userID);
      $stmt->execute();
      $data = $stmt->fetchAll();

      echo "<h1 class='display-6'><strong>Clubs You Are In</strong></h1>";
      echo "<table class='table table-striped'>";
      if ($data) {
        echo "<thead>";
        echo "<tr><th>Club</th><th>Rank</th><th>Score</th><th>Membership Status</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        if ($data) {
          foreach ($data as $info) {
            if ($info["activityStatus"]) {
              $status = "Active";
            } else {
              $status = "Inactive";
            }
            echo "<tr><td><a href='club_page.php?clubID=" . $info["clubID"] .  
            "' class='link-light'>" . 
            $info["clubName"] . "</a></td><td>" .
            $info["clubRank"] . "</td><td>" . $info["score"] . "</td><td>" . $status . "</td></tr>";
          }
        } 
      } else {
        echo "<tr><td>You are not in any clubs yet.</td></tr>";
      }
      echo "</tbody></table>"; 
      ?>
    </div> 
  </div> 

  <!-- past competitions participated in -->
  <div class="row">
    <?php
      include 'config/database_pdo.php';
  
      // find competitor details
      $stmt = $con->prepare(
        "SELECT competitions.name, clubs.clubID, clubs.clubName, 
        competitors.score, competitions.numRounds,
        competitors.finalRank, competitions.numParticipants,
        competitions.competitionID
        FROM clubs
        JOIN competitions ON clubs.clubID = competitions.club
        JOIN competitors ON competitions.competitionID = competitors.competition
        WHERE competitors.userID = ?
        AND competitions.finished = true"
      );

      $stmt->execute([$userID]);
      $data = $stmt->fetchAll();

      // displays past competition details
      if ($data) {
        echo "<h1 class='display-6'><strong>Competitions Participated In</strong></h1>";
        echo "<table class='table table-striped'><thead>";
        echo "<tr><th>Competition</th><th>Club</th><th>Score</th><th>Final Rank</th></tr>";
        echo "</thead><tbody>";
        foreach ($data as $info) {
          echo "<tr>" . 
          "<td><a href='competition_page.php?competitionID=" . 
                $info["competitionID"] .  "' class='link-light'>" . 
                $info["name"] . "</a></td>" .

          "<td><a href='club_page.php?clubID=" . $info["clubID"] .  
          "' class='link-light'>" . 
          $info["clubName"] . "</a></td>" .

          "<td>" . $info["score"] . " / " . $info["numRounds"] . "</td>" .
          "<td>" . $info["finalRank"] . " / " . $info["numParticipants"] . "</td>" .
          "</tr>";
        }
        echo "</tbody></table>";
      }
    ?>
  </div>  

</body>
</html>
