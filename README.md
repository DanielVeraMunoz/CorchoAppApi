# CorchoApp API

REST API backend for **CorchoApp**, a neighborhood community board application where residents can post notes, offer help, leave comments, and thank their neighbors.

Built with **Laravel 13** + **Laravel Passport** (OAuth2).

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features](#features)
- [Data Model](#data-model)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [API Endpoints](#api-endpoints)
- [Roles & Permissions](#roles--permissions)
- [Seed Users](#seed-users)
- [Running Tests](#running-tests)
- [Project Structure](#project-structure)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3+ |
| Framework | Laravel 13 |
| Authentication | Laravel Passport (OAuth2) |
| Database | MySQL |
| Testing | PHPUnit |
| Documentation | Scribe |

---

## Features

- JWT-style token authentication via **Laravel Passport**
- Full CRUD for **Notes**, **Comments**, and **Users**
- **Note lifecycle**: active → completed → reopened
- **Thanks system**: note authors can thank the neighbors who helped them
- **Role-based access control**: `admin` and `user` roles
- **Community stats** and **top helpers** leaderboard
- Predefined **Categories** and **Communities**
- 56 passing tests

---

## Data Model

```
Community
  └── has many Users

User (belongs to Community, has role: admin|user)
  ├── has many Notes
  ├── has many Comments
  ├── has many Thanks (given)
  └── has many Thanks (received)

Note (belongs to User, belongs to Category)
  ├── has many Comments
  └── has many Thanks

Comment (belongs to Note, belongs to User)

Thank (belongs to Note, giver: User, recipient: User)

Category (predefined, read-only)
```

---

## Getting Started

### Requirements

- PHP >= 8.3
- Composer
- MySQL
- XAMPP / Laravel Valet / any local server

### Installation

```bash
# Clone the repository
git clone <repo-url>
cd CorchoAppApi

# Install dependencies
composer install

# Copy environment file and configure it
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations and seed the database
php artisan migrate:fresh --seed

# Install Passport (generates OAuth keys)
php artisan passport:install

# Start the development server
php artisan serve
```

The API will be available at `http://localhost:8000/api`.

---

## Environment Variables

Create a `.env` file based on `.env.example` and fill in:

```env
APP_NAME=CorchoApp
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=corchoapp
DB_USERNAME=root
DB_PASSWORD=

PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=
```

> After running `php artisan passport:install`, copy the generated client ID and secret into the Passport env vars.

---

## API Endpoints

All endpoints require a `Bearer` token in the `Authorization` header, except **register** and **login**.

### Authentication

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/api/register` | Register a new user | No |
| POST | `/api/login` | Login and get access token | No |
| DELETE | `/api/logout` | Revoke current token | Yes |

### Notes

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/notes` | List all notes |
| POST | `/api/notes` | Create a new note |
| GET | `/api/notes/{id}` | Get a single note |
| PUT | `/api/notes/{id}` | Update a note |
| DELETE | `/api/notes/{id}` | Delete a note |
| PATCH | `/api/notes/{id}/complete` | Mark note as completed |
| PATCH | `/api/notes/{id}/reopen` | Reopen a completed note |

### Comments

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/notes/{id}/comments` | List comments for a note |
| POST | `/api/notes/{id}/comments` | Add a comment to a note |
| PUT | `/api/comments/{id}` | Update a comment |
| DELETE | `/api/comments/{id}` | Delete a comment |

> Comments cannot be added to completed notes.

### Thanks

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/users/{id}/thanks` | List thanks received by a user |
| POST | `/api/users/{id}/thanks` | Give thanks to a user (`body: { note_id }`) |
| DELETE | `/api/thanks/{id}` | Remove a thanks |

> Only the note author can give thanks. The recipient is the neighbor who helped.

### Users

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/users` | List all users in the community |
| GET | `/api/users/{id}` | Get a user profile |
| PUT | `/api/users/{id}` | Update a user profile |
| DELETE | `/api/users/{id}` | Delete a user account |

### Stats

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/stats/community` | General community stats (notes, comments, thanks) |
| GET | `/api/stats/top-helpers` | Top users ranked by thanks received |

### Categories

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | List all predefined categories |

---

## Roles & Permissions

| Action | `user` | `admin` |
|---|---|---|
| Edit / delete own notes | Yes | Yes |
| Edit / delete any note | No | Yes |
| Edit / delete own comments | Yes | Yes |
| Edit / delete any comment | No | Yes |
| Update own profile | Yes | Yes |
| Update / delete any user | No | Yes |
| View all users | Yes | Yes |

---

## Seed Users

After running `php artisan migrate:fresh --seed`, the following test accounts are available:

| Email | Password | Role |
|---|---|---|
| admin@corcho.com | password | admin |
| demo@corcho.com | password | user |
| john@corcho.com | password | user |
| jane@corcho.com | password | user |

---

## Running Tests

```bash
php artisan test
```

The test suite covers authentication, CRUD operations, role-based access, note lifecycle, thanks system, and stats — **56 tests** in total.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/   # AuthController, NoteController, CommentController...
│   └── Middleware/        # IsAdmin
├── Models/                # User, Note, Comment, Thank, Category, Community
└── Services/              # StatsService (Service Layer pattern)

database/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
└── Feature/               # PHPUnit feature tests
```

---

