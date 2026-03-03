# Mini Wallet API

A robust RESTful API for a digital wallet application built with Laravel, enabling users to securely manage their accounts, top-up balances, transfer funds to other users, and track their transaction histories.

## 🚀 Features
- **User Authentication**: Secure registration, login, and token-based authentication using Laravel Sanctum.
- **Profile Management**: Update account details and securely change passwords.
- **Wallet Operations**: Real-time balance checking and wallet statistics.
- **Transactions**:
  - Top-up account balance.
  - Securely transfer funds to other users across the platform.
  - View full, detailed transaction history.
- **User Directory**: Search functionality to find users when making transfers.

## 🛠️ Tech Stack
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Authentication**: Laravel Sanctum API Tokens
- **Database**: Supports MySQL / PostgreSQL / SQLite (Configurable)

## 📦 Installation & Setup

1. **Clone the repository** (if applicable):
   ```bash
   git clone <repository_url>
   cd mini-wallet
   ```

2. **Install PHP and Node dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**:
   Copy the example environment file and configure your database settings.
   ```bash
   cp .env.example .env
   ```
   *Make sure to update your `DB_CONNECTION` and database credentials in `.env` if using MySQL/PostgreSQL.*

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**:
   Execute the migrations to set up the `users`, `transactions`, and `personal_access_tokens` tables.
   ```bash
   php artisan migrate
   ```

6. **Start the Development Server**:
   ```bash
   php artisan serve
   ```
   The API will now be accessible at `http://127.0.0.1:8000`.

---

## 📡 API Endpoints Overview

All responses are typically returned in `JSON` format. 

### Public Routes
- **`POST`** `/api/register` - Create a new user account (Requires: username, email, password)
- **`POST`** `/api/login` - Authenticate user and receive a Sanctum Bearer token

### Protected Routes (Requires Bearer Token)
*All routes below require the `Authorization: Bearer <token>` header.*

**User & Profile**
- **`GET`** `/api/user/profile` - Retrieve the currently authenticated user's details
- **`POST`** `/api/user/update` - Update the user's profile information
- **`PUT`** `/api/user/change-password` - Update the user's password
- **`POST`** `/api/logout` - Revoke current access token and log out

**Wallet**
- **`GET`** `/api/balance` - Retrieve current wallet balance
- **`GET`** `/api/users/search` - Search a list of users by username/email (useful for finding a payee)
- **`GET`** `/api/wallet/stats` - Get user's wallet statistics (e.g., total inflow/outflow)

**Transactions**
- **`GET`** `/api/transactions` - Retrieve a paginated list of the user's transactions
- **`GET`** `/api/transactions/{id}` - Get full details of a specific transaction
- **`POST`** `/api/topup` - Add funds to the wallet
- **`POST`** `/api/transfer` - Transfer balance to a related user

## 📝 Usage Example (API Testing)

When testing via Postman or cURL, remember to pass the token generated from `/api/login` to all protected routes in the Headers:

```http
Accept: application/json
Authorization: Bearer 1|YourGeneratedSanctumTokenHere...
```

## ⚖️ License
This project utilizes the Laravel framework, which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
