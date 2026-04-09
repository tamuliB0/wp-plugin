<?php
session_start();
require __DIR__ . "/helpers.php";
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    <p> Welcome, <?= htmlspecialchars($_SESSION["user"] ?? "") ?> </p>
    <a href="/logout.php">Logout</a>
</body>
</html>