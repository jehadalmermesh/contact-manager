# Contact Manager: Custom PHP MVC (Assignment 1)

**Student:** Jehad Almarzooq · **ID:** 202106712

A small contact manager built on a hand-written MVC structure (no frameworks) with PHP 8, MySQL and PDO.
Anyone can view the contact list. Only the logged-in admin can add, edit or delete contacts.

---

## Features
- `contacts` table (`id, name, email, phone, created_at`) with full CRUD: list, add, edit, delete
- Single admin login/logout with session-based access control
- Front controller (`index.php`) with a whitelist router (`?route=contacts/edit&id=3`)
- Server-side validation with per-field error messages
- Flash messages after every action

## Folder structure
```
contact-manager/
├── index.php                 # Front controller: boots the app and routes every request
├── config/
│   └── config.example.php    # Copy to config.php (git-ignored) and add DB credentials
├── core/
│   ├── Database.php          # Shared PDO connection (real prepared statements)
│   ├── Controller.php        # Base controller: view rendering, POST + CSRF guard, 404
│   ├── Auth.php              # Login state, session regeneration, requireLogin()
│   ├── Csrf.php              # CSRF token generation and verification
│   └── helpers.php           # e() escaping, url(), redirect(), flash()
├── models/
│   ├── Contact.php           # Contact queries + validation
│   └── User.php              # Admin lookup, password_verify, rehash
├── controllers/
│   ├── ContactController.php
│   └── AuthController.php
├── views/
│   ├── layout/ (header.php, footer.php)
│   ├── contacts/ (index.php, form.php)
│   └── auth/login.php
├── public/css/style.css
├── database/schema.sql       # Creates the database, tables and sample contacts
└── scripts/create_admin.php  # CLI: creates the admin with a hashed password
```

## Setup
1. Requirements: PHP 8.1+ with `pdo_mysql`, and MySQL 8 or MariaDB (XAMPP works).
2. Import the database:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. Configure the connection:
   ```bash
   cp config/config.example.php config/config.php
   # then edit config/config.php with your DB user/password
   ```
4. Create the admin user. You'll be asked for a password (min 8 characters):
   ```bash
   php scripts/create_admin.php admin
   ```
5. Run it:
   ```bash
   php -S localhost:8000
   ```
   Open http://localhost:8000. On XAMPP, put the folder in `htdocs/` and open `http://localhost/contact-manager/`.

## Routes
| Route | Method | Access | Action |
|---|---|---|---|
| `contacts` | GET | public | List contacts |
| `contacts/create` | GET | admin | Add form |
| `contacts/store` | POST | admin | Insert contact |
| `contacts/edit&id=N` | GET | admin | Edit form |
| `contacts/update&id=N` | POST | admin | Update contact |
| `contacts/delete&id=N` | POST | admin | Delete contact |
| `login` / `login/submit` | GET / POST | guest | Admin login |
| `logout` | POST | admin | Log out |

---

## Security list

| # | Measure | Where | What it prevents |
|---|---|---|---|
| 1 | **Prepared statements** with named, bound parameters for every query that takes input; `PDO::ATTR_EMULATE_PREPARES = false` so the database does the binding | `models/*.php`, `core/Database.php` | SQL injection |
| 2 | **Output escaping**: every dynamic value goes through `e()` (`htmlspecialchars`, `ENT_QUOTES`, UTF-8) | `core/helpers.php`, all views | Cross-site scripting (XSS) |
| 3 | **Password hashing** with `password_hash(PASSWORD_DEFAULT)` (bcrypt), checked with `password_verify`, auto-rehashed with `password_needs_rehash`. No plain-text password exists in code, SQL or Git. | `models/User.php`, `scripts/create_admin.php` | Credential theft if the DB leaks |
| 4 | **Server-side validation**: name 2–100 characters, `FILTER_VALIDATE_EMAIL`, phone regex, unique email, IDs checked with `FILTER_VALIDATE_INT` | `models/Contact.php`, `ContactController::findOr404()` | Bad or malicious data |
| 5 | **CSRF tokens** (`random_bytes`, compared with `hash_equals`) on every POST form, including login, logout and delete | `core/Csrf.php`, `Controller::requirePost()` | Cross-site request forgery |
| 6 | **Access control**: `Auth::requireLogin()` runs at the top of every add/edit/delete action, on the server, not just by hiding buttons | `controllers/ContactController.php` | Unauthorised changes |
| 7 | **State-changing actions are POST-only**; GET on delete returns 405 | `Controller::requirePost()` | Deletes triggered by links or crawlers |
| 8 | **Session hardening**: `session_regenerate_id(true)` on login, HttpOnly + SameSite cookie (Secure on HTTPS), full session destroy on logout | `index.php`, `core/Auth.php` | Session fixation / hijacking |
| 9 | **Login throttling**: 5 failures locks login for 5 minutes; one generic error message | `controllers/AuthController.php` | Brute force, username enumeration |
| 10 | **Route whitelist**: only routes listed in `index.php` can run; anything else is 404 | `index.php` | Calling arbitrary classes or files |
| 11 | **Error handling**: DB exceptions are logged; users see a generic message when `debug` is false | `index.php` | Leaking SQL or server details |
| 12 | **Secrets kept out of Git**: `config/config.php` is in `.gitignore`; only `config.example.php` is committed | `.gitignore` | Leaking DB credentials |
| 13 | **Security headers**: CSP, `X-Frame-Options: DENY`, `nosniff`, `Referrer-Policy` | `index.php` | Clickjacking, injected scripts |

### How I tested it
I ran these tests myself in Chrome on XAMPP (MariaDB 10.4, PHP 8.2) on 22 Nov 2026:

| # | Test | Result |
|---|---|---|
| 1 | Added a contact named `<script>alert(1)</script>` | Shown as plain text, no alert popup |
| 2 | Submitted name `a`, email `aaa`, phone `qqqwwe` | Three validation errors, nothing saved |
| 3 | Opened the delete URL directly with GET | "Method not allowed." Nothing deleted |
| 4 | Opened the edit page with `id=abc` | "Page not found." |
| 5 | Logged out, then opened the add-contact URL | Redirected to the login page |
| 6 | Logged in with a wrong password | "Invalid username or password." |

## Week-5 progress
I did not record any week-5 progress for this assignment. All work on this project was done and committed on 22 Nov 2026, as the commit history shows.

## Integrity statement
I, Jehad Almarzooq (202106712), declare that I did not share my code with other students or copy theirs.

**Use of AI:** I used an AI assistant (Claude, by Anthropic) to generate the initial project code (front controller, models, controllers, views, security helpers and SQL script) and the first draft of this README. I then set up, tested and modified the project myself, and I understand how each part works.

**My own work:**
- Set up the project on XAMPP (database import, config file, admin account)
- Changed the colour scheme (`public/css/style.css`)
- Added a footer with my name and student ID
- Added the total contacts count to the list page
- Replaced the sample data and switched sample emails to the `example.com` domain
- Ran the six security tests listed above and recorded the results
- Managed version control with Git and GitHub

**Other sources:**
- PHP Manual: https://www.php.net/manual/
- OWASP Cheat Sheets: https://cheatsheetseries.owasp.org/
- Course lecture notes, topics 3–7

Signed: Jehad Almarzooq    Date: 22 Nov 2026

## Integrity statement
<!-- Edit this so it is accurate, then sign it. Keep the AI declaration. The course treats UNDECLARED AI code as plagiarism. -->

I, Jehad Almarzooq (202106712), declare that this submission is my own work, apart from the sources listed below. I did not share my code with other students or copy theirs.

**Use of AI:** I used Claude (Anthropic) to help generate the initial code structure and parts of the implementation (front controller, models, controllers, views, CSRF/auth helpers) and the first draft of this README. I have read and understood all of the code, tested it myself, and can explain every part of it. Changes I made myself: _[list them]_.

**Other sources:**
- PHP Manual: PDO, prepared statements, `password_hash`, `htmlspecialchars`, `filter_var`. https://www.php.net/manual/
- OWASP Cheat Sheets: SQL Injection Prevention, XSS Prevention, CSRF Prevention, Session Management. https://cheatsheetseries.owasp.org/
- Course lecture notes, topics 3–7 (MySQL with PDO, custom MVC).

Signed: ____________________  Date: ____________
