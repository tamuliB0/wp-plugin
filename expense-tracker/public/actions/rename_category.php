<?php 
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/categories.php");
}
if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/categories.php");
}
$id = (int) $_POST["id"];
$name = trim($_POST["name"] ?? "");
if ($name === "") {
    setFlash("error", "Category name cannot be empty");
    redirect("/categories.php");
}

$stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ":name" => $name,
    ":id" => $id,
    ":user_id" => $_SESSION["id"]
]);

setFlash("success", "$name renamed");
redirect("/categories.php");