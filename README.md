# 🗳️ Real-Time Live Poll Platform

> A full-stack real-time polling system with IP-restricted voting, admin moderation, vote audit trails, and live results — built with PHP, MySQL, and AJAX.

![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple?logo=bootstrap)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Option A — Docker (Recommended)](#option-a--docker-recommended)
  - [Option B — Docker Compose](#option-b--docker-compose)
  - [Option C — Manual Setup (XAMPP / Local Server)](#option-c--manual-setup-xampp--local-server)
- [Default Accounts](#default-accounts)
- [Project Structure](#project-structure)
- [Architecture](#architecture)
- [API Reference](#api-reference)
- [Database Schema](#database-schema)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Future Roadmap](#future-roadmap)
- [License](#license)

---

## Overview

This platform lets you run live polls where each IP address can only vote once per poll. Admins can create polls, see who voted, release votes to let someone vote again, and review a full audit trail of every action. Results update in real time on the voting page without any page reloads.

It is built entirely without a heavy framework — the routing, templating, and database layers are hand-rolled PHP, making the codebase easy to read and deploy anywhere PHP runs.

---

## Features

### For Voters
- 🔐 Secure session-based login
- 🗳️ One vote per IP address per poll (enforced server-side)
- 📊 Live results that refresh every second via AJAX — no page reload
- 📱 Fully responsive — works on phones, tablets, and desktops

### For Admins
- ➕ Create polls with any number of options
- 👥 See a full list of who voted and what they chose
- 🔓 Release an IP's vote so they can vote again
- 🗑️ Delete or deactivate polls
- 📜 Complete vote history log (vote → release → revote)

### Technical Highlights
- Clean MVC architecture with custom PHP router
- PDO prepared statements — SQL injection protected
- CSRF token on every form
- IPv4 and IPv6 support for IP tracking
- 3D card hover effects with CSS transforms
- Bootstrap 5 UI, no extra build tools needed

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2, Core PHP MVC |
| Database | MySQL 8.0 |
| Frontend | Bootstrap 5, jQuery, AJAX |
| Web Server | Apache (mod_rewrite) |
| Containerisation | Docker, Docker Compose |

---

## Getting Started

### Prerequisites

| Tool | Version | Notes |
|---|---|---|
| Docker | 20.10+ | For Docker setup options |
| Docker Compose | v2+ | Comes with Docker Desktop |
| PHP | 8.0+ | For manual setup only |
| MySQL | 5.7+ | For manual setup only |

---

### Option A — Docker (Recommended)

The fastest way to get running. One script handles everything: network, MySQL, schema import, and the app container.

```bash
# Clone the repository
git clone https://github.com/HyperPS/Real-time-poll-platform.git
cd Real-time-poll-platform

# Make the launch script executable
chmod +x docker-run.sh

# Start everything on port 8080 (default)
./docker-run.sh

# Or start on a custom port
./docker-run.sh 9090
```

The script will:
1. Remove any old containers with the same names
2. Create an isolated Docker network
3. Start a MySQL 8.0 container and wait for it to be healthy
4. Import the full database schema and seed data automatically
5. Build the PHP/Apache app image
6. Start the app container linked to the database

Once finished, open your browser at **http://localhost:8080**

**Useful commands while running:**

```bash
# View application logs
docker logs poll_app

# View database logs
docker logs poll_mysql

# Open a MySQL shell
docker exec -it poll_mysql mysql -u polluser -ppollpass123 polling_system

# Stop both containers
docker stop poll_app poll_mysql

# Remove both containers
docker rm poll_app poll_mysql

# Rebuild the app image after code changes
docker build -t poll_app_image .
docker rm -f poll_app
docker run -d \
  --name poll_app \
  --network poll_network \
  -e DB_HOST=poll_mysql \
  -e DB_NAME=polling_system \
  -e DB_USER=polluser \
  -e DB_PASS=pollpass123 \
  -p 8080:80 \
  poll_app_image
```

---

### Option B — Docker Compose

If you prefer Docker Compose, a `docker-compose.yml` is included. This is useful for persistent development where you want named volumes and automatic restarts.

```bash
# Clone the repository
git clone https://github.com/HyperPS/Real-time-poll-platform.git
cd Real-time-poll-platform

# Start all services in the background
docker compose up -d

# Watch the startup logs
docker compose logs -f

# Stop and remove containers (data volume is preserved)
docker compose down

# Stop and wipe all data (clean slate)
docker compose down -v
```

The app will be available at **http://localhost:8080**  
MySQL is also exposed on port **3307** for direct access with a GUI tool like TablePlus or DBeaver:

```
Host:     127.0.0.1
Port:     3307
User:     polluser
Password: pollpass123
Database: polling_system
```

**Other helpful Compose commands:**

```bash
# Rebuild the app image (run after code changes)
docker compose build app
docker compose up -d app

# Run a one-off command inside the app container
docker compose exec app php -v

# Reset only the database container
docker compose rm -sf db
docker compose up -d db
```

---

### Option C — Manual Setup (XAMPP / Local Server)

Use this path if you prefer to run without Docker, for example with XAMPP on Windows or a native LAMP stack on Linux/macOS.

#### 1. Clone the repository

```bash
git clone https://github.com/HyperPS/Real-time-poll-platform.git
```

On Windows with XAMPP, copy or clone the folder into `C:\xampp\htdocs\`.

#### 2. Create the database

```bash
# Linux / macOS — MySQL command line
mysql -u root -p < database/schema.sql

# Windows — XAMPP MySQL shell
C:\xampp\mysql\bin\mysql.exe -u root -p < database\schema.sql
```

Or open **phpMyAdmin** (`http://localhost/phpmyadmin`), create a database named `polling_system`, and import `database/schema.sql`.

#### 3. Set environment variables

Copy the example file and fill in your credentials:

```bash
cp .env.example .env.local
```

Edit `.env.local`:

```ini
DB_HOST=localhost
DB_PORT=3306
DB_NAME=polling_system
DB_USER=root
DB_PASS=your_password
```

The app reads environment variables in `bootstrap/app.php`. If you are not using environment variables, you can also edit those constants directly:

```php
// bootstrap/app.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'polling_system');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

#### 4. Configure the web server

**Apache** — the `public/.htaccess` file is already included. Make sure `mod_rewrite` is enabled and `AllowOverride All` is set for the directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

Enable mod_rewrite on Ubuntu/Debian:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Nginx** — add this location block to your server config:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### 5. Point the document root

Your web server must serve from the `public/` subdirectory, not the project root.

- **XAMPP**: Copy the project to `C:\xampp\htdocs\poll-platform` and visit `http://localhost/poll-platform/public/`
- **Apache vhost**: Set `DocumentRoot /path/to/project/public`
- **Nginx**: Set `root /path/to/project/public`

#### 6. Windows quick setup

A helper batch file is included for Windows users:

```bat
setup-windows.bat
```

This checks for XAMPP, shows step-by-step instructions, and opens the setup guide.

---

## Default Accounts

These accounts are created automatically by the database seed:

| Role | Email | Password | Permissions |
|---|---|---|---|
| Admin | admin@polling.test | admin123 | Create polls, manage voters, delete polls, view audit logs |
| User | user@polling.test | user123 | Browse polls, cast votes, view results |

> ⚠️ Change these passwords before deploying to any public environment.

---

## Project Structure

```
Real-time-poll-platform/
├── app/
│   ├── Core/
│   │   └── VotingEngine.php        # All voting logic — IP checks, cast, release, revote
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php  # Login, logout
│   │       ├── PollController.php  # Poll listing, creation, AJAX data
│   │       ├── VoteController.php  # Cast vote, check status, get results
│   │       └── AdminController.php # Admin dashboard, vote release, user management
│   ├── Models/
│   │   ├── User.php
│   │   ├── Poll.php
│   │   └── PollOption.php
│   └── helpers.php                 # Shared utility functions
│
├── bootstrap/
│   └── app.php                     # DB connection, constants, autoloader bootstrap
│
├── config/
│   └── database.php                # Database configuration helper
│
├── database/
│   ├── schema.sql                  # Full schema + seed data (use for manual setup)
│   └── init/
│       └── 01-schema.sql           # Same schema loaded automatically by Docker
│
├── public/
│   ├── index.php                   # Single entry point for all requests
│   └── check-status.php            # Health check endpoint
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php       # Main HTML layout
│       ├── auth/
│       │   └── login.blade.php
│       ├── polls/
│       │   ├── dashboard.blade.php
│       │   ├── show.blade.php
│       │   └── create.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           └── manage-poll.blade.php
│
├── routes/
│   └── web.php                     # All route definitions + custom PHP router
│
├── Dockerfile                      # PHP 8.2 + Apache image
├── docker-compose.yml              # Multi-container setup
├── docker-run.sh                   # One-command Docker launcher
├── setup-windows.bat               # Windows XAMPP setup helper
├── .env.example                    # Environment variable template
└── API.md                          # Full API documentation
```

---

## Architecture

The application follows a classic five-layer MVC design:

```
┌─────────────────────────────────────────┐
│         Browser (HTML + AJAX)           │
│   jQuery, Bootstrap 5, CSS 3D effects   │
└──────────────────┬──────────────────────┘
                   │ HTTP requests
┌──────────────────▼──────────────────────┐
│         Custom PHP Router               │
│    routes/web.php  →  dispatches to     │
│    controllers based on method + path   │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│              Controllers                │
│  AuthController  PollController         │
│  VoteController  AdminController        │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│          Core — VotingEngine            │
│  IP validation, vote casting/release,   │
│  history logging, results aggregation   │
└──────────────────┬──────────────────────┘
                   │ PDO prepared statements
┌──────────────────▼──────────────────────┐
│             MySQL 8.0                   │
│  users · polls · poll_options · votes   │
│  vote_history · activity_logs           │
└─────────────────────────────────────────┘
```

### Key components

**`bootstrap/app.php`** — Runs on every request. Opens the PDO connection, defines constants, starts the session, and registers the PSR-4 autoloader.

**`routes/web.php`** — Defines every URL route and maps it to a controller method. Includes a small regex-based router that supports `{param}` placeholders.

**`app/Core/VotingEngine.php`** — The heart of the voting logic. Everything that touches votes goes through here: checking whether an IP has voted, writing a vote to the DB, releasing a vote, handling revotes, and pulling results. Controllers call this class rather than writing SQL directly.

**`public/index.php`** — The single front controller. Bootstraps the app and hands off to the router.

---

## API Reference

All endpoints require an active session (log in first). AJAX endpoints return JSON. Form endpoints redirect.

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/login` | Authenticate with email + password |
| `GET` | `/logout` | Destroy session and redirect to login |

### Polls

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/dashboard` | User poll listing page |
| `GET` | `/poll/{pollId}` | Single poll voting page |
| `GET` | `/polls/create` | Create poll form (admin only) |
| `POST` | `/polls/store` | Save new poll (admin only) |
| `GET` | `/api/polls` | JSON list of all active polls |
| `GET` | `/api/polls/{pollId}` | JSON single poll with options |

### Voting (AJAX)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/vote/cast` | Submit a vote — body: `{poll_id, option_id}` |
| `GET` | `/api/vote/status?poll_id=1` | Check if current IP has voted |
| `GET` | `/api/results?poll_id=1` | Live vote counts and percentages |

**Example — cast a vote:**

```javascript
$.ajax({
  url: '/api/vote/cast',
  method: 'POST',
  contentType: 'application/json',
  data: JSON.stringify({ poll_id: 1, option_id: 2 }),
  success: function(res) { console.log(res); }
});
```

**Example response:**
```json
{
  "success": true,
  "message": "Vote recorded successfully",
  "vote_id": 42,
  "ip_address": "192.168.1.100"
}
```

### Admin (AJAX — admin session required)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/admin/dashboard` | Admin overview page |
| `GET` | `/admin/polls/{pollId}` | Manage a specific poll |
| `GET` | `/api/admin/voters?poll_id=1` | List all voters for a poll |
| `POST` | `/api/admin/vote/release` | Release an IP's vote — body: `{poll_id, ip_address}` |
| `GET` | `/api/admin/vote-history?poll_id=1` | Full audit trail for a poll |
| `POST` | `/api/admin/poll/status` | Activate or deactivate — body: `{poll_id, status}` |
| `POST` | `/api/admin/poll/delete` | Permanently delete — body: `{poll_id}` |
| `GET` | `/api/admin/activity-logs` | System-wide activity log |
| `GET` | `/api/admin/user/stats` | User statistics |
| `POST` | `/api/admin/user/create` | Create a new user |
| `POST` | `/api/admin/user/delete` | Delete a user |

See [API.md](API.md) for full request/response examples for every endpoint.

---

## Database Schema

The database has six tables:

```
users           — accounts with bcrypt passwords and an admin/user role
polls           — poll questions with active/inactive status
poll_options    — the choices that belong to a poll
votes           — one row per active vote, keyed by (poll_id, ip_address)
vote_history    — append-only audit log of every vote, release, and revote
activity_logs   — broader system log (IP, user-agent, action, device info)
```

The votes table has a composite index on `(poll_id, ip_address, is_active)` so IP-uniqueness checks are fast even with millions of rows.

To inspect the schema in full, see [`database/schema.sql`](database/schema.sql).

---

## Security

| Threat | Mitigation |
|---|---|
| SQL injection | PDO prepared statements with bound parameters throughout |
| CSRF | Token generated at session start, validated on every POST form |
| Duplicate voting | Server-side IP check before every vote insert |
| XSS | Output escaped with `htmlspecialchars` in all views |
| Brute force | Bcrypt password hashing; rate limiting recommended for production |
| Session fixation | Session regenerated on login |

> **For production:** enable HTTPS, set strong session cookie flags (`Secure`, `HttpOnly`, `SameSite`), and add a rate-limiting layer (e.g. nginx `limit_req`).

---

## Troubleshooting

### The page shows "Database connection failed"
- Check that your DB host, name, user, and password are correct in `bootstrap/app.php` or your environment variables.
- If using Docker, make sure the `db` container is healthy before the app starts: `docker compose logs db`
- The Docker Compose file includes a health check — the app container will not start until MySQL is ready.

### Routes return 404 in Apache
- Confirm `mod_rewrite` is enabled: `sudo a2enmod rewrite && sudo systemctl restart apache2`
- Make sure `AllowOverride All` is set for the `public/` directory in your Apache config.
- The `.htaccess` file in `public/` must exist and be readable.

### AJAX calls fail in the browser
- Open DevTools → Network tab and look at the failing request.
- A `401` response means the session has expired — log in again.
- A CSRF error means the token is missing. Ensure the meta tag `csrf-token` is in the layout.

### IP restriction not working as expected
- Behind a reverse proxy (nginx, Cloudflare), the real client IP is in `HTTP_X_FORWARDED_FOR`. Check `VoteController.php` to confirm the correct header is being read.
- Test by checking the `ip_address` column in the `votes` table: `SELECT ip_address FROM votes ORDER BY id DESC LIMIT 10;`

### Docker — port already in use
```bash
# Find what is using port 8080
lsof -i :8080

# Run on a different port
./docker-run.sh 8888
```

### Resetting everything with Docker Compose
```bash
docker compose down -v   # removes containers and the DB volume
docker compose up -d     # starts fresh with a clean database
```

---

## Future Roadmap

- [ ] WebSocket support for push-based real-time updates (e.g. Laravel Reverb or Ratchet)
- [ ] Per-poll vote deadline (auto-close after a set time)
- [ ] Multiple votes per user (ranked-choice or multi-select)
- [ ] Email notifications when a vote is released
- [ ] Advanced analytics — charts, export to CSV
- [ ] Dark mode toggle
- [ ] Rate limiting on the voting endpoint
- [ ] Multi-language / i18n support

---

## License

This project is open source under the [MIT License](LICENSE).

---

**Built with ❤️ using PHP, MySQL, Bootstrap 5, and AJAX**
