<!-- IMPORTANT! as of now, only competitions with 2 rounds can be made 
(the web application only generates pairings for two rounds then ends the competition) -->

<?php
session_start();

if (!ISSET($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
    header("Location: login.php"); 
    exit();
  }
?>

<?php
    require 'config/database_pdo.php'; 

    if (isset($_POST["createCompAndRound1"])) {
        try{
            // find the club id to insert
            $stmt = $con->prepare(
                "SELECT clubID FROM clubs
                JOIN admins ON clubs.clubID = admins.club
                WHERE adminID = ?"
            );
            $stmt->execute([$_SESSION["id"]]);            
            $clubID = $stmt->fetchColumn();


            // inserting new competition into database
            $stmt = $con->prepare(
                "INSERT INTO competitions (club, name, numParticipants, numRounds, start, end) VALUES (?, ?, ?, ?, ?, ?)"
            );

            $compName = $_POST["compName"];
            $numParticipants = $_POST["numCompetitors"];
            $numRounds = $_POST["numRounds"];
            $startDate = $_POST["startDate"];
            $endDate = $_POST["endDate"];

            if ($stmt->execute([$clubID, $compName, $numParticipants, $numRounds, $startDate, $endDate])) {
                echo '<script>alert("New competition creation successful!")</script>';
            } else {
                echo '<script>alert("Error encountered creating club admin")</script>';
            }


            // find competition id
            $stmt = $con->prepare(
                "SELECT competitionID FROM competitions
                JOIN clubs ON competitions.club = clubs.clubID
                WHERE clubs.clubID = ? 
                AND competitions.name = ? 
                AND competitions.numParticipants = ? 
                AND competitions.numRounds = ? 
                AND competitions.start = ? 
                AND competitions.end = ?"
            );
            $stmt->execute([$clubID, $compName, $numParticipants, $numRounds, $startDate, $endDate]);         
            $competitionID = $stmt->fetchColumn();

            // adding competitors into database 
            if (isset($_POST['competitors'])) {
                $competitors = $_POST['competitors'];
                foreach ($competitors as $userID) {
                    $stmt = $con->prepare(
                        "INSERT INTO competitors (competition, userID) VALUES (?, ?)"
                    );
                    $stmt->execute([$competitionID, $userID]);
                }
            }


            // fixing formatting of datetime-local input from user for first round timing
            $round1schedule = null; // just in case but shouldn't be null after the if statement below
            if(isset($_POST['round1time'])) {
                $round1 = $_POST['round1time'];
                $round1schedule = strtotime($round1);
                $round1schedule = date("Y-m-d H:i:s", $round1schedule);
            }


            // generating round 1 pairings and displaying them
            // should display starting rank on the side
            // display pairings on the other side

            $stmt = $con->prepare(
            "SELECT users.username, users.firstName, users.lastName, competitors.userID, competitors.score 
            FROM competitors
            JOIN users on competitors.userID=users.userID
            WHERE competitors.competition = ?
            ORDER BY competitors.score DESC, users.username");

            $stmt->execute([$competitionID]); 
            $competitorData = $stmt->fetchAll();


            // first round pairings
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
    
                if ($stmt->execute([$competitionID, $competitor1['userID'], $competitor2['userID'], $round1schedule, 1])) {
                    echo '<script>alert("Matches creation successful!")</script>';
                } else {
                    echo '<script>alert("Error encountered creating matches")</script>';
                }
            }

            if ($bye) {
                // inserting bye into match table in database
                $stmt = $con->prepare(
                    "INSERT INTO matches (competition, competitor1, competitor2, schedule, roundNum, result) VALUES (?, ?, ?, ?, ?, ?)"
                );
    
                if ($stmt->execute([$competitionID, $bye['userID'], 0, $round1schedule, 1, 1])) {
                    echo '<script>alert("Matches creation successful!")</script>';
                } else {
                    echo '<script>alert("Error encountered creating matches")</script>';
                }
            }

            echo "</tbody>";
            echo "</table>";


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        echo '<script>alert("Competition and round 1 pairings creation successful!")</script>';
        
        // set session variable to pass on competitionID
        $_SESSION["competition"] = $competitionID;

        // Redirect to the competition page showing first round pairings and starting rank
        header("Location: comp_1st_round.php");
        exit();
    }
?>



<!DOCTYPE html>

    <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">    
            
            <title>Create A Club & Admin Account</title>

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>
        
            <link rel="stylesheet" type="text/css" href="css/styles.css">

        </head>
        
        <body>

        <header>
            <!-- php for menu -->
            <?php 
                include 'commonPhp/headerLogOut.php';
            ?>
        </header>

            <div class="container">

                <h1 class="display-3"><strong>Create A Competition</strong></h1>
        
                <form class="createComp" action="create_competition.php" method="post" >

                    <!-- create competition form -->
                    <div class="mb-3">
                        <label for="competitionName"><h5><strong>Competition Name</strong></h5></label>
                        <input class="formInput" name="compName" required="" type="text" placeholder="Enter the name for your competition..."  />
                    </div>

                    <div class="mb-3">
                        <label for="numCompetitors"><h5><strong>Number of Participants</strong></h5></label>
                        <input class="formInput" name="numCompetitors" required="" type="text" placeholder="Enter the number of participants entered..."  />
                    </div>

                    <div class="mb-3">
                        <label for="numRounds"><h5><strong>Number of Rounds</strong></h5></label>
                        <input class="formInput" name="numRounds" required="" type="text" placeholder="Enter the number of rounds in the competition..."  />
                    </div>

                    <div class="mb-3">
                        <label for="startDate"><h5><strong>Start Date</strong></h5></label>
                        <input class="formInput" name="startDate" required="" type="date" />
                    </div>

                    <div class="mb-3">
                        <label for="endDate"><h5><strong>End Date</strong></h5></label>
                        <input class="formInput" name="endDate" required="" type="date" />
                    </div>

                    
                    <!-- select time for the 1st round -->
                    <div class="mb-3">
                        <label for="round1time"><h5><strong>Select 1st Round Timing</strong></h5></label><br>
                        <input class="formInput" name="round1time" type="datetime-local">
                    </div>


                    <!-- add members to competition -->
                    <div class="mb-3">
                        <label><h5><strong>Select Participating Members</strong></h5></label><br>
                        <?php
                            require 'config/database_pdo.php';

                            try {
                                // find the club id to insert
                                $stmt = $con->prepare(
                                    "SELECT clubID FROM clubs
                                    JOIN admins ON clubs.clubID = admins.club
                                    WHERE adminID = ?"
                                );
                                $stmt->execute([$_SESSION["id"]]);
                                $clubID = $stmt->fetchColumn();

                                // displaying all members in the club
                                $stmt = $con->prepare(
                                    "SELECT users.userID, users.firstName, users.lastName, users.username FROM users
                                    JOIN memberships on users.userID=memberships.userID
                                    JOIN clubs on memberships.clubID=clubs.clubID
                                    WHERE clubs.clubID = ?"
                                );

                                $stmt->execute([$clubID]);
                                $clubMemberData = $stmt->fetchAll();

                                if ($clubMemberData) {
                                    echo "<table class='table' id='addToCompetitorTable'>" . 
                                    "<thead>" . 
                                    "<tr>" .
                                    "<th>Name</th>" .
                                    "<th>Username</th>" .
                                    "<th>Add To Competition</th>" .
                                    "</tr>" .
                                    "</thead>" .
                                    "<tbody>";
                                
                                    foreach ($clubMemberData as $info) {
                                        $userID = htmlspecialchars($info["userID"]);
                                        $firstName = htmlspecialchars($info["firstName"]);
                                        $lastName = htmlspecialchars($info["lastName"]);
                                        $username = htmlspecialchars($info["username"]);
                                
                                        echo "<tr>" . 
                                        "<td>$firstName $lastName</td>" . 
                                        "<td>$username</td>" . 
                                        "<td><input type='checkbox' name='competitors[]' value='$userID'></td>" . 
                                        "</tr>";
                                    }
                                
                                    echo "</tbody>";
                                    echo "</table>";
                                } else {
                                    echo "<script>alert('No members found in this club');</script>";
                                }
                                
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                        ?>
                    </div>
                    
                    <div class="container">
                        <div class="row">
                            <div class="col text-center">
                                <button name="createCompAndRound1" type="submit" class="btn btn-dark">Create Competition & Generate Round 1</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

        </body>

    </html>
