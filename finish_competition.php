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
  <title>Competition Results</title>
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


<div class="container-fluid">
  <div class="row d-flex">
    <?php
        require 'config/database_pdo.php'; 

        $competitionID = $_SESSION["competition"];

        // unsetting session variable as this is the last time it'll be referenced
        unset($_SESSION['competition']);

        // get competitor data
        $stmt = $con->prepare(
            "SELECT users.username, users.firstName, users.lastName, competitors.userID, competitors.score 
            FROM competitors
            JOIN users on competitors.userID=users.userID
            WHERE competitors.competition = ?
            ORDER BY competitors.score DESC, users.username");

        $stmt->execute([$competitionID]); 
        $competitorData = $stmt->fetchAll();

        // display final ranking
        $finalRank = []; // an array storing the final rank details to put in json
        echo "<div class='col-sm-5'>";
        echo "<h1 class='display-6'><strong>Final Ranking</strong></h1>";
        echo "<table class='table' id='startingRank'>"; 
        echo "<thead>" . 
        "<tr>" . 
        "<th>Final Ranking</th><th>Username</th><th>Name</th><th>Score</th>" .
        "</tr>" . 
        "</thead>";
        echo "<tbody>";
        $count = 0;
        if ($competitorData) {
            foreach ($competitorData as $info) {
                $count++;
                echo "<tr>" .
                "<td>" . $count . "</td>" . 
                "<td>" . $info["username"] ."</td>" . 
                "<td>" . $info["firstName"] . " " . $info["lastName"] . "</td>" . 
                "<td>" . $info["score"] . "</td>" . 
                "</tr>";

                // insert finalRank via $count
                $stmt = $con->prepare(
                  "UPDATE competitors SET finalRank = ? WHERE userID = ?"
                );
                $stmt->execute([$count, $info["userID"]]);

                // saving starting rank data to array
                $finalRank[] = [
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

        // display podium
        $podium = []; // an array storing the podium details to put in json
        echo "<div class='col-sm-7' id='matches'>"; // change id
        echo "<h1 class='display-6'><strong>Winners</strong></h1>";
        echo "<table class='table' id='competitionTables'>";
        echo "<thead>
        <tr>
            <th>1st Place</th>
            <th>2nd Place</th>
            <th>3rd Place</th>
        </tr>
        </thead>";
        echo "<tbody>";

        echo "<tr>" . 
        "<td>" . $competitorData[0]["firstName"] . " " . $competitorData[0]["lastName"] . " (" . $competitorData[0]['username'] . ")</td>" . 
        "<td>" . $competitorData[1]["firstName"] . " " . $competitorData[1]["lastName"] . " (" . $competitorData[1]['username'] . ")</td>" . 
        "<td>" . $competitorData[2]["firstName"] . " " . $competitorData[2]["lastName"] . " (" . $competitorData[2]['username'] . ")</td>" . 
        "</tr>";

        $podium[] = [
          'competitor1' => $competitorData[0]["firstName"] . ' ' . $competitorData[0]["lastName"] . ' (' . $competitorData[0]['username'] . ')',
          'competitor2' => $competitorData[1]["firstName"] . ' ' . $competitorData[1]["lastName"] . ' (' . $competitorData[1]['username'] . ')',
          'competitor3' => $competitorData[2]["firstName"] . ' ' . $competitorData[2]["lastName"] . ' (' . $competitorData[2]['username'] . ')'
        ];

        echo "</tbody>";
        echo "</table>";

        $stmt = $con->prepare(
            "UPDATE competitions SET 1stPlace = ?, 2ndPlace = ?, 3rdPlace = ? WHERE competitionID = ?"
        );
        $stmt->execute([$competitorData[0]["userID"], $competitorData[1]["userID"], $competitorData[2]["userID"], $competitionID]); 


        // combine data for json file
        $jsonData = [
          'finalRank' => $finalRank,
          'podium' => $podium
        ];

        // save to file
        $jsonFileName = 'final_' . $competitionID . '.json';
        file_put_contents($jsonFileName, json_encode($jsonData, JSON_PRETTY_PRINT));
    
    ?>

    <div class="container">
      <div class="row">
        <div class="col text-center">
          <form action="admin_dashboard.php">
            <button name="returnAdminDash" type="submit" class="btn btn-secondary">Return To Admin Dashboard</button>
          </form>        
        </div>
      </div>
    </div> 

    </div>
  </div>
</div>


</body>
</html>