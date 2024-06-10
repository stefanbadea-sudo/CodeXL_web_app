<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL-progress</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/progress.css">
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
        </ul>
    </div>
</div>

<!-- main content -->

<h1>User Progress Chart</h1>

<!-- incud graficul intr un box-->
<div class="box">
    <canvas id="userProgressChart" width="400" height="200"></canvas>
</div>

<!-- footer -->
<div class="footer">
    <!-- contact us  -->
    <div class="contact">
        <h2>contact us</h2>
        <p>contact@codexl.us</p>
        <p>@codexl</p>
        <p>CODExLinternational</p>
    </div>
    <p>© 2024 CODExL. All rights reserved.</p>
</div>

<!-- Script pentru grafic -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datele pentru grafic
    const labels = ['Quiz java', 'Quiz c++', 'Quiz python', 'Quiz javascript']; // Actualizează cu numele quizurilor
    const data = {
        labels: labels,
        datasets: [{
            label: 'User Progress',
            backgroundColor: 'rgba(153, 102, 255, 0.2)', // Culoare mov a barelor
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1,
            data: [8, 15, 12, 10, 18], // Actualizează cu punctajele obținute la fiecare quiz
        }]
    };

    // Configurația graficului
    const config = {
        type: 'bar', // Schimbăm tipul graficului în bar
        data: data,
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Inițializează graficul
    const myChart = new Chart(
        document.getElementById('userProgressChart'),
        config
    );
</script>

</body>
</html>
