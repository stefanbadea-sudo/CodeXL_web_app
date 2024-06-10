<?php
session_start(); // Start the session to manage user authentication

// Check if the user is logged in
$is_logged_in = isset($_SESSION['username']);
?>
     <?php include '../header/header.php'; ?>

    <!-- main content -->

    <div class="big_name">
        <div class="container">
            <h1>CODExL</h1>
            <p>your coding companion</p>
        </div>
    </div>

    <div class="text">
        <p>Master programming languages and paradigms in six months with CODExL.
            Dive deep into coding styles and diverse paradigms to refine your
            skills and expand your horizons. Join us and unlock your full potential
            with CODExL – where learning to code becomes a transformative experience.
        </p>
    </div>

    <!-- buttons for login and sign in -->
    <div class="buttons">
        <a href="login.php"><button>Log in</button></a>
        <a href="register.php"><button>Register</button></a>
    </div>

    <!-- cards and photo container -->
    <div class="content-container">
        <div class="cards">
            <div class="card">
                <h2>Python</h2>
                <p>Learn Python, the most popular programming language in the world.
                    Develop your skills in Python and become a master coder with CODExL.
                </p>
            </div>

            <div class="card">
                <h2>Java</h2>
                <p>Master Java, the most versatile programming language.
                    Develop your skills in Java and become a master coder with CODExL.
                </p>
            </div>

            <div class="card">
                <h2>JavaScript</h2>
                <p>Learn JavaScript, the language of the web.
                    Develop your skills in JavaScript and become a master coder with CODExL.
                </p>
            </div>

            <div class="card">
                <h2>C++</h2>
                <p>Master C++, the language of systems programming.
                    Develop your skills in C++ and become a master coder with CODExL.
                </p>
            </div>
        </div>

        <!-- photo -->
        <div class="photo">
            <img src="../images/9a68716efc331fcc84e3a4ce5f23d18d 1.png" alt="">
        </div>
    </div>

    <div class="get_info_courses">
        <div class="container">
            <a href="./courses.php">Get more information about courses here</a>
        </div>
    </div>


    <!-- footer -->
    <?php include '../footer/footer.php'; ?>