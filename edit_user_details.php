<?php
  session_start();

  if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
    header('Location: login.php'); 
    exit();
  }

  include 'config/database_pdo.php';

  if (isset($_POST["saveUserDetails"])) {
    $userID = $_SESSION["id"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (!empty($_POST["firstName"])) {
      $stmt = $con->prepare(
        "UPDATE users SET firstName = ? WHERE userID = ?"
      );
      $stmt->execute([$firstName, $userID]);
    }

    if (!empty($_POST["lastName"])) {
      $stmt = $con->prepare(
        "UPDATE users SET lastName = ? WHERE userID = ?"
      );
      $stmt->execute([$lastName, $userID]);
    }

    if (!empty($_POST["email"])) {
      $stmt = $con->prepare(
        "UPDATE users SET email = ? WHERE userID = ?"
      );
      $stmt->execute([$email, $userID]);
    }

    if (!empty($_POST["username"])) {
      $stmt = $con->prepare(
        "UPDATE users SET username = ? WHERE userID = ?"
      );
      $stmt->execute([$username, $userID]);
    }

    if (!empty($_POST["password"])) {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $con->prepare(
        "UPDATE users SET password = ? WHERE userID = ?"
      );
      $stmt->execute([$hashedPassword, $userID]);
    }

    header("Location: user_dashboard.php");
    exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">    
  <title>Edit Admin Details</title>
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


<div class="container">

  <h1 class="display-3"><strong>Edit Your Admin Details</strong></h1>

  <form class="registration" action="edit_user_details.php" method="post" >

  <div class="mb-3">
    <label for="firstName"><h5><strong>First Name</strong></h5></label>
      <input class="formInput" name="firstName" type="text">
    </div>

    <div class="mb-3">
        <label for="lastName"><h5><strong>Last Name</strong></h5></label>
        <input class="formInput" name="lastName" type="text">
    </div>

    <div class="mb-3">
        <label for="email"><h5><strong>Email</strong></h5></label>
        <input class="formInput" name="email" type="email">
    </div>

    <div class="mb-3">
        <label for="username"><h5><strong>Username</strong></h5></label> 
        <input class="formInput" name="username" type="text">
    </div> 

    <div class="mb-3">
        <label for="password"><h5><strong>Password</strong></h5></label>
        <input class="formInput" name="password" type="password">
    </div>

    <button id="registrationButton" name="saveUserDetails" type="submit" class="btn btn-primary">Save Changes</button>

  </form>

</div>

</body>
</html>

