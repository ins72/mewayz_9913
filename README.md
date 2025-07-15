<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Mewayz Platform

<div align="center">
  <img src="public/assets/image/others/branding-logo-dark.png" alt="Mewayz Logo" width="200">
  
  **All-in-One Business Platform for Modern Creators**
  
  [![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
  [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](docs/CONTRIBUTING.md)
</div>

## 🚀 About Mewayz

Mewayz is a comprehensive, enterprise-grade business platform designed to empower modern creators and businesses. Built with Laravel, it provides a seamless experience for managing your entire business ecosystem from a single, powerful dashboard.

### ✨ Key Features

- **🎯 Link in Bio**: Professional bio pages with custom domains
- **📊 Analytics Dashboard**: Comprehensive business intelligence
- **💳 Stripe Integration**: Secure payment processing
- **📱 Social Media Management**: Instagram, Twitter, Facebook integration
- **📧 Email Marketing**: Advanced campaign management
- **🛍️ E-commerce**: Complete store management
- **📚 Course Management**: Online learning platform
- **💼 CRM System**: Customer relationship management
- **📅 Booking System**: Appointment scheduling
- **🎨 Website Builder**: Drag-and-drop site creation
- **📄 Invoice Management**: Professional billing system
- **👥 Team Collaboration**: Multi-user workspace management

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    MEWAYZ PLATFORM                             │
│                 (Laravel Full-Stack)                           │
└─────────────────────────────────────────────────────────────────┘
           │                                    │
┌─────────────────┐                  ┌─────────────────┐
│   Laravel Web   │                  │   Laravel API   │
│   Frontend      │                  │   Backend       │
│   (Blade +      │                  │   (11 API       │
│   Livewire)     │                  │   Controllers)  │
└─────────────────┘                  └─────────────────┘
           │                                    │
           └────────────────┬───────────────────┘
                           │
                  ┌─────────────────┐
                  │   MySQL/        │
                  │   MariaDB       │
                  │   Database      │
                  └─────────────────┘
```

## 🛠️ Tech Stack

- **Backend**: Laravel 10.x, PHP 8.2+
- **Frontend**: Blade Templates, Livewire, Alpine.js
- **Database**: MySQL/MariaDB
- **Payment**: Stripe Integration
- **Email**: Laravel Mail + ElasticMail
- **Storage**: Local/Cloud Storage
- **Assets**: Vite.js, Tailwind CSS
- **Testing**: PHPUnit, Laravel Dusk

## 📦 Installation

### Requirements

- PHP 8.2 or higher
- MySQL 8.0 or MariaDB 10.4
- Composer
- Node.js 16+ & npm/yarn

### Quick Start

```bash
# Clone the repository
git clone https://github.com/mewayz/platform.git
cd platform

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# Then run migrations
php artisan migrate

# Build assets
npm run build

# Start the development server
php artisan serve --host=0.0.0.0 --port=8001
```

### Environment Configuration

```env
# Application
APP_NAME="Mewayz Platform"
APP_URL=http://localhost:8001
APP_ENV=local
APP_DEBUG=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz_platform
DB_USERNAME=root
DB_PASSWORD=

# Stripe
STRIPE_API_KEY=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.elasticemail.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## 🚀 Deployment

### Production Deployment

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Docker Deployment

```dockerfile
FROM php:8.2-fpm

# Install dependencies and configure Laravel
# See docs/DEPLOYMENT.md for complete Docker setup
```

## 📚 Documentation

- [📖 User Guide](docs/04-USER-GUIDE.md)
- [🏗️ Technical Architecture](docs/02-TECHNICAL-ARCHITECTURE.md)
- [🔌 API Reference](docs/03-API-REFERENCE.md)
- [🚀 Deployment Guide](docs/DEPLOYMENT.md)
- [🧪 Testing Guide](docs/05-TESTING-GUIDE.md)
- [🔧 Development Guide](docs/DEVELOPMENT.md)
- [🎨 Branding Guidelines](docs/BRANDING_REPORT.md)

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](docs/CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 🔐 Security

Security is a top priority. Please review our [Security Policy](docs/SECURITY.md) and report vulnerabilities responsibly.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🏢 About Mewayz Technologies

Mewayz Technologies Inc. is dedicated to building innovative solutions for modern businesses and creators. Our platform empowers users to manage their entire business ecosystem efficiently and professionally.

### Contact

- **Website**: [https://mewayz.com](https://mewayz.com)
- **Email**: support@mewayz.com
- **Documentation**: [https://docs.mewayz.com](https://docs.mewayz.com)

## 🙏 Acknowledgments

- Laravel community for the excellent framework
- All contributors and supporters
- Open source community for amazing tools and libraries

---

<div align="center">
  <strong>Built with ❤️ by Mewayz Technologies Inc.</strong>
</div>
