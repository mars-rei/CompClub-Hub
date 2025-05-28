<?php
  session_start();

  if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
    header('Location: login.php'); 
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">    
  <title>Round 1</title>
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

<?php
    require 'config/database_pdo.php'; 

    // find competition name
    $stmt = $con->prepare(
        "SELECT name FROM competitions
        WHERE competitionID = ?"
    );
    $stmt->execute([$_SESSION["competition"]]);         
    $competitionName = $stmt->fetchColumn();

    echo "<div class='container'>" .
    "<h1 class='display-3'><strong>Round 1 of $competitionName</strong></h1>" . 
    "</div>";
?>

<div class="container-fluid">
  <div class="row d-flex">
    <?php
      require 'config/database_pdo.php'; 
        
      $competitionID = $_SESSION["competition"];

      // get competitor data
      $stmt = $con->prepare(
          "SELECT users.username, users.firstName, users.lastName, competitors.userID, competitors.score 
          FROM competitors
          JOIN users on competitors.userID=users.userID
          WHERE competitors.competition = ?
          ORDER BY competitors.score DESC, users.username");

      $stmt->execute([$competitionID]); 
      $competitorData = $stmt->fetchAll();


      // display starting rank and save
      $startingRank = []; // an array storing the starting rank details to put in json
      echo "<div class='col-sm-5'>";
      echo "<h1 class='display-6'><strong>Starting Rank</strong></h1>";
      echo "<table class='table' id='startingRank'>";
      echo "<thead>" . 
      "<tr>" . 
      "<th>Starting Rank</th><th>Username</th><th>Name</th><th>Score</th>" .
      "</tr>" . 
      "</thead>";
      echo "<tbody>";
      $count = 0;
      if ($competitorData) {
          foreach ($competitorData as $info) {
              $count += 1;
              echo "<tr>" .
              "<td>" . $count . "</td>" . 
              "<td>" . $info["username"] ."</td>" . 
              "<td>" . $info["firstName"] . " " . $info["lastName"] . "</td>" . 
              "<td>" . $info["score"] . "</td>" . 
              "</tr>";

              // saving starting rank data to array
              $startingRank[] = [
                "rank" => $count,
                "username" => $info['username'],
                "name" => $info['firstName'] . ' ' . $info['lastName'],
                "score" => $info['score']
              ];
          }
      } 

      echo "</tbody>";
      echo "</table>";

      echo "</div>";

      // first round pairings
      $half = floor(count($competitorData) / 2);

      // assigns a bye to the bottom competitor if number is odd
      $bye = null;
      if (count($competitorData) % 2 !== 0) {
          $bye = array_pop($competitorData);
      }

      $topHalf = array_slice($competitorData, 0, $half);
      $bottomHalf = array_slice($competitorData, $half);

      // get time of first round matches
      $stmt = $con->prepare(
        "SELECT schedule FROM matches
        WHERE competition = ?"
      );
      $stmt->execute([$competitionID]);         
      $round1schedule = $stmt->fetchColumn();


      // display first round matches
      $round1pairings = []; // an array storing the first round matches details to put in json
      echo "<div class='col-sm-7' id='matches'>";
      echo "<h1 class='display-6'><strong>Round 1 Pairings @ $round1schedule</strong></h1>";
      echo "<table class='table' id='competitionTables'>";
      echo "<thead>
      <tr>
          <th>Match</th>
          <th>Competitor 1</th>
          <th>Score</th>
          <th>Competitor 2</th>
          <th>Score</th>
      </tr>
      </thead>";
      echo "<tbody>";

      // creation of matches 
      $matchNo = 1;
      for ($i = 0; $i < min(count($topHalf), count($bottomHalf)); $i++) {
          $competitor1 = $topHalf[$i];
          $competitor2 = $bottomHalf[$i];

          echo "<tr>
              <td>$matchNo</td>
              <td>{$competitor1['firstName']} {$competitor1['lastName']} ({$competitor1['username']})</td>
              <td>{$competitor1['score']}</td>
              <td>{$competitor2['firstName']} {$competitor2['lastName']} ({$competitor2['username']})</td>
              <td>{$competitor2['score']}</td>
          </tr>";

          $round1pairings[] = [
            'match' => $matchNo,
            'competitor1' => $competitor1['firstName'] . ' ' . $competitor1['lastName'] . ' (' . $competitor1['username'] . ')',
            'competitor1_score' => $competitor1['score'],
            'competitor2' => $competitor2['firstName'] . ' ' . $competitor2['lastName'] . ' (' . $competitor2['username'] . ')',
            'competitor2_score' => $competitor2['score']
          ];

          $matchNo++;
      }

      if ($bye) {
          echo "<tr>
              <td>Bye</td>
              <td>{$bye['firstName']} {$bye['lastName']} ({$bye['username']})</td>
              <td>{$bye['score']}</td>
          </tr>";

          $round1pairings[] = [
            'match' => 'Bye',
            'competitor1' => $bye['firstName'] . ' ' . $bye['lastName'] . ' (' . $bye['username'] . ')',
            'competitor1_score' => $bye['score']
          ];
      }

      echo "</tbody>";
      echo "</table>";

      // combine data for json file
      $jsonData = [
        'competitionName' => $competitionName,
        'startingRank' => $startingRank,
        'round1schedule' => $round1schedule,
        'pairings' => $round1pairings
      ];

      // save to file
      $jsonFileName = 'round1_' . $competitionID . '.json';
      file_put_contents($jsonFileName, json_encode($jsonData, JSON_PRETTY_PRINT));
    ?>

    <div class="container">
        <div class="row">
          <div class="col text-center">
          <form action="edit_round1_results.php" method="post">
            <button name="createComp" type="submit" class="btn btn-secondary">Enter Results</button>
          </form>
          </div>
        </div>
      </div>    
    </div>

  </div>
</div>

</body>
</html>
