<?php include 'includes/header.php'; ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Segoe UI", sans-serif;
        background: linear-gradient(-45deg, #ffafbd, #ffc3a0, #2193b0, #6dd5ed);
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
        color: #fff;
    }

    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .container {
        max-width: 400px;
        margin: 80px auto;
        background: rgba(255, 255, 255, 0.1);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
        position: relative;
        overflow: hidden;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #fff;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: none;
        border-radius: 10px;
        outline: none;
        background-color: rgba(0, 0, 0, 0.2);
        color: #000;
        font-size: 16px;
    }

    input::placeholder {
        color: #555;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border: none;
        border-radius: 10px;
        background-color: #00c6ff;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0072ff;
    }

    .links {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .links a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .links a:hover {
        color: #ffd700;
    }
</style>

<div class="container">
    <?php 
    $dept = $_GET['dept'] ?? '';
    if (!$dept) {
        echo "<p style='color:red; text-align:center;'>❌ No department selected!</p>";
        exit;
    }
    ?>
    
    <h2>Login - <?php echo htmlspecialchars($dept); ?></h2>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red; text-align:center;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form action="process_login.php" method="POST">
    <label>Username:</label>
    <input type="text" name="username" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <!-- hidden department from selection in index.php -->
    <input type="hidden" name="department" value="<?php echo $_GET['dept'] ?? ''; ?>">

    <button type="submit">Login</button>
</form>

    <div class="links">
        <a href="register.php?dept=<?php echo urlencode($dept); ?>">📝 Register</a>
        <a href="index.php">🏠 Home</a>
    </div>
    <div class="footer-link" style="text-align: center; margin-top: 10px;">
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
</div>
</body>
</html>
