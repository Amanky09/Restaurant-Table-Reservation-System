# Restaurant Table Reservation System

La Tavola is a PHP and MySQL reservation demo. Customers can submit a booking, look it up by their exact email address or phone number, and cancel it. An admin can filter reservations, update status, and delete records.

## Requirements

- PHP 8.0+ with the `mysqli` extension
- MySQL or MariaDB
- A MySQL user allowed to create the `restaurant_db` database and tables (or an existing database selected with `DB_NAME`)

## Run locally

Set the database connection and an admin password in your shell, then start PHP's development server:

```bash
export DB_HOST=localhost DB_USER=root DB_PASSWORD='your-mysql-password' DB_NAME=restaurant_db
export ADMIN_PASSWORD='choose-a-long-unique-password'
php -S localhost:8000
```

Open <http://localhost:8000>. The admin login is at <http://localhost:8000/login.php>. The database table is created on first request. You can inspect it with phpMyAdmin or another MySQL client.

## Notes

- Admin changes require a login and CSRF token. Form inputs use prepared SQL statements and displayed customer data is escaped.
- This is a local demo, not a deployed reservation service. The customer lookup uses possession of the exact email or phone number rather than verified identity. Add email verification and rate limiting before publishing customer data online.
- Keep `ADMIN_PASSWORD` and database credentials outside the repository. Do not use the built-in PHP server for production traffic.
