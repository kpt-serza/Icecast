<?php
// Povolení přístupu z jiných domén (CORS) a nastavení JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// --- KONFIGURACE ---
// IP adresa a port tvého Shoutcast serveru
$shoutcast_url = "http://your_ip:8000"; 
// -------------------

$default_response = [
    "currentArtist" => "Rádio",
    "currentSong"   => "24/7 Stream",
    "listeners"     => 0
];

// Shoutcast v1/v2 endpoint pro rychlá data o streamu
$stats_url = $shoutcast_url . "/7.html";

$ctx = stream_context_create([
    'http' => [
        'timeout' => 3,
        'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x44)\r\n"
    ]
]);

$raw_data = @file_get_contents($stats_url, false, $ctx);

if (!$raw_data) {
    echo json_encode($default_response);
    exit;
}

// Shoutcast 7.html vrací řetězec ve formátu: 
// posluchači, stav, peak, max, unikátní, bitrate, SongTitle
// Příklad: 10,1,15,100,8,128,Hedex - Lowkey
$data_parts = explode(',', $raw_data);

if (count($data_parts) >= 7) {
    // Odstranění HTML značek, které Shoutcast někdy lepí na konec
    $full_title = preg_replace('/<[^>]*>|body|html/i', '', $data_parts[6]);
    $full_title = trim($full_title);

    // Rozdělení na Artist a Song (očekává formát "Artist - Song")
    $song_parts = explode(" - ", $full_title, 2);

    $response = [
        "currentArtist" => isset($song_parts[0]) ? trim($song_parts[0]) : "Rádio",
        "currentSong"   => isset($song_parts[1]) ? trim($song_parts[1]) : trim($song_parts[0]),
        "listeners"     => (int)$data_parts[0]
    ];

    echo json_encode($response);
} else {
    echo json_encode($default_response);
}
?>
