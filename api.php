<?php
// Získání URL z parametru (voláno ze script.js)
$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);

error_reporting(E_ALL);
ini_set('display_errors', 0);

$array = [];

if(!empty($url)) {
    // Icecast endpoint pro JSON data
    $curl = curl_init($url . '/status-json.xsl');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    $data = curl_exec($curl);
    curl_close($curl);

    if(!empty($data)) {
        $json = json_decode($data, true);
        
        // Icecast vrací data v struktuře icestats -> source
        $sources = isset($json['icestats']['source']) ? $json['icestats']['source'] : null;

        if ($sources) {
            $mySource = null;

            // Pokud je tam jen jeden mountpoint, Icecast nevrátí pole, ale přímo objekt
            if (isset($sources['listenurl'])) {
                $mySource = $sources;
            } else {
                // přidáme mountpoint Icecastu /radio.mp3
                foreach ($sources as $source) {
                    $mountName = isset($source['mount']) ? $source['mount'] : '';
                    if (strpos($mountName, 'radio.mp3') !== false) {
                        $mySource = $source;
                        break;
                    }
                }
            }

            if ($mySource && isset($mySource['title'])) {
                $playingNow = $mySource['title']; 
                
                // Rozdělení na Artist - Song
                $currentSongParts = explode(' - ', $playingNow, 2);

                if(count($currentSongParts) === 2) {
                    $array['currentArtist'] = trim($currentSongParts[0]);
                    $array['currentSong']   = trim($currentSongParts[1]);
                } else {
                    $array['currentArtist'] = "YOUR ID STATION";
                    $array['currentSong']   = trim($currentSongParts[0]);
                }
                $array['listeners'] = $mySource['listeners'] ?? 0;
            } else {
                $array = ['error' => 'Stream is offline or metadata empty'];
            }
        } else {
            $array = ['error' => 'No sources found in Icecast'];
        }
    } else {
        $array = ['error' => 'Failed to fetch data from Icecast'];
    }
} else {
    $array = ['error' => 'URL parameter not found'];
}

// Odeslání čistého JSONu
header('Content-type: application/json; charset=utf-8');
echo json_encode($array);
