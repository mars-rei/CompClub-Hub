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
  <title>Edit Match Results</title>
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

        // get match data
        $stmt = $con->prepare(
          "SELECT u1.firstName AS competitor1_firstName, 
            u1.lastName AS competitor1_lastName, 
            u1.userID AS competitor1_id,
            u1.username AS competitor1_username,
            u2.firstName AS competitor2_firstName, 
            u2.lastName AS competitor2_lastName,
            u2.userID AS competitor2_id, 
            u2.username AS competitor2_username,
            results.result
        FROM matches
        JOIN users u1 ON matches.competitor1 = u1.userID
        JOIN users u2 ON matches.competitor2 = u2.userID
        JOIN results ON matches.result = results.resultID
        WHERE matches.competition = ?
          AND matches.roundNum = 2");

        $stmt->execute([$competitionID]); 
        $matchData = $stmt->fetchAll();

        // data is in reverse
        $matchData = array_reverse($matchData);


        // get result data for dropdown
        $stmt2 = $con->prepare(
          "SELECT resultID, result FROM results"
        );
        $stmt2->execute();
        $resultOptions = $stmt2->fetchAll();


        // display second round matches
        echo "<h1 class='display-6'><strong>Round 2 Results</strong></h1>";
        echo '<form action="submit_round2_results.php" method="post">'; 
        echo "<table class='table' id='competitionTables'>";
        echo "<thead>
        <tr>
            <th>Match</th>
            <th>Competitor 1</th>
            <th>Result</th>
            <th>Competitor 2</th>
        </tr>
        </thead>";
        echo "<tbody>";

        $count = 1;
        foreach ($matchData as $info) {
          echo "<tr>";
          if ($info["competitor2_username"] == "Bye") {
              echo "<td>Bye</td>";
              echo "<td>" . $info["competitor1_firstName"] . " " . $info["competitor1_lastName"] . " (" . $info["competitor1_username"] . ")" . "</td>";
              echo "<td>" . $info["result"] . "</td>";
              echo '<input type="hidden" name="result[]" value="1">';
              echo '<input type="hidden" name="competition[]" value="' . $competitionID . '">';
              echo '<input type="hidden" name="competitor1[]" value="' . $info['competitor1_id'] . '">';
              echo '<input type="hidden" name="competitor2[]" value="' . $info['competitor2_id'] . '">';
          } else {
              echo "<td>" . $count . "</td>";
              echo "<td>" . $info["competitor1_firstName"] . " " . $info["competitor1_lastName"] . " (" . $info["competitor1_username"] . ")" . "</td>";
      
              echo '<td>';
              echo '<select name="result[]">'; 
              foreach ($resultOptions as $result) {
                // default option should be 0-0 which has the resultID of 4
                $selected = ($result['resultID'] == 4) ? 'selected' : (($result['resultID'] == $info['result']) ? 'selected' : ''); 
                echo "<option value='" . $result['resultID'] . "' $selected>" . $result['result'] . "</option>";
              }
              echo '</select>';
              echo '</td>'; 
              echo '<input type="hidden" name="competition[]" value="' . $competitionID . '">';
              echo '<input type="hidden" name="competitor1[]" value="' . $info['competitor1_id'] . '">';
              echo '<input type="hidden" name="competitor2[]" value="' . $info['competitor2_id'] . '">';

              echo "<td>" . $info["competitor2_firstName"] . " " . $info["competitor2_lastName"] . " (" . $info["competitor2_username"] . ")" . "</td>";
          }
          echo "</tr>";
          $count++;
        }
        
        echo "</tbody>";
        echo "</table>";

        echo "</div>";
    ?>

    <div class="container">
      <div class="row">
        <div class="col text-center">
          <button name="nextRound" type="submit" class="btn btn-secondary">Update Results & Finish Competition</button>
        </div>
      </div>
    </div>  

    </form>

  </div>
</div>


</body>
</html>

