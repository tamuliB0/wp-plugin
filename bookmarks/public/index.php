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
        // insert new bookmark 
        $insertBookmarkStmt = $pdo->prepare("INSERT INTO bookmarks (title, url, notes) VALUES (:title, :url, :notes)");
        $insertBookmarkStmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes
        ]);
        $bookmarkId = $pdo->lastInsertId();
        $selectedTags = $_POST["tags"] ?? [];
        $newTag = trim($_POST["new_tag"] ?? "");

        // create new tag if provided and add to selected tags
        if ($newTag !== "") {
            $selectedTags[] = findOrCreateTag($pdo, $newTag);
        }
        // link tags to bookmarks
        saveBookmarkTags($pdo, $bookmarkId, $selectedTags);
        header("Location: /index.php");
        exit();
    }
}
// sorting
$allowedSort = [
    "title" => "bookmarks.title",
    "date"=> "bookmarks.created_at" 
];
$sortKey = trim($_GET["sort"] ?? "date");
$sortColumn = $allowedSort[$sortKey] ?? $allowedSort["date"];
$dir = trim($_GET["dir"] ?? "");
$dir = in_array($dir, ["desc", "asc"]) ? $dir : "desc";
//pagination 
$page = max(1, (int) ($_GET["page"] ?? 1));
$perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));
$offset = ($page - 1) * $perPage;
//filters
$search = trim($_GET["search"] ?? "");  
$tagFilter = trim($_GET["tag"] ?? "");

$conditions = [];
$params = [];
if ($tagFilter !== "") {
    $conditions[] = "tags.name = :tag";
    $params[":tag"] = $tagFilter;
}
if ($search !== "") {
    $conditions[] = "bookmarks.title LIKE :search";
    $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $search);
    $params[":search"] = "%$escapedSearch%";
}
//base query
$sql = " FROM bookmarks
        LEFT JOIN bookmark_tags
        ON bookmarks.id = bookmark_tags.bookmark_id
        LEFT JOIN tags
        ON bookmark_tags.tag_id = tags.id ";

$whereClause = "";
if (!empty($conditions)) {
    $whereClause = " WHERE " . implode(" AND ", $conditions);
}
// query to count total items for pagination 
$sqlCount = "SELECT COUNT(DISTINCT bookmarks.id) AS bookmark_count " . $sql . $whereClause;
$countStmt = $pdo->prepare($sqlCount);
$countStmt->execute($params);
$total = $countStmt->fetch()["bookmark_count"];

$totalPages = (int) ceil($total / $perPage);
//query to display paginated bookmarks
$mainSql = "SELECT bookmarks.*, 
        GROUP_CONCAT(tags.name ORDER BY tags.name SEPARATOR ', ') AS tag_names " . $sql . $whereClause . 
        " GROUP BY bookmarks.id
          ORDER BY favourite DESC, $sortColumn $dir
          LIMIT :limit OFFSET :offset";
$listStmt = $pdo->prepare($mainSql);
foreach ($params as $key => $value) {
    $listStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$listStmt->bindValue(":limit", $perPage, PDO::PARAM_INT);
$listStmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$listStmt->execute();
$bookmarks = $listStmt->fetchAll();
//query to fetch all tags 
$tagsStmt = $pdo->query("SELECT * FROM tags");
$tags = $tagsStmt->fetchAll();
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
        .pagination { display: flex; justify-content: flex-end; gap: 10px; margin-top: 2px; }
        .tag-filter {display:flex; gap:5px}
        .bar {display:flex; justify-content:space-between}
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
    <p>Import bookmarks:</p>
    <form method="POST" action="import.php" style="display:inline" id="import-url">
        <textarea name="url" placeholder="https://example.com"></textarea>
        <input type="submit" value="Import" form="import-url">
    </form>
    <h2>Bookmarks</h2>
    <p><strong>Filter by tag:</strong></p>
    <div class="bar">
        <div class="tag-filter">
            <a href="index.php">All Tags</a>
            <?php foreach ($tags as $tag) : ?>
                <a href="index.php?tag=<?= htmlspecialchars($tag["name"]) ?>"><?= htmlspecialchars($tag["name"]) ?></a>
            <?php endforeach; ?>
        </div>
        <form method="GET">
            <input type="hidden" name="tag" value="<?= htmlspecialchars($tagFilter)?>">
            <input type="text" name="search" value="<?= htmlspecialchars($search)?>" placeholder="find bookmark">
            <input type="submit" value="Search">
        <label>
            Sort by:
            <select name="sort">
                <option value="date" <?= $sortKey === "date" ? "selected" : ""?>>Date</option>
                <option value="title" <?= $sortKey === "title" ? "selected" : ""?>>Title</option>
            </select>

            <select name="dir">
                <option value="asc" <?= $dir === "asc" ? "selected" : ""?>>Ascending</option>
                <option value="desc" <?= $dir === "desc" ? "selected" : ""?>>Descending</option>
            </select>
        </label>
        <input type="submit" value="Sort">
        </form> 
    </div>
        <table>
        <thead>
            <tr>
                <th></th>
                <th>FAV</th>
                <th>TITLE</th>
                <th>URL</th>
                <th>NOTES</th>
                <th>TAGS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookmarks as $bookmark) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected_ids[]" value="<?= htmlspecialchars($bookmark["id"])?>" form="bulk_form">
                    </td>
                    <td>
                        <form method="POST" action="actions.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($bookmark["id"]) ?>">
                            <input type="hidden" name="action" value="favourite">
                            <button type="submit"><?=$bookmark["favourite"] ? "&starf;" : "&star;"?></button>
                        </form>
                    </td>
                    <td><?= htmlspecialchars($bookmark["title"]?? "")?></td>
                    <td>
                        <a href="<?= htmlspecialchars($bookmark["url"]) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($bookmark["url"]?? "")?></a>
                    </td>
                    <td><?= htmlspecialchars($bookmark["notes"]?? "") ?></td>
                    <td><?= htmlspecialchars($bookmark["tag_names"]?? "") ?></td>
                    <td><a href="edit.php?id=<?= htmlspecialchars($bookmark["id"]) ?>">Edit</a></td>
                    <td>
                        <form method="POST" action="actions.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($bookmark["id"]) ?>">
                            <input type="hidden" name="action" value="delete_single">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST" action="actions.php" id="bulk_form">
        <div>
            <p>Add tags to selected bookmarks:</p>
            <?php foreach ($tags as $tag) : ?>
                <label>
                    <input type="checkbox" name="bulk_tags[]" value="<?= $tag["id"] ?>"><?= htmlspecialchars($tag["name"]) ?>
                </label>
            <?php endforeach; ?>
        </div>
        <select name="action">
            <option value="">Bulk Action</option>
            <option value="delete">Delete</option>
            <option value="add_tags">Add Tags</option>
        </select>
        <input type="submit" value="Apply">
    </form>
    <div class="pagination">
    <?php if ($totalPages > 1) : ?>
        <?php if ($page > 1) : ?>
            <a href="?page=<?=$page - 1?>&per_page=<?=htmlspecialchars($perPage)?>&search=<?=htmlspecialchars($search)?>&tag=<?=htmlspecialchars($tagFilter)?>&sort=<?=htmlspecialchars($sortKey)?>&dir=<?=htmlspecialchars($dir)?>">Previous</a>
         <?php endif; ?>

        <?php if ($page < $totalPages) : ?>
            <a href="?page=<?=$page + 1?>&per_page=<?=htmlspecialchars($perPage)?>&search=<?=htmlspecialchars($search)?>&tag=<?=htmlspecialchars($tagFilter)?>&sort=<?=htmlspecialchars($sortKey)?>&dir=<?=htmlspecialchars($dir)?>">Next</a>
        <?php endif; ?>
    <?php endif;  ?>
    </div>
</body>
</html>