<?php
$dept = $_GET['dept'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($dept); ?> Department</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .box {
            background: rgba(255,255,255,0.15);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            color: #fff;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }
        h2 {
            margin-bottom: 15px;
            text-shadow: 0 0 8px rgba(255,255,255,0.6);
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .buttons a button {
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 30px;
            color: #fff;
            background: linear-gradient(135deg, #667eea, #764ba2);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .buttons a button:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #89f7fe, #66a6ff);
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Welcome to <?php echo htmlspecialchars($dept); ?> Department</h2>
        <p>Please choose an option:</p>
        <div class="buttons">
            <a href="login.php?dept=<?php echo urlencode($dept); ?>"><button>Login</button></a>
            <a href="register.php?dept=<?php echo urlencode($dept); ?>"><button>Register</button></a>
        </div>
    </div>
</body>
</html>
