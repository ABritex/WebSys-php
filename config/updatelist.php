<?php
session_start();
require('../db.php');

if (!isset($_SESSION['username'])) {
    header("Location: main.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book = mysqli_real_escape_string($con, $_POST['book']);

    if (isset($_POST['category'])) {
        $category = mysqli_real_escape_string($con, $_POST['category']);

        $check_query = "SELECT * FROM listofbooks WHERE username = '$username'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            $insert_query = "INSERT INTO listofbooks (username) VALUES ('$username')";
            mysqli_query($con, $insert_query);
        }

        $update_query = "UPDATE listofbooks SET $book = '$category' WHERE username = '$username'";
        $update_result = mysqli_query($con, $update_query);

        if (!$update_result) {
            die("Update failed: " . mysqli_error($con));
        }
    }

    if (isset($_POST['rating'])) {
        $rating = mysqli_real_escape_string($con, $_POST['rating']);

        error_log("Book: $book, Rating: $rating");

        $check_query = "SELECT * FROM booksrating WHERE username = '$username'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            $insert_query = "INSERT INTO booksrating (username) VALUES ('$username')";
            mysqli_query($con, $insert_query);
        }

        $update_query = "UPDATE booksrating SET $book = '$rating' WHERE username = '$username'";
        $update_result = mysqli_query($con, $update_query);

        if (!$update_result) {
            die("Update failed: " . mysqli_error($con));
        }
    }

    header("Location: ../main.php");
    exit();
}
?>
