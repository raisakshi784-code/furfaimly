# FurFaimily PHP Prototype Design Spec

## 1. Overview
The goal of this project is to convert the existing static HTML/CSS "FurFaimily" pet adoption website into a dynamic, functional prototype backed by PHP and MySQL. This prototype is being developed for a college presentation tomorrow and will include several high-impact features (Wow Factors) to impress the judges.

## 2. Architecture & Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla). Files will be renamed from `.html` to `.php` to allow server-side logic execution.
- **Backend**: PHP 8+ running on a local server (XAMPP/MAMP).
- **Database**: MySQL.
- **Authentication**: Google OAuth 2.0 via the `google/apiclient` PHP library.

## 3. Database Schema (`furfamily_db`)
### `users` table
- `id` (INT, Primary Key, Auto Increment)
- `google_id` (VARCHAR, Unique): Stores the Google OAuth unique identifier.
- `name` (VARCHAR): User's full name from Google.
- `email` (VARCHAR): User's email from Google.
- `avatar` (VARCHAR): URL to the user's Google profile picture.

### `pets` table
- `id` (INT, Primary Key, Auto Increment)
- `name` (VARCHAR): Name of the pet.
- `category` (VARCHAR): e.g., 'Cat', 'Dog', 'Hamster'.
- `status` (VARCHAR): 'available' or 'adopted'.
- `trait_tag` (VARCHAR): E.g., 'active', 'calm', 'family'. Used by the Matchmaker Quiz.

## 4. Core Features
### Google Authentication
- Users will authenticate exclusively via Google Auth.
- The `sign.php` page will feature a single "Continue with Google" button.
- A callback script (`callback.php`) will handle the OAuth response, register new users, and establish a PHP session (`$_SESSION['user']`).

### Dynamic Navigation
- The navigation bar will read the session state.
- Unauthenticated users will see the "Sign In" button.
- Authenticated users will see their Google avatar, name, and a "Log Out" dropdown instead.

### Dynamic Data
- Pet listings on the main pages (e.g., `index.php`, category pages) will be fetched dynamically from the `pets` database table rather than being hardcoded.

## 5. Wow Factors (Presentation Highlights)
### AI Pet Matchmaker Quiz (`quiz.php`)
- A simple 3-question lifestyle quiz (e.g., house size, activity level).
- PHP logic will calculate a score and match the user with a pet whose `trait_tag` best fits their answers.

### Interactive QR Codes
- Pet profile sections will dynamically generate a QR code using a free external API (`api.qrserver.com/v1/create-qr-code`).
- This allows judges to interactively scan the site with their phones during the live demo.

### Dark Mode Toggle
- A site-wide Dark Mode implemented via CSS variables (`--bg-color`, `--text-color`).
- Controlled by a JavaScript toggle button in the navbar, persisting state via `localStorage`.

## 6. UI/UX Polish
- Form submission loading states (spinners on buttons).
- Accessible touch targets (minimum 44px for buttons).
- Error messages displayed inline rather than using native JavaScript `alert()` dialogs.
- Smooth scrolling and hover micro-interactions (e.g., scaling up pet cards on hover).
