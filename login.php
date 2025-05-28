<?php
  session_start();

  if (isset($_POST["register"])) {
    // Redirect to the registration page
    header("Location: registration.php");
    exit();
  }

  include 'config/database_pdo.php';
  
  if (isset($_POST["login"])) {
      $username = $_POST["username"];
      $password = $_POST["password"];
  
      try {
          // Prepare SQL statement for normal users
          $stmt = $con->prepare(
              "SELECT userID, password FROM users WHERE username = ? AND deleted = false"
          );
  
          $stmt->execute([$username]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
          // Check if normal user exists
          if ($user) {
              $userID = $user['userID'];
              $hashed_password = $user['password'];
  
              // Verify the password
              if (password_verify($password, $hashed_password)) {
                  // Set session variables for the user
                  $_SESSION["loggedin"] = true;
                  $_SESSION["id"] = $userID;
                  $_SESSION["username"] = $username;
                  $_SESSION["type"] = "user"; 
  
                  // Redirect to the user's dashboard
                  header("Location: user_dashboard.php");
                  exit();
              }
          } 
  
          // Prepare SQL statement for admins
          $stmt2 = $con->prepare(
              "SELECT adminID, password FROM admins WHERE username = ?"
          );
  
          $stmt2->execute([$username]);
          $admin = $stmt2->fetch(PDO::FETCH_ASSOC);
  
          if ($admin) {
              $adminID = $admin['adminID'];
              $hashed_password = $admin['password'];
  
              if (password_verify($password, $hashed_password)) {
                  $_SESSION["loggedin"] = true;
                  $_SESSION["id"] = $adminID;
                  $_SESSION["username"] = $username;
                  $_SESSION["type"] = "admin"; 
  
                  // Redirect to the admin's dashboard
                  header("Location: admin_dashboard.php");
                  exit();
              }
          }
  
          // If no normal user or admin user is found, show an error message
          echo '<script>alert("ERROR: Incorrect username and password combination")</script>';
  
      } catch (PDOException $e) {
        echo '<script>alert("DATABASE ERROR: " . $e->getMessage() )</script>';
      }
  }
?>
  

<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   

    <title>Login</title>

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

      <h1 class="display-3"><strong>Login</strong></h1>

      <!-- Login Form -->

      <form class="login" action="login.php" method="post">

        <div class ="mb-3">
          <label for="username"><h5><strong>Username</strong></h5></label>
          <input class="formInput" id="username" name="username" required="" type="text" placeholder="Enter your username..." />
        </div>

        <div class="mb-3">
          <label for="password"><h5><strong>Password</strong></h5></label>
          <input class="formInput" id="password" name="password" required="" type="password" placeholder="Enter your password..." />
        </div>

        <div class="mb-3">
          <label id="showPasswordLabel" class="form-check-label" for="showPassword"><h5>Show Password</h5></label>
          <input id="showPasswordCheckBox" type="checkbox" class="form-check-input" id="showPassword" onclick="togglePassword()">
        </div>

        <button id="loginButton" name="login" type="submit" class="btn btn-dark">Login</button>

      </form>

      <form class="register" action="registration.php" method="post">
        <div class ="mb-3">
          <label><h5><strong>Don't have an account? Register here.</strong></h5></label>
        </div>
        <button id="createAccountButton" name="register" type="submit" class="btn btn-dark">Create Account</button>
      </form>

    </div>

    <!-- Java to toggle password visibility-->
    <script>
      function togglePassword() {
        var passwordField=document.getElementById("password");
        if (passwordField.type =="password") {
          passwordField.type="text";
        } else {
          passwordField.type="password";
        }
      }
    </script>

  </body>

</html>
