<?php 
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/categories.php");

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/categories.php");
}
$data = [
    $id = (int) $_POST["id"],
    $name = trim($_POST["name"] ?? "")
];
validateRequiredFields($data, "Category name cannot be empty", "/categories.php");
executeQuery(
    $pdo,
    "UPDATE categories SET name = :name WHERE id = :id AND user_id = :user_id",
    array(
        ":name" => $name,
        ":id" => $id,
        ":user_id" => $_SESSION["id"]
    )
);
flashAndRedirect("success", "Category renamed to '$name'", "/categories.php");