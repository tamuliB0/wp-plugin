<?php
session_start();
require __DIR__ . "/db.php";
require __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/index.php");
}
$submittedUsername = trim($_POST["username"] ?? "");
$submittedEmail = trim($_POST["email"] ?? "");
$submittedPassword = trim($_POST["password"] ?? "");

if (!filter_var($submittedEmail, FILTER_VALIDATE_EMAIL) || strlen($submittedPassword) < 3) {
    setFlash("error", "Invalid email or password");
    redirect("/index.php");
}
$checkStmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE username = :username OR email = :email");
$checkStmt->execute([
    ":username" => $submittedUsername,
    ":email" => $submittedEmail
    ]);
$existingUser = $checkStmt->fetch()["count"];
if ($existingUser > 0) {
    setFlash("error", "Username or email already exists");
    redirect("/index.php");
}

$hash = password_hash($submittedPassword, PASSWORD_DEFAULT);
$insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
$insertStmt->execute([
    ":username" => $submittedUsername,
    ":email" => $submittedEmail, 
    ":password" => $hash 
]);

setFlash("success", "User registered successfully!");
redirect("/index.php");
