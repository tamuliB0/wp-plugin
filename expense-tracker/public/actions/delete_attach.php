<?php 
require dirname(__DIR__) . "/bootstrap.php";
requirePost("/details.php");

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/dashboard.php");
}
$id = (int) $_POST["id"];
$userId = $_SESSION["id"];
$row = fetchOrFail(
    $pdo,
    "SELECT receipt from expenses WHERE id = :id AND user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $userId
    ),
    "error",
    "No expense found",
    "/dashboard.php"
);
$receipt = $row["receipt"];

if (empty($receipt)) {
    flashAndRedirect("error", "No attachment found", "/details.php?id=". $id);
} else {
    $file = $dir . "/uploads/" . $receipt;
    if (file_exists($file)) {
        unlink($file);
    }
    executeQuery(
        $pdo,
        "UPDATE expenses SET receipt = NULL WHERE id = :id AND user_id = :user_id",
        array(
            ":id" => $id,
            ":user_id" => $userId
        )
    );
    redirect("/details.php?id=". $id);
}
