<?php
$directory = __DIR__ . "/uploads";

if (!is_dir($directory)) {
    die("Directory does not exist.");
}

$items = scandir($directory);

echo "<h2>Files in uploads directory</h2>";
echo "<ul>";

foreach ($items as $item) {
    if ($item === "." || $item === "..") {
        continue;
    }

    $fullPath = $directory . DIRECTORY_SEPARATOR . $item;

    if (is_file($fullPath)) {
        echo "<li>File: " . htmlspecialchars($item) . "</li>";
    } elseif (is_dir($fullPath)) {
        echo "<li>Folder: " . htmlspecialchars($item) . "</li>";
    }
}

echo "</ul>";
?>