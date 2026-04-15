<?php 
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/details.php");
}

if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
    redirect("/dashboard.php");
}
$id = (int) $_POST["id"];
$sessionId = $_SESSION["id"];

$stmt = $pdo->prepare("SELECT receipt from expenses WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ":id" => $id,
    ":user_id" => $sessionId
]);
$row = $stmt->fetch();
if ($row === false) {
    setFlash("error", "No expense found");
    redirect("/dashboard.php");
}
$receipt = $row["receipt"];

if (empty($receipt)) {
    setFlash("error", "No attachment found");
    redirect("/details.php?id=". $id);
} else {
    $file = $dir . "/uploads/" . $receipt;

    if (file_exists($file)) {
        unlink($file);
    }
    $deleteStmt = $pdo->prepare("UPDATE expenses SET receipt = NULL WHERE id = :id AND user_id = :user_id");
    $deleteStmt->execute([
        ":id" => $id,
        ":user_id" => $sessionId
    ]);
    redirect("/details.php?id=". $id);
}

