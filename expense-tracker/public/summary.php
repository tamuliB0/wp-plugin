<?php
session_start();
require __DIR__ . "/helpers.php";
require __DIR__ . "/db.php";
requireLogin();
$currentMonth = $_GET["month"] ?? date("Y-m");
$date = new DateTime($currentMonth . "-01");
$startDate = $date->format("Y-m-01");
$endDate = $date->format("Y-m-t");

$prevDate = clone $date;
$prevDate->modify("-1 month");
$prevMonth = $prevDate->format("Y-m");

$nextDate = clone $date;
$nextDate->modify("+1 month");
$nextMonth = $nextDate->format("Y-m");

$sql = "SELECT categories.name AS category, SUM(expenses.amount) AS total FROM expenses
        JOIN categories 
        ON expenses.category_id = categories.id
        WHERE expenses.user_id = :user_id
        AND expenses.date BETWEEN :start AND :end
        GROUP BY expenses.category_id
        ORDER BY total DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":user_id" => $_SESSION["id"],
    ":start" => $startDate,
    ":end" => $endDate
]);
$summary = $stmt->fetchAll();

$totalStmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses 
                WHERE user_id = :user_id AND expenses.date BETWEEN :start AND :end");
$totalStmt->execute([
    ":user_id" => $_SESSION["id"],
    ":start" => $startDate,
    ":end" => $endDate
]);
$total = $totalStmt->fetch()["total"] ?? 0;

$prevStartDate = $prevDate->format("Y-m-01");
$prevEndDate = $prevDate->format("Y-m-t");
$previousTotalStmt = $pdo->prepare("SELECT SUM(amount) AS previous_total FROM expenses 
                WHERE user_id = :user_id AND expenses.date BETWEEN :start AND :end");
$previousTotalStmt->execute([
    ":user_id" => $_SESSION["id"],
    ":start" => $prevStartDate,
    ":end" => $prevEndDate
]);
$previousTotal = $previousTotalStmt->fetch()["previous_total"] ?? 0;

if ($previousTotal <= 0 && $total <= 0) {
    $message = "No expenses in either months";
} elseif ($previousTotal == 0) {
    $message = "";
} elseif ($total == 0) {    
    $message = "No expenses this month";
} else {
    $change = (($total - $previousTotal) / $previousTotal) * 100;
    $formatted = number_format(abs(round($change)),0);

    $message = $change < 0 ? "spent " . $formatted . "% less than previous month" : "spent " . $formatted . "% more than previous month";
}
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
        .a { display:flex; justify-content:flex-end;margin-top: 10px;}
    </style>
</head>
<body>
    <h1>Expense Summary</h1>
    <a href="/dashboard.php" >Go back</a>

    <p><strong>Total spent:</strong> <?= "$" . number_format($total, 2) ?></p>
    <?php if (isset($message)) : ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif;?>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total spent</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($summary)) : ?>
            <tr>
                <td>No expense for this month</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($summary as $data) : ?>
            <tr>
                <td><?= htmlspecialchars($data["category"]) ?></td>
                <td><?= "$" . number_format($data["total"], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="GET">
        <label>Select month:</label>
        <input type="month" name="month" value="<?= htmlspecialchars($currentMonth) ?>">
        <button type="submit">Go</button>
    </form>
    <a href="?month=<?=$prevMonth?>" class="a">Previous month</a>
    <a href="?month=<?=$nextMonth?>" class="a">Next month</a>
</body>
</html>