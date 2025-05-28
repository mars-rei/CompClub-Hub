<?php
  session_start();
?>

<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous" defer></script>
    
    <link rel="stylesheet" type="text/css" href="css/styles.css">

  </head>

  <body>

  <header> 
    <!-- php for menu -->
    <?php 
      include "commonPhp/headerLogIn.php";
    ?>
  </header>
    
    <div class="container" id="homeMenu">

      <h1 class="display-3"><strong>Welcome to CompClub Hub!</strong></h1>

      <div class="row" id="homeRow">
        <form action="club_search.php" method="GET">
            <input type="text" id="clubSearch" name="clubSearch" placeholder="SEARCH CLUBS">
        </form>
      </div>

      <div class="row" id="homeRow">
        <form action="club_category_leaderboard_search.php" method="GET">
            <input type="text" id="clubCategoryLeaderboard" name="clubCategoryLeaderboard" placeholder="SEARCH LEADERBOARDS BY CATEGORY">
        </form>
      </div>

    </div>

    <!--adding Java for clearing input field -->
    <script>
    //function to clear the search field when the user clicks outside
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('clubSearch');

        //clears search input when the user clicks outside
        searchInput.addEventListener('blur', function() {
            this.value = '';
        });

        //makes sure that the value is cleared when the user presses the "escape" //key?
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                this.value = '';
            }
        });
    });

    </script>

  </body>
</html>
