<?php
    $con = mysqli_connect("localhost", "root", "");
    if(mysqli_connect_errno()){
        echo "Failed Connection: " . mysqli_connect_error();
    }

    $databaseName = "library";
    $checkDatabaseQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'";
    $checkDatabaseResult = mysqli_query($con, $checkDatabaseQuery);

    if(mysqli_num_rows($checkDatabaseResult) == 0) {
        $createDatabaseQuery = "CREATE DATABASE $databaseName";
        if(mysqli_query($con, $createDatabaseQuery)){
        } else {
            echo "Error creating database: " . mysqli_error($con) . "<br>";
        }
    }

    $con = mysqli_connect("localhost", "root", "", $databaseName);
    if(mysqli_connect_errno()){
        echo "Failed Connection: " . mysqli_connect_error();
    }

    $checkTableQuery = "SHOW TABLES LIKE 'user'";
    $checkTableResult = mysqli_query($con, $checkTableQuery);
    $tableExists = mysqli_num_rows($checkTableResult) > 0;
    if(!$tableExists) {
        $createTableQuery = "CREATE TABLE user (
            id INT(50) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(50) NOT NULL,
            profile_pic VARCHAR(50) NOT NULL,
            trn_date VARCHAR(50) NOT NULL,
            isadmin INT(11) NOT NULL
        )";
        if(mysqli_query($con, $createTableQuery)){
            echo "Table 'user' created successfully<br>";
            $adminUsername = "admin";
            $adminPassword = "admin";
            $adminProfilePic = "noprofile.jpg"; 
            $adminTrnDate = date("Y-m-d H:i:s");
            $adminIsAdmin = 1;
            $insertAdminQuery = "INSERT INTO user (username, password, profile_pic, trn_date, isadmin) VALUES ('$adminUsername', '" . md5($adminPassword) . "', '$adminProfilePic', '$adminTrnDate', '$adminIsAdmin')";
            if(mysqli_query($con, $insertAdminQuery)){
                echo "Admin account created successfully<br>";
            } else {
                echo "Error creating admin account: " . mysqli_error($con) . "<br>";
            }
        } else {
            echo "Error creating table 'user': " . mysqli_error($con) . "<br>";
        }
    }

    $createListofBooksQuery = "CREATE TABLE IF NOT EXISTS listofbooks (
        username VARCHAR(50) NOT NULL,
        book1 INT(50),
        book2 INT(50),
        book3 INT(50),
        book4 INT(50),
        book5 INT(50),
        book6 INT(50),
        book7 INT(50),
        book8 INT(50),
        book9 INT(50),
        book10 INT(50),
        book11 INT(50),
        book12 INT(50),
        book13 INT(50),
        book14 INT(50),
        book15 INT(50),
        book16 INT(50),
        book17 INT(50),
        book18 INT(50),
        book19 INT(50),
        book20 INT(50),
        book21 INT(50),
        book22 INT(50),
        book23 INT(50),
        book24 INT(50)
    )";
    if(mysqli_query($con, $createListofBooksQuery)){
    } else {
        echo "Error creating table 'listofbooks': " . mysqli_error($con) . "<br>";
    }

    $createBooksRatingQuery = "CREATE TABLE IF NOT EXISTS booksrating (
        username VARCHAR(50) NOT NULL,
        book1 INT(50),
        book2 INT(50),
        book3 INT(50),
        book4 INT(50),
        book5 INT(50),
        book6 INT(50),
        book7 INT(50),
        book8 INT(50),
        book9 INT(50),
        book10 INT(50),
        book11 INT(50),
        book12 INT(50),
        book13 INT(50),
        book14 INT(50),
        book15 INT(50),
        book16 INT(50),
        book17 INT(50),
        book18 INT(50),
        book19 INT(50),
        book20 INT(50),
        book21 INT(50),
        book22 INT(50),
        book23 INT(50),
        book24 INT(50)
    )";
    if(mysqli_query($con, $createBooksRatingQuery)){
    } else {
        echo "Error creating table 'booksrating': " . mysqli_error($con) . "<br>";
    }

    $createUserInfoQuery = "CREATE TABLE IF NOT EXISTS userinfo (
        username VARCHAR(255) NOT NULL,
        location VARCHAR(255),
        gender VARCHAR(255),
        bday VARCHAR(255),
        email VARCHAR(255)
    )";
    if(mysqli_query($con, $createUserInfoQuery)){
    } else {
        echo "Error creating table 'userinfo': " . mysqli_error($con) . "<br>";
    }
?>
