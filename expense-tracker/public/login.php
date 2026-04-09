<?php 
session_start();
require __DIR__ . "/db.php";
require __DIR__ . "/helpers.php";

if (isLoggedIn()) {
    redirect("/dashboard.php");
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedUsername = trim($_POST["username"] ?? "");
    $submittedPassword = trim($_POST["password"] ?? "");

    if ($submittedUsername === "" || $submittedPassword === "") {
        setFlash("error", "All fields are required");
        redirect("/login.php");
    }
    $checkStmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
    $checkStmt->execute([":username" => $submittedUsername]);
    $user = $checkStmt->fetch();

    if ($user === false || !password_verify($submittedPassword, $user["password"])) {
        setFlash("error", "Invalid email or password");
        redirect("/login.php");
    }
    $_SESSION["id"] = $user["id"];
    $_SESSION["user"] = $user["username"];
    redirect("/dashboard.php");
}
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .main {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 300px;
        }

        .main h2 {
            color: #4caaaf;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 15px;
            border-radius: 10px;
            border: none;
            background-color: #4caaaf;
            color: white;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .flash { margin-bottom: 10px; }
        .flash.error { background-color:  #f8d7da; color: #f43838}
        .flash.success { background-color:  #b5d0b8; color: #13521f}
    </style>
</head>
<body>
    <div class="main">
        <h2>Login</h2>
        <?php if ($flash) : ?>
            <div class="flash <?= htmlspecialchars($flash["type"])?>">
                <?= htmlspecialchars($flash["message"])?>
            </div>
        <?php endif ; ?>

        <form action="login.php" method="POST">
            <label>Username:</label>
            <input type="text" id="username" name="username" />

            <label for="password">Password:</label>
            <input type="password" id="password" name="password"/>

            <button type="submit">Login</button>
            <p>
                Don't have an account? <a href="/index.php">Register</a>
            </p>
        </form>
    </div>
</body>
</html>