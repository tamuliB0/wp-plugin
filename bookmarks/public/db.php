<?php
$dsn = 'mysql:host=db;dbname=db;charset=utf8mb4';
$username = 'db';
$password = 'db';
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
