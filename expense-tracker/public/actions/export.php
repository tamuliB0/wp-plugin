<?php
session_start();
$dir = dirname(__DIR__);
require $dir . "/helpers.php";
require $dir . "/db.php";
requireLogin();

$startDate = $_GET["start_date"] ?? "";
$endDate = $_GET["end_date"] ?? "";

if (isset($_GET["action"]) && $_GET["action"] === "export") {
   $downloadStmt = $pdo->prepare("SELECT expenses.amount,
                   expenses.description, expenses.date, categories.name AS category
                   FROM expenses
                   JOIN categories
                   ON expenses.category_id = categories.id
                   WHERE expenses.user_id = :user_id
                   AND expenses.date BETWEEN :start AND :end");
   $downloadStmt->execute([
       ":user_id" => $_SESSION["id"],
       ":start" => $startDate,
       ":end" => $endDate
   ]);
   $data = $downloadStmt->fetchAll();

   header("Content-Type: text/csv");
   header("Content-Disposition: attachment; filename='expenses.csv'");

   $handle = fopen("php://output", "w");
   if ($handle !== false) {
       if (!empty($data)) {
           fputcsv($handle, array_keys($data[0]), ",", '"', "\\");
       }
       foreach ($data as $row) {
           fputcsv($handle, $row, ",", '"', "\\");
       }
       fclose($handle);
       exit();
   }
}