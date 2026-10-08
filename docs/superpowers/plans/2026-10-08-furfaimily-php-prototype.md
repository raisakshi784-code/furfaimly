# FurFaimily PHP Prototype Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convert static HTML prototype to a PHP/MySQL app with Google Auth, Matchmaker Quiz, and QR Codes for a live college presentation.

**Architecture:** Basic PHP scripts running on a local server. Files are converted from `.html` to `.php`. A central `db.php` manages MySQL connections, and `auth.php` configures the Google API Client.

**Tech Stack:** PHP 8+, MySQL, HTML/CSS/Vanilla JS, Google API Client (PHP).

**Spec:** `docs/superpowers/specs/2026-10-08-furfaimily-php-prototype-design.md`

## Global Constraints
- Target environment: Local XAMPP/MAMP.
- All primary views must be `.php` to support server-side rendering.
- UI must maintain existing styling while adding UI/UX pro-max improvements (touch targets, loading states).

## Review Focus
- **Google Auth rejection/failure**: Must redirect back to sign.php cleanly without crashing.
- **Database offline**: Should fail gracefully with a user-friendly error message, not a PDO exception stack trace.
- **Empty Pets Database**: Must show a "No pets available" message rather than a broken layout.

---

### Task 1: Environment Setup & Database Initialization

**Files:**
- Create: `HTML/db.php`
- Modify: `HTML/*.html` (Rename to `.php`)

**Interfaces:**
- Produces: `$pdo` connection object (used by all data-fetching scripts).

- [ ] **Step 1: Write database configuration**
Implement `HTML/db.php` using PDO to connect to `furfamily_db`. Ensure it catches `PDOException` and displays a clean error.
- [ ] **Step 2: Rename static files**
Rename all `.html` files in the `HTML/` directory to `.php`. Update internal links (e.g., `href="index.html"` to `href="index.php"`).
- [ ] **Step 3: Commit**
```bash
git add HTML/
git commit -m "chore: convert html to php and setup db connection"
```

### Task 2: Google Authentication

**Files:**
- Create: `HTML/callback.php`
- Modify: `HTML/sign.php`

**Interfaces:**
- Produces: `$_SESSION['user']` array containing user data (id, name, email, avatar).

- [ ] **Step 1: Update sign.php UI**
Remove email/password inputs. Add a single "Continue with Google" button wrapped in an anchor linking to the Google Auth URL.
- [ ] **Step 2: Write callback logic**
Implement `HTML/callback.php` to handle the Google OAuth response. Fetch user profile, insert/update in `users` table, and set `$_SESSION['user']`.
- [ ] **Step 3: Handle auth failure**
Add logic in `callback.php` to redirect back to `sign.php?error=auth_failed` if the user denies access.
- [ ] **Step 4: Commit**
```bash
git add HTML/sign.php HTML/callback.php
git commit -m "feat: implement google oauth flow"
```

### Task 3: Dynamic Navbar & Pet Listings

**Files:**
- Modify: `HTML/index.php`
- Modify: `HTML/Cat.php` (and other category pages)

**Interfaces:**
- Consumes: `$_SESSION['user']`
- Consumes: `$pdo` from `db.php`

- [ ] **Step 1: Navbar session logic**
Add `session_start()` to `index.php`. Conditionally render the 'Sign In' button or the user's avatar + 'Log Out' button based on `$_SESSION['user']`.
- [ ] **Step 2: Dynamic pet listing**
In a category page (e.g., `Cat.php`), write a PDO query to `SELECT * FROM pets WHERE category='Cat'`. Loop through results to render pet cards.
- [ ] **Step 3: Commit**
```bash
git add HTML/index.php HTML/Cat.php
git commit -m "feat: dynamic navbar and db-driven pet listings"
```

### Task 4: Matchmaker Quiz

**Files:**
- Create: `HTML/quiz.php`
- Create: `HTML/quiz-result.php`

**Interfaces:**
- Consumes: User POST data (quiz answers)
- Produces: Matched pet profile UI

- [ ] **Step 1: Create Quiz UI**
Write `HTML/quiz.php` with a `<form>` containing 3 simple questions (e.g., home size, activity level).
- [ ] **Step 2: Process Quiz Results**
Write `HTML/quiz-result.php` that calculates a score based on POST data, queries the `pets` table matching the score to `trait_tag`, and displays the recommended pet.
- [ ] **Step 3: Commit**
```bash
git add HTML/quiz.php HTML/quiz-result.php
git commit -m "feat: ai pet matchmaker quiz"
```

### Task 5: QR Codes & Dark Mode

**Files:**
- Modify: `HTML/Cat.php`
- Create: `CSS/dark-mode.css`
- Create: `HTML/js/dark-mode.js`

- [ ] **Step 1: Add interactive QR Codes**
In the pet rendering loop, add an `<img>` tag where `src` points to `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://localhost/FURFAIMILY/HTML/adopt.php?id={pet_id}`.
- [ ] **Step 2: Implement Dark Mode toggle**
Write `CSS/dark-mode.css` defining CSS variables. Write `dark-mode.js` to toggle a `dark` class on the `<body>` and save preference to `localStorage`. Add toggle button to navbar.
- [ ] **Step 3: Commit**
```bash
git add HTML/Cat.php CSS/ HTML/js/
git commit -m "feat: qr codes and dark mode ui polish"
```
