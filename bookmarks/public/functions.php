<?php
function validateBookmark($title, $url) {
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