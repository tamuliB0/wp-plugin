<?php 
require __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
        header("Location: /index.php");
        exit();
    }
    $id = (int) $_POST["id"];
    $stmt = $pdo->prepare("SELECT title, url, notes FROM bookmarks WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $bookmark = $stmt->fetch();
    if ($bookmark === false) {
        header("Location: /index.php");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = :id");
    $stmt->execute([":id" => $id]);
}
header("Location: /index.php");
exit();