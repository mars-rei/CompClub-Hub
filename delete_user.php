<!-- things to improve next time: update clubRank of each member in each club the deleted user is in 
 (but was able to adjust so deleted users are hidden in club category leaderboard -->

<?php
    session_start();
    include 'config/database_pdo.php';

    // update user to deleted (cannot be recovered)
    $stmt = $con->prepare(
        "UPDATE users SET deleted = true WHERE userID = ?"
    );
    $stmt->execute([$_SESSION["id"]]);

    // same code as logged out
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

    // redirect to login page
    header("Location: login.php");
    exit();
?>