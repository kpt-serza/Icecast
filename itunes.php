<?php
// itunes.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Povolí volání z tvého webu

$term = $_GET['term'] ?? '';
if (empty($term)) {
    echo json_encode(['resultCount' => 0]);
    exit;
}

$url = 'https://itunes.apple.com/search?term=' . urlencode($term) . '&media=music&limit=1';

// Použijeme cURL nebo file_get_contents pro získání dat ze serveru Apple
$response = file_get_contents($url);

echo $response;
