<?php
  session_start();

  if ($_SESSION['loggedin'] !== true || $_SESSION['loggedin'] == false) {
    header('Location: login.php'); 
    exit();
  }

  include 'config/database_pdo.php';

  if (isset($_POST["saveAdminDetails"])) {
    $adminID = $_SESSION["id"];
    $username = $_POST["adminUsername"];
    $password = $_POST["adminPassword"];

    if (!empty($_POST["adminUsername"])) {
      $stmt = $con->prepare(
        "UPDATE admins SET username = ? WHERE adminID = ?"
      );
      $stmt->execute([$username, $adminID]);
    }

    if (!empty($_POST["adminPassword"])) {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $con->prepare(
        "UPDATE admins SET password = ? WHERE adminID = ?"
      );
      $stmt->execute([$hashedPassword, $adminID]);
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

  <h1 class="display-3"><strong>Edit Your Admin Details</strong></h1>

  <form class="registration" action="edit_admin_details.php" method="post" >

    <div class="mb-3">
        <label for="adminUsername"><h5><strong>Admin Username<strong></h5></label>
        <input class="formInput" name="adminUsername" required="" type="text"/>
    </div>

    <div class="mb-3">
        <label for="adminPassword"><h5><strong>Admin Password</strong></h5></label>
        <input class="formInput" name="adminPassword" required="" type="password"/>
    </div>

    <button id="registrationButton" name="saveAdminDetails" type="submit" class="btn btn-primary">Save Changes</button>

  </form>

</div>

</body>
</html>

