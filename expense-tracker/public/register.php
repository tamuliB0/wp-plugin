<?php
session_start();
require __DIR__ . "/db.php";
require __DIR__ . "/helpers.php";

requirePost("/index.php");
$submittedUsername = trim($_POST["username"] ?? "");
$submittedEmail = trim($_POST["email"] ?? "");
$submittedPassword = trim($_POST["password"] ?? "");

if (!filter_var($submittedEmail, FILTER_VALIDATE_EMAIL) || strlen($submittedPassword) < 3 || $submittedUsername === "") {
    flashAndRedirect("error", "Invalid email or password", "/index.php");
}
$checkStmt = executeQuery(
    $pdo,
    "SELECT COUNT(*) AS count FROM users WHERE username = :username OR email = :email",
    array(
        ":username" => $submittedUsername,
        ":email" => $submittedEmail
    )
);
$existingUser = $checkStmt->fetch()["count"];
if ($existingUser > 0) {
    flashAndRedirect("error", "Username or email already exists", "/index.php");
}
$hash = password_hash($submittedPassword, PASSWORD_DEFAULT);
executeQuery(
    $pdo,
    "INSERT INTO users (username, email, password) 
    VALUES (:username, :email, :password)",
    array(
        ":username" => $submittedUsername,
        ":email" => $submittedEmail, 
        ":password" => $hash 
    )
);
flashAndRedirect("success", "User registered successfully!", "/index.php");
