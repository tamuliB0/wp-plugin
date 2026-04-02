<?php
function validateBookmark(string $title, string $url): array 
{
    $errors = [];
    if ($title === "") {
        $errors[] = "title is required";
    }
    if ($url === "") {
        $errors[] = "url is required";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid url";
    }
    return $errors;
}

function saveBookmarkTags(PDO $pdo, int $bookmarkId, array $tagIds): void
{
    $deleteStmt = $pdo->prepare("DELETE FROM bookmark_tags WHERE bookmark_id = :id");
    $deleteStmt->execute([":id" => $bookmarkId]);

    if(empty($tagIds)) {
        return;
    }
    $insertStmt = $pdo->prepare("INSERT INTO bookmark_tags (bookmark_id, tag_id) VALUES (:bookmark_id, :tag_id)");
    foreach ($tagIds as $tagId) {
        $insertStmt->execute([
            ":bookmark_id" => $bookmarkId,
            ":tag_id" => $tagId
        ]);
    }
}

function findOrCreateTag(PDO $pdo, string $newTag): int
{
    $findTagStmt = $pdo->prepare("SELECT id FROM tags WHERE name = :name");
    $findTagStmt->execute([":name" => $newTag]);
    $existingTag = $findTagStmt->fetch();

    if ($existingTag === false) {
        $newTagStmt = $pdo->prepare("INSERT INTO tags (name) VALUES (:name)");
        $newTagStmt->execute([":name" => $newTag]);
        $newTagId = (int) $pdo->lastInsertId();                
    } else {
        $newTagId = (int) $existingTag["id"];
    }
    return $newTagId;
}