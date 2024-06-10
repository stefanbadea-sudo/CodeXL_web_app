<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectarea la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codexl_user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("The connection was not successful: " . $conn->connect_error);
}

// Functie pentru afisarea mesajelor de eroare
function display_error($message) {
    echo "<div style='color: red;'><strong>Error:</strong> $message</div>";
}

// Verificăm dacă utilizatorul a trimis un formular
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificăm dacă toate câmpurile sunt completate
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['username']) && isset($_POST['phone_no']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['agree'])) {
        // Prelucrăm datele introduse de utilizator pentru a preveni SQL injection
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
        $agree = mysqli_real_escape_string($conn, $_POST['agree']);

        // Verificăm dacă parola coincide cu confirmarea parolei
        if ($password != $confirm_password) {
            display_error("Parola și confirmarea parolei nu coincid.");
            exit();
        }

        // Verificăm dacă utilizatorul a fost de acord cu termenii și condițiile
        if ($agree != 'on') {
            display_error("Trebuie să fiți de acord cu termenii și condițiile pentru a vă înregistra.");
            exit();
        }

        // Verificăm dacă adresa de email este deja înregistrată
        $sql_check_email = "SELECT * FROM users WHERE email='$email'";
        $result_email = $conn->query($sql_check_email);
        if ($result_email->num_rows > 0) {
            display_error("Această adresă de email este deja înregistrată.");
            exit();
        }

        // Criptăm parola pentru a o salva în baza de date
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Interogarea pentru a adăuga un nou utilizator în baza de date
        $sql = "INSERT INTO users (first_name, last_name, username, phone_no, email, password) VALUES ('$first_name', '$last_name', '$username', '$phone_no', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            echo "<div style='color: green;'>Utilizatorul a fost înregistrat cu succes!</div>";
            // Aici poți adăuga orice alte acțiuni după înregistrare, cum ar fi redirecționarea către pagina de autentificare
            // header("Location: login.html");
            // exit();
        } else {
            display_error("Eroare: " . $sql . "<br>" . $conn->error);
        }
    } else {
        // Unul dintre câmpuri nu a fost completat, afișăm un mesaj de eroare
        display_error("Toate câmpurile sunt obligatorii.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-Register</title>

    <!-- css -->
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
    <div class="register">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="register_form">
                        <h1>Register</h1>
                        <input type="text"name="first_name" placeholder="First Name" class="form-control mb-3">
                        <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-3">
                        <input type="text" name="username" placeholder="Username" class="form-control mb-3">
                        <input type="tel" name="phone_no" placeholder="Phone Number" class="form-control mb-3">
                        <input type="email" name="email" placeholder="Email" class="form-control mb-3">
                        <input type="password" name="password" placeholder="Password" class="form-control mb-3">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control mb-3">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="agree" name="agree">
                            <label class="form-check-label" for="agree">Agree to terms and conditions</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>
                    <div class="login_account mt-3">
                        <a href="login.php">Already have an account? Log In</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="../images/loginphoto.png" alt="Register Photo" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include '../footer/footer.php'; ?>