# MuslimSystem Backend API

MuslimSystem is a feature-rich backend application built with **Laravel 12**. It serves as the core API framework for the MuslimSystem mobile application (Flutter), providing robust endpoints for user management, gamification, prayer times, and more.

## 🚀 Key Features

- **User Authentication & Authorization**: Secure API access managed via Laravel Sanctum.
- **Profile Management**: Comprehensive profile endpoints tracking avatars, user activity, and balance.
- **Gamification & Progress Tracking**: 
  - Dynamic Quran reading progress handling.
  - Daily progress, prayer countdowns, and Islamic insights integration.
- **Affiliate & Referral System**: Automated referral code generation, referral tracking, and commission calculations.
- **Payment Gateway (Plink)**: Webhook and inquiry processing to handle accurate transaction settling (`SETLD`) and automated account activation.
- **Adzan & Prayer Times**: Precision management for Adzan notifications and schedules.
- **Database**: Optimized for **PostgreSQL** (with a smooth migration history from MySQL).

## 🧰 Tech Stack & Requirements

- **PHP**: `^8.2`
- **Framework**: Laravel `^12.0`
- **Database**: PostgreSQL
- **Dependency Management**: Composer, NPM

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd MuslimSystem
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configure the environment:**
   ```bash
   cp .env.example .env
   ```
   *Important: Update your `.env` file with correct PostgreSQL database credentials, Google API keys, and Plink payment gateway secrets.*

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the local development server:**
   ```bash
   # This custom script runs serve, queue, logs, and vite concurrently
   composer run dev
   ```
   *Alternatively, run `php artisan serve`.*

## 🧪 Testing
To run the automated test suite, use the built-in Composer script:
```bash
composer run test
```
Or directly via Artisan:
```bash
php artisan test
```

## 📄 License
This application is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
