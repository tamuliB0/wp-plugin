<?php 
require __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
        header("Location: /index.php");
        exit();
    }
    $id = (int) $_POST["id"];
    $fetchBookmarkStmt = $pdo->prepare("SELECT title, url, notes FROM bookmarks WHERE id = :id");
    $fetchBookmarkStmt->execute([":id" => $id]);
    $bookmark = $fetchBookmarkStmt->fetch();
    if ($bookmark === false) {
        header("Location: /index.php");
        exit();
    }

    $deleteStmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = :id");
    $deleteStmt->execute([":id" => $id]);
}
header("Location: /index.php");
exit();