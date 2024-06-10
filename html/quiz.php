<?php
session_start(); // Start sesiunea pentru gestionarea autentificării utilizatorului

// Verifică dacă utilizatorul este autentificat
$is_logged_in = isset($_SESSION['user_id']);

// Funcție pentru a încărca întrebările din baza de date pentru un anumit limbaj
function fetch_questions_from_database($language) {
    // Conectare la baza de date
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "codexl_user_db"; // Actualizăm numele bazei de date

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifică conexiunea la baza de date
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Construiește interogarea pentru a prelua întrebările pentru limbajul dat
    $sql = "SELECT * FROM quiz_questions WHERE language = '$language' ORDER BY RAND() LIMIT 10";
    $result = $conn->query($sql);

    // Initializează un array pentru a stoca întrebările și opțiunile
    $questions = array();

    if ($result->num_rows > 0) {
        // Extrage fiecare rând din rezultatele interogării
        while ($row = $result->fetch_assoc()) {
            // Adaugă întrebarea și opțiunile în array-ul de întrebări
            $question = array(
                'id' => $row['id'],
                'question' => $row['question'],
                'options' => array($row['option1'], $row['option2'], $row['option3'], $row['option4']),
                'correct_option' => $row['correct_option'] // Adăugăm răspunsul corect
            );
            $questions[] = $question;
        }
    }

    // Închide conexiunea la baza de date
    $conn->close();

    // Returnează array-ul de întrebări și opțiuni
    return $questions;
}

// Verifică dacă există un formular trimis pentru a procesa răspunsurile la quiz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_answers'])) {
    // Verifică dacă răspunsurile sunt setate și nu sunt goale
    if (isset($_POST['answers']) && !empty($_POST['answers'])) {
        // Initializează scorul
        $score = 0;

        // Procesează fiecare răspuns trimis
        foreach ($_POST['answers'] as $question_id => $selected_option) {
            // Încarcă răspunsul corect din baza de date
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "codexl_user_db"; // Actualizăm numele bazei de date

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verifică conexiunea la baza de date
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Construiește interogarea pentru a prelua răspunsul corect din baza de date
            $sql = "SELECT correct_option FROM quiz_questions WHERE id = '$question_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $correct_option = $row['correct_option'];

                // Compară răspunsul selectat de utilizator cu răspunsul corect
                if ($selected_option == $correct_option) {
                    $score++;
                }
            }

            // Închide conexiunea la baza de date
            $conn->close();
        }

        // Actualizează scorul utilizatorului în baza de date
        $user_id = $_SESSION['user_id'];
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "UPDATE users SET score = score + $score WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            echo "Your score is: $score";
        } else {
            echo "Error updating score: " . $conn->error;
        }

        $conn->close();
    } else {
        // Afișează un mesaj de eroare dacă nu sunt răspunsuri selectate
        echo "Please select answers for all questions!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-quiz</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/quiz.css">
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
                <li><a href="./notes.php">notes</li>
                <?php if ($is_logged_in): ?>
                    <li><a href="profile.php">signed in as @<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- main content -->
    <div class="quiz-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php
            // Verifică dacă utilizatorul este autentificat
            if ($is_logged_in) {
                // Preia limbajul din URL
                $language = isset($_GET['language']) ? $_GET['language'] : '';

                // Verifică dacă limbajul este valid
                if (!empty($language)) {
                    echo "<h2>" . htmlspecialchars($language) . " Quiz</h2>";
                    // Încarcă întrebările pentru limbajul specificat din baza de date și afișează-le sub forma de întrebări și opțiuni
                    $questions = fetch_questions_from_database($language);
                    if (!empty($questions)) {
                        foreach ($questions as $question) {
                            echo "<div class='question'><h3>" . htmlspecialchars($question['question']) . "</h3></div>";
                            echo "<ul class='options'>";
                            foreach ($question['options'] as $index => $option) {
                                echo "<li><input type='radio' name='answers[" . $question['id'] . "]' value='" . ($index + 1) . "'>" . htmlspecialchars($option) . "</li>";
                            }
                            echo "</ul>";
                        }
                    } else {
                        // Afișează un mesaj dacă nu există întrebări pentru limbajul specificat
                        echo "No questions available for this language!";
                    }
                } else {
                    // Afișează un mesaj dacă limbajul nu este specificat în URL
                    echo "Please select a quiz!";
                }
            } else {
                // Afișează un mesaj dacă utilizatorul nu este autentificat
                echo "Please sign in to take the quiz!";
            }
            ?>
            <!-- Submit button -->
            <input class="submit-button" type="submit" name="submit_answers" value="Submit Answers">
        </form>
    </div>

    <!--footer-->
    <div class="footer">
    <!-- contact us -->
        <div class="contact">
            <h2>contact us</h2>
            <p>contact@codexl.us</p>
            <p>@codexl</p>
            <p>CODExLinternational</p>
        </div>
        <p>© 2024 CODExL. All rights reserved.</p>
    </div>
</body>
</html>
