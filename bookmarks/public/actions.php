<?php
require __DIR__ . "/db.php";

$action = $_POST["action"] ?? "";
$ids = $_POST["selected_ids"] ?? [];

if ($action === "") {
    header("Location: /index.php");
    exit();
}
if ($action === "favourite") {
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
}

if ($action === "add_tags" && !empty($ids)) {
    $tagIds = $_POST["bulk_tags"] ?? [];

    foreach ($ids as $bookmarkId) {
        $bookmarkId = (int) $bookmarkId;

        foreach ($tagIds as $tagId) {
            $tagId  = (int) $tagId;
            
            $fetchStmt = $pdo->prepare("SELECT COUNT(*) AS count FROM bookmark_tags WHERE bookmark_id = :bookmark_id AND tag_id = :tag_id");
            $fetchStmt->execute([
                ":bookmark_id" => $bookmarkId,
                ":tag_id" => $tagId
            ]);
            $row = $fetchStmt->fetch()["count"];
            
            if ($row === 0) {
                $insertStmt = $pdo->prepare("INSERT INTO bookmark_tags (bookmark_id, tag_id) VALUES (:bookmark_id, :tag_id)");
                $insertStmt->execute([
                    ":bookmark_id" => $bookmarkId,
                    ":tag_id" => $tagId
                ]);
            }}
    }
    header("Location: /index.php");
    exit();
}
if ($action === "delete_single") {
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
    header("Location: /index.php");
    exit();
}
if ($action === "delete" && !empty($ids)) {
    foreach ($ids as $id) {
        $id = (int) $id;
        $deleteStmt = $pdo->prepare("DELETE from bookmarks WHERE id = :id");
        $deleteStmt->execute([":id" => $id]);
    }
    header("Location: /index.php");
    exit();
}