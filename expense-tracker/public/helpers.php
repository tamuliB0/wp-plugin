<?php

function redirect(string $path): void 
{
    header("Location: $path");
    exit();
}

function setFlash(string $type, string $message): void
{
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

function flash(): ?array
{
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $flash;
}

function isLoggedIn(): bool 
{
    return (isset($_SESSION["id"]));
}

function requireLogin(): void 
{
    if (!isLoggedIn()) {
        redirect("/login.php"); 
    }
}
function formatDate(string $date): string
{
    $date = new DateTime($date);
    return $date->format("d-m-Y");
}

function getCategoryId(PDO $pdo, string $newCategory, ?string $categoryId, int $userId): ?int
{
    if ($newCategory !== "") {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE name = :name AND user_id = :user_id");
        $checkStmt->execute([
            ":name" => $newCategory,
            ":user_id" => $userId
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            $insertStmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (:name, :user_id)");
            $insertStmt->execute([
                ":name" => $newCategory,
                ":user_id" => $userId
            ]);
            return (int) $pdo->lastInsertId();
        } else {
            return (int) $category["id"];
            }
    } elseif (!empty($categoryId)) {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([
            ":id" => $categoryId,
            ":user_id" => $userId
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            setFlash("error", "Invalid category selected");
            redirect("/dashboard.php");
        }
        return (int) $category["id"];
    }  else {
        setFlash("error", "Please select or add a category");
        redirect("/dashboard.php");
        }
}

function handleFileUpload(array $file, 
    string $uploads,
    int $maxSize = 2 * 1024 * 1024, 
    array $allowedType = ["image/jpeg", "image/png", "application/pdf"]
): string
{
    if ($file["error"] !== UPLOAD_ERR_OK) {
        setFlash("error", "File upload failed");
        redirect("/dashboard.php");
    }
    if ($file["size"] > $maxSize) {
        setFlash("error", "File size exceeded max limit");
        redirect("/dashboard.php");

    } 
    if (!in_array($file["type"], $allowedType)) {
        setFlash("error", "File type not supported");
        redirect("/dashboard.php");
    }
    $filename = $file["name"];
    if (!move_uploaded_file($file["tmp_name"], $uploads . $filename)) {
        setFlash("error", "Failed to save file");
        redirect("/dashboard.php");
    }
    return $filename;
}