<?php
$url = "http:/localhost:3000/api/products";

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);

$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($response === false) {
    die("cURL error: " . htmlspecialchars(curl_error($curl)));
}

curl_close($curl);

if ($status !== 200) {
    die("API request failed with HTTP status " . $status);
}

$products = json_decode($response, true);

if (!is_array($products)) {
    die("Invalid JSON response.");
}

echo "<h2>Products from Express API</h2>";

foreach ($products as $product) {
    echo "ID: " . (int)$product["id"] . "<br>";
    echo "Name: " . htmlspecialchars($product["name"]) . "<br>";
    echo "Price: ₹" . number_format((float)$product["price"], 2) . "<hr>";
}
?>