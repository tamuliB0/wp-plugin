<?php
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/dashboard.php");

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/dashboard.php");
}
$id = (int) $_POST['id'];
fetchOrFail(
    $pdo, 
    "SELECT amount, description, date FROM expenses WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
    ":user_id" => $_SESSION["id"]
    ),
    "error",
    "Expense not found",
    "/dashboard.php"
);

executeQuery(
    $pdo,
    "DELETE FROM expenses WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $_SESSION["id"]
    )
);
flashAndRedirect("success", "Expense deleted successfully", "/dashboard.php");
