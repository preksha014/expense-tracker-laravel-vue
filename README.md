# Expense Tracker

A full-stack expense tracking application built with Laravel and Vue.js.

![Expense Tracker](https://github.com/preksha014/expense-tracker-laravel-vue/raw/main/public/screenshot.png)

## Features

- **User Authentication:** Register, login, and manage your personal expense data
- **Dashboard:** Visual representation of your expenses with charts and statistics
- **Expense Management:** Add, edit, delete, and categorize expenses
- **Income Tracking:** Record and monitor your income sources
- **Reports:** Generate detailed reports based on categories, time periods, and more
- **Budget Planning:** Set budgets and get alerts when nearing limits
- **Responsive Design:** Works seamlessly on desktop and mobile devices

## Technology Stack

- **Backend:** Laravel 10
- **Frontend:** Vue.js 3
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **UI Framework:** Tailwind CSS
- **Charts:** Chart.js

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM or Yarn
- MySQL

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/preksha014/expense-tracker-laravel-vue.git
   cd expense-tracker-laravel-vue
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   # or
   yarn install
   ```

4. Set up environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=expense_tracker
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

7. Build the frontend assets:
   ```bash
   npm run dev
   # or
   yarn dev
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Visit `http://localhost:8000` in your browser.

## Usage

1. **Dashboard:** View your financial summary and recent transactions
2. **Add Expense:** Record new expenses with details like amount, date, category, and notes
3. **Reports:** Generate and export reports based on your preferences
4. **Budget Management:** Create and manage budget limits for different categories

## API Documentation

The application provides a RESTful API for managing expenses:

- `GET /api/expenses` - Get all expenses
- `POST /api/expenses` - Create a new expense
- `GET /api/expenses/{id}` - Get a specific expense
- `PUT /api/expenses/{id}` - Update an expense
- `DELETE /api/expenses/{id}` - Delete an expense

For more details, refer to the API documentation at `/api/documentation`.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Tailwind CSS](https://tailwindcss.com)

