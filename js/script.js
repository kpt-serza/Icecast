// Globální proměnné
var lastSongTitle = "";
var animationId;
var startTime;

window.onload = function () {
    // Inicializace stránky
    getStreamingData();
    setInterval(getStreamingData, 8000);
    document.getElementById('audio').src = URL_STREAMING2;
    
    // NASTAVENÍ HLASITOSTI
    var audio = document.getElementById('audio');
    var volumeSlider = document.getElementById('volume');
    var volIndicator = document.getElementById('volIndicator');

    // Načtení uložené hlasitosti z prohlížeče
    var savedVol = localStorage.getItem('volume') || 100;
    audio.volume = savedVol / 100;
    volumeSlider.value = savedVol;
    volIndicator.innerHTML = savedVol + '%';

    // Funkce, která reaguje na pohyb slideru
    volumeSlider.oninput = function() {
        var val = this.value;
        audio.volume = val / 100;
        volIndicator.innerHTML = val + '%';
        localStorage.setItem('volume', val); // Uloží si hlasitost i pro příště
    };
}

function animateWaves(timestamp) {
    if (!startTime) startTime = timestamp;
    var elapsed = timestamp - startTime;
    var waves = document.querySelectorAll('.wave');
    
    waves.forEach((wave, i) => {
        var size = 30 + Math.sin((elapsed + (i * 500)) * 0.003) * 15;
        wave.setAttribute('r', size);
    });
    animationId = requestAnimationFrame(animateWaves);
}

function togglePlay() {
    var audio = document.getElementById('audio'); 
    var icon = document.getElementById('playIcon');
    var waves = document.getElementById('helax-waves');
    var btn = document.getElementById('playerButton');
    
    if (audio.paused) {
        audio.load(); 
        var playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                icon.className = 'fa fa-pause';
                waves.style.display = 'block';
                btn.style.backgroundColor = 'transparent';
                btn.style.border = '2px solid #b32d44';
                startTime = 0;
                requestAnimationFrame(animateWaves);
            }).catch(error => {
                console.log("Chyba přehrávání: ", error);
            });
        }
    } else {
        audio.pause();
        icon.className = 'fa fa-play';
        waves.style.display = 'none';
        btn.style.backgroundColor = '#b32d44';
        btn.style.border = 'none';
        cancelAnimationFrame(animationId);
    }
}


function getStreamingData() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            try {
                var data = JSON.parse(this.responseText);
                if (data.error || !data.currentSong) return;

                // Odstranění HTML entit a mezer
                let song = data.currentSong.replace(/&apos;/g, '\'').replace(/&amp;/g, '&').trim();
                let artist = data.currentArtist.replace(/&apos;/g, '\'').replace(/&amp;/g, '&').trim();
                let fullTitle = artist + " - " + song;

                // Aktualizace webu pouze pokud se změnila písnička
                if (fullTitle !== lastSongTitle) {
                    lastSongTitle = fullTitle;
                    
                    document.getElementById('currentSong').innerHTML = song;
                    document.getElementById('currentArtist').innerHTML = artist;
                    document.title = fullTitle + " | Radio"; //
                    
                    getCoverArt(artist, song); 
                }
            } catch (e) { 
                console.error("Chyba při parsování dat z Icecastu", e); 
            }
        }
    };
    xhttp.open('GET', 'api.php?url=' + URL_STREAMING + '&streamtype=icecast2&t=' + new Date().getTime(), true);
    xhttp.send();
};
function getCoverArt(artist, song) {
    var defaultLogo = "img/logo.png";
    var query = artist + " " + song;

    // Voláme PHP proxy
    var url = 'itunes.php?term=' + encodeURIComponent(query);

    $.getJSON(url, function(data) {
        var artworkUrl = defaultLogo;

        if (data.resultCount > 0) {
            // Získáme obrázek ve vysokém rozlišení
            artworkUrl = data.results[0].artworkUrl100.replace('100x100bb.jpg', '600x600bb.jpg');
        }

        // 1. Aktualizace vzhledu webové stránky
        $("#cover_art").attr("src", artworkUrl);
        $("#bgCover").css("background-image", "url('" + artworkUrl + "')");

        // 2. AKTUALIZACE ZAMYKACÍ OBRAZOVKY TELEFONU
        if ('mediaSession' in navigator) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: song,
                artist: artist,
                album: 'Rádio',
                artwork: [
                    { src: artworkUrl, sizes: '96x96',   type: 'image/jpeg' },
                    { src: artworkUrl, sizes: '128x128', type: 'image/jpeg' },
                    { src: artworkUrl, sizes: '192x192', type: 'image/jpeg' },
                    { src: artworkUrl, sizes: '256x256', type: 'image/jpeg' },
                    { src: artworkUrl, sizes: '384x384', type: 'image/jpeg' },
                    { src: artworkUrl, sizes: '512x512', type: 'image/jpeg' },
                ]
            });
        }

    }).fail(function() {
        // Při chybě sítě nastavíme aspoň logo
        $("#cover_art").attr("src", defaultLogo);
        $("#bgCover").css("background-image", "url('" + defaultLogo + "')");
        console.error("Nepodařilo se načíst data z itunes.php");
    });
}
