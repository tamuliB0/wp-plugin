<?php 
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $url = trim($_POST["url"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    $errors = validateBookmark($title, $url);

    if (empty($errors)) {
        $Insertstmt = $pdo->prepare("INSERT INTO bookmarks (title, url, notes) VALUES (:title, :url, :notes)");
        $Insertstmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes
        ]);
        $bookmarkId = $pdo->lastInsertId();
        $selectedTag = $_POST["tags"] ?? [];
        $newTag = trim($_POST["new_tag"] ?? "");

        if ($newTag !== "") {
            $stmt  = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->execute([$newTag]);
            $existingTag = $stmt->fetch();

            if ($existingTag === false) {
                $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                $stmt->execute([$newTag]);
                $newTagId = $pdo->lastInsertId();                
            } else {
                $newTagId = $existingTag["id"];
            }
            $selectedTag[] = $newTagId;
        }
        $Tagstmt = $pdo->prepare("INSERT INTO bookmark_tags (bookmark_id, tag_id) VALUES (?, ?)");
        foreach ($selectedTag as $tagId) {
            $Tagstmt->execute([$bookmarkId, $tagId]);
        }
        header("Location: /index.php");
        exit();
    }
}
$tagFilter = $_GET["tags"] ?? "";
if ($tagFilter !== "") {
    $stmt = $pdo->prepare("SELECT bookmarks.*, 
        GROUP_CONCAT(tags.name ORDER BY tags.name SEPARATOR ', ') AS tag_name
        FROM bookmarks
        LEFT JOIN bookmark_tags
        ON bookmarks.id = bookmark_tags.bookmark_id
        LEFT JOIN tags
        ON bookmark_tags.tag_id = tags.id
        WHERE tags.name = ?
        GROUP BY bookmarks.id 
        ORDER BY bookmarks.created_at DESC");
    $stmt->execute([$tagFilter]);
} else {
    $stmt = $pdo->query("SELECT bookmarks.*, 
        GROUP_CONCAT(tags.name ORDER BY tags.name SEPARATOR ', ') AS tag_name
        FROM bookmarks
        LEFT JOIN bookmark_tags
        ON bookmarks.id = bookmark_tags.bookmark_id
        LEFT JOIN tags
        ON bookmark_tags.tag_id = tags.id
        GROUP BY bookmarks.id 
        ORDER BY bookmarks.created_at DESC");
}
$bookmarks = $stmt->fetchAll();

$stmt= $pdo->query("SELECT * FROM tags");
$tags = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarks</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 2rem; }
        form { margin-bottom: 10px; }
        h1   { font-size: 1.6rem; margin-bottom: 1.25rem; }
        table { width: 100%; border-collapse: collapse;background: #e2e2e2; border-radius: 8px;box-shadow: 0 2px 6px rgba(0,0,0,.1); overflow: hidden; }
        th { text-align: left;padding: .75rem 1rem; font-size: .9rem; }
        td { padding: .65rem 1rem; border-bottom: 1px solid #eee; color: #333; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <h2>Add new bookmark</h2>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="url">Url:</label>
        <input type="text" name="url" id="url" required>

        <label for="notes">Notes:</label>
        <input type="text" name="notes" id="notes">
        <p>Select Tag:</p>
        <?php foreach ($tags as $tag) : ?>
            <label>
                <input type="checkbox" name="tags[]" value="<?= $tag["id"] ?>"><?=htmlspecialchars($tag["name"]) ?>
            </label>
        <?php endforeach; ?>
        <input type="text" name="new_tag" placeholder="enter new tag">
        <input type="submit" value="Add Bookmark">
        
    </form>
    <?php if (!empty($errors)) : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <h3>Bookmarks</h3>

    <p><strong>Filter by tag:</strong></p>
    <a href="index.php">All Tags</a>

    <?php foreach ($tags as $tag) : ?>
        <a href="index.php?tags=<?= htmlspecialchars($tag["name"]) ?>"><?= htmlspecialchars($tag["name"]) ?></a>
    <?php endforeach; ?>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Url</th>
                <th>Notes</th>
                <th>Tags</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookmarks as $bookmark) : ?>
                <tr>
                    <td><?= htmlspecialchars($bookmark["title"]?? "") ?></td>
                    <td><a href="<?= htmlspecialchars($bookmark["url"]) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($bookmark["url"])?></a>
                    </td>
                    <td><?= htmlspecialchars($bookmark["notes"]?? "") ?></td>
                    <td><?= htmlspecialchars($bookmark["tag_name"]?? "") ?></td>
                    <td><a href="edit.php?id=<?= htmlspecialchars($bookmark["id"]) ?>">Edit</a></td>
                    <td><form method="POST" action="delete.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($bookmark["id"]) ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>