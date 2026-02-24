<?php //include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

       body {
    background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    /* Removed overflow: hidden */
    padding-top: 60px; /* Added padding to prevent heading from being cut */
}

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
    position: relative; /* For proper glow positioning */
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 50px 40px;
    margin: 0 auto 60px auto; /* Adjusted margin */
    text-align: center;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    color: #fff;
    max-width: 700px;
    z-index: 1;
}

        .container::before {
            content: '';
            position: absolute;
            top: -30px;
            left: -30px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.3), transparent 70%);
            border-radius: 50%;
            animation: moveLight 8s linear infinite;
            z-index: -1;
        }

        @keyframes moveLight {
            0%   { transform: translate(0, 0); }
            25%  { transform: translate(500px, 0); }
            50%  { transform: translate(500px, 300px); }
            75%  { transform: translate(0, 300px); }
            100% { transform: translate(0, 0); }
        }

        h2 {
    font-size: 36px; /* Slightly larger for visibility */
    margin-bottom: 15px;
    font-weight: 700;
    text-shadow: 0 0 8px rgba(255,255,255,0.6); /* Glowing text */
}

        .subtitle {
            font-size: 16px;
            margin-bottom: 25px;
            color: #fefefe;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        .feature-title {
            font-size: 13px;
            font-weight: bold;
        }

        .buttons {
            margin-top: 35px;
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

        footer {
            margin-top: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
* { box-sizing: border-box; margin: 0; padding: 0; }
        body, html { height: 100%; font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding-top: 60px;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px 40px;
            margin: 0 auto 60px auto;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            color: #fff;
            max-width: 800px;
            z-index: 1;
        }
        h2 {
            font-size: 36px;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 0 0 8px rgba(255,255,255,0.6);
        }
        .subtitle { font-size: 16px; margin-bottom: 25px; color: #fefefe; }
        .departments {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .dept-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .dept-card:hover {
            transform: scale(1.05);
            background: rgba(255,255,255,0.25);
        }
        .dept-title { font-size: 15px; font-weight: bold; }
        footer {
            margin-top: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📅 Leave Management System</h2>
        <p class="subtitle">Simplify your leave process — apply, approve, and track leaves in one place.</p>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">📝</div>
                <div class="feature-title">Easy Leave Application</div>
            </div>
            <div class="feature">
                <div class="feature-icon">✅</div>
                <div class="feature-title">Fast Approvals</div>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <div class="feature-title">Track Leave Balance</div>
            </div>
            <div class="feature">
                <div class="feature-icon">👨‍💼</div>
                <div class="feature-title">Admin Dashboard</div>
            </div>
            <div class="feature">
                <div class="feature-icon">🔒</div>
                <div class="feature-title">Secure Login</div>
            </div>
            <div class="feature">
                <div class="feature-icon">📂</div>
                <div class="feature-title">Leave History</div>
            </div>
            <div class="feature">
                <div class="feature-icon">📅</div>
                <div class="feature-title">Calendar View</div>
            </div>
            <div class="feature">
                <div class="feature-icon">🔔</div>
                <div class="feature-title">Email Notifications</div>
            </div>
            <div class="feature">
                <div class="feature-icon">👥</div>
                <div class="feature-title">Multiple Roles</div>
            </div>
            <div class="feature">
                <div class="feature-icon">📱</div>
                <div class="feature-title">Mobile Friendly</div>
            </div>
            <div class="feature">
                <div class="feature-icon">🌐</div>
                <div class="feature-title">Web Based Access</div>
            </div>
            <div class="feature">
                <div class="feature-icon">💼</div>
                <div class="feature-title">Department-wise Reports</div>
            </div>
        </div>

        <p class="subtitle">Choose your department to continue:</p>

        <!-- Department Selection -->
        <div class="departments">
            <a href="department.php?dept=CSE"><div class="dept-card"><div class="dept-title">CSE - Computer Science & Engineering</div></div></a>
            <a href="department.php?dept=ECE"><div class="dept-card"><div class="dept-title">ECE - Electronics & Communication Engineering</div></div></a>
            <a href="department.php?dept=EEE"><div class="dept-card"><div class="dept-title">EEE - Electrical & Electronics Engineering</div></div></a>
            <a href="department.php?dept=MECH"><div class="dept-card"><div class="dept-title">MECH - Mechanical Engineering</div></div></a>
            <a href="department.php?dept=CIVIL"><div class="dept-card"><div class="dept-title">CIVIL - Civil Engineering</div></div></a>
            <a href="department.php?dept=CHEMICAL"><div class="dept-card"><div class="dept-title">CHEMICAL - Chemical Engineering</div></div></a>
        </div>
        <footer>
            &copy; <?php echo date("Y"); ?> Leave Management System. All rights reserved.
        </footer>
    </div>
</body>
</html>
