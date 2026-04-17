<?php
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/dashboard.php");
}
$description = trim($_POST["description"] ?? "");
$amount = is_numeric($_POST["amount"]) ? $_POST["amount"] : null;
$newCategory = trim($_POST["new_category"] ?? "");
$categoryId = $_POST["category_id"] ?? null;
$date = $_POST["date"] ?? "";
$userId = $_SESSION["id"];

if ($description === "" || $amount === null || $date === "") {
    setFlash("error", "Missing fields");
    redirect("/dashboard.php");
}
$categoryId = getCategoryId($pdo, $newCategory, $categoryId, $userId);
 

$insertExStmt = $pdo->prepare("INSERT INTO expenses (amount, description, date, category_id, user_id) 
    VALUES (:amount, :description, :date, :category_id, :user_id)"
);
$insertExStmt->execute([
    ":amount" => $amount, 
    ":description" => $description, 
    ":date" => $date,
    ":category_id" => $categoryId,
    ":user_id" => $userId
]);
$expenseId = $pdo->lastInsertId();

if (isset($_FILES["uploads"]) && $_FILES["uploads"]["error"] === UPLOAD_ERR_OK) {
    $uploads = $dir . "/uploads/";
    $filename = handleFileUpload($_FILES["uploads"], $uploads);

    $fileStmt = $pdo->prepare("UPDATE expenses SET receipt = :receipt WHERE id = :expense_id");
    $fileStmt->execute([
        ":receipt" => $filename,
        ":expense_id" => $expenseId
    ]);

} 
redirect("/dashboard.php");


