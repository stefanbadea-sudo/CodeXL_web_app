<?php
session_start(); // Start the session to manage user authentication

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codexl_user_db";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to display error messages
function display_error($message) {
    echo "<div style='color: red;'><strong>Error:</strong> $message</div>";
}

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username_or_email = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Search for the user in the database
        $sql = "SELECT * FROM users WHERE username='$username_or_email' OR email='$username_or_email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Save user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to the profile page after successful login
                header("Location: profile.php");
                exit();
            } else {
                display_error("Incorrect password.");
            }
        } else {
            display_error("User not found.");
        }
    } else {
        display_error("All fields are required.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-LogIn</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>

<body style="background: linear-gradient(90deg, #5D75F7 0%, #A42FD9 100%);">
    <!-- navigation bar -->
    <div class="header">
        <div class="navigation">
            <ul>
                <li id="C"><a href="index.php">C</a></li>
                <li><a href="product.html">product</a></li>
                <li><a href="./profile.php">profile</a></li>
                <li><a href="./courses.php">courses</a></li>
                <li><a href="./progress.php">progress</a></li>
                <li><a href="./notes.php">notes</a></li>
            </ul>
        </div>
    </div>

    <!-- main content -->
    <div class="login">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="../images/loginphoto.png" alt="Login Photo" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <form action="login.php" method="post">
                        <h1>Log In</h1>
                        <input type="text" name="username" placeholder="Username/Email" class="form-control mb-3">
                        <input type="password" name="password" placeholder="Password" class="form-control mb-3">
                        <button type="submit" class="btn btn-primary btn-block">Log In</button>
                        <a href="profile.php" class="d-block mt-2">Forgot Password?</a>
                    </form>
                    <div class="create_account mt-3">
                        <a href="register.php">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include '../footer/footer.php'; ?>