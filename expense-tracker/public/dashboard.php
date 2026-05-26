<?php
require __DIR__ . "/bootstrap.php";

$selectedCategory = $_GET["category"] ?? "";
$startDate = $_GET["start_date"] ?? "";
$endDate = $_GET["end_date"] ?? "";

$conditions = [];
$params = [];
$baseSql = "SELECT expenses.*, categories.name AS category 
        FROM expenses
        JOIN categories
        ON expenses.category_id = categories.id";

$conditions[] = "expenses.user_id = :user_id";
$params = [":user_id" => $_SESSION["id"]];

if ($selectedCategory !== "") {
    $conditions[] = "expenses.category_id = :category_id";
    $params[":category_id"] = $selectedCategory;
}
if ($startDate !== "" && $endDate !== "") {
    $conditions[] = "expenses.date BETWEEN :start_date AND :end_date";
    $params[":start_date"] = $startDate;
    $params[":end_date"] = $endDate;
} elseif ($startDate !== "") {
    $conditions[] = "expenses.date >= :start_date";
    $params[":start_date"] = $startDate;    
} elseif ($endDate !== "") {
    $conditions[] = "expenses.date <= :end_date";
    $params[":end_date"] = $endDate;
}

if (!empty($conditions)) {
    $baseSql .= " WHERE " . implode(" AND ", $conditions);
}

$baseSql .= " ORDER BY expenses.date DESC";
$listStmt = $pdo->prepare($baseSql);
$listStmt->execute($params);
$expenses = $listStmt->fetchAll();

$fetchCatergoryStmt = executeQuery(
    $pdo,
    "SELECT id, name FROM categories WHERE user_id = :user_id",
    array(
       ":user_id" => $_SESSION["id"] 
    )
);
$categories = $fetchCatergoryStmt->fetchAll();
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 2rem; }
        form { margin-top: 10px;margin-bottom: 10px;}
        h1   { font-size: 1.6rem; margin-bottom: 1.25rem; }
        table { width: 100%; border-collapse: collapse;background: #e2e2e2; border-radius: 8px;box-shadow: 0 2px 6px rgba(0,0,0,.1); overflow: hidden; }
        th { text-align: left;padding: .75rem 1rem; font-size: .9rem; }
        td { padding: .65rem 1rem; border-bottom: 1px solid #eee; color: #333; }
        tr:last-child td { border-bottom: none; }
        .flash { margin-bottom: 10px; }
        .flash.error { background-color:  #f2f0f0; color: #f43838}
        .flash.success { background-color:  #b5d0b8; color: #13521f}
        .a { display:flex; justify-content:flex-end; margin-top: 10px;margin-bottom: 10px;}
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <p> Welcome,<strong><?= htmlspecialchars($_SESSION["user"] ?? "") ?></strong> </p>
    <a href="/logout.php">Logout</a>
    <a href="budget.php">Create a budget</a>
    <h3>Add new expense</h3>
    <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
        </div>
    <?php endif; ?>
    <form method="POST" action="/actions/add_exp.php" enctype="multipart/form-data">
        <input type="text" name="description" placeholder="Expense name">
        <input type="number" name="amount" placeholder="amount">
        <input type="text" name="new_category" placeholder="add new category">
        <input type="date" name="date" placeholder="dd-mm-yyyy">
        <select name="category_id">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= htmlspecialchars($category["id"]) ?>">
                    <?= htmlspecialchars($category["name"]) ?>
                </option>
            <?php endforeach; ?>
        </select>        
        <label>Attachment:</label>
        <input type="file" name="uploads">
        <button type="submit">Add Expense</button>
    </form>
    <a href="/summary.php" class="a">View summary</a>
    <a href="/categories.php" class="a">Manage Categories</a>
    <table>
        <thead>
            <tr>
                <th>Expense name</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
                <th>View</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense) : ?>
            <tr>
                <td><?= htmlspecialchars($expense["description"]) ?></td>
                <td><?= "$" . number_format($expense["amount"], 2) ?></td>
                <td><?= htmlspecialchars($expense["category"]) ?></td>
                <td>
                    <?= htmlspecialchars(formatDate($expense["date"])) ?>
                    <?php if ((int) $expense["is_recurring"] === 1) : ?>
                        (Monthly)
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/details.php?id=<?= htmlspecialchars($expense["id"])?>">View details</a>
                </td>
                <td>
                    <a href="/edit.php?id=<?= htmlspecialchars($expense["id"]) ?>">Edit</a>
                    <form method="POST" action="/actions/delete_exp.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($expense["id"])?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="GET">
        <select name="category">
            <option value="">Filter by:</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= htmlspecialchars($category["id"]) ?>"
                <?= $selectedCategory == $category["id"] ? "selected" : ""?>>
                    <?= htmlspecialchars($category["name"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?? "") ?>">
        <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?? "") ?>">
        <button type="submit" name="action" value="filter">Filter</button>
    </form>
    <form method="GET" action="/actions/export.php">
        <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?? "") ?>">
        <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?? "") ?>">        
        <button type="submit" name="action" value="export">Export</button>
    </form>
</body>
</html>