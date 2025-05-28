<?php
session_start();

//making sure that that the user is an admin and logged in
if ($_SESSION['loggedin'] !== true || $_SESSION['type'] !== 'admin') {
    header('Location:login.php');
    exit();
}

include 'config/database_pdo.php';

if (isset($_GET['toggle'])) {
    $userId = $_GET['userId'];
    $type = $_GET['type']; // 'user' or 'admin'

    try {
        if ($type === 'user') {
            // Toggle user account status (Active or Inactive)
            $stmt = $con->prepare("SELECT isActive FROM users WHERE userID = ?");
            $stmt->execute([$userId]);
            $status = $stmt->fetchColumn();

            //Toggle the status
            $newStatus = ($status == 1) ? 0 : 1;

            //Updating the status
            $stmt = $con->prepare("UPDATE users SET isActive = ? WHERE userID = ?");
            $stmt->execute([$newStatus, $userId]);

            echo '<script>alert("User account status updated successfully.")</script>';
        } elseif ($type === 'admin') {
            //Toggle admin account status (Active/Inactive)
            $stmt = $con->prepare("SELECT isActive FROM admins WHERE adminID = ?");
            $stmt->execute([$userId]);
            $status = $stmt->fetchColumn();

            //Toggle the status
            $newStatus = ($status == 1) ? 0 : 1;

            //Updating the status
            $stmt = $con->prepare("UPDATE admins SET isActive = ? WHERE adminID = ?");
            $stmt->execute([$newStatus, $userId]);

            echo '<script>alert("Admin account status updated successfully.")</script>';
        }

        //Redirecting to the appropriate page
        header("Location:admin_dashboard.php"); 
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
