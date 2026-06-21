<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Radio - ON Air</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" type="image/png" href="img/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
    
        :root { --accent-red: #b32d44; }
        body, html { margin: 0; padding: 0; height: 100vh; background-color: #121212; color: white; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; overflow-y: auto; }
        
        //#bgCover {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            filter: blur(50px); transform: scale(1.1); opacity: 0.4;
            background-size: cover; background-position: center; transition: background-image 1s;
            background-image: url('img/logo.png');
        }

        .bg-mask {
            position: fixed; top: 0; left: 0; width: 100vh; height: 100vh;
            background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(18,18,18,1) 100%); z-index: -1;
        }

        .main-wrapper {
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            height: 100vh; 
            padding: 0px; box-sizing: border-box; text-align: center;
        }

        .cover-container { width: 100%; max-width: 250px; margin-bottom: 20px; }
        #cover_art { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); }
        

        .player-main { position: relative; width: 100px; height: 100px; display: flex; justify-content: center; align-items: center;  }
        #playerButton { width: 65px; height: 65px; background-color: var(--accent-red); border-radius: 50%; border: none; cursor: pointer; z-index: 10; display: flex; justify-content: center; align-items:             center; }
        #playerButton i { color: white; font-size: 26px; }


        #helax-waves { position: absolute; width: 120px; height: 120px; display: none; }
        .wave { fill: none; stroke: var(--accent-red); stroke-width: 2; }


        .volume-box { display: flex; align-items: center; gap: 15px; width: 100%; max-width: 280px; margin-top: 30px; }
        #volume { flex-grow: 1; height: 5px; cursor: pointer; accent-color: var(--accent-red); }

        
        .playlist-link {margin-top: 30px; background-color: rgba(255,255,255,0.1); color: white; text-decoration: none; padding: 12px 30px; border-radius: 30px; font-size: 0.9rem; text-transform: uppercase;}
        
        
        
        
/* Kontejner pro text o skladbě */

.info-current-song {
    width: 100%;
    margin-top: 10px;
    margin-bottom: 10px;
    min-height: 80px; /* Zabrání skákání obsahu při načítání */
}

/* Styl pro Název skladby (H2) */
#currentSong {
    font-size: 2.2rem;
    font-weight: 800;
    //margin: 15px 0 0 0;
    text-transform: uppercase;
    color: #ffffff;
    margin-bottom: 30px;
    /* Animace pro plynulý přechod textu */
    transition: all 0.5s ease;
}

/* Styl pro Umělce/Rádio (H3) */
#currentArtist {
    font-size: 1.8rem;
    opacity: 0.6;
    //margin: 15px 0 0 0;
    font-weight: 400;
    color: #ffffff;
    margin-bottom: 30px;
    text-transform: capitalize;
}

/* Responzivita pro větší obrazovky (PC) */
@media (min-width: 768px) {
    #currentSong {
        font-size: 2rem;
    }
    #currentArtist {
        font-size: 1.3rem;
    }
    height: 100vh;body, html {
        overflow: hidden;
        height: 100vh;
    }
}
    

</style>
    
    
    
</head>

<body>
    <div id="bgCover"></div>
    <div class="bg-mask"></div>

    <div class="main-wrapper">
        <div class="cover-container">
            <img id="cover_art" src="img/logo.png">
        </div>

        <div class="info-current-song">
            <h2 id="currentSong">NAČÍTÁM...</h2>
            <h3 id="currentArtist"></h3>
        </div>

        <div class="player-main">
            <svg id="helax-waves" viewBox="0 0 100 100">
                <circle class="wave" cx="50" cy="50" r="30" opacity="0.8"></circle>
                <circle class="wave" cx="50" cy="50" r="30" opacity="0.5"></circle>
                <circle class="wave" cx="50" cy="50" r="30" opacity="0.2"></circle>
            </svg>
            <button id="playerButton" onclick="togglePlay()">
                <i class="fa fa-play" id="playIcon"></i>
            </button>
        </div>

        <div class="volume-box">
            <i class="fa fa-volume-down"></i>
            <input type="range" id="volume" min="0" max="100" value="100">
            <span id="volIndicator" style="min-width: 35px;">100%</span>
        </div>

        <a href="http://YOUR_IP/playlist" class="playlist-link" target="_blank">
            <i class="fa fa-list-ul"></i> Playlist
        </a>
    </div>

    <audio id="audio" src="http://YOUR_IP_IP:8084/radio.mp3" preload="none"></audio>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="config.js"></script>
    <script src="js/script.js"></script>
</body>
</html>