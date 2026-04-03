<?php
require __DIR__ . "/db.php";

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    header("Location: /index.php");
    exit();
}
$id = (int) $_POST["id"];

$fetchStmt = $pdo->prepare("SELECT favourite FROM bookmarks WHERE id = :id");
$fetchStmt->execute([":id" => $id]);
$row = $fetchStmt->fetch();

$currentStatus = (int) $row["favourite"];
$status = ($currentStatus === 1) ? 0 : 1;

$updateStmt = $pdo->prepare("UPDATE bookmarks SET favourite = :status WHERE id = :id");
$updateStmt->execute([":status" => $status, ":id" => $id]);
header("Location: /index.php");
exit();