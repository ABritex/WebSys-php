<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header("Location: main.php");
    exit();
}
require('db.php');
$query = "SELECT id, username, profile_pic, trn_date, isadmin FROM user";
$result = mysqli_query($con, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AnimeLitHub</title>
    <link rel="stylesheet" href="css/admin.css">
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

    <div class="container">
        <h1>Admin Panel</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>

        <h2>User List</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Profile Picture</th>
                <th>Registration Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><img src="image/<?php echo $row['profile_pic']; ?>" style="width: 50px; height: 50px; border-radius: 50%;"></td>
                    <td><?php echo $row['trn_date']; ?></td>
                    <td>
                        <form method="post" action="config/deleteuser.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        document.querySelector('.logout-button').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
    </script>
</body>
</html>
