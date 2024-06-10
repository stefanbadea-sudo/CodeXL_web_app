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

// Setăm variabila $is_logged_in
$is_logged_in = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_note'])) {
    // Preia datele din formular
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepare și execută interogarea pentru inserarea unei noi înregistrări în tabela notes
    $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);

    if ($stmt->execute()) {
        // Afișează un mesaj de succes
        $success_message = "Note saved successfully!";
    } else {
        // În caz de eroare, afișează un mesaj de eroare
        $error_message = "Error: " . $conn->error;
    }
}

// Verifică dacă formularul a fost trimis pentru ștergerea unei note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_note'])) {
    // Preia ID-ul notei de șters
    $note_id = $_POST['note_id'];

    // Prepare și execută interogarea pentru ștergerea înregistrării din tabela notes
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        // Afișează un mesaj de succes
        $delete_message = "Note deleted successfully!";
    } else {
        // În caz de eroare, afișează un mesaj de eroare
        $error_message = "Error: " . $conn->error;
    }
}

// Interogare pentru a prelua toate notele utilizatorului curent
$sql = "SELECT * FROM notes WHERE user_id = " . $_SESSION['user_id'];
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL - Notes</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/notes.css">
    <link rel="stylesheet" href="../css/bootstrap.css">

</head>

<script>
        function resetForm() {
            document.getElementById("title-notes").value = "";
            document.getElementById("content").value = "";
        }
    </script>

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
    <!-- main content -->
    <div class="main-content"> 
        <h1 style="color: #fff;">Your Notes</h1>
        <div class="notes-container">
            <!-- Notițele utilizatorului -->
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                   echo "<style>
                   .notes-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                    justify-content: center;
                }
                
                .note-card {
                    background: rgba(255, 255, 255, 0.2);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    border-radius: 15px;
                    padding: 20px;
                    width: 300px;
                    text-align: center;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    backdrop-filter: blur(10px);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                
                .note-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
                }
                
                .note-card h2 {
                    font-size: 1.5em;
                    margin-bottom: 10px;
                    color: #fff;
                }
                
                .note-card p {
                    font-size: 1em;
                    margin-bottom: 20px;
                    color: #fff;
                }         
               
                .note-card button {
                    background-color: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: #5D75F7;
                    padding: 10px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    display: block;
                    margin: 0 auto;
                }

                .note-card a {
                    text-decoration: none;
                    color: #ffffff;
                    font-size: large;
                }

                .note-card button:hover {
                    background-color: #A42FD9;
                    color: #ffffff;
                    transition: color 0.5s;
                    transition: background-color 0.5s;
                }          
                    </style>";

                    echo "<div class='note-card'>";
                    echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='note_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' name='delete_note'>Delete Note</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: #fff;'>No notes found.</p>";
              
            }
            ?>

            <!-- Card pentru "New Note" -->
            <div class="note-card">
                <h2></h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <a href="./courses.php"><button type="button">Add a New Note</button></a>
                </form>

                <!-- Mesajul de succes/eroare -->
                <?php
                if (isset($success_message)) {
                    echo '<p style="color: green;">' . $success_message . '</p>';
                }
                if (isset($delete_message)) {
                    echo '<p style="color: green;">' . $delete_message . '</p>';
                }
                if (isset($error_message)) {
                    echo '<p style="color: red;">' . $error_message . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
 <!-- footer -->
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

