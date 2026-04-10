<?php
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/dashboard.php");
}

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/dashboard.php");
}

$id = (int) $_POST['id'];

$fetchExpStmt = $pdo->prepare("SELECT amount, description, date FROM expenses WHERE id = :id AND user_id = :user_id");
$fetchExpStmt->execute([
    ":id" => $id,
    ":user_id" => $_SESSION["id"]
]);
$expense = $fetchExpStmt->fetch();
if ($expense === false) {
    setFlash("error", "Expense not found");
    redirect("/dashboard.php");
}

$deleteStmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id AND user_id = :user_id");
$deleteStmt->execute([
    ":id" => $id,
    ":user_id" => $_SESSION["id"]
]);
setFlash("success", "Expense deleted successfully");
redirect("/dashboard.php");