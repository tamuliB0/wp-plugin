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
$id = (int) $_POST["id"];
$description = trim($_POST["description"] ?? "");
$amount = $_POST["amount"] ?? "";
$date = $_POST["date"] ?? "";
$newCategory = trim($_POST["new_category"] ?? "");
$categoryId = $_POST["category_id"] ?? null;
$sessionId = $_SESSION["id"];

if ($description === "" || !is_numeric($amount) || $amount < 0 || $date === "") {
    setFlash("error", "Fields are missing");
    redirect("/dashboard.php");
}

if ($newCategory !== "") {
    $checkStmt = $pdo->prepare("SELECT id from categories WHERE name = :name AND user_id = :user_id");
    $checkStmt->execute([
        ":name" => $newCategory,
        ":user_id" => $sessionId    
    ]);
    $category = $checkStmt->fetch();
    if ($category === false) {
        $insertStmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (:name, :user_id)");
        $insertStmt->execute([
            ":name" => $newCategory,
            ":user_id" => $sessionId    
        ]);
        $categoryId = (int) $pdo->lastInsertId();
    } else {
        $categoryId = (int) $category["id"];
    }
} elseif (!empty($categoryId)) {
    $checkStmt = $pdo->prepare("SELECT id from categories WHERE id = :id AND user_id = :user_id");
    $checkStmt->execute([
        ":id" => $categoryId,
        ":user_id" => $sessionId    
    ]);
    $category = $checkStmt->fetch();
    if ($category === false) {
        setFlash("error", "Invalid category selected");
        redirect("/dashboard.php");
    } 
}  else {
    setFlash("error", "Please select or add a category");
    redirect("/dashboard.php");
    }

$fetchExpStmt = $pdo->prepare("SELECT id FROM expenses WHERE id = :id AND user_id = :user_id");
$fetchExpStmt->execute([
    ":id" => $id,
    ":user_id" => $sessionId,
]);
$expense = $fetchExpStmt->fetch();
if ($expense === false) {
    setFlash("error", "Expense not found");
    redirect("/dashboard.php");
}
$updateStmt = $pdo->prepare("UPDATE expenses 
            SET description = :description, amount = :amount, date = :date, category_id = :category_id
            WHERE id = :id AND user_id = :user_id");

$updateStmt->execute([
    ":description" => $description,
    ":amount" => $amount,
    ":date" => $date,
    ":category_id" => $categoryId,
    ":id" => $id,
    ":user_id" => $sessionId    
]);
redirect("/dashboard.php");
