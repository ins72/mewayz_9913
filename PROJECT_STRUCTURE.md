# 🎯 Project Structure

```
mewayz-v2/
├── 📂 app/                     # Core application code
│   ├── 📂 Console/             # Artisan commands
│   ├── 📂 Events/              # Event classes
│   ├── 📂 Exceptions/          # Exception handlers
│   ├── 📂 Http/                # Controllers, middleware, requests
│   │   ├── 📂 Controllers/     # Application controllers
│   │   │   ├── 📂 Admin/       # Admin panel controllers
│   │   │   └── 📂 Api/         # API controllers
│   │   ├── 📂 Middleware/      # HTTP middleware
│   │   └── 📂 Requests/        # Form request validation
│   ├── 📂 Jobs/                # Queue job classes
│   ├── 📂 Listeners/           # Event listeners
│   ├── 📂 Models/              # Eloquent models
│   ├── 📂 Providers/           # Service providers
│   └── 📂 Services/            # Business logic services
├── 📂 bootstrap/               # Application bootstrap
├── 📂 config/                  # Configuration files
├── 📂 database/                # Database files
│   ├── 📂 factories/           # Model factories
│   ├── 📂 migrations/          # Database migrations
│   └── 📂 seeders/             # Database seeders
├── 📂 docker/                  # Docker configuration
│   ├── 📂 nginx/               # Nginx configuration
│   ├── 📂 mysql/               # MySQL initialization
│   └── 📂 supervisor/          # Process management
├── 📂 docs/                    # Documentation
│   ├── 📂 api/                 # API documentation
│   ├── 📂 developer/           # Developer guides
│   └── 📂 user-guide/          # User documentation
├── 📂 public/                  # Web server document root
├── 📂 resources/               # Frontend resources
│   ├── 📂 css/                 # Stylesheets
│   ├── 📂 js/                  # JavaScript files
│   ├── 📂 lang/                # Language files
│   └── 📂 views/               # Blade templates
├── 📂 routes/                  # Route definitions
│   ├── 📄 api.php              # API routes
│   ├── 📄 web.php              # Web routes
│   └── 📄 admin.php            # Admin routes
├── 📂 storage/                 # Generated files
│   ├── 📂 app/                 # Application storage
│   ├── 📂 framework/           # Framework storage
│   └── 📂 logs/                # Log files
├── 📂 tests/                   # Test files
├── 📄 .env.example             # Environment template
├── 📄 docker-compose.yml       # Docker composition
├── 📄 Dockerfile               # Docker image definition
├── 📄 setup.sh                 # Automated setup script
├── 📄 deploy.sh                # Production deployment script
├── 📄 SETUP_GUIDE.md           # Complete setup guide
└── 📄 README.md                # Project overview
```

## 📁 Key Directories Explained

### `/app/Http/Controllers/`
- **Admin/**: Administrative panel controllers
- **Api/**: RESTful API endpoints
- **Auth/**: Authentication controllers

### `/app/Models/`
Core business entities:
- User management and authentication
- Workspace and team management
- Social media accounts and content
- E-commerce products and orders
- Course creation and enrollment
- CRM contacts and leads
- Analytics and reporting

### `/app/Services/`
Business logic services:
- Payment processing
- Social media integration
- Course delivery
- Email marketing
- Analytics calculation
- AI content generation

### `/database/migrations/`
Database structure:
- User and authentication tables
- Business entity tables
- Social media integration tables
- E-commerce and payment tables
- Course and learning management
- CRM and analytics tables

### `/resources/views/`
Frontend templates:
- Dashboard and main interface
- Administrative panels
- Authentication pages
- E-commerce storefront
- Course delivery interface

### `/docker/`
Container configuration:
- **nginx/**: Web server configuration
- **mysql/**: Database initialization
- **supervisor/**: Process management

This structure follows Laravel conventions while organizing the complex feature set of Mewayz v2 into logical, maintainable sections.