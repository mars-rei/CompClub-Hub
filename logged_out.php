<?php
  if (isset($_POST["logout"])) {
      session_start();
      $_SESSION = array();
      if (ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 42000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
          );
      }
      session_destroy();

      session_start();
      $_SESSION["loggedin"] = false;
      $_SESSION["type"] = "viewer"; 

      header("Location: logged_out.php");
      exit(); 
  }
?>

<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    
    <title>Logged Out</title>

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
    
    <div class="container" id="homeMenu">

      <h1 class="display-3"><strong>You have logged out.</strong></h1>

    </div>

  </body>
</html>
