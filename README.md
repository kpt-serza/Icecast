# 📻 Webový Přehrávač Internetového Rádia (Icecast2 edice)

Tato složka obsahuje lehký, responzivní a plně klientský webový přehrávač pro streamování hudby z Icecast2 serveru. Aplikace automaticky parsuje aktuálně přehrávanou skladbu z JSON statusu a dynamicky k ní dohledává obaly alb pomocí API od Apple iTunes.

## 🚀 Hlavní Funkce

* **Live Stream** – Přehrávání audia přímo v prohlížeči (podpora MP3 streamu z konkrétního mountpointu).
* **Icecast2 Metadata Parser** – Na pozadí (každých 8 sekund) asynchronně kontroluje endpoint `/status-json.xsl`, vybere správný mountpoint a vytáhne název aktuálního interpreta, písničky a počet posluchačů.
* **iTunes Cover Art Integration** – Jakmile se změní písnička, web se dotáže oficiálního API Apple iTunes, stáhne obal alba ve vysokém rozlišení (`600x600px`) a hodí ho na pozadí (blur efekt) i do středu přehrávače.
* **MediaSession API** – Název písničky, interpreta i obal alba se natvrdo propisují do systému telefonu/PC (uvidíš je na zamykací obrazovce mobilu nebo v ovládání médií v systému podobně jako u Spotify).
* **Persistentní Hlasitost** – Úroveň hlasitosti se ukládá do `localStorage` prohlížeče, takže si ji web pamatuje i po obnovení stránky.
* **Audio Vizualizace** – Pulzující efekt CSS vln kolem tlačítka Play, který reaguje na to, zda rádio aktivně hraje.

---

## 📂 Struktura Souborů

### Hlavní Soubory frontendu

* **`index.php`** – Základní HTML/PHP struktura přehrávače. Obsahuje čistý tmavý design, ovládací prvky (Play/Pause, volume slider) a SVG animaci vln.
* **`config.js`** – Konfigurační soubor. Zde se definuje název rádia, typ serveru (`icecast2`) a URL adresy.
* **`script.js`** – Jádro aplikace v JavaScriptu (jQuery). Stará se o logiku přehrávání, časovače, animace a komunikaci s API.

### Backend Bridges (PHP)

* **`api.php`** – Pomocný cURL skript, který obchází ochranu CORS. Sahá si na Icecast endpoint `/status-json.xsl`, bezpečně parsuje strukturu `icestats -> source`, najde mountpoint `/radio.mp3` a vrátí vyčištěná data frontendu jako čistý JSON.
* **`itunes.php`** – Proxy skript, který bezpečně předává dotazy na vyhledávací API iTunes (`https://itunes.apple.com/search`), čímž zamezuje míchání HTTP/HTTPS obsahu a CORS chybám.

---

## ⚙️ Nastavení a Zprovoznění

Pro správný běh stačí upravit konfigurační soubor **`config.js`**:

```javascript
var settings = {
    'radio_name': 'Radio ID',                            // Název tvého rádia
    'url_streaming': 'http://YOUR_IP:8084',              // Základní URL Icecast serveru (pro status-json.xsl)
    'url_streaming2': 'http://YOUR_IP:8084/radio.mp3',   // Přímý odkaz na streamovaný mountpoint
    'streaming_type': 'icecast2',                        // Nastaveno na icecast2
    'default_cover_art': 'img/album.png',                // Výchozí logo, když iTunes nic nenajde
};

```

*Poznámka: Nezapomeň mít ve složce podsložku `img/` s výchozím obrázkem (např. `album.png` nebo `logo.png`), který slouží jako fallback.*

---

## 🔄 Životní cyklus dat na webu

```
[Prohlížeč (script.js)] --(každých 8s)--> [api.php] --> [Icecast Server (/status-json.xsl)]
          ^                                                            |
          |-------- (Vrátí: Interpret, Písnička, Posluchači) <----------|
          |
          v
[Dotaz na itunes.php] --> [Apple iTunes API] --> (Nalezení obalu alba 600x600bb.jpg)
          |
          v
[Aktualizace DOM] --> Změna obrázků na webu + Aktualizace zamykací obrazovky (MediaSession)

```

Aplikace je kompletně ořezaná o zbytečný balast z původních šablon, je bleskově rychlá, optimalizovaná pro mobilní telefony (Android/iOS) a šetrná k výkonu i datům.
