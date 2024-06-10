<?php
session_start(); // Start the session to manage user authentication

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

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

// Retrieve user information from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Check if the form for deleting the account was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    // Delete the user account from the database
    $sql_delete = "DELETE FROM users WHERE id='$user_id'";

    if ($conn->query($sql_delete) === TRUE) {
        // Set a success message
        $_SESSION['success_message'] = "Account with username @" . htmlspecialchars($_SESSION['username']) . " was deleted successfully.";
        // Unset user session data
        session_unset();
        session_destroy();
    } else {
        echo "Error deleting account: " . $conn->error;
    }
}

// Check if the form for logging out was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Unset user session data
    session_unset();
    session_destroy();
    // Set a logout success message
    $_SESSION['logout_message'] = "You have been logged out successfully.";
    // Redirect to the login page
    header("Location: login.php");
    exit();
}

// Check if the form for resetting the password was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash the new password

    // Update the user's password in the database
    $sql_update_password = "UPDATE users SET password='$new_password' WHERE id='$user_id'";

    if ($conn->query($sql_update_password) === TRUE) {
        // Set a success message
        $_SESSION['success_message'] = "Password was reset successfully.";
    } else {
        echo "Error resetting password: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-profile</title>
    <!-- css -->
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>
<body>

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
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php">signed in as @<?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- main content -->
    <div class="profile-container">
        <div class="profile-image">
            <img src="../images/coding-illustration-3d-png.png" alt="Profile Illustration">
        </div>
        <div class="profile">
            <div class="container">
                <div class="profile_info">
                    <h2>Profile</h2>
                    
                    <!-- Display user's profile information -->
                    <img src="../images/User_alt.png" alt="Profile Picture">
                    
                    <div class="profile-details">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_no']); ?></p>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Score:</strong> <?php echo htmlspecialchars($user['score']); ?></p>
                    </div>

                    <!-- Form for password reset -->
                    <div class="password-reset">
                        <h3>Reset Password</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="password" name="new_password" placeholder="New Password" required>
                            <button type="submit" name="reset_password">Reset Password</button>
                        </form>
                    </div>

                    <!-- Button for deleting the account -->
                    <div class="delete-account">
                        <h3>Delete Account</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <button type="submit" name="delete_account">Delete Account</button>
                        </form>
                    </div>

                    <!-- Button for logging out -->
                    <div class="logout">
                        <h3>Logout</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <button type="submit" name="logout">Logout</button>
                        </form>
                    </div>

                    <!-- Display success message if exists -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="success-message">
                            <p><?php echo $_SESSION['success_message']; ?></p>
                            <?php unset($_SESSION['success_message']); ?> <!-- Clear the success message after displaying -->
                        </div>
                    <?php endif; ?>

                    <!-- Display logout message if exists -->
                    <?php if (isset($_SESSION['logout_message'])): ?>
                        <div class="success-message">
                            <p><?php echo $_SESSION['logout_message']; ?></p>
                            <?php unset($_SESSION['logout_message']); ?> <!-- Clear the logout message after displaying -->
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include '../footer/footer.php'; ?>