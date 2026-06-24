# 🥁 Taiko Tournament Scoreboard

A web-based score tracking system for Taiko tournaments. Create tournaments, track scores across multiple rounds, and crown champions.  
  
All without a database!\
<br>

🌐 [**Live Demo**](https://www.danieleborghi.com/tools/taikoscore/) (*sync & cloud saves enabled*)

📸 [View all screenshots](#-screenshots)  
<br>

## 🥁🥁 Features

- **No login required** - just a 6-digit code to join
- **Per-player scoring rules** – handicap your friends or adjust difficulty values individually
- **Works offline** – falls back to localStorage when server isn't reachable
- **Export results** – generates a printable HTML report
- **Mobile friendly** – works on phones and tablets
- **No database** – everything stored as JSON files

## 🥢🥢 Quick Start

### Option 1: Just the HTML (local storage only)

Open `index.html` directly in your browser. Everything saves to your browser's localStorage.

### Option 2: With PHP backend (multi-device sync)

1. Upload the `api/` folder to your PHP-enabled web server
2. Make sure the `data/` folder is writable (chmod 755 or 777)
4. That's it! No configuration needed!

**Requirements (multi-device sync):**
- PHP 7.0+ (only needed for backend)
- The data/ folder must be writable by the web server

## 🥁🥢 How to Use

### Creating a tournament
1. Enter a tournament name
2. Click "Create Tournament"
3. Add players (at least 2)
4. Configure scoring rules for each player (optional)
5. Set total rounds (0 = infinite)
6. Hit "Start Tournament!"

### During the tournament
- Enter results for each player per round (difficulty + result + optional handicap)
- Leaderboard updates automatically
- Players can have different scoring tables (great for balancing skill levels)

### Scoring values
Each combination of (Difficulty × Result) gives points:
- **Easy** / **Normal** / **Hard** / **Extreme**
- **Incomplete** / **Cleared** / **Full Combo**

Default values are balanced, but you can tweak them per player.

## API Endpoints (if using PHP)

| Action | Description |
|--------|-------------|
| `save` | Create or update a tournament |
| `load` | Load tournament by 6-digit code |
| `list` | List all tournaments (debug only) |

All requests are POST with JSON body.

**Highly recommended:** leave .htaccess file inside the /data/ folder with "deny from all" to prevent direct access to the JSON files.

## 📸 Screenshots

| | |
|-|-|
| [<img width="140" alt="01" src="https://github.com/user-attachments/assets/923fab91-04a2-4912-bdbd-448ca3642d84">](https://github.com/user-attachments/assets/923fab91-04a2-4912-bdbd-448ca3642d84)<br>*Create Tournament* | [<img width="140" alt="02" src="https://github.com/user-attachments/assets/f9785a09-6246-4b87-afa1-d774c9a61395">](https://github.com/user-attachments/assets/f9785a09-6246-4b87-afa1-d774c9a61395)<br>*Player Setup* |
| [<img width="140" alt="03" src="https://github.com/user-attachments/assets/9c3fc8bc-5ea9-4529-9da3-26bc680370fa">](https://github.com/user-attachments/assets/9c3fc8bc-5ea9-4529-9da3-26bc680370fa)<br>*Scoring Rules* | [<img width="140" alt="04" src="https://github.com/user-attachments/assets/636c825c-9f2e-483e-8287-54869be0d1d1">](https://github.com/user-attachments/assets/636c825c-9f2e-483e-8287-54869be0d1d1)<br>*Round Entry* |
| [<img width="140" alt="05" src="https://github.com/user-attachments/assets/66d1ea80-3980-4804-b4a1-78ac36bbdea1">](https://github.com/user-attachments/assets/66d1ea80-3980-4804-b4a1-78ac36bbdea1)<br>*Leaderboard* | [<img width="140" alt="06" src="https://github.com/user-attachments/assets/08319df7-560b-4c20-9657-dc3ce350a6d7">](https://github.com/user-attachments/assets/08319df7-560b-4c20-9657-dc3ce350a6d7)<br>*Tournament Over* |
| [<img width="140" alt="07" src="https://github.com/user-attachments/assets/57b0d7ce-05dc-41fc-99ae-00df90720112">](https://github.com/user-attachments/assets/57b0d7ce-05dc-41fc-99ae-00df90720112)<br>*Final Results* | [<img width="140" alt="08" src="https://github.com/user-attachments/assets/0eb42275-3b25-43eb-92e2-2a4a38d64c46">](https://github.com/user-attachments/assets/0eb42275-3b25-43eb-92e2-2a4a38d64c46)<br>*Export Report* |

<br>

## 🛠️ Customization

### Default scoring values
Edit the `scoringDefaults` object in `index.html`:

```javascript
scoringDefaults: {
  easy:    { incomplete: -2,   cleared: 1, fullcombo: 2   },
  normal:  { incomplete: -1,   cleared: 2,   fullcombo: 4   },
  hard:    { incomplete: -0.5, cleared: 3, fullcombo: 8   },
  extreme: { incomplete: 0,    cleared: 5,   fullcombo: 20  },
}
```

<br>

Made with 🥁 ~ Enjoy the rhythm!
