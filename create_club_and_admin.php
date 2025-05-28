<?php
    session_start();

    if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
        header('Location: login.php'); 
        exit();
    }
?>

<?php

include 'config/database_pdo.php';

if (isset($_POST["createClubAdmin"])) {
    try {
        $name = $_POST["clubName"];
        $email = $_POST["clubEmail"];
        $category = $_POST["clubCategory"];
        $description = $_POST["clubDescription"];
        $address = $_POST["clubAddress"];

        $stmt = $con->prepare(
            "INSERT INTO clubs (clubName, clubEmail, category, description, address) VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt->execute([$name, $email, $category, $description, $address])) {
            echo '<script>alert("Club creation successful!")</script>';
        } else {
            echo "<h1>Error encountered creating club</h1>";
        }

        $stmt = $con->prepare(
            "SELECT clubID FROM clubs WHERE clubEmail = ?"
        );
        $stmt->execute([$email]);
        $clubID = $stmt->fetchColumn();

        $stmt = $con->prepare(
            "INSERT INTO admins (club, userID, username, password) VALUES (?, ?, ?, ?)"
        );

        $normalUserID = $_SESSION["id"];
        $adminUsername = $_POST["adminUsername"];
        $adminPassword = $_POST["adminPassword"];

        $adminPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

        if ($stmt->execute([$clubID, $normalUserID, $adminUsername, $adminPassword])) {
            echo '<script>alert("Club admin creation successful!")</script>';
        } else {
            echo "<h1>Error encountered creating club admin</h1>";
        }

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }

    echo '<script>alert("Club and club admin creation successful!")</script>';
    header("refresh: 1");
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

            <h1 class="display-3"><strong>Create A Club & Admin Account</strong></h1>
    
            <form class="registration" action="create_club_and_admin.php" method="post" >

                <!--Admin account registration-->
                <div class="mb-3">
                    <label for="adminUsername"><h5><strong>Admin Username<strong></h5></label>
                    <input class="formInput" name="adminUsername" required="" type="text" placeholder="Enter the username for your admin account..."  />
                </div>

                <div class="mb-3">
                    <label for="adminPassword"><h5><strong>Admin Password</strong></h5></label>
                    <input class="formInput" name="adminPassword" required="" type="password" placeholder="Enter the password for your admin account..."  />
                </div>

                <!--Club creation-->
                <div class="mb-3">
                    <label for="clubName"><h5><strong>Club Name</strong></h5></label>
                    <input class="formInput" name="clubName" required="" type="text" placeholder="Enter your a club name..."  />
                </div>

                <div class="mb-3">
                    <label for="clubEmail"><h5><strong>Club Email</strong></h5></label> 
                    <input class="formInput" name="clubEmail" required="" type="email" placeholder="Enter the club's email..." />
                </div> 

                <div class="mb-3">
                    <label for="clubCategory"><h5><strong>Category</strong></h5></label>
                    <input class="formInput" name="clubCategory" required="" type="text" placeholder="Enter the club category..." />
                </div>

                <div class="mb-3">
                    <label for="clubDescription"><h5><strong>Description</strong></h5></label> 
                    <input class="formInput" name="clubDescription" required="" type="text" placeholder="Enter the club description..." />
                </div> 

                <div class="mb-3">
                    <label for="clubAddress"><h5><strong>Address</strong></h5></label>
                    <input class="formInput" name="clubAddress" required="" type="text" placeholder="Enter the club address..." />
                </div>

                <button id="registrationButton" name="createClubAdmin" type="submit" class="btn btn-primary">Create Club & Admin</button>

            </form>

        </div>

    </body>

</html>
