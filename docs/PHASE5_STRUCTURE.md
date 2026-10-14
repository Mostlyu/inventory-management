# Phase 5 — Projektstruktur in PHP (Summary)

**Goal:** Move from "everything in one file" to a clean, layered, maintainable
structure. Not just *"es läuft"* but *"es ist wartbar."*

---

## The core idea: Separation of Concerns
Each piece of code has exactly **one job**, and lives in its own layer. A request
flows through the layers, each talking only to its neighbour:

```
Browser → /public → Controller → Repository → Model → Template → back to browser
                                      ↑
                              /config + .env (settings & secrets)
```

| Folder | One-sentence job |
|--------|------------------|
| `/public` | The ONLY web-reachable folder; `index.php` is the single entry point |
| `/src/Controller` | Coordinates one request (decides what to do, calls repo, picks template) |
| `/src/Repository` | Talks to the database — all SQL lives here |
| `/src/Model` | The domain objects (Item, Category, StockMovement) — data + behaviour |
| `/templates` | The HTML views |
| `/config` + `.env` | Settings and secrets, read once, used everywhere |

**Checkpoint:** to swap MariaDB for another DB, how many files change? Ideally
only the Repository layer. The fewer, the better the separation.

---

## What we built

### 1. `.env` + `.gitignore` (secrets out of code)
- `.env` holds the 4 DB values: `DB_HOST=db`, `DB_NAME=mydb`, `DB_USER=root`,
  `DB_PASSWORD=example`.
- `.gitignore` lists `.env` (and `vendor/`) so secrets never reach git.
- Why: passwords/hosts/db-names must never be hardcoded or committed; they also
  differ between laptop and server, so the *same code* runs everywhere — only
  `.env` changes.

### 2. Single reusable DB connection — `src/Database.php`
- One place opens the PDO connection (single source of truth): change the
  password once, in one file.
- Reads the env vars with `getenv(...)`, builds a PDO **DSN**
  (`mysql:host=...;dbname=...;charset=utf8mb4`), returns a configured `PDO`.
- Two important PDO options:
  - `ERRMODE_EXCEPTION` → DB errors throw (no silent failures).
  - `FETCH_ASSOC` → rows come back as clean associative arrays.

### 3. Namespaces + PSR-4 autoloading (Composer)
- `composer.json` maps namespace `App\` → folder `src/`.
- `composer dump-autoload` generates `vendor/autoload.php`.
- `index.php` now needs ONE line: `require '../vendor/autoload.php';` — every
  `App\...` class loads automatically. No more manual `require` per class.
- **Namespace gotcha learned:** inside `namespace App;`, an unqualified `PDO`
  means `App\PDO`. Built-in classes live in the global namespace — fix with a
  leading `\PDO` or a `use PDO;` import. In a non-namespaced file (index.php),
  reference our class fully-qualified: `App\Database`.

### 4. Docker adjustments
- **Dockerfile** now: installs `pdo_mysql` (PDO driver for MariaDB) and rewrites
  Apache's config so **DocumentRoot = `/var/www/html/public`**.
- **docker-compose** `inventory_app`: `build: .`, mounts whole project
  (`./:/var/www/html`), and `env_file: .env` loads the DB vars into the
  container so `getenv()` sees them.
- Result: `localhost:8000/` serves `public/index.php`, and `localhost:8000/.env`
  returns 404 — secrets are outside the web root.

---

## Definition of done ✅
- App loads at `localhost:8000` and prints "Connected to the database!"
- Connection runs through: `.env` → `env_file` → `getenv()` → `Database` (PDO,
  autoloaded) → `pdo_mysql` → MariaDB.
- `.env` is unreachable from the browser (404) and untracked by git.

Everything is now in place to build real features (Phase 6) on a clean foundation.
