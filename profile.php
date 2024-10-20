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
                $query = "INSERT INTO `user` (username, password, profile_pic, trn_date) VALUES ('$username', '" . md5($password) . "', '$defaultprofile', '$trn_date')";
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
        return null;
    }
   
  
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $location = trim($_POST['location']);
        $gender = trim($_POST['gender']);
        $bday = trim($_POST['bday']);
        $email = trim($_POST['email']);
        
        $username = $_SESSION['username'];
        $stmt = $con->prepare("SELECT COUNT(*) FROM userinfo WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $con->prepare("UPDATE userinfo SET location=?, gender=?, bday=?, email=? WHERE username=?");
            $stmt->bind_param("sssss", $location, $gender, $bday, $email, $username);
        } else {
            $stmt = $con->prepare("INSERT INTO userinfo (username, location, gender, bday, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $location, $gender, $bday, $email);
        }
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Error updating preferences.";
        }
        $stmt->close();
    }
    $username = $_SESSION['username'];
    $stmt = $con->prepare("SELECT * FROM userinfo WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $userinfo = $result->fetch_assoc();
    $stmt->close();

    $username = $_SESSION['username'];
    $list_query = "SELECT * FROM listofbooks WHERE username = '$username'";
    $list_result = mysqli_query($con, $list_query);
    if (!$list_result) {
        die("Error fetching list data: " . mysqli_error($con));
    }
    $list_data = mysqli_fetch_assoc($list_result);
    $read_total = 0;
    $reading_total = 0;
    $want_to_read_total = 0;
    $stalled_total = 0;
    $dropped_total = 0;
    
    foreach ($list_data as $book => $category) {
        switch ($category) {
            case 1:
                $read_total++;
                break;
            case 2:
                $reading_total++;
                break;
            case 3:
                $want_to_read_total++;
                break;
            case 4:
                $stalled_total++;
                break;
            case 5:
                $dropped_total++;
                break;
        }
    }
    
    $rating_query = "SELECT * FROM booksrating WHERE username = '$username'";
    $rating_result = mysqli_query($con, $rating_query);
    if (!$rating_result) {
        die("Error fetching rating data: " . mysqli_error($con));
    }
    $rating_data = mysqli_fetch_assoc($rating_result);
    
    $rating_totals = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    
    foreach ($rating_data as $book => $rating) {
        if (is_numeric($rating) && $rating >= 1 && $rating <= 5) {
            $rating_totals[$rating]++;
        }
    }
    
    mysqli_close($con);
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>


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
                        <a href="#" class="settings-asd">Settings</a>
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
            
    <section class="main">
        <div class="container">
            <div class="pics">
                <div class="header-banner">
                    <img src="image/banner.png" alt="">
                </div>
                <div class="profile-picture">
                    <img id="profile-img" src="<?php echo isset($_SESSION['profile_pic']) ? 'image/' . $_SESSION['profile_pic'] : 'image/noprofile.jpg'; ?>" style="width: 150px; height: 150px; border-radius: 5%;">
                    <div class="status-username">
                        <div class="status-circle"></div>
                        <p class="username"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></p>
                        <i class="fa fa-cog settings-icon settings-asd" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="lifeonanime">
                <h2>Life on Books</h2>
            </div>
            <div class="listrating">
                <div class="list">
                    <h2>Novel List</h2>
                    <ul class="anime_list">
                        <li class="anime_item">
                            <p class="circle read"><?php echo $read_total; ?></p><a href="#" class="">Read</a>
                        </li>
                        <li class="anime_item">
                            <p class="circle reading"><?php echo $reading_total; ?></p><a href="#" class="">Reading</a>
                        </li>
                        <li class="anime_item">
                            <p class="circle want-to-read"><?php echo $want_to_read_total; ?></p><a href="#" class="">Want to Read</a>
                        </li>
                        <li class="anime_item">
                            <p class="circle stalled"><?php echo $stalled_total; ?></p><a href="#" class="">Stalled</a>
                        </li>
                        <li class="anime_item">
                            <p class="circle dropped"><?php echo $dropped_total; ?></p><a href="#" class="">Dropped</a>
                        </li>
                    </ul>
                </div>
                <div class="rating">
                    <h2>Novel Rating</h2>
                    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
                </div>

            </div>
            <div class="personalinfo">
                <h2>Personal Information</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="location" placeholder="Location" value="<?php echo isset($userinfo['location']) ? $userinfo['location'] : ''; ?>">
                    <select name="gender" id="gender">
                        <option value="male" <?php echo isset($userinfo['gender']) && $userinfo['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo isset($userinfo['gender']) && $userinfo['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo isset($userinfo['gender']) && $userinfo['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <input type="date" name="bday" placeholder="Birthdate" value="<?php echo isset($userinfo['bday']) ? $userinfo['bday'] : ''; ?>">
                    <input type="email" name="email" placeholder="Email" value="<?php echo isset($userinfo['email']) ? $userinfo['email'] : ''; ?>">
                    <button type="submit" name="submit">Update Preferences</button>
                </form>
            </div>
        </div>
    </section>



    <script src="js/s.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.settings-icon').click(function(){
                $('.lifeonanime, .listrating, .rating').hide();
                $('.personalinfo').show();
            });
        });
        $(document).ready(function(){
            $('.settings-asd').click(function(){
                $('.lifeonanime, .listrating, .rating').hide();
                $('.personalinfo').show();
            });
        });
        var xValues = ["5", "4", "3", "2", "1"];
        var yValues = [
            <?php echo $rating_totals[5]; ?>,
            <?php echo $rating_totals[4]; ?>,
            <?php echo $rating_totals[3]; ?>,
            <?php echo $rating_totals[2]; ?>,
            <?php echo $rating_totals[1]; ?>
        ];
        var barColors = ["red", "green", "blue", "orange", "brown"];

        new Chart("myChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                }
            }
        });
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