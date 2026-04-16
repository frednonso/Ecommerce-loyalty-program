# E-Commerce Loyalty Program Backend

This is the backend implementation of the E-Commerce Loyalty Program, built with Laravel. It features an event-driven architecture that listens for user purchase events and automatically unlocks relevant achievements and badges.

## Features

- **Event-Driven Architecture**: Uses Laravel events and listeners to decouple purchase processing from achievement/badge logic.
- **Achievements**: Unlocked based on the number of purchases.
- **Badges**: Unlocked based on the number of achievements earned.
- **Mock Cashback Payments**: Simulates a 300 Naira cashback reward to users when a new badge is unlocked, logging it to a custom `storage/logs/cashback.log`.
- **RESTful API**: Exposes a clean endpoint to consume user progress data for a React frontend.

## API Endpoint Requirements Satisfied

The `GET /api/users/{user}/achievements` endpoint perfectly matches the assessment requirements, returning the following JSON structure:

```json
{
  "success": true,
  "message": "User progress retrieved successfully.",
  "data": {
    "unlocked_achievements": ["First Purchase", "5 Purchases"],
    "next_available_achievements": ["10 Purchases"],
    "current_badge": "Bronze",
    "next_badge": "Silver",
    "remaining_to_unlock_next_badge": 6
  }
}
```

---

## Setup Instructions (for XAMPP)

Follow these steps to set up and run the backend locally using XAMPP on Windows.

### Prerequisites
1. Ensure **XAMPP** is installed and running.
2. Ensure you have **Composer** installed globally.

### 1. Start XAMPP Services
Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.

### 2. Prepare the Database
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click on **Databases** and create a new database named exactly: `ecommerce_loyalty`
3. Click "Create".

### 3. Install Dependencies
Open your terminal (e.g., Command Prompt, PowerShell, or Git Bash), navigate to the `backend` folder, and run:

```bash
cd c:\xampp\htdocs\Ecommerce-Loyalty-Program\backend
composer install
```

### 4. Configure Environment Variables
Copy the example environment file and create your own `.env` file:

```bash
cp .env.example .env
```
*(If using Windows Command Prompt, use `copy .env.example .env` instead)*

Open the `.env` file in your code editor and verify that your database credentials match XAMPP's defaults:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_loyalty
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key
Run the following Artisan command to set your application key:

```bash
php artisan key:generate
```

### 6. Run Migrations & Seed the Database
Run the database migrations and populate the database with achievements, badges, and demo users:

```bash
php artisan migrate:fresh --seed
```

**Note**: The seeder creates 3 test users with varying purchase histories so you can test different states immediately:
- User 1 (`alice@example.com`): 1 Purchase (Beginner Badge)
- User 2 (`bob@example.com`): 7 Purchases (Bronze Badge)
- User 3 (`carol@example.com`): 26 Purchases (Gold Badge)
All user passwords are: `password`

### 7. Run Background Queue (Optional but Recommended)
Because the application uses `ShouldQueue` for processing achievements and badges asynchronously, you need to run the queue worker. In a separate terminal window, run:

```bash
php artisan queue:work
```
*(Alternatively, for simple local testing without running the queue command, you can change `QUEUE_CONNECTION=database` to `QUEUE_CONNECTION=sync` in your `.env` file).*

### 8. Access the API
Since you are using XAMPP, the application is already served by Apache. You can access the API endpoint via your browser or Postman at:

```
http://localhost/Ecommerce-Loyalty-Program/backend/public/api/users/1/achievements
```

*(You can replace `1` with `2` or `3` to see different user progress states).*

---

## Testing

The project includes both Feature and Unit tests for the loyalty logic.

To run the test suite:
```bash
php artisan test
```

Tests run using an in-memory SQLite database, so they won't interfere with your XAMPP MySQL database.
