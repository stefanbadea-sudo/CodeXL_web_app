// Datele pentru grafic
const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
const data = {
    labels: labels,
    datasets: [{
        label: 'User Progress',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        data: [0, 10, 5, 2, 20, 30, 45],
    }]
};

// Configurația graficului
const config = {
    type: 'line',
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
