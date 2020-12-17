<?php

function env(string $name, $fallback = null) {
    $value = $fallback;

    if(isset($_ENV[$name])) {

        $value = $_ENV[$name];
    }

    return $value;
}

function redirect(string $path, $statusCode = 302) {

    header('Location: ' . $path, true, $statusCode);
    die();
}