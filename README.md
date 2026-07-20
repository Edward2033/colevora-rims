# 🍽️ Colevora Restaurant Management System (RIMS)

A comprehensive, full-stack restaurant management system built with Laravel 12, Livewire 4, and modern web technologies.

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-4-pink.svg)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## ✨ Features

### 🎯 Core Functionality
- **Multi-Role Authentication** - Administrator, Manager, Chef, Waiter, Cashier, Inventory Officer, Customer
- **Order Management** - Complete order lifecycle from creation to payment
- **Inventory Management** - Stock tracking, alerts, and purchase orders
- **Table Management** - Real-time table status and reservations
- **Payment Processing** - Multiple payment methods (Cash, Card, Mobile)
- **Reports & Analytics** - Sales, orders, customers, inventory analytics
- **CMS System** - Hero slides, pages, testimonials, site settings

### 👥 User Dashboards
- **Admin** - Full system control, reports, user management
- **Manager** - Operations oversight, approvals, analytics
- **Chef** - Kitchen orders, preparation tracking
- **Waiter** - Table management, order service
- **Cashier** - Payment processing, billing
- **Inventory Officer** - Stock management, purchase orders
- **Customer** - Menu browsing, cart, orders, reservations

---

## 🚀 Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | Livewire 4, Flux UI, Alpine.js |
| **Styling** | Tailwind CSS 4 |
| **Database** | MySQL 8.0 / MariaDB 10+ |
| **Authentication** | Laravel Breeze + Custom RBAC |
| **Testing** | Pest 3, PHPUnit 11 |
| **Code Quality** | Laravel Pint |

---

## 📋 Requirements

- **PHP** 8.2 or higher
- **MySQL** 8.0+ or **MariaDB** 10.6+
- **Composer** 2.x
- **Node.js** 18+ and **NPM** 9+
- **Apache** or **Nginx** web server
- **Extensions**: PDO, MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

---

## 🔧 Installation

### 1️⃣ Clone Repository
```bash
git clone https://github.com/Edward2033/colevora-rims.git
cd colevora-rims
```

### 2️⃣ Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3️⃣ Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env file with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=colevora_rims
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4️⃣ Setup Database
```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 5️⃣ Link Storage
```bash
php artisan storage:link
```

### 6️⃣ Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7️⃣ Optimize Application
```bash
php artisan optimize
```

### 8️⃣ Serve Application

**Option A: Using PHP Built-in Server**
```bash
php artisan serve
# Access at: http://localhost:8000
```

**Option B: Using XAMPP/Apache**
- Configure virtual host or access via: `http://localhost/colevora-rims/public`

---

## 👤 Default Accounts

After seeding, you can login with these accounts:

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@colevora.com | password |
| Manager | manager@colevora.com | password |
| Chef | chef@colevora.com | password |
| Waiter | waiter@colevora.com | password |
| Cashier | cashier@colevora.com | password |
| Inventory Officer | inventory@colevora.com | password |
| Customer | customer@colevora.com | password |

> ⚠️ **IMPORTANT:** Change all default passwords immediately after first login!

---

## 📂 Project Structure

```
colevora-rims/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form requests
│   ├── Livewire/             # Livewire components
│   ├── Models/               # Eloquent models
│   └── Services/             # Business logic services
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── resources/
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript
│   └── views/
│       ├── livewire/         # Livewire component views
│       └── components/       # Blade components
├── routes/
│   └── web.php               # Web routes
├── tests/                    # Test suites
└── public/                   # Public assets
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

---

## 🛠️ Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# View all routes
php artisan route:list

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Fix code style
vendor/bin/pint

# Check migration status
php artisan migrate:status

# Database tinker
php artisan tinker
```

---

## 📊 Database Schema

The system includes 41 tables covering:
- **Authentication** - users, roles, permissions, sessions
- **Restaurant** - tables, reservations, categories, food
- **Orders** - orders, order_items, payments, carts
- **Inventory** - inventory_items, suppliers, purchases, stock_transactions
- **CMS** - hero_slides, pages, testimonials, site_settings
- **System** - notifications, audit_logs, jobs, cache

---

## 🔒 Security Features

- ✅ CSRF protection
- ✅ XSS prevention
- ✅ SQL injection protection (Eloquent ORM)
- ✅ Password hashing (bcrypt)
- ✅ Role-based access control (RBAC)
- ✅ Session management
- ✅ Email verification support
- ✅ OTP verification
- ✅ Audit logging

---

## 🎨 UI Features

- 🌓 Dark mode support
- 📱 Fully responsive design
- ⚡ Real-time updates (Livewire)
- 🎯 Interactive dashboards
- 📊 Charts and analytics
- 🔔 Notifications system
- 🖼️ Image upload support
- 🎭 Modal dialogs
- 📋 Data tables with sorting/filtering

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY`
- [ ] Configure proper database credentials
- [ ] Set up HTTPS/SSL certificate
- [ ] Configure mail server (not 'log')
- [ ] Change all default passwords
- [ ] Set up automated backups
- [ ] Configure queue worker
- [ ] Set up scheduled tasks (cron)
- [ ] Optimize caches (`php artisan optimize`)
- [ ] Build production assets (`npm run build`)

### Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_strong_password

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
```

---

## 📖 Documentation

Additional documentation available in the `/docs` directory (coming soon):
- User Guide
- API Documentation
- Developer Guide
- Deployment Guide

For migration reports and technical details, see:
- `DATABASE-MIGRATION-REPORT.md`
- `DATABASE-SCHEMA-SYNC-REPORT.md`
- `MYSQL-MIGRATION-COMPLETE.md`

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🐛 Known Issues

- None currently reported

Report issues at: [GitHub Issues](https://github.com/Edward2033/colevora-rims/issues)

---

## 💡 Support

For support, email dev@colevora.com or open an issue on GitHub.

---

## 🙏 Acknowledgments

- Laravel Framework
- Livewire
- Tailwind CSS
- Flux UI
- All open-source contributors

---

## 📸 Screenshots

_(Add screenshots of your application here)_

---

## 🔄 Changelog

### Version 1.0.0 (2026-07-20)
- ✅ Initial release
- ✅ Complete MySQL migration
- ✅ Multi-role authentication
- ✅ Full restaurant management features
- ✅ Reports and analytics
- ✅ Dark mode support
- ✅ Responsive design

---

**Built with ❤️ using Laravel 12**

Repository: [https://github.com/Edward2033/colevora-rims](https://github.com/Edward2033/colevora-rims)
