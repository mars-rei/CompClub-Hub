<!-- errors, doesn't work -->
<!-- feature missed: join button for normal users if not part of this club yet & competitions view -->

<?php
  session_start();

  include 'config/database_pdo.php'; 
  $clubID = $_GET['clubID'];

  $stmt = $con->prepare(
    "SELECT clubName FROM clubs
    WHERE clubID = ?"
  );
  
  $stmt->execute([$clubID]);
  $clubName = $stmt->fetchColumn();

?>

<!DOCTYPE html> 
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 

      <?php
        echo "<title>$clubName</title>"
      ?>

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

    <div class="contdash">
      <?php
        include 'config/database_pdo.php';
        // this code works when the user is not logged in they the join button will display
        
        $clubID = isset($_GET["clubID"]) ? $_GET["clubID"] : 1; // default value

        $isLoggedIn = isset($_SESSION["userID"]);
        $userID = $_SESSION["userID"] ?? null;

        $isMember = false; 

        if ($isLoggedIn) {
          try{
            $stmt = $con->prepare("SELECT * FROM memberships WHERE userID = ? AND clubID = ?");
            $stmt->execute([$userID, $clubID]);


          if ($stmt->rowCount() > 0) {
            $isMember = true; // if alray a memebr
          }
        } catch (PDOException $e) {
          $_SESSION["message"] = "Error: " . $e->getMessage();
        }

        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["clubID"])) {
          if ($isLoggedIn && !$isMember) {
            try {
                  // insert new membership
              $stmt = $con->prepare("INSERT INTO memberships (userID, clubID, score, activityStatus) VALUES (?, ?, 0, 1)");
              $stmt->execute([$userID, $clubID]);
              $_SESSION["message"] = "Successfully joined the club.";
                //edirect to club page
              header("Location: club_page.php?clubID=" .$clubID);
              exit;

            } catch (PDOException $e) {
              $_SESSION["message"] = "error: " .$e->getmessage();
            }
            
          }
        }
        ?>

        <?php
          include 'config/database_pdo.php';

          if ($isLoggedIn && !$isMember) {
            echo '<form method="post" action="club_page.php">';
            echo '<input type="hidden" name="clubID" value=" ' . htmlspecialchars($clubID) .'">'; 
            echo '<div class="d-flex justify-content-end">';
            echo '<button type="submit" class="btn btn-secondary"> Join Club</button>';
            echo '</div>';
          echo '</form>';
          }
        ?> 
    </div>

    <div class="container">
      <?php
        echo "<h1 class='display-3'><strong>$clubName</strong></h1>"
      ?>
    </div>

    <div class="container-fluid">
      <div class="row d-flex">
        <div class="col-sm-8" id="clubPageLeaderboard">
          <h1 class='display-6'><strong>Leaderboard</strong></h1>
          <table class="table">
            <thead>
              <tr>
                <!-- will be full name if logged in (for member)-->
                <!-- will be username if not logged in -->
                <th>Member</th>
                <th>Score</th>
                <th>Competitions Attended</th>
                <th>Win rate (%)</th>
              </tr>
            </thead>

            <tbody>
              <?php
              include 'config/database_pdo.php';
            
              // check if the user is logged in
              $isLoggedIn = isset($_SESSION['userID']) ? $_SESSION['userId'] : null;

              //fetch data for learderboard
            $query = "
                SELECT
                u.UserId,
                CONCAT(u.FirstName, ' ', u.LastName) AS FullName,
                u.Username,
                COUNT(CASE WHEN m.result = u.UserID THEN 1 END) AS Score, 
                COUNT(CASE WHEN u.UserId IN (m.competitor1, m.competitor2) THEN 1 END) AS CompetitionsAttended,
                ROUND(
                (COUNT(CASE WHEN m.result = u.UserId THEN 1 END) * 100.0) /
                COUNT(CASE WHEN u.userId IN (m.competitor1, m.competitor2) THEN 1 END), 2
                ) AS WinRate
                FROM
                  tbl_user u
                LEFT JOIN
                  tbl_match m
                ON
                  u.UserId IN (m.competitor1, m.competitor2)
                GROUP BY
                  u.UserId, u.Username, u.FirstName, u.LastName
                ORDER BY
                  score DESC, WinRate DESC; ";
            
                  $stmt = $con->prepare($query);
                
                  $stmt->execute();
                  $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

              //fetch data and display
              
               foreach ($leaderboard as $player) {
                $displayName = ($isLoggedIn) ? $player['FullName'] : $player['Username'];
                echo "<tr>";
                echo "<td>{$displayName}</td>";
                echo "<td>{$player['Score']}</td>";
                echo "<td>{$player['CompetitionsAttended']}</td>";
                echo "<td>{$player['WinRate']}</td>";
                echo "</tr>";
               }
              
              ?>
              </tbody>

          </table>
       
         
        </div>

        <div class="col-sm-4" id="clubDetails">
          <!-- club details (moved from bottom to top) -->
          <!-- adjust so no white lines in this table -->
          <h1 class='display-6'><strong>Club Details</strong></h1>
          <table class="table">
            <tbody>
              <?php 
          include 'config/database_pdo.php';
          $clubID = isset($_GET['clubID']) ? $_GET['clubID'] : 1;
         
          $query = "SELECT * FROM clubs WHERE clubID = ?";

          $stmt = $con->prepare($query);
          $stmt->execute([$clubID]);
          $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

       

          if (!$clubs) {
            echo "Club not found.";
            exit;
          }
          $club = $clubs[0];
          ?>
         
              <tr>
                <!-- must adjust other pages too but -->
                <!-- instead of doing 'Name: name', just put name -->
                <th>Club Name:</th>
                <td><?php echo htmlspecialchars($club['clubName'] ?? ''); ?></td>
              </tr>
              <tr>
                <th>Club Email:</th>
                <td><?php echo htmlspecialchars($club['clubEmail'] ?? ''); ?></td>
              </tr>
              <tr>
                <th>Category:</th>
                <td><?php echo htmlspecialchars($club['category'] ?? ''); ?></td>
              </tr>
              <tr>
                <th>Description:</th>
                <td><?php echo htmlspecialchars($club['description'] ?? ''); ?></td>
              </tr>
              <tr>
                <th>Address:</th>
                <td><?php echo htmlspecialchars($club['address'] ?? ''); ?></td>
              </tr>
              <tr>
                <th>Activity Status:</th>
                <td><?php echo $club['activityStatus'] ? 'Active' : 'Inactive';?></td>
              </tr>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="container-fluid">
      <div class="row d-flex">
        <div class="col-sm-4" id="clubDetails">
          <h1 class='display-6'><strong>Club Details</strong></h1>
          <table class="table table-striped">
            <tbody>
              <?php
                  $sql = "SELECT * FROM clubs
                    WHERE clubID = :query";

                  $stmt = $con->prepare($sql);
                  $stmt->bindParam(':query', $clubID);
                  $stmt->execute();
                  $data = $stmt->fetchAll();

                  if ($data) {
                    foreach ($data as $info) {
                      echo "<tr><td><strong>Name:</strong></td><td>" . $info["clubName"] . "</td></tr>";
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
                ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row d-flex">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
          <h1 class='display-6'><strong>Competitions</strong></h1>
          <table class="table">
            <thead>
              <tr>
                <th>Competition Name</th>
                <th>Members Participated</th>
                <th>Date</th>
                <th>Rounds</th>
                <th>1st Place</th>
                <th>2nd Place</th>
                <th>3rd Place</th>
              </tr>
            </thead>

            <tbody>
              <?php
              include 'config/database_pdo.php';
              // fetch competition details

              $query = "SELECT * FROM competitions
                      JOIN clubs ON competitions.club = clubs.clubID
                      WHERE clubs.clubID = :clubID";

              $stmt = $con->prepare($query);
              $stmt->bindValue(":clubID", $clubID, PDO::PARAM_INT);
              $stmt->execute();
              $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
              
              foreach ($competitions as $competition) {
               // Query for fetching the latest round info and winners for each competition
          $queryRound = "SELECT
          m.roundNum,
          c.start,
          c.end,
          (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM tbl_user u WHERE u.userID = c.firstPlace) AS FirstPlace,
          (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM tbl_user u WHERE u.userID = c.secondPlace) AS SecondPlace,
          (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM tbl_user u WHERE u.userID = c.thirdPlace) AS ThirdPlace
          FROM competitions c
          LEFT JOIN matches m ON c.competitionID = m.competitionID
          WHERE c.competitionID = :competitionID
          ORDER BY m.roundNum DESC LIMIT 1";  // Fetch the latest round

        $stmtRound = $con->prepare($queryRound);
        $stmtRound->bindValue(':competitionID', $competition['competitionID']);
        $stmtRound->execute();
        $roundData = $stmtRound->fetch(PDO::FETCH_ASSOC);


                                //get the top 3 winners
                                $queryWinners = "SELECT
                                                  (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM users u WHERE u.userID = c.firstPlace) AS FirstPlace,
                                                  (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM users u WHERE u.userID = c.secondPlace) AS SecondPlace,
                                                  (SELECT CONCAT(u.firstName, ' ', u.lastName) FROM users u WHERE u.userID = c.thirdPlace) AS ThirdPlace
                                                  FROM competitions c
                                                  WHERE competition = :competitionID";
                              $stmtRound = $con->prepare($queryWinners);
                              $stmtRound->bindValue(':competitionID', $competition['competitionID']);
                              $stmtRound->execute();
                              $roundData = $stmtRound->fetchAll(PDO::FETCH_ASSOC);  
                              echo "<tr>";
                              echo "<td>" . (isset($roundData[0]['roundNum']) ? htmlspecialchars($roundData[0]['roundNum']) : 'N/A') . "</td>";
                              echo "<td>" . htmlspecialchars($competition["competitionName"]) . "</td>";
                              echo "<td>" . htmlspecialchars($competition["clubName"]) . "</td>";
                              echo "<td>" . htmlspecialchars($competition["MembersParticipated"]) . "</td>";
                              echo "<td>" . htmlspecialchars($competition["start"]) . " - " . htmlspecialchars($competition["end"]) . "</td>";
                              echo "<td>" . htmlspecialchars($roundData["roundNum"] ?? 'N/A') . "</td>";
                              echo "<td>" . (isset($roundData[0]['FirstPlace']) ? htmlspecialchars($roundData[0]['FirstPlace']) : 'N/A') . "</td>";
                              echo "<td>" . (isset($roundData[0]['SecondPlace']) ? htmlspecialchars($roundData[0]['SecondPlace']) : 'N/A') . "</td>";
                              echo "<td>" . (isset($roundData[0]['ThirdPlace']) ? htmlspecialchars($roundData[0]['ThirdPlace']) : 'N/A') . "</td>";
                              echo "</tr>";

              }

              ?>
            
            </tbody>
          </table>
        </div>
        <div class="col-sm-1"></div>
      </div>
    </div>

  </body>
</html>
