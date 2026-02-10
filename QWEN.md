# Qwen Code Assistant Context - Text-Tools Project

## Project Overview

This is a multi-tenant property description generation system built on CodeIgniter 4. The application provides AI-powered tools for generating, translating, and rewriting property descriptions using both Claude and OpenAI APIs. It supports three user roles (Superadmin, Admin, User) with role-based access control and project isolation.

The project evolved from a single-file proof-of-concept (`all-demos.php`) into a production-ready multi-tenant application with proper MVC architecture, authentication, and database persistence.

## Architecture

### Main Components
- **CodeIgniter 4 Framework**: Located in `public_html/` directory
- **Multi-tenant System**: Isolated projects with individual API keys and configurations
- **AI Service Layer**: Supports both Claude and OpenAI with factory pattern implementation
- **Role-based Access Control**: Superadmin, Admin, and User roles with different permissions
- **Database Layer**: MySQL with encrypted API keys and JSON language configurations

### Directory Structure
```
public_html/
├── app/                    # Application code
│   ├── Config/            # Configuration files
│   ├── Controllers/       # Request handlers
│   ├── Filters/           # Middleware (Auth, Superadmin, Admin, Tenant)
│   ├── Libraries/         # Custom services (AIService, EncryptionService)
│   ├── Models/            # Database models
│   └── Views/             # Template files
├── public/                # Web root
├── system/                # CodeIgniter framework
├── writable/              # Writable directories
└── tests/                 # PHPUnit tests
```

## Building and Running

### Prerequisites
- PHP 8.2 or higher
- Required extensions: intl, mbstring, json, curl
- MySQL database
- Composer for dependency management

### Setup Instructions
1. Navigate to the `public_html/` directory
2. Install dependencies: `composer install`
3. Copy `env` to `.env` and configure:
   - Database credentials
   - Base URL
   - Encryption key
4. Run database migrations: `php spark migrate`
5. Seed the database: `php spark db:seed SuperadminSeeder`
6. Start development server: `php spark serve`

### Key Commands
```bash
# Development server
php spark serve

# Database operations
php spark migrate
php spark migrate:rollback
php spark db:seed SuperadminSeeder

# Code generation
php spark make:controller <name>
php spark make:model <name>
php spark make:migration <name>

# Testing
composer test
php spark routes
```

## Development Conventions

### Coding Standards
- Follow CodeIgniter 4 conventions and PSR-4 autoloading
- Use proper MVC pattern with controllers extending `BaseController`
- Models should extend `CodeIgniter\Model`
- Views use PHP template syntax with CodeIgniter helper functions

### Security Practices
- API keys are AES encrypted before database storage
- Password reset tokens are randomly generated and time-limited (1 hour)
- CSRF protection is implemented for all forms
- Input validation and sanitization required for all user inputs

### Multi-tenancy Implementation
- Each project has isolated data with foreign key relationships
- TenantFilter injects project context for admin/user roles
- API keys are scoped to individual projects
- Language configurations are per-project

### AI Service Architecture
- Interface-based design with `AIServiceInterface`
- Factory pattern for selecting Claude or OpenAI services
- English-first workflow (generate/rewrite in English, translate to DE/ES)
- Proper error handling for API failures

## Key Features

### Authentication & Authorization
- Three-tier role system (Superadmin, Admin, User)
- Session-based authentication
- Password reset with time-limited tokens
- Role-based dashboard redirection

### AI-Powered Tools
- Property description generator with form-based input
- Multilingual translator (English → German/Spanish)
- Text rewriter to avoid duplicate content
- Support for both Claude and OpenAI providers

### Multi-tenant Management
- Superadmin manages projects and their admins
- Admins manage users within their project
- Encrypted API keys per project
- Configurable language support per project

### User Interface
- Three-tab interface (Generator, Translator, Rewriter)
- Dark-themed responsive design
- AJAX-powered real-time processing
- Loading spinners and copy-to-clipboard functionality

## Database Schema

### Core Tables
- `projects`: Tenant information with encrypted API keys and language configs
- `users`: User accounts with role-based access and project associations
- `password_resets`: Time-limited password reset tokens
- `activity_logs`: Optional audit trail for AI operations

### Relationships
- Users belong to projects (with CASCADE delete)
- Superadmins have NULL project_id
- Password reset tokens linked to users
- Activity logs track user actions per project

## Testing Strategy

### Unit Tests
- Model CRUD operations and validations
- Library functionality (EncryptionService, PasswordResetService)
- Filter middleware behavior

### Integration Tests
- Complete user workflows
- Multi-tenant data isolation
- AI service integration
- Authentication and authorization flows

## Environment Configuration

### Required .env Variables
```ini
# Application
CI_ENVIRONMENT = development
app.baseURL = 'http://text-tools.local/'

# Database
database.default.hostname = localhost
database.default.database = texttools
database.default.username = texttools
database.default.password = texttools

# Encryption
encryption.key = hex2bin:34e052b69d64449ea458ede9bc74d65628f4fe12c9ef1f1a3e5b6b1b0d0f75ff
```

## Production Deployment

### Checklist
- Set `CI_ENVIRONMENT = production`
- Configure database with strong passwords
- Use HTTPS with forcehttps filter
- Switch from Tailwind CDN to optimized build
- Configure email for password reset notifications
- Set up database backups
- Monitor API usage and costs

### Performance Optimization
- Use DatabaseHandler for sessions in multi-server setups
- Implement caching strategies
- Optimize database queries with proper indexing
- Minimize asset sizes and enable compression

## Future Enhancements

1. Email integration for automated password reset links
2. API usage tracking and cost analytics
3. Configurable language support per project
4. Batch processing for bulk property descriptions
5. Template system for reusable property description patterns
6. REST API endpoints for external integrations
7. Advanced analytics dashboard
8. Version control for generated descriptions
9. Multi-factor authentication for superadmin accounts
10. Rate limiting for AI API calls