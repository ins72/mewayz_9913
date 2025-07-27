# Database Configuration
MONGO_URL = "mongodb://localhost:5000/mewayz_production"
REDIS_URL = "redis://localhost:6379"

# Application Configuration
ENVIRONMENT = "production"
DEBUG = False
JWT_SECRET = "mewayz-production-jwt-secret-key-2025-ultra-secure"
ENCRYPTION_KEY = "mewayz-32-byte-encryption-key-2025"

# API Keys (Add your actual keys)
OPENAI_API_KEY = "your-openai-api-key"
STRIPE_SECRET_KEY = "your-stripe-secret-key"
STRIPE_PUBLISHABLE_KEY = "your-stripe-publishable-key"
GOOGLE_CLIENT_ID = "your-google-client-id"
GOOGLE_CLIENT_SECRET = "your-google-client-secret"

# Email Configuration
SMTP_HOST = "smtp.gmail.com"
SMTP_PORT = 587
SMTP_USER = "your-email@gmail.com"
SMTP_PASSWORD = "your-app-password"

# Server Configuration
HOST = "0.0.0.0"
PORT = 8000 