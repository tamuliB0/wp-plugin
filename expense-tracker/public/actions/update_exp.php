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

$fetchExpStmt = $pdo->prepare("SELECT receipt FROM expenses WHERE id = :id AND user_id = :user_id");
$fetchExpStmt->execute([
    ":id" => $id,
    ":user_id" => $sessionId,
]);
$expense = $fetchExpStmt->fetch();
if ($expense === false) {
    setFlash("error", "Expense not found");
    redirect("/dashboard.php");
}

$attachment = $expense["receipt"];

if (isset($_FILES["uploads"]) && $_FILES["uploads"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $allowedType = ["image/jpeg", "image/png", "application/pdf"];
    $maxSize = 2 * 1024 * 1024;
    
    if ($_FILES["uploads"]["error"] !== UPLOAD_ERR_OK) {
        setFlash("error", "File upload failed");
        redirect("/dashboard.php");
    }
    if ($_FILES["uploads"]["size"] > $maxSize) {
        setFlash("error", "File size exceeded max limit");
        redirect("/dashboard.php");
    } 
    if (!in_array($_FILES["uploads"]["type"], $allowedType)) {
        setFlash("error", "File type not supported");
        redirect("/dashboard.php");
    }

    $uploads = $dir . "/uploads/";
    $filename = $_FILES["uploads"]["name"];

    if (!move_uploaded_file($_FILES["uploads"]["tmp_name"], $uploads . $filename)) {
        setFlash("error", "Failed to save file");
        redirect("/dashboard.php");
    }
    $attachment = $filename;

} 
$updateStmt = $pdo->prepare("UPDATE expenses 
            SET description = :description, amount = :amount, date = :date, 
            category_id = :category_id, receipt = :receipt
            WHERE id = :id AND user_id = :user_id");

$updateStmt->execute([
    ":description" => $description,
    ":amount" => $amount,
    ":date" => $date,
    ":category_id" => $categoryId,
    ":receipt" => $attachment,
    ":id" => $id,
    ":user_id" => $sessionId    
]);
redirect("/dashboard.php");
