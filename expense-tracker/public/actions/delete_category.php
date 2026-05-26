<?php 
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/categories.php");

$id = $_POST["id"] ?? null;
$userId = $_SESSION["id"];
if (!isset($_POST["id"]) || !ctype_digit($id)) {
    redirect("/categories.php");
}
fetchOrFail(
    $pdo,
    "SELECT id FROM categories WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $userId,
    ),
    "error", 
    "Category not found", 
    "/categories.php"
);
$stmt = executeQuery(
    $pdo,
    "SELECT COUNT(*) AS count FROM expenses WHERE category_id = :id",
    array(
        ":id" => $id
    )
);
$count = $stmt->fetch()["count"];
if ($count > 0) {
    flashAndRedirect("error", "Cannot delete category with expenses", "/categories.php");
}
executeQuery(
    $pdo,
    "DELETE FROM categories WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $userId
    )
);
flashAndRedirect("success", "Category deleted", "/categories.php");
