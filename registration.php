<!DOCTYPE html>

    <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">    
            
            <title>Registration</title>

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

                <h1 class="display-3"><strong>Register</strong></h1>

                <?php
                    include 'config/database_pdo.php';

                    if (isset($_POST["registration"])) {
                        try {
                            $firstName = $_POST["firstName"];
                            $lastName = $_POST["lastName"];
                            $email = $_POST["email"];
                            $username = $_POST["username"];
                            $password = $_POST["password"];

                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                            $stmt = $con->prepare(
                                "INSERT INTO users (firstName, lastName, email, username, password) VALUES (?, ?, ?, ?, ?)"
                            );

                            if ($stmt->execute([$firstName, $lastName, $email, $username, $hashedPassword])) {
                                echo '<script>alert("Registration successful!")</script>';
                                header("refresh: 1");

                            } else {
                                echo '<script>alert("ERROR: Unable to register user")</script>';
                            }
                        } catch (PDOException $e) {
                            echo '<script>alert("DATABASE ERROR: " . $e->getMessage() )</script>';
                        }
                    }
                ?>
        
                <form class="registration" action="registration.php" method="post" >

                    <div class="mb-3">
                        <label for="firstName"><h5><strong>First Name</strong></h5></label>
                        <input class="formInput" name="firstName" required="" type="text" placeholder="Enter your first name..."  />
                    </div>

                    <div class="mb-3">
                        <label for="lastName"><h5><strong>Last Name</strong></h5></label>
                        <input class="formInput" name="lastName" required="" type="text" placeholder="Enter your last name..."  />
                    </div>

                    <div class="mb-3">
                        <label for="email"><h5><strong>Email</strong></h5></label>
                        <input class="formInput" name="email" required="" type="email" placeholder="Enter your email..."  />
                    </div>

                    <div class="mb-3">
                        <label for="username"><h5><strong>Username</strong></h5></label> 
                        <input class="formInput" name="username" required="" type="text" placeholder="Enter your username..." />
                    </div> 

                    <div class="mb-3">
                        <label for="password"><h5><strong>Password</strong></h5></label>
                        <input class="formInput" name="password" required="" type="password" placeholder="Enter your password..." />
                    </div>

                    <button id="registrationButton" name="registration" type="submit" class="btn btn-dark">Register</button>

                </form>
            </div>

        </body>

    </html>
