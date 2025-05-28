<?php
  session_start();

  if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
    header('Location: login.php'); 
    exit();
  }

  include 'config/database_pdo.php';

  if (isset($_POST["saveClubDetails"])) {
    $adminID = $_SESSION["id"];

    $stmt = $con->prepare(
      "SELECT clubID
      FROM clubs
      JOIN admins ON clubs.clubID=admins.club
      WHERE adminID = ?"
    );
    $stmt->execute([$adminID]);
    $clubID = $stmt->fetchColumn();

    $name = $_POST["clubName"];
    $email = $_POST["clubEmail"];
    $category = $_POST["clubCategory"];
    $description = $_POST["clubDescription"];
    $address = $_POST["clubAddress"];

    if (!empty($_POST["clubName"])) {
      $stmt = $con->prepare(
        "UPDATE clubs SET clubName = ? WHERE clubID = ?"
      );
      $stmt->execute([$name, $clubID]);
    }

    if (!empty($_POST["clubEmail"])) {
      $stmt = $con->prepare(
        "UPDATE clubs SET clubEmail = ? WHERE clubID = ?"
      );
      $stmt->execute([$email, $clubID]);
    }

    if (!empty($_POST["clubCategory"])) {
      $stmt = $con->prepare(
        "UPDATE clubs SET category = ? WHERE clubID = ?"
      );
      $stmt->execute([$category, $clubID]);
    }

    if (!empty($_POST["clubDescription"])) {
      $stmt = $con->prepare(
        "UPDATE clubs SET description = ? WHERE clubID = ?"
      );
      $stmt->execute([$description, $clubID]);
    }

    if (!empty($_POST["clubAddress"])) {
      $stmt = $con->prepare(
        "UPDATE clubs SET address = ? WHERE clubID = ?"
      );
      $stmt->execute([$address, $clubID]);
    }

    header("Location: admin_dashboard.php");
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

  <h1 class="display-3"><strong>Edit Your Club's Details</strong></h1>

  <form class="registration" action="edit_club_details.php" method="post" >

    <div class="mb-3">
      <label for="clubName"><h5><strong>Club Name</strong></h5></label>
        <input class="formInput" name="clubName" type="text">
      </div>

      <div class="mb-3">
          <label for="clubEmail"><h5><strong>Club Email</strong></h5></label> 
          <input class="formInput" name="clubEmail" type="email">
      </div> 

      <div class="mb-3">
          <label for="clubCategory"><h5><strong>Category</strong></h5></label>
          <input class="formInput" name="clubCategory" type="text">
      </div>

      <div class="mb-3">
          <label for="clubDescription"><h5><strong>Description</strong></h5></label> 
          <input class="formInput" name="clubDescription" type="text">
      </div> 

      <div class="mb-3">
          <label for="clubAddress"><h5><strong>Address</strong></h5></label>
          <input class="formInput" name="clubAddress" type="text">
      </div>

    <button id="registrationButton" name="saveClubDetails" type="submit" class="btn btn-primary">Save Changes</button>

  </form>

</div>

</body>
</html>

