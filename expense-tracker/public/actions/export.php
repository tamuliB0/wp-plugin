<?php
require dirname(__DIR__) . "/bootstrap.php";

$startDate = $_GET["start_date"] ?? "";
$endDate = $_GET["end_date"] ?? "";

if (isset($_GET["action"]) && $_GET["action"] === "export") {
    $stmt = executeQuery(
        $pdo,
        "SELECT expenses.amount, expenses.description, expenses.date, categories.name AS category 
        FROM expenses
        JOIN categories
        ON expenses.category_id = categories.id
        WHERE expenses.user_id = :user_id
        AND expenses.date BETWEEN :start AND :end",
        array(
            ":user_id" => $_SESSION["id"],
            ":start" => $startDate,
            ":end" => $endDate
        )
    );
   $data = $stmt->fetchAll();

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