<?php
session_start(); // Start sesiunea pentru gestionarea autentificării utilizatorului

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    // Dacă nu este autentificat, redirecționează către pagina de autentificare
    header("Location: login.php");
    exit();
}

// Parametrii de conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codexl_user_db";

// Stabilește conexiunea la baza de date
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea la baza de date
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifică dacă formularul a fost trimis pentru adăugarea unei noi note
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preia datele din formular
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepare și execută interogarea pentru inserarea unei noi înregistrări în tabela notes
    $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);

    if ($stmt->execute()) {
        // Redirecționează către pagina notes.php după ce nota a fost salvată cu succes
        header("Location: notes.php");
        exit();
    } else {
        // În caz de eroare, afișează un mesaj de eroare
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-courses</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/courses.css">
    <link rel="stylesheet" href="../css/bootstrap.css">

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const courseLinks = {
                "python": "https://docs.google.com/presentation/d/e/2PACX-1vQ9SB5Ulh3JIipUWMgH8cCSSxUDd1FkG81-ur7jVAKBVIgV9DRQif4gFXh9wJCq6LGrV0nLgC3inX3E/embed?start=false&loop=false&delayms=3000",
                "java": "https://docs.google.com/presentation/d/e/2PACX-1vQPqu5rOoP4eoiT80NP3n1FgaRYF65OhFaCdu6eMdW2e0QkyrmHPvHZ2Qpw790bihhrhX58BhqnNyXD/embed?start=false&loop=false&delayms=10000",
                "javascript": "https://docs.google.com/presentation/d/e/2PACX-1vSo6-bnNCTSz6yfTgfFXz8tuZnUkn-y6Zc5jY7_ngN4EKnPU9_utizFdY2YBzBxLJlns0e8pHi0eb97/embed?start=false&loop=false&delayms=3000",
                "c++": "https://docs.google.com/presentation/d/e/2PACX-1vTqYZHorV-mqsz_pfpnAU5HnpYJp9kY8Osa2c34YHcqBPIEGxY_mXRc58noZV2FPBSqG8zwaSbs8LmH/embed?start=false&loop=false&delayms=3000"
            };

            document.querySelectorAll('.card button').forEach(button => {
                button.addEventListener('click', () => {
                    const course = button.getAttribute('data-course');
                    const iframe = document.querySelector('.presentation iframe');
                    iframe.src = courseLinks[course];
                });
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const courseLinks = {
                "python": "https://docs.google.com/presentation/d/e/2PACX-1vQ9SB5Ulh3JIipUWMgH8cCSSxUDd1FkG81-ur7jVAKBVIgV9DRQif4gFXh9wJCq6LGrV0nLgC3inX3E/embed?start=false&loop=false&delayms=3000",
                "java": "https://docs.google.com/presentation/d/e/2PACX-1vQPqu5rOoP4eoiT80NP3n1FgaRYF65OhFaCdu6eMdW2e0QkyrmHPvHZ2Qpw790bihhrhX58BhqnNyXD/embed?start=false&loop=false&delayms=10000",
                "javascript": "https://docs.google.com/presentation/d/e/2PACX-1vSo6-bnNCTSz6yfTgfFXz8tuZnUkn-y6Zc5jY7_ngN4EKnPU9_utizFdY2YBzBxLJlns0e8pHi0eb97/embed?start=false&loop=false&delayms=3000",
                "c++": "https://docs.google.com/presentation/d/e/2PACX-1vTqYZHorV-mqsz_pfpnAU5HnpYJp9kY8Osa2c34YHcqBPIEGxY_mXRc58noZV2FPBSqG8zwaSbs8LmH/embed?start=false&loop=false&delayms=3000"
            };

            document.querySelectorAll('.card button').forEach(button => {
                button.addEventListener('click', () => {
                    const course = button.getAttribute('data-course');
                    const iframe = document.querySelector('.presentation iframe');
                    iframe.src = courseLinks[course];
                });
            });

            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const cards = document.querySelectorAll('.card');

            searchButton.addEventListener('click', () => {
                const searchTerm = searchInput.value.toLowerCase();
                cards.forEach(card => {
                    const courseTitle = card.querySelector('h2').textContent.toLowerCase();
                    if (courseTitle.includes(searchTerm)) {
                        card.style.display = 'block';
} else {
card.style.display = 'none';
}
});
});

        // Optional: Add real-time search as user types
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            cards.forEach(card => {
                const courseTitle = card.querySelector('h2').textContent.toLowerCase();
                if (courseTitle.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>



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
                <li id="search">
                    <input type="text" id="searchInput" placeholder="search for courses...">
                    <button id="searchButton">search</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- cards -->
<div class="other-courses">
    <h1>Available courses:</h1>
</div>  

<div class="cards">
    <div class="card">
        <h2>Python</h2>
        <p>Learn Python, the most popular programming language in the world.
            Develop your skills in Python and become a master coder with CODExL.
        </p>
        <button data-course="python">Go to course</button>
    </div>

    <div class="card">
        <h2>Java</h2>
        <p>Master Java, the most versatile programming language.
            Develop your skills in Java and become a master coder with CODExL.
        </p>
        <button data-course="java">Go to course</button>
    </div>

    <div class="card">
        <h2>JavaScript</h2>
        <p>Learn JavaScript, the language of the web world.
            Develop your skills in JavaScript and become a master coder with CODExL.
        </p>
        <button data-course="javascript">Go to course</button>
    </div>

    <div class="card">
        <h2>C++</h2>
        <p>Master C++, the language of systems programming.
            Develop your skills in C++ and become a master coder with CODExL.
        </p>
        <button data-course="c++">Go to course</button>
    </div>
</div>

<!-- main content -->
<div class="main-content">
    <div class="presentation">
        <h1>Presentation</h1>
        <p>here you can take your lecture notes that will be saved into your account</p>
        <!-- embed presentation from google slides -->
        <iframe
            src="https://docs.google.com/presentation/d/e/2PACX-1vQPqu5rOoP4eoiT80NP3n1FgaRYF65OhFaCdu6eMdW2e0QkyrmHPvHZ2Qpw790bihhrhX58BhqnNyXD/embed?start=false&loop=false&delayms=10000"
            frameborder="0" width="700" height="400" allowfullscreen="true" mozallowfullscreen="true"
            webkitallowfullscreen="true">
        </iframe>
    </div>
  
    <!-- notes -->
<div class="notes">
    <h2>Notes</h2>
    <!-- Formular pentru adăugarea de note -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="title" id="title-notes" placeholder="Title...">
        <textarea name="content" id="content" cols="30" rows="11" placeholder="Write your notes here..."></textarea>
        <button type="submit" name="save_note">Save Note</button>
        <button type="button" onclick="resetForm()">New Note</button>
        <a href="./chatbot.php"><button type="button">Get help from CODExLchat</button></a>
    </form>

    <!-- Mesajul de succes -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_note'])) {
        if ($stmt->execute()) {
            echo '<p style="color: green;">Note saved successfully!</p>';
        } else {
            echo '<p style="color: red;">Error: ' . $conn->error . '</p>';
        }
    }
    ?>
</div>

    </div>



</div>

<!-- quiz redirecting area -->
<div class="container-quiz">
    <div class="quiz">
        <h2>Feeling ready?</h2>
        <p>Take a quiz to test your knowledge</p>
        <a href="quiz.php?language=python"><button class="quiz-button">Take Python Quiz</button></a>
        <a href="quiz.php?language=java"><button class="quiz-button">Take Java Quiz</button></a>
        <a href="quiz.php?language=javascript"><button class="quiz-button">Take JavaScript Quiz</button></a>
        <a href="quiz.php?language=cpp"><button class="quiz-button">Take C++ Quiz</button></a>
    </div>
</div>


<!-- footer -->
<div class="footer">
    <!-- contact us  -->
    <div class="contact">
        <h2>Contact Us</h2>
        <p>contact@codexl.us</p>
        <p>@codexl</p>
        <p>CODExLinternational</p>
    </div>
    <p>© 2024 CODExL. All rights reserved.</p>
</div>
</body>
</html>


