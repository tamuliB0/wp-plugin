<?php 
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/dashboard.php");

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/dashboard.php");
}
$data = [
    $id = (int) $_POST["id"],
    $description = trim($_POST["description"] ?? ""),
    $amount = $_POST["amount"] ?? "",
    $date = $_POST["date"] ?? "",
    $newCategory = trim($_POST["new_category"] ?? ""),
    $categoryId = $_POST["category_id"] ?? null,
    $userId = $_SESSION["id"]
];
validateRequiredFields($data, "Missing fields", "/dashboard.php");
$categoryId = getCategoryId($pdo, $newCategory, $categoryId, $userId);
fetchOrFail(
    $pdo,
    "SELECT receipt FROM expenses WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $userId,
    ),
    "error",
    "Expense not found", 
    "/dashboard.php"
);
$filename = $expense["receipt"];
$uploads = $dir . "/uploads/";
if (isset($_FILES["uploads"]) && $_FILES["uploads"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $filename = handleFileUpload($_FILES["uploads"], $uploads);
}
executeQuery(
    $pdo,
    "UPDATE expenses SET description = :description, amount = :amount, date = :date, 
    category_id = :category_id, receipt = :receipt WHERE id = :id AND user_id = :user_id",
    array(
        ":description" => $description,
        ":amount" => $amount,
        ":date" => $date,
        ":category_id" => $categoryId,
        ":receipt" => $filename,
        ":id" => $id,
        ":user_id" => $userId  
    )
);
redirect("/dashboard.php");
