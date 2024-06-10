<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CODExL Chatbot</title>
    <link rel="stylesheet" href="../css/chatbot.css">
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
<div class="main-content">
    <h1>Chat with CODExL Bot</h1>
    <div class="container">
        <div id="chatbox">
            <div id="chat-content"></div>
        </div>
        <input type="text" id="user-input" placeholder="Type your message...">
        <button onclick="sendMessage()">Send</button>
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

<script>
    function sendMessage() {
        const userInput = document.getElementById('user-input').value;
        if (!userInput) {
            console.error('No input provided');
            return;
        }
        fetch(`http://127.0.0.1:5000/chat?message=${encodeURIComponent(userInput)}`, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }
})

        .then(response => response.json())
        .then(data => {
            const chatContent = document.getElementById('chat-content');
            const userMessage = document.createElement('div');
            userMessage.textContent = 'You: ' + userInput;
            const botMessage = document.createElement('div');
            botMessage.textContent = 'Bot: ' + data.response;
            chatContent.appendChild(userMessage);
            chatContent.appendChild(botMessage);
        })
        .catch(error => console.error('Error:', error));
    }
</script>

</body>
</html>
