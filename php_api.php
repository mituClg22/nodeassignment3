<?php
header("Content-Type: application/json; charset=UTF-8");

$products = [
    ["id" => 1, "name" => "Laptop", "price" => 50000],
    ["id" => 2, "name" => "Mobile", "price" => 20000],
    ["id" => 3, "name" => "Shirt", "price" => 1000]
];

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

echo json_encode($products, JSON_PRETTY_PRINT);
?>