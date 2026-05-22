# Mobeecars Assessment

This repository contains a full-stack project consisting of:

* Mobile Application (React Native / Expo) → `mobeecars-app`
* Backend API (Laravel) → `mobeecars-web`

---

## Requirements

Make sure you have the following installed:

* Node.js >= 22
* PHP >= 7.4
* Composer
* Expo Go (for mobile testing on physical device)
* No global Expo CLI required (uses `npx expo`)
* SQLite support enabled in PHP

---

## Project Structure

```
mobeecars-assesment/
├── mobeecars-app/   # React Native (Expo) mobile app
└── mobeecars-web/   # Laravel backend API
```

---

# Backend Setup (Laravel)

## 1. Install dependencies

```bash
cd mobeecars-web
composer install
```

---

## 2. Environment setup

Ensure `.env` file exists in `mobeecars-web`.

Configure SQLite database:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

If the SQLite file does not exist, create it manually:

```bash
touch database/database.sqlite
```

---

## 3. Run migrations and seed database

```bash
php artisan migrate
php artisan db:seed
```

This will:

* Create all required tables
* Seed default users

---

## 4. Start backend server

To allow access from mobile devices on the same network, run:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Backend will be accessible at:

```
http://YOUR_LOCAL_IP:8000
```

---

# Mobile App Setup (Expo)

## 1. Install dependencies

```bash
cd mobeecars-app
npm install
```

---

## 2. Configure environment variables

Update `.env` inside `mobeecars-app`:

```
EXPO_PUBLIC_URL=http://YOUR_LOCAL_IP:8000
EXPO_PUBLIC_API_URL=http://YOUR_LOCAL_IP:8000/api
```

Example:

```
EXPO_PUBLIC_URL=http://192.168.1.7:8000
EXPO_PUBLIC_API_URL=http://192.168.1.7:8000/api
```

> Important: Use your local machine IP address instead of `localhost` when running on a physical device using Expo Go.

---

## 3. Start mobile app

```bash
npx expo start
```

Then scan the QR code using **Expo Go**.

---

# Default Users (Seeder)

After running `php artisan db:seed`, the following users will be available:

## Admin (Web only)

* Email: [admin@admin.com](mailto:admin@admin.com)
* Password: admin

## User (Mobile only)

* Email: [user@user.com](mailto:user@user.com)
* Password: user

---

# Login Rules

* Web application → [admin@admin.com](mailto:admin@admin.com) only
* Mobile application → [user@user.com](mailto:user@user.com) only

---

# Running Summary

## Backend

```bash
cd mobeecars-web
composer install
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0 --port=8000
```

## Mobile

```bash
cd mobeecars-app
npm install
npx expo start
```

---

# Notes

* Ensure backend is running before starting mobile app
* Ensure both devices are on the same network when using Expo Go
* SQLite database file will be created automatically if it does not exist
