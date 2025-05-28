<?php
    echo '
    <form class="homeButton" action="index.php" method="post">
        <input id="logoIcon" src="imgs/logo.png" name="home" type="image">
    </form>
    <h2 class="display-3"><strong>COMPCLUB HUB</strong></h2>
    <form class="loginButton" action="login.php" method="post" >
        <input id="loginIcon" src="imgs/login.png" name="login" type="image">
    </form>';

    // to redirect to the home page
    if (isset($_POST["home"])) {
        header("Location: index.php");
        exit();
    }

    // to redirect to login page
    if (isset($_POST["login"])) {
        header("Location: login.php");
        exit();
    }
?>