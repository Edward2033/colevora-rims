# Colevora Restaurant ERP System

A comprehensive Restaurant Management System built with Laravel 12, Livewire 3, and Tailwind CSS.

## 🚀 Live Demo

**GitHub Repository**: [https://github.com/Edward2033/colevora-rims](https://github.com/Edward2033/colevora-rims)

## 📋 Features

### Customer Portal
- Menu browsing with search and filters
- Shopping cart system
- Online ordering
- Order tracking
- Reservation system
- Account management
- Order history

### Admin Dashboard
- Complete order management
- User and role management
- Food and category management
- Inventory tracking
- Purchase order system
- Supplier management
- Table management
- Payment tracking
- CMS (Hero slides, pages, settings)
- Reports and analytics
- Audit logs
- Dark mode UI

### Employee Portals
- **Chef Dashboard**: Order preparation and kitchen management
- **Waiter Dashboard**: Table service and order taking
- **Cashier Dashboard**: Payment processing
- **Inventory Officer Dashboard**: Stock management
- **Receptionist Dashboard**: Reservation management

### Design Features
- Fully responsive (mobile to desktop)
- Dark mode for admin and employee interfaces
- Light mode for customer and public site
- Premium gold color scheme
- Touch-friendly interface
- Modern UI with Flux UI components

## 🛠️ Tech Stack

- **Framework**: Laravel 12.x
- **Frontend**: Livewire 3 + Volt
- **Styling**: Tailwind CSS
- **UI Components**: Flux UI
- **Database**: MySQL 8.0
- **Build Tool**: Vite
- **Icons**: Heroicons

## 📦 Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or 8.0+
- Composer
- Node.js & NPM
- Apache/Nginx web server

## 🔧 Installation

### 1. Clone Repository

```bash
git clone https://github.com/Edward2033/colevora-rims.git
cd colevora-rims
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=colevora_rims
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Import Database

**Option A: Using SQL File**
```bash
# Import the database dump
mysql -u your_username -p colevora_rims < database/colevora_rims.sql
```

**Option B: Using Migrations**
```bash
# Run migrations and seeders
php artisan migrate --seed
```

### 6. Storage Link

```bash
# Create storage symbolic link
php artisan storage:link
```

### 7. Build Assets

```bash
# Build frontend assets
npm run build

# Or for development with hot reload
npm run dev
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🔐 Default Credentials

### Admin
```
Email: edwardcole203@gmail.com
Password: password
```

### Employees
```
Chef:              chef@colevora.com / password
Waiter:            waiter@colevora.com / password
Cashier:           cashier@colevora.com / password
Inventory Officer: inventory@colevora.com / password
Receptionist:      receptionist@colevora.com / password
```

⚠️ **Change all passwords immediately in production!**

## 🌐 Deployment to InfinityFree

### Quick Deployment Guide

1. **Push to GitHub**
```bash
git add .
git commit -m "Ready for deployment"
git push origin main
```

2. **On InfinityFree**
- Login to control panel
- Open File Manager
- Navigate to `/htdocs/`
- Use Git Clone or manual upload

3. **Setup Environment**
```bash
# Copy and edit .env
cp .env.example .env

# Edit with InfinityFree credentials
nano .env
```

4. **Import Database**
- Open PHPMyAdmin
- Create database
- Import `database/colevora_rims.sql`

5. **Set Permissions**
```bash
chmod -R 755 storage bootstrap/cache
```

6. **Test Website**
Visit your domain and verify everything works

### InfinityFree Configuration

Edit `.env` with your InfinityFree details:

```env
APP_URL=https://yourdomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sql202.infinityfree.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📁 Project Structure

```
colevora-rims/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Livewire/
│   └── Models/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── colevora_rims.sql
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   └── components/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── auth.php
├── public/
│   ├── storage/
│   └── build/
└── storage/
    └── app/public/
```

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Author

**Edward Cole**
- Email: edwardcole203@gmail.com
- GitHub: [@Edward2033](https://github.com/Edward2033)

## 🙏 Acknowledgments

- Laravel Framework
- Livewire
- Tailwind CSS
- Flux UI Components
- InfinityFree Hosting

## 📞 Support

For support, email edwardcole203@gmail.com or open an issue in the GitHub repository.

---

**⭐ If you find this project useful, please give it a star on GitHub!**
