<?php
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/dashboard.php");

$data = [
    $description = trim($_POST["description"] ?? ""),
    $amount = is_numeric($_POST["amount"]) ? $_POST["amount"] : null,
    $newCategory = trim($_POST["new_category"] ?? ""),
    $categoryId = $_POST["category_id"] ?? null,
    $date = $_POST["date"] ?? "",
    $userId = $_SESSION["id"]
];
validateRequiredFields($data, "Missing fields", "/dashboard.php");
$categoryId = getCategoryId($pdo, $newCategory, $categoryId, $userId);
executeQuery(
    $pdo, 
    "INSERT INTO expenses (amount, description, date, category_id, user_id) 
    VALUES (:amount, :description, :date, :category_id, :user_id)",
    array(
        ":amount" => $amount, 
        ":description" => $description, 
        ":date" => $date,
        ":category_id" => $categoryId,
        ":user_id" => $userId  
    )
);
$expenseId = $pdo->lastInsertId();
if (isset($_FILES["uploads"]) && $_FILES["uploads"]["error"] === UPLOAD_ERR_OK) {
    $uploads = $dir . "/uploads/";
    $filename = handleFileUpload($_FILES["uploads"], $uploads);

    executeQuery(
        $pdo,
        "UPDATE expenses SET receipt = :receipt WHERE id = :expense_id",
        array(
            ":receipt" => $filename,
            ":expense_id" => $expenseId 
        )
    );
} 
redirect("/dashboard.php");
