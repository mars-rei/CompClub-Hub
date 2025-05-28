<?php
session_start();
include 'config/database_pdo.php';

$matchResults = []; // array to store round 1 match results 

if (isset($_POST["nextRound"])) {
    foreach ($_POST['competition'] as $index => $competitionID) {
        $competitor1 = $_POST['competitor1'][$index];
        $competitor2 = $_POST['competitor2'][$index];
        $matchResult = $_POST['result'][$index];

        $stmt = $con->prepare(
            "UPDATE matches SET result = ? WHERE competition = ? AND competitor1 = ? AND competitor2 = ?"
        );
        $stmt->execute([$matchResult, $competitionID, $competitor1, $competitor2]);

        $stmt = $con->prepare(
            "SELECT club FROM competitions WHERE competitionID = ?"
        );
        $stmt->execute([$competitionID]);
        $clubID = $stmt->fetchColumn();

        if ($matchResult == 1) {
            // result is 1-0
            $stmt = $con->prepare("UPDATE competitors SET score = score + 1 WHERE userID = ?");
            $stmt->execute([$competitor1]);
            
            $stmt = $con->prepare("UPDATE users SET totalScore = totalScore + 1 WHERE userID = ?");
            $stmt->execute([$competitor1]);

            $stmt = $con->prepare("UPDATE memberships SET score = score + 1 WHERE userID = ? AND clubID = ?");
            $stmt->execute([$competitor1, $clubID]);

        } elseif ($matchResult == 2) {
            // result is 0-1
            $stmt = $con->prepare("UPDATE competitors SET score = score + 1 WHERE userID = ?");
            $stmt->execute([$competitor2]);
            
            $stmt = $con->prepare("UPDATE users SET totalScore = totalScore + 1 WHERE userID = ?");
            $stmt->execute([$competitor2]);

            $stmt = $con->prepare("UPDATE memberships SET score = score + 1 WHERE userID = ? AND clubID = ?");
            $stmt->execute([$competitor2, $clubID]);
            
        } elseif ($matchResult == 3) {
            // result is 0.5-0.5
            $stmt = $con->prepare("UPDATE competitors SET score = score + 0.5 WHERE userID = ?");
            $stmt->execute([$competitor1]);
            $stmt->execute([$competitor2]);
            
            $stmt = $con->prepare("UPDATE users SET totalScore = totalScore + 0.5 WHERE userID = ?");
            $stmt->execute([$competitor1]);
            $stmt->execute([$competitor2]);

            $stmt = $con->prepare("UPDATE memberships SET score = score + 0.5 WHERE userID = ? AND clubID = ?");
            $stmt->execute([$competitor1, $clubID]);
            $stmt->execute([$competitor2, $clubID]);
        }

        $matchResults[] = [
            'competitionID' => $competitionID,
            'competitor1' => [
                'userID' => $competitor1,
                'score' => $matchResult == 1 ? 1 : ($matchResult == 3 ? 0.5 : 0)
            ],
            'competitor2' => [
                'userID' => $competitor2,
                'score' => $matchResult == 2 ? 1 : ($matchResult == 3 ? 0.5 : 0)
            ],
            'result' => $matchResult
        ];
    }

    $jsonFilePath = 'r1_results_' . $competitionID . '.json';
    file_put_contents($jsonFilePath, json_encode($matchResults, JSON_PRETTY_PRINT));

    // update clubRanking
    $stmt = $con->prepare(
        "SELECT users.userID, memberships.score 
        FROM users 
        JOIN memberships ON users.userID = memberships.userID 
        WHERE memberships.clubID = ? 
        ORDER BY memberships.score DESC"
    );

    $stmt->execute([$clubID]);
    $clubMembers = $stmt->fetchAll();

    $rank = 1;
    foreach ($clubMembers as $clubMember) {
        $stmt = $con->prepare(
            "UPDATE memberships SET clubRank = ? WHERE userID = ?"
        );
        $stmt->execute([$rank, $clubMember['userID']]);
        $rank++;
    }


    // generating 2nd round

    // fixing formatting of datetime-local input from user for second round timing
    $round2schedule = null; // just in case but shouldn't be null after the if statement below
    if(isset($_POST['round2time'])) {
        $round2 = $_POST['round2time'];
        $round2schedule = strtotime($round2);
        $round2schedule = date("Y-m-d H:i:s", $round2schedule);
    }
          
    $competitionID = $_SESSION["competition"];
    
    // generating round 2 pairings and displaying them
    $stmt = $con->prepare(
    "SELECT users.username, users.firstName, users.lastName, competitors.userID, competitors.score 
    FROM competitors
    JOIN users on competitors.userID=users.userID
    WHERE competitors.competition = ?
    ORDER BY competitors.score DESC, users.username");

    $stmt->execute([$competitionID]); 
    $competitorData = $stmt->fetchAll();
    
    
    $half = floor(count($competitorData) / 2);


    // assigns a bye to the bottom competitor if number is odd
    $bye = null;
    if (count($competitorData) % 2 !== 0) {
        $bye = array_pop($competitorData);
    }

    $topHalf = array_slice($competitorData, 0, $half);
    $bottomHalf = array_slice($competitorData, $half);
    
    // creation of matches 
    for ($i = 0; $i < min(count($topHalf), count($bottomHalf)); $i++) {
        $competitor1 = $topHalf[$i];
        $competitor2 = $bottomHalf[$i];

        // inserting pairings into match table in database
        $stmt = $con->prepare(
            "INSERT INTO matches (competition, competitor1, competitor2, schedule, roundNum) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$competitionID, $competitor1['userID'], $competitor2['userID'], $round2schedule, 2]);
    }
    
    if ($bye) {
        // inserting bye into match table in database
        $stmt = $con->prepare(
            "INSERT INTO matches (competition, competitor1, competitor2, schedule, roundNum, result) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$competitionID, $bye['userID'], 0, $round2schedule, 2, 1]);
    }

    // Redirect to 2nd round pairings and ranking after 1st round
    header("Location: comp_2nd_round.php");
    exit();
}
?>
