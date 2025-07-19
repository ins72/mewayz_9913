# Mewayz v2 - All-in-One Business Platform
*Version: 2.0 | Date: July 19, 2025*

![Mewayz Logo](./assets/logo.png)

## 🚀 Overview

Mewayz v2 is a comprehensive all-in-one business platform that combines social media management, course creation, e-commerce, CRM, and advanced business tools into a single, powerful application. Built with modern technologies for scalability, performance, and user experience.

### ✨ Key Features

- **Multi-Workspace System** - Manage multiple businesses/projects from one account
- **6 Core Business Goals** - Instagram, Link in Bio, Courses, E-commerce, CRM, Website Builder
- **Feature-Based Subscriptions** - Pay only for what you use
- **Professional UI/UX** - Dark/Light themes with enterprise-grade design
- **Advanced Analytics** - Comprehensive reporting and insights
- **Team Collaboration** - Role-based access control
- **Template Marketplace** - Buy and sell templates
- **AI-Powered Tools** - Content generation, image creation, and more

## 🛠 Technical Architecture

### Backend Stack
- **FastAPI 0.104+** - Modern, fast web framework for building APIs
- **Python 3.11+** - Core programming language
- **SQLAlchemy 2.0** - SQL toolkit and Object-Relational Mapping
- **MySQL 8.0** - Primary database
- **Redis** - Caching and session storage
- **Celery** - Distributed task queue
- **Stripe** - Payment processing
- **JWT** - Authentication tokens

### Frontend Stack
- **React 18** - Modern JavaScript library
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS** - Utility-first CSS framework
- **Framer Motion** - Animation library
- **React Query** - Data fetching and caching
- **React Hook Form** - Form validation
- **Heroicons** - Icon library

### Infrastructure
- **Docker** - Containerization
- **Nginx** - Reverse proxy and load balancer
- **Kubernetes** - Container orchestration
- **AWS S3** - File storage
- **CloudFlare** - CDN and security

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **Docker** (20.10+) and Docker Compose
- **Python** (3.11+)
- **Node.js** (18+) and npm/yarn
- **MySQL** (8.0+)
- **Redis** (6.0+)

## 🚀 Quick Start

### 1. Clone the Repository
```bash
git clone https://github.com/your-org/mewayz-v2.git
cd mewayz-v2
```

### 2. Environment Setup
```bash
# Copy environment files
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Update environment variables with your settings
```

### 3. Start with Docker (Recommended)
```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Access the application
# Frontend: http://localhost:3000
# Backend API: http://localhost:8001
# Admin Panel: http://localhost:3000/dashboard/admin
```

### 4. Manual Setup (Development)

#### Backend Setup
```bash
cd backend

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Run database migrations
alembic upgrade head

# Start the server
python main.py
```

#### Frontend Setup
```bash
cd frontend

# Install dependencies
npm install  # or yarn install

# Start development server
npm start    # or yarn start
```

## 🔧 Configuration

### Environment Variables

#### Backend (.env)
```env
# Database
DATABASE_URL=mysql://user:password@localhost/mewayz_v2
REDIS_URL=redis://localhost:6379

# Security
JWT_SECRET_KEY=your-super-secret-jwt-key
ENCRYPTION_KEY=your-encryption-key

# External APIs
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
OPENAI_API_KEY=sk-...
GOOGLE_CLIENT_ID=your-google-client-id
APPLE_CLIENT_ID=your-apple-client-id

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# Storage
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_S3_BUCKET=your-bucket-name
```

#### Frontend (.env)
```env
# API Configuration
REACT_APP_API_URL=http://localhost:8001
REACT_APP_ENVIRONMENT=development

# External Services
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id
REACT_APP_APPLE_CLIENT_ID=your-apple-client-id
REACT_APP_STRIPE_PUBLISHABLE_KEY=pk_test_...

# Analytics
REACT_APP_GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
```

## 📚 Core Features Documentation

### 1. Multi-Workspace System

Mewayz v2 supports multiple workspaces per user, allowing management of different businesses or projects:

```python
# Backend: Creating a workspace
@app.post("/api/workspaces/create")
def create_workspace(workspace_data: dict, current_user: User = Depends(get_current_user)):
    workspace = Workspace(
        name=workspace_data.get("name"),
        slug=workspace_data.get("slug"),
        brand_color=workspace_data.get("brand_color", "#007AFF")
    )
    # ... implementation
```

```javascript
// Frontend: Workspace selector
import { WorkspaceSelector } from '../components/WorkspaceSelector';

const Dashboard = () => {
  const { currentWorkspace, setCurrentWorkspace } = useAuth();
  
  return (
    <div>
      <WorkspaceSelector />
      {/* Dashboard content */}
    </div>
  );
};
```

### 2. Feature-Based Access Control

Fine-grained control over feature access based on subscription plans:

```python
# Feature gate decorator
def requires_feature(feature_key: str):
    def decorator(func):
        def wrapper(*args, **kwargs):
            workspace = get_current_workspace()
            if not FeatureGate.check(workspace, feature_key):
                raise HTTPException(
                    status_code=403, 
                    detail=f"Feature '{feature_key}' not available"
                )
            return func(*args, **kwargs)
        return wrapper
    return decorator

@app.post("/api/ai/generate")
@requires_feature("ai_content_generation")
def generate_content(prompt: str):
    # AI content generation logic
    pass
```

### 3. Subscription Management

Workspace-based subscriptions with flexible pricing models:

```python
# Subscription plans
class SubscriptionPlan:
    FREE = "free"           # 10 features max, basic quotas
    PRO = "pro"            # $1/feature/month, $10/feature/year
    ENTERPRISE = "enterprise" # $1.5/feature/month, $15/feature/year, whitelabel

# Feature pricing calculation
def calculate_subscription_cost(workspace_id: int, billing_cycle: str):
    workspace = get_workspace(workspace_id)
    enabled_features = workspace.features.filter(is_enabled=True).count()
    
    if workspace.plan == SubscriptionPlan.PRO:
        monthly_cost = enabled_features * 1.00
        yearly_cost = enabled_features * 10.00
    elif workspace.plan == SubscriptionPlan.ENTERPRISE:
        monthly_cost = enabled_features * 1.50
        yearly_cost = enabled_features * 15.00
    
    return yearly_cost if billing_cycle == "yearly" else monthly_cost
```

### 4. Six Core Business Goals

#### Instagram Management
- Lead generation and search
- Content posting and scheduling
- Analytics and insights
- Hashtag research

#### Link in Bio
- Drag-and-drop page builder
- Custom domains
- Click tracking
- QR code generation

#### Course Creation
- Video hosting and streaming
- Student management
- Progress tracking
- Certificate generation

#### E-commerce
- Product catalog
- Order management
- Payment processing
- Inventory tracking

#### CRM & Email Marketing
- Contact management
- Email campaigns
- Lead scoring
- Automation workflows

#### Website Builder
- No-code drag-and-drop editor
- SEO optimization
- Mobile responsive
- Custom code injection

## 🎨 Design System

### Color Palette

#### Light Theme
```css
:root {
  --app-bg: #FAFAFA;
  --card-bg: #FFFFFF;
  --text-primary: #1A1A1A;
  --text-secondary: #6B6B6B;
  --border-color: #E5E5E5;
}
```

#### Dark Theme
```css
:root {
  --app-bg: #101010;
  --card-bg: #191919;
  --text-primary: #F1F1F1;
  --text-secondary: #7B7B7B;
  --border-color: #282828;
}
```

### Component Examples

```jsx
// Professional button styles
<button className="btn-primary">
  Primary Action
</button>

<button className="btn-secondary">
  Secondary Action
</button>

// Card component
<div className="bg-surface-elevated p-6 rounded-lg shadow-default">
  <h3 className="text-lg font-semibold text-primary">Card Title</h3>
  <p className="text-secondary">Card content</p>
</div>
```

## 🔐 Authentication & Security

### Authentication Methods
- **Email/Password** - Traditional authentication
- **Google OAuth** - Social login
- **Apple Sign-In** - iOS/macOS integration
- **Two-Factor Authentication** - Enhanced security

### Security Features
- **JWT Tokens** - Secure, stateless authentication
- **Rate Limiting** - API abuse prevention
- **Input Validation** - SQL injection and XSS protection
- **CORS Configuration** - Cross-origin request security
- **Encryption** - Sensitive data encryption at rest

### Implementation Example
```python
# JWT token generation
def create_access_token(data: dict, expires_delta: timedelta = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, JWT_SECRET_KEY, algorithm="HS256")
    return encoded_jwt
```

## 📊 Database Schema

### Core Tables

#### Workspaces
```sql
CREATE TABLE workspaces (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    brand_color VARCHAR(7) DEFAULT '#007AFF',
    subscription_status ENUM('active', 'trialing', 'past_due', 'cancelled'),
    trial_ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Users & Workspace Relationships
```sql
CREATE TABLE workspace_users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    workspace_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role ENUM('owner', 'admin', 'editor', 'viewer') NOT NULL,
    invitation_token VARCHAR(255),
    joined_at TIMESTAMP NULL,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### Feature Management
```sql
CREATE TABLE workspace_features (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    workspace_id BIGINT NOT NULL,
    feature_key VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    quota_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id)
);
```

## 🔌 API Documentation

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "securepassword"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "email": "user@example.com",
      "name": "John Doe"
    }
  }
}
```

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "securepassword"
}
```

### Workspace Endpoints

#### Get User Workspaces
```http
GET /api/workspaces
Authorization: Bearer {token}
```

Response:
```json
{
  "success": true,
  "data": {
    "workspaces": [
      {
        "id": 1,
        "name": "Marketing Agency",
        "slug": "marketing-agency",
        "role": "owner",
        "subscription_status": "active"
      }
    ]
  }
}
```

#### Create Workspace
```http
POST /api/workspaces/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My New Business",
  "slug": "my-new-business",
  "goals": ["instagram", "link_bio", "ecommerce"]
}
```

### Feature Management

#### Check Feature Access
```http
GET /api/workspaces/{workspace_id}/features/{feature_key}
Authorization: Bearer {token}
```

Response:
```json
{
  "success": true,
  "data": {
    "enabled": true,
    "quota_limit": 100,
    "usage_count": 47,
    "usage_percentage": 47.0
  }
}
```

## 🧪 Testing

### Running Tests

```bash
# Backend tests
cd backend
pytest

# Frontend tests
cd frontend
npm test

# Integration tests
npm run test:integration

# End-to-end tests
npm run test:e2e
```

### Test Structure

```python
# Backend test example
def test_create_workspace():
    response = client.post(
        "/api/workspaces/create",
        json={"name": "Test Workspace", "slug": "test-workspace"},
        headers={"Authorization": f"Bearer {token}"}
    )
    assert response.status_code == 200
    assert response.json()["success"] == True
```

```javascript
// Frontend test example
describe('WorkspaceSelector', () => {
  test('renders workspace list', async () => {
    render(<WorkspaceSelector />);
    
    await waitFor(() => {
      expect(screen.getByText('Marketing Agency')).toBeInTheDocument();
    });
  });
});
```

## 🚀 Deployment

### Production Deployment

#### Using Docker Compose
```bash
# Production environment
docker-compose -f docker-compose.prod.yml up -d

# Scale services
docker-compose -f docker-compose.prod.yml up -d --scale api=3
```

#### Environment-Specific Configurations

**Staging**
```env
DATABASE_URL=mysql://user:pass@staging-db:3306/mewayz_staging
REDIS_URL=redis://staging-redis:6379
DEBUG=false
```

**Production**
```env
DATABASE_URL=mysql://user:pass@prod-db:3306/mewayz_prod
REDIS_URL=redis://prod-redis:6379
DEBUG=false
SENTRY_DSN=https://your-sentry-dsn
```

### Health Checks

```python
@app.get("/health")
def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.utcnow(),
        "services": {
            "database": check_database_connection(),
            "redis": check_redis_connection(),
            "external_apis": check_external_apis()
        }
    }
```

## 📈 Monitoring & Analytics

### Performance Monitoring
- **Application Performance Monitoring (APM)** - New Relic/DataDog
- **Error Tracking** - Sentry
- **Uptime Monitoring** - Pingdom
- **Log Aggregation** - ELK Stack

### Business Analytics
- **User Analytics** - Google Analytics 4
- **Product Analytics** - Mixpanel
- **Revenue Analytics** - Stripe Dashboard
- **Feature Usage** - Custom dashboard

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Workflow

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Make your changes** and add tests
4. **Commit your changes**: `git commit -m 'Add amazing feature'`
5. **Push to the branch**: `git push origin feature/amazing-feature`
6. **Open a Pull Request**

### Code Style

#### Python (Backend)
```bash
# Format with black
black backend/

# Sort imports
isort backend/

# Lint with flake8
flake8 backend/
```

#### JavaScript/TypeScript (Frontend)
```bash
# Format with prettier
prettier --write frontend/src/

# Lint with ESLint
eslint frontend/src/
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Documentation
- [API Documentation](./api/README.md)
- [User Guide](./user-guide/README.md)
- [Developer Guide](./developer-guide/README.md)

### Community
- **GitHub Issues** - Bug reports and feature requests
- **Discord Server** - Community discussions
- **Email Support** - support@mewayz.com

### Enterprise Support
For enterprise customers, we offer:
- **Priority Support** - 24/7 technical support
- **Custom Integrations** - Tailored solutions
- **Dedicated Account Manager** - Personalized service
- **SLA Guarantees** - 99.9% uptime commitment

---

## 📊 Current Status

### ✅ Completed Features
- [x] Multi-workspace system
- [x] Feature-based access control
- [x] Professional UI/UX with dark/light themes
- [x] Admin dashboard with comprehensive controls
- [x] Payment processing with Stripe
- [x] AI-powered content generation
- [x] Advanced booking system
- [x] Financial management
- [x] Escrow system
- [x] Team collaboration

### 🚧 In Progress
- [ ] Template marketplace
- [ ] Instagram lead generation API
- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Workflow automation

### 📋 Planned Features
- [ ] WhatsApp Business integration
- [ ] Voice/Video calling
- [ ] Advanced AI features
- [ ] International localization
- [ ] Enterprise SSO

---

**Mewayz v2** - Built with ❤️ for modern businesses
*Last updated: July 19, 2025*