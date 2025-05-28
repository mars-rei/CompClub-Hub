<!-- this page represents the code for submitting the
results of the last round of a competition -->

<?php
session_start();
include 'config/database_pdo.php';

$matchResults = []; // array to store round 2 match results 

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

    $jsonFilePath = 'r2_results_' . $competitionID . '.json';
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

    // update competition to be finished
    $stmt = $con->prepare(
        "UPDATE competitions SET finished = True WHERE competitionID = ?"
    );
    $stmt->execute([$_SESSION["competition"]]);

    // Redirect to end of competition 
    header("Location: finish_competition.php");
    exit();
}
?>
