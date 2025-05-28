<!-- improvements for next time: 
  - use functions 
  - change header depending on if user is logged in or not
 -->

<?php
  session_start();

  include 'config/database_pdo.php'; 
  $competitionID = $_GET['competitionID'];

  $stmt = $con->prepare(
    "SELECT name FROM competitions
    WHERE competitionID = ?"
  );
  
  $stmt->execute([$competitionID]);
  $competitionName = $stmt->fetchColumn();
?>

<!DOCTYPE html> 
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 

    <?php
        echo "<title>$competitionName</title>"
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

    <div class="container">
      <?php
        echo "<h1 class='display-4'><strong>$competitionName</strong></h1>"
      ?>
    </div>

    
    <?php
      // display starting rank and 1st round pairings
      $json = 'round1_' . $competitionID . '.json';

      if (file_exists($json)) {
        $jsonData = file_get_contents($json);

        $data = json_decode($jsonData, true);

        echo "<div class='container-fluid'>";
        echo "<div class='row d-flex'>";

        echo "<div class='col-sm-5'>";
        echo "<h1 class='display-6'><strong>Starting Rank</strong></h1>";
        echo "<table class='table' id='startingRank'>";
        echo "<thead><tr><th>Starting Rank</th><th>Username</th><th>Name</th><th>Score</th></tr></thead>";

        echo "<tbody>";
        foreach ($data['startingRank'] as $rank) {
          echo
          "<td>" . $rank['rank'] . "</td>" . 
          "<td>" . $rank['username'] ."</td>" . 
          "<td>" . $rank['name'] . "</td>" . 
          "<td>" . $rank['score'] . "</td>" . 
          "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "<div class='col-sm-7' id='matches'>";
        echo "<h1 class='display-6'><strong>Round 1 Pairings @ " . $data['round1schedule'] . "</strong></h1>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead><tr><th>Match</th><th>Competitor 1</th><th>Score</th><th>Competitor 2</th><th>Score</th></tr></thead>";

        echo "<tbody>";
        foreach ($data['pairings'] as $pairing) {
          echo "<tr>";
          echo "<td>" . $pairing['match'] . "</td>";
          echo "<td>" . $pairing['competitor1'] . "</td>";
          echo "<td>" . $pairing['competitor1_score'] . "</td>";
          echo "<td>" . (isset($pairing['competitor2']) ? $pairing['competitor2'] : '') . "</td>";
          echo "<td>" . (isset($pairing['competitor2_score']) ? $pairing['competitor2_score'] : '') . "</td>";
          echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "</div>";
      }


      // 1st round results
      $json = 'r1_results_' . $competitionID . '.json';

      if (file_exists($json)) {
        $jsonData = file_get_contents($json);

        $data = json_decode($jsonData, true);

        echo "<h1 class='display-6'><strong>Round 1 Results</strong></h1>";
        echo "<div class='row d-flex'>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead><tr><th>Match</th><th>Competitor 1</th><th>Result</th><th>Competitor 2</th></tr></thead>";

        echo "<tbody>";
        $count = 1;
        foreach ($data as $result) {
          $stmt = $con->prepare(
            "SELECT firstName, lastName, username FROM users
            WHERE userID = ?"
          );
          
          $stmt->execute([$result['competitor1']['userID']]);
          $competitor1 = $stmt->fetchAll();

          $stmt->execute([$result['competitor2']['userID']]);
          $competitor2 = $stmt->fetchAll();

          echo "<tr>" .
          "<td>" . $count . "</td>" . 
          "<td>" . $competitor1[0]['firstName'] . " " . $competitor1[0]['lastName'] . " (" . $competitor1[0]['username'] . ")</td>" . 
          "<td>" . $result['competitor1']['score'] . " - " . $result['competitor2']['score'] . "</td>";
          if ($result['competitor2']['userID'] == 0) { // is a bye
            echo "<td></td>";
          } else {
            echo "<td>" . $competitor2[0]['firstName'] . " " . $competitor2[0]['lastName'] . " (" . $competitor2[0]['username'] . ")</td>"; 
          }
          echo "</tr>";

          $count++;
        }
        echo "</tbody>";
        echo "</table>";
        
        echo "</div>";
      }


      // display rank after round 1 and 2nd round pairings
      $json = 'round2_' . $competitionID . '.json';

      if (file_exists($json)) {
        $jsonData = file_get_contents($json);

        $data = json_decode($jsonData, true);

        echo "<div class='row d-flex'>";

        echo "<div class='col-sm-5'>";
        echo "<h1 class='display-6'><strong>Rank After Round 1</strong></h1>";
        echo "<table class='table' id='startingRank'>";
        echo "<thead><tr><th>Starting Rank</th><th>Username</th><th>Name</th><th>Score</th></tr></thead>";

        echo "<tbody>";
        foreach ($data['currentRank'] as $rank) {
          echo
          "<td>" . $rank['rank'] . "</td>" . 
          "<td>" . $rank['username'] ."</td>" . 
          "<td>" . $rank['name'] . "</td>" . 
          "<td>" . $rank['score'] . "</td>" . 
          "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "<div class='col-sm-7' id='matches'>";
        echo "<h1 class='display-6'><strong>Round 2 Pairings @ " . $data['round2schedule'] . "</strong></h1>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead><tr><th>Match</th><th>Competitor 1</th><th>Score</th><th>Competitor 2</th><th>Score</th></tr></thead>";

        echo "<tbody>";
        foreach ($data['pairings'] as $pairing) {
          echo "<tr>";
          echo "<td>" . $pairing['match'] . "</td>";
          echo "<td>" . $pairing['competitor1'] . "</td>";
          echo "<td>" . $pairing['competitor1_score'] . "</td>";
          echo "<td>" . (isset($pairing['competitor2']) ? $pairing['competitor2'] : '') . "</td>";
          echo "<td>" . (isset($pairing['competitor2_score']) ? $pairing['competitor2_score'] : '') . "</td>";
          echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "</div>";
      }


      // 2nd round results
      $json = 'r2_results_' . $competitionID . '.json';

      if (file_exists($json)) {
        $jsonData = file_get_contents($json);

        $data = json_decode($jsonData, true);

        echo "<h1 class='display-6'><strong>Round 2 Results</strong></h1>";
        echo "<div class='row d-flex'>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead><tr><th>Match</th><th>Competitor 1</th><th>Result</th><th>Competitor 2</th></tr></thead>";

        echo "<tbody>";
        $count = 1;
        foreach ($data as $result) {
          $stmt = $con->prepare(
            "SELECT firstName, lastName, username FROM users
            WHERE userID = ?"
          );
          
          $stmt->execute([$result['competitor1']['userID']]);
          $competitor1 = $stmt->fetchAll();

          $stmt->execute([$result['competitor2']['userID']]);
          $competitor2 = $stmt->fetchAll();

          echo "<tr>" .
          "<td>" . $count . "</td>" . 
          "<td>" . $competitor1[0]['firstName'] . " " . $competitor1[0]['lastName'] . " (" . $competitor1[0]['username'] . ")</td>" . 
          "<td>" . $result['competitor1']['score'] . " - " . $result['competitor2']['score'] . "</td>";
          if ($result['competitor2']['userID'] == 0) { // is a bye
            echo "<td></td>";
          } else {
            echo "<td>" . $competitor2[0]['firstName'] . " " . $competitor2[0]['lastName'] . " (" . $competitor2[0]['username'] . ")</td>";
          }
          echo "</tr>";

          $count++;
        }
        echo "</tbody>";
        echo "</table>";
        
        echo "</div>";
      }


      // display final rank round 1 and winners
      $json = 'final_' . $competitionID . '.json';

      if (file_exists($json)) {
        $jsonData = file_get_contents($json);

        $data = json_decode($jsonData, true);

        echo "<div class='container-fluid'>";
        echo "<div class='row d-flex'>";

        echo "<div class='col-sm-5'>";
        echo "<h1 class='display-6'><strong>Final Ranking</strong></h1>";
        echo "<table class='table' id='startingRank'>";
        echo "<thead><tr><th>Final Rank</th><th>Username</th><th>Name</th><th>Score</th></tr></thead>";

        echo "<tbody>";
        foreach ($data['finalRank'] as $rank) {
          echo
          "<td>" . $rank['rank'] . "</td>" . 
          "<td>" . $rank['username'] ."</td>" . 
          "<td>" . $rank['name'] . "</td>" . 
          "<td>" . $rank['score'] . "</td>" . 
          "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "<div class='col-sm-7' id='matches'>";
        echo "<h1 class='display-6'><strong>Winners</strong></h1>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead><tr><th>1st Place</th><th>2nd Place</th><th>3rd Place</th></tr></thead>";

        echo "<tbody>";
        echo "<tr>" . 
        "<td>" . $data['podium'][0]['competitor1'] . "</td>" . 
        "<td>" . $data['podium'][0]['competitor2'] . "</td>" . 
        "<td>" . $data['podium'][0]['competitor3'] . "</td>" . 
        "</tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "</div>";
      }

    ?>


  </body>
</html>
