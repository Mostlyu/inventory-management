# Phase 1 — Projektsetup & Docker-Basics (Instruction Guide)

**Goal:** Stand up a local, reproducible dev environment with Docker Compose —
PHP+Apache, MariaDB, and Adminer — with a database whose data survives restarts.

---
services:

  adminer:              # the service name → also its hostname on the private network
    image: adminer      # pull the ready-made "adminer" image from Docker Hub
    restart: always     # if the container crashes/reboots, Docker restarts it
    ports:
      - 8080:8080       # HOST:CONTAINER → reach Adminer at localhost:8080

  db:
    image: mariadb                          # official MariaDB image
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: example          # REQUIRED: sets the root password on first init
      MARIADB_MYSQL_LOCALHOST_USER: "true"  # optional: creates a mysql@localhost helper user
      MARIADB_DATABASE: mydb                # optional: auto-creates a database named "mydb"
    command: --ssl=0                        # override startup: run MariaDB with SSL turned off
    volumes:
      - db_data:/var/lib/mysql              # persist DB data on the named volume

  inventory_app:
    image: php:8.3-apache                   # PHP + Apache bundled in one image
    ports:
      - 8000:80                             # localhost:8000 → Apache's port 80 inside
    volumes:
      - ./inventory/app:/var/www/html       # your code → Apache's document root
    depends_on:
      - db                                  # start db before this service
    environment:
      DATABASE_URL: "mysql://root:example@db:3306/mydb?..."   # connection info for your PHP

  volumes:              # sibling of services: — zero indent
    db_data:            # declares the named volume so the db service can mount it

---

## The mental model (read this first)

| Term | What it is | Analogy |
|------|-----------|---------|
| **Image** | Read-only blueprint (e.g. `php:8.3-apache`) | A class / a recipe |
| **Container** | A running instance of an image | An object / a cooked meal |
| **Volume** | Storage that outlives the container | A filing cabinet |
| **Service** | One entry under `services:` — something that RUNS | — |

**Golden rule:** *Does it run and stay running?* → it's a **service**.
A volume, a Dockerfile, or a network is a **mechanism** a service uses — never a service.

**Two beginner traps to burn in:**
1. Services reach each other by **service name**, never `localhost`.
   (Inside a container, `localhost` = "this same container".)
2. Containers forget; **volumes remember.** DB data must sit on a volume.

---

## Step-by-step

### 1. Decide the services
This app needs exactly **three** running things:
- `db` — MariaDB (the database)
- `adminer` — a web GUI to inspect the database
- `inventory_app` — PHP + Apache serving the app

### 2. Write `docker-compose.yaml`
Structure — note the **two top-level keys** (`services:` and `volumes:` are siblings):

```
services:
  <service>:
    image: ...        # OR build: (a Dockerfile recipe)
    ports: HOST:CONTAINER
    environment: ...
    volumes: SOURCE:TARGET
    depends_on: ...
volumes:              # <-- zero indent, sibling of services:
  db_data:
```

Key meanings:
- `image:` — which blueprint to pull/run. Needed unless you `build:` your own.
- `ports:` — `HOST:CONTAINER`. Left = the port you type in the browser;
  right = the port the process listens on *inside*. e.g. `8000:80` for Apache.
- `environment:` — config passed in. MariaDB needs a root password here.
- `volumes:` (in a service) — mount storage/code. `SOURCE:TARGET`.
  - Bind mount (your code): `./inventory/app:/var/www/html`
  - Named volume (DB data): `db_data:/var/lib/mysql`
- `depends_on:` — start order only. Does NOT wait for the DB to be *ready*.
- top-level `volumes:` — *declares* the named volume so services can mount it.

### 3. Persist the database
- MariaDB writes to **`/var/lib/mysql`** inside the container.
- Mount the named volume there: `- db_data:/var/lib/mysql`
- Declare it at the bottom: `volumes:` → `db_data:`
- Without this, `docker compose down` wipes all data.

### 4. Serve your own PHP
- Apache's document root is **`/var/www/html`** (fixed in the image).
- Mount your code there: `- ./inventory/app:/var/www/html`
- Mount it anywhere else and you'll only see Apache's default page.

### 5. Bring it up
```
docker compose up          # start everything, logs in foreground
docker compose up -d       # same, but detached (background)
```
Watch the logs for three "ready" signals:
- adminer: `Development Server ... started`
- inventory_app: `Apache/2.4 ... resuming normal operations`
- db: `mariadbd: ready for connections`

---

## Adminer login (localhost:8080)
| Field | Value |
|-------|-------|
| System | MySQL / MariaDB |
| **Server** | `db`  ← the **service name**, NOT `localhost` |
| Username | `root` (or the user you defined) |
| Password | matches your `MYSQL_ROOT_PASSWORD` |
| Database | leave blank, or `mydb` if it exists |

---

## Definition of done — the 3-part checkpoint
- **(a)** App loads in the browser at `localhost:8000`
- **(b)** You can reach the DB through Adminer at `localhost:8080`
- **(c)** Create a table → `docker compose down` → `up` → **table still there**

If (c) fails, the volume wiring is wrong — fix that before moving on.

---

## Gotchas learned the hard way
- **YAML indentation = structure.** A misindented `volumes:` or `build:` becomes a
  phantom "service" and Docker complains about unknown properties.
- **`build:` is a block inside a service**, not a top-level key.
- **Env vars only apply on FIRST init.** MariaDB reads `MYSQL_ROOT_PASSWORD` /
  `MARIADB_DATABASE` once, when the volume is empty. Changing them later does
  nothing until you `docker compose down -v` (which DELETES the volume + data).
- **`down` vs `down -v`:** plain `down` keeps your data; `down -v` wipes it.
- **A database (`mydb`) is not a volume (`db_data`).** The volume is the storage;
  the database is one thing stored on it.

---

## Useful commands
```
docker compose up -d           # start detached
docker compose down            # stop + remove containers (data survives)
docker compose down -v         # stop + remove containers AND volumes (wipes data)
docker compose ps              # list services
docker compose logs -f db      # follow one service's logs
docker compose exec db bash    # shell inside the running db container
```
