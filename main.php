<?php
    session_start();  
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
                $query = "INSERT INTO `user` (username, password, profile_pic, trn_date, isadmin) VALUES ('$username', '" .md5($password). "', '$defaultprofile', '$trn_date', 0)";  // Assuming new users are not admins by default
                $result = mysqli_query($con, $query);
                if ($result) {
                    $success = true;
                    $_SESSION['signup_processed'] = true;
                }
            }
        }
    }

    if (isset($_POST['username'])) {
        $username = stripslashes($_REQUEST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $query = "SELECT * FROM `user` WHERE username='$username' and password='" . md5($password) . "'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $username;
            $_SESSION['profile_pic'] = $row['profile_pic'];
            $_SESSION['isadmin'] = $row['isadmin']; 
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimeLitHub</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
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
                    <?php if(isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1) { ?>
                        <a href="admin.php">Administrator</a>
                    <?php } ?>
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

    <div class="headline">
        <div class="headline_images">
            <div class="head_image item1">
                <p class="head_text">Death's Daughter and the Ebony Blade</p>
            </div>
            <div class="head_image item2">
                <p class="head_text">Berserk of Gluttony</p>
            </div>
            <div class="head_image item3">
                <p class="head_text">The Eminence in Shadow</p>
            </div>
            <div class="head_image item4">
                <p class="head_text">Unnamed Memory</p>
            </div>
            <div class="head_image item5">
                <p class="head_text">That Time I Got Reincarnated as a Slime</p>
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

    <section class="books">
        <a>Popular Light Novels</a>
        <div class="popular_list" id="list">
            <div class="item">
                <img src="" alt="">
                <button>+ Add to list</button>
                <h1>asd</h1>
                <h2>asd</h2>
                <div class="sub_info">
                    <h2>asd</h2>
                    <div class="h3-container"> 
                        <h3>${book.volume}</h3>
                        <h3>${book.publisher}</h3>
                        <h3>${book.year}</h3>
                        <h3>${book.rating}</h3>
                    </div>
                </div>
            </div>
        </div>
        <button class="button-40" id="dimmerButton">SEE ALL LIGHT NOVELS</button>
    </section>

    <section class="books" id="white">
        <a>Recently Most Read Light Novels</a>
        <div class="most_read_books" id="list">
            <div class="item">
                <img src="" alt="">
                <button>+ Add to list</button>
                <h1>asd</h1>
                <h2>asd</h2>  
            </div>
        </div>
        <button class="button-40">SEE ALL LIGHT NOVELS</button>
    </section>

    <section class="books">
        <a>Others Isekai Light Novels</a>
        <div class="isekai_list" id="list">
            <div class="item">
                <img src="" alt="">
                <button>+ Add to list</button>
                <h1>asd</h1>
                <h2>asd</h2>
                <div class="sub_info">
                    <h2>asd</h2>
                    <div class="h3-container"> 
                        <h3>${book.volume}</h3>
                        <h3>${book.publisher}</h3>
                        <h3>${book.year}</h3>
                        <h3>${book.rating}</h3>
                    </div>
                </div>
            </div>
        </div>
        <button class="button-40" id="dimmerButton">SEE ALL LIGHT NOVELS</button>
    </section>

    <section class="books" id="white">
        <a>Romance Light Novels</a>
        <div class="romance_books" id="list">
            <div class="item">
                <img src="" alt="">
                <button>+ Add to list</button>
                <h1>asd</h1>
                <h2>asd</h2>  
            </div>
        </div>
        <button class="button-40">SEE ALL LIGHT NOVELS</button>
    </section>

    <script src="js/mainpage.js"></script>
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