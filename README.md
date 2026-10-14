# inventory_mngmnt

A small inventory management web app — items, categories and stock movements with
user login. Built as a guided learning project (Fachinformatiker Anwendungs-
entwicklung) in plain PHP on MariaDB, running in Docker.

## Stack

- **PHP 8** (no framework) with PSR-4 autoloading via Composer
- **MariaDB** — schema in [`db/schema.sql`](db/schema.sql), loaded on first container init
- **Docker Compose** — app (Apache + PHP), database, and Adminer
- Layered by hand: Controller → Repository → Model → Template

## Getting started

```bash
cp .env.example .env     # adjust if you change the DB credentials in compose
composer install         # installs the PSR-4 autoloader
docker compose up -d --build
```

| Service | URL | Notes |
| --- | --- | --- |
| App | http://localhost:8000 | document root is `/public` |
| Adminer | http://localhost:8080 | server `db`, user `root`, password from compose |

Local-dev login: `admin` / `admin123` (seeded by the schema — learning setup only).

The database is seeded **only on the first start**, while the `db_data` volume is
empty. To re-apply a changed schema: `docker compose down -v` (this wipes the data).

## Layout

```
public/          front controller — the only web-reachable directory
src/
  Controller/    handles requests, decides what happens
  Repository/    all SQL; the only layer that talks to the database
  Model/         plain data objects
  templates/     HTML output
db/schema.sql    tables + seed data
config/          configuration read from .env
docs/            write-up, diagrams (ER, class, use-case, activity)
```

Secrets live in `.env`, which is gitignored — `.env.example` is the committed
template. Nothing in this repo contains a real credential.

## Project phases

The build is split into eight phases; see [`PHASE1_GUIDE.md`](PHASE1_GUIDE.md),
[`PHASE2_ANALYSE.md`](PHASE2_ANALYSE.md) and [`docs/`](docs/) for the notes and
reasoning behind each step.
