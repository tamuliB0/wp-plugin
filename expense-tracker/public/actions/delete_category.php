<?php 
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/categories.php");
}
$id = $_POST["id"] ?? null;
$sessionId = $_SESSION["id"];

if (!isset($_POST["id"]) || !ctype_digit($id)) {
    redirect("/categories.php");
}

$stmt = $pdo->prepare("SELECT id FROM categories WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ":id" => $id,
    ":user_id" => $sessionId
]);
$category = $stmt->fetch();
if ($category === false) {
    setFlash("error", "Category not found");
    redirect("/categories.php");
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM expenses WHERE category_id = :id");
$stmt->execute([":id" => $id]);
$count = $stmt->fetch()["count"];
if ($count > 0) {
    setFlash("error", "Cannot delete category with expenses");
    redirect("/categories.php");
}

$stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ":id" => $id,
    ":user_id" => $sessionId
]);
setFlash("success", "Category deleted");
redirect("/categories.php");