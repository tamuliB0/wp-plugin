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
$userId = $_SESSION["id"];

if ($description === "" || $amount === null || $date === "") {
    setFlash("error", "Fields are missing");
    redirect("/dashboard.php");
}

$categoryId = getCategoryId($pdo, $newCategory, $categoryId, $userId);

$fetchExpStmt = $pdo->prepare("SELECT receipt FROM expenses WHERE id = :id AND user_id = :user_id");
$fetchExpStmt->execute([
    ":id" => $id,
    ":user_id" => $userId,
]);
$expense = $fetchExpStmt->fetch();
if ($expense === false) {
    setFlash("error", "Expense not found");
    redirect("/dashboard.php");
}
$attachment = $expense["receipt"];
$uploads = $dir . "/uploads/";

if (isset($_FILES["uploads"]) && $_FILES["uploads"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $filename = handleFileUpload($_FILES["uploads"], $uploads);
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
    ":user_id" => $userId    
]);
redirect("/dashboard.php");
