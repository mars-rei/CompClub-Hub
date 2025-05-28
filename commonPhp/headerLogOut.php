<?php
    echo '<form class="homeButton" action="index.php" method="post">
    <input id="logoIcon" src="imgs/logo.png" name="home" type="image">
    </form>
    <h2 class="display-3"><strong>COMPCLUB HUB</strong></h2>

    <form class="logoutButton" action="logged_out.php" method="post" >
    <input id="logoutIcon" src="imgs/logout.png" name="logout" type="image">
    </form>';

    // to redirect to the home page
    if (isset($_POST["home"])) {
        header("Location: index.php");
        exit();
    }

    // to logged out message page
    if (isset($_POST["logout"])) {
        header("Location: logged_out.php");
        exit();
    }
?>