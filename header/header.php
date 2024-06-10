
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/index.css">
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
                <?php if ($is_logged_in): ?>
                    <li><a href="profile.php">signed in as @<?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>