<?php   
    require('db.php');
    $error = "";
    $success = false;
    if (isset($_POST['signup_submit']) && !isset($_SESSION['signup_processed'])) {
        $username = stripslashes($_POST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_POST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $cpassword = stripslashes($_POST['cpassword']);
        $cpassword = mysqli_real_escape_string($con, $cpassword);
        $trn_date = date("Y-m-d H:i:s");
        $check_query = "SELECT * FROM `user` WHERE username='$username'";
        $check_result = mysqli_query($con, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            if ($password != $cpassword) {
                $error = "Passwords do not match.";
            } else {
                $defaultprofile = 'noprofile.jpg';
                $query = "INSERT INTO `user` (username, password, profile_pic, trn_date) VALUES ('$username', '" .md5($password). "', '$defaultprofile', '$trn_date')";
                $result = mysqli_query($con, $query);
                if ($result) {
                    $success = true;
                    $_SESSION['signup_processed'] = true;
                }
            }
        }
    }
    
    session_start();    

    if(isset($_POST['username'])){
        $username = stripslashes($_REQUEST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $query = "SELECT * FROM `user` WHERE username='$username' and password='".md5($password)."'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        $rows = mysqli_num_rows($result);
        if ($rows==1){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $username;
            $_SESSION['profile_pic'] = $row['profile_pic']; 
        }
    }  

    function fetchBookInfo($bookId) {
        $jsonFile = file_get_contents('json/booklibrary.json');
        $data = json_decode($jsonFile, true);
        foreach ($data['popular_books'] as $book) {
            if ($book['id'] == $bookId) {
                return $book;
            }
        }
        foreach ($data['most_read_books'] as $book) {
            if ($book['id'] == $bookId) {
                return $book;
            }
        }
        foreach ($data['isekai_books'] as $book) {
            if ($book['id'] == $bookId) {
                return $book;
            }
        }
        foreach ($data['romance_books'] as $book) {
            if ($book['id'] == $bookId) {
                return $book;
            }
        }
        return null;
    }

    $bookId = $_GET['id'];
    $bookName = $_GET['name'];
    $book = fetchBookInfo($bookId);
    
    if ($book) {
        $userId = $_SESSION['username']; 
        $columnName = "book" . $bookId;
        $check_query = "SELECT $columnName FROM `listofbooks` WHERE username='$userId'";
        $check_result = mysqli_query($con, $check_query);
        $bookExists = false;
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $row = mysqli_fetch_assoc($check_result);
            if ($row[$columnName] > 0) {
                $bookExists = true;
            }
        }
    
        if ($bookExists) {
            echo "
            <section class='main'>
                <div class='novelinfo' id='infos'>
                    <div class='desc'>
                        <div class='TITLE'>
                            <h2>$bookName</h2>
                            <h1>Alt: {$book['alt']}</h1>
                        </div>
                        <hr class='dropdown-divider'>
                        <div class='h3-container'> 
                            <h3>{$book['volume']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['publisher']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['year']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['rating']}</h3>
                        </div>
                        <div class='image-description-container'>
                            <img src='{$book['image']}' alt=''>
                            <h1>{$book['desc']}</h1>
                        </div>
                        <button onclick='addToList(\"$bookId\")' class='button-update'>Update</button> 
                    </div>
                </div>
            </section>";
        } else {
            echo "
            <section class='main'>
                <div class='novelinfo' id='infos'>
                    <div class='desc'>
                        <div class='TITLE'>
                            <h2>$bookName</h2>
                            <h1>Alt: {$book['alt']}</h1>
                        </div>
                        <hr class='dropdown-divider'>
                        <div class='h3-container'> 
                            <h3>{$book['volume']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['publisher']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['year']}</h3>
                            <hr class='vertical-line'>
                            <h3>{$book['rating']}</h3>
                        </div>
                        <div class='image-description-container'>
                            <img src='{$book['image']}' alt=''>
                            <h1>{$book['desc']}</h1>
                        </div>
                        <button onclick='addToList(\"$bookId\")' class='button-add'>+ Add to list</button> 
                    </div>
                </div>
            </section>";
        }
    } else {
        echo "<p>Book not found!</p>";
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/novels.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
    <header class="header">
        <a href="main.php" class="nav_logo">AnimeLitHub</a>
        <ul class="nav_items">
            <li class="nav_item">
                <a href="main.php" class="nav_link">Home</a>
                <a href="#" class="nav_link">asd</a>
                <a href="#" class="nav_link">asd</a>
                <a href="#" class="nav_link">asd</a>
            </li>
        </ul>
        <?php if(!isset($_SESSION['username'])) { ?>
            <div class="button-container">
                <button class="button" id="form-open">Login</button>
            </div>
            <?php } else { ?>
                <div class="profile-dropdown" id="profile-dropdown">
                    <img id="profile-img" src="<?php echo isset($_SESSION['profile_pic']) ? 'image/' . $_SESSION['profile_pic'] : 'image/noprofile.jpg'; ?>" style="width: 27px; height: 27px; border-radius: 50%;">
                    <span class="username"><?php echo $_SESSION['username']; ?></span>
                    <div class="dropdown-content" id="dropdown-content">
                        <a href="profile.php">View Profile</a>
                        <a href="profile.php#settings-asd">Settings</a>
                        <hr class="dropdown-divider">
                        <button class="logout-button">Sign out</button>
                    </div>
                </div>
            <?php } ?>
    </header>
    
    <div class="home">
        <div class="form_container">
            <i class="uil uil-times form_close"></i>
            <div class="form login_form">
                <form action="" method="post" name="login">
                    <h1>AnimeLitHub</h1>
                    <h2>Login</h2>
                    <div class="input_box">
                        <input type="text" name="username" placeholder="Enter your username" required />
                        <i class="uil uil-user email"></i>
                    </div>
                    <div class="input_box">
                        <input type="password" name="password" placeholder="Enter your password" required />
                        <i class="uil uil-lock password"></i>
                        <i class="uil uil-eye-slash pw_hide"></i>
                    </div>
                    <div class="option_field">
                        <span class="checkbox">
                            <input type="checkbox" id="check" />
                            <label for="check">Remember me</label>
                        </span>
                        <a href="#" class="forgot_pw">Forgot password?</a>
                    </div>
                    <button class="button" name="submit" type="submit" value="Login">Login Now</button>
                    <div class="login_signup">Don't have an account? <a href="#" id="signup">Signup</a></div>
                </form>
            </div>
            
            <div class="form signup_form">
                <form action="" method="post">
                    <h1>AnimeLitHub</h1>
                    <h2>Signup</h2>
                    <div class="input_box">
                        <input type="text" name="username" placeholder="Enter your username" required />
                        <i class="uil uil-user email"></i>
                    </div>
                    <div class="input_box">
                        <input type="password" name="password" placeholder="Create password" required />
                        <i class="uil uil-lock password"></i>
                        <i class="uil uil-eye-slash pw_hide"></i>
                    </div>
                    <div class="input_box">
                        <input  type="password" name="cpassword" placeholder="Confirm password"  />
                        <i class="uil uil-lock password"></i>
                        <i class="uil uil-eye-slash pw_hide"></i>
                    </div>
                    <button type="submit" name="signup_submit" class="button">Signup Now</button>
                    <div class="login_signup">Already have an account? <a href="#" id="login">Login</a></div>
                </form>
            </div>
        </div>
    </div>

    <div id="myModal" class="addtolist">
        <div class="add_list">
            <a>Status<span class="close" onclick="closeModal()">&times;</span><a>
            <form method="POST" action="config/updatelist.php" class="content-wrapper personal-profile">
                <input type="hidden" id="book" name="book"/>
                <a>My Anime:  </a>
                <select id="category" name="category" class="selection">
                    <option value="0">Unread</option>
                    <option value="1">Read</option>
                    <option value="2">Reading</option>
                    <option value="3">Want to Read</option>
                    <option value="4">Stalled</option>
                    <option value="5">Dropped</option>
                </select>

                <a>Rating: </a>
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5"/><label for="star5" title="5 stars">☆</label>
                    <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="4 stars">☆</label>
                    <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="3 stars">☆</label>
                    <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="2 stars">☆</label>
                    <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="1 star">☆</label>
                </div>

                <button type="submit" id="asd" name="submit" class="button-84">Done</button>
            </form>
        </div>
    </div>        
    
    <footer>
        <div class="footer-content">
            <h3>AnimeLitHub</h3>
            <p>Learn about more ways to support <a href="#">AnimeLitHub</p>
            <ul class="socials">
                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                <li><a href="#"><i class="fa fa-youtube"></i></a></li>
                <li><a href="#"><i class="fa fa-linkedin-square"></i></a></li>
            </ul>
        </div>
        <div class="footer-bottom">
            <p>copyright &copy;2024 AnimeLitHub. designed by <span>Group 4</span></p>
        </div>
    </footer>



    <script src="js/main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutButton = document.querySelector('.logout-button');
            logoutButton.addEventListener('click', function () {
                fetch('logout.php', {
                    method: 'POST',
                })
                .then(response => {
                    if (response.ok) {
                        window.location.replace('main.php');
                    } else {
                        console.error('Failed to sign out');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
        
    </script>
</body>
</html>