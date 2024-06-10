<?php
// Conectarea la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codexl_user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("The connection was not succesful: " . $conn->connect_error);
}
?>
