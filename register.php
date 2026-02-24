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
        background-color: rgba(255, 255, 255, 0.3);
        color: #000;
        font-weight: bold;
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

    .success-msg {
        background-color: rgba(0, 255, 0, 0.2);
        color: #0f0;
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 15px;
        animation: fadeIn 1s ease-in-out, glow 2s infinite alternate;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes glow {
        from { box-shadow: 0 0 5px #0f0; }
        to { box-shadow: 0 0 15px #0f0, 0 0 30px #0f0; }
    }
</style>

<div class="container">
    <h2>Registration</h2>

    <?php 
    $dept = $_GET['dept'] ?? '';
    if (!$dept) {
        echo "<p style='color:red; text-align:center;'>❌ No department selected!</p>";
        exit;
    }
    ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success-msg">✅ Registered successfully! You can now login.</div>
    <?php endif; ?>

    <form action="process_register.php?dept=<?php echo urlencode($dept); ?>" method="POST">
        <input type="text" name="username" required placeholder="Username">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Password">

        <!-- Role selection -->
        <select name="role" required style="width:100%; padding:12px; margin:10px 0; border-radius:10px;">
            <option value="">-- Select Role --</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Register in <?php echo htmlspecialchars($dept); ?></button>
    </form>

    <div class="links">
        <a href="login.php?dept=<?php echo urlencode($dept); ?>">🔐 Login</a>
        <a href="index.php">🏠 Home</a>
    </div>
</div>
</body>
</html>