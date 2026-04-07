<?php
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";

$input = isset($_POST["url"]) ? $_POST["url"] : "";
$urls = explode("\n", str_replace(["\r\n", ","], "\n", $input));
foreach ($urls as $url) {
    $url = trim($url);
    if ($url === "") {
        continue;
    }
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $options = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36"
            ]
        ];
        $context = stream_context_create($options);
        $data = file_get_contents($url, false, $context);
        if ($data !== false) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($data);
            $titleNodes = $dom->getElementsByTagName("title");
            $title = ($titleNodes->length > 0) ? ($titleNodes->item(0)->textContent) : "";

            $errors = validateBookmark($title, $url);
            if (empty($errors)) {

                $checkStmt = $pdo->prepare("SELECT COUNT(*) AS count FROM bookmarks WHERE url = :url");
                $checkStmt->execute([":url" => $url]);
                $row = $checkStmt->fetch()["count"];

                if ($row === 0) {
                    $insertStmt = $pdo->prepare("INSERT INTO bookmarks (title, url) VALUES (:title, :url)");
                    $insertStmt->execute([
                        ":title" => $title,
                        ":url" => $url
                    ]);
                }
            }
        }
    }    
}
header("Location: /index.php");
exit();