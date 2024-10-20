<?php
    session_start();
    if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require('../db.php');
        $user_id = intval($_POST['user_id']);
        $query = "SELECT isadmin FROM user WHERE id = $user_id";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row['isadmin'] == 1) {
            header("Location: admin.php?error=Cannot delete admin users.");
            exit();
        }

        $query = "DELETE FROM user WHERE id = $user_id";
        if (mysqli_query($con, $query)) {
            header("Location: admin.php?success=User deleted successfully.");
        } else {
            header("Location: admin.php?error=Error deleting user.");
        }
        exit();
    } else {
        header("Location: admin.php");
        exit();
    }
?>
