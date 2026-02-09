# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a CodeIgniter 4 application for text-tools, specifically focused on property description generation using the Anthropic Claude API. The application is located in the `public_html/` directory.

## Requirements

- PHP 8.2 or higher
- Required extensions: intl, mbstring, json
- Optional: mysqlnd (for MySQL), libcurl (for HTTP requests)

## Directory Structure

The CodeIgniter 4 application lives in `public_html/`:

- `public_html/app/` - Application code (Controllers, Models, Views, Config)
- `public_html/public/` - Web root (index.php, assets)
- `public_html/writable/` - Writable directories (cache, logs, session, uploads)
- `public_html/system/` - CodeIgniter 4 framework files (don't modify)
- `public_html/tests/` - PHPUnit tests

Key application directories:
- `app/Controllers/` - Request handlers
- `app/Models/` - Database models
- `app/Views/` - View templates
- `app/Config/` - Configuration files
- `app/Database/Migrations/` - Database migrations
- `app/Database/Seeds/` - Database seeders

## Common Commands

All commands should be run from the `public_html/` directory.

### Spark CLI (CodeIgniter's command-line tool)

```bash
cd public_html
php spark list                    # List all available commands
php spark help <command>          # Get help for a specific command
```

### Development Server

```bash
cd public_html
php spark serve                   # Start dev server at localhost:8080
php spark serve --host=0.0.0.0 --port=8081  # Custom host/port
```

### Database

```bash
cd public_html
php spark migrate                 # Run all pending migrations
php spark migrate:rollback        # Rollback last migration batch
php spark migrate:refresh         # Rollback all + re-run all migrations
php spark migrate:status          # Check migration status
php spark db:seed <SeederName>    # Run a specific seeder
php spark db:create <database>    # Create a new database
```

### Cache Management

```bash
cd public_html
php spark cache:clear             # Clear all caches
php spark cache:info              # Show cache information
```

### Code Generation

```bash
cd public_html
php spark make:controller <name>  # Create a new controller
php spark make:model <name>       # Create a new model
php spark make:migration <name>   # Create a new migration
php spark make:seeder <name>      # Create a new seeder
php spark make:filter <name>      # Create a new filter
```

### Testing

```bash
cd public_html
composer test                     # Run all PHPUnit tests
vendor/bin/phpunit                # Run PHPUnit directly
vendor/bin/phpunit --filter <testName>  # Run specific test
vendor/bin/phpunit --testsuite App      # Run specific test suite
```

### Routes and Configuration

```bash
cd public_html
php spark routes                  # Display all registered routes
php spark filter:check <route>    # Check filters for a specific route
php spark config:check            # Verify configuration values
php spark namespaces              # Verify namespace setup
```

### Production Optimization

```bash
cd public_html
php spark optimize                # Optimize for production
```

## Architecture Notes

### Configuration

- Configuration files are in `app/Config/`
- Environment-specific settings use `.env` file (copy from `env` template)
- Routes are defined in `app/Config/Routes.php`
- Database config in `app/Config/Database.php`

### MVC Pattern

- **Controllers** handle HTTP requests and return responses
- **Models** interact with database tables
- **Views** render HTML (located in `app/Views/`)
- Use `BaseController` as parent for all controllers

### Autoloading

- PSR-4 autoloading configured in `composer.json`
- Custom namespaces in `app/Config/Autoload.php`
- Application namespace: `App\`
- CodeIgniter namespace: `CodeIgniter\`

### Request Flow

1. Request hits `public/index.php`
2. Routes defined in `app/Config/Routes.php` map URL to Controller
3. Filters (defined in `app/Config/Filters.php`) can process before/after
4. Controller method executes and returns response
5. Response sent to client

### Testing

- Tests are in `public_html/tests/`
- Configuration in `phpunit.xml.dist`
- Extend `CodeIgniter\Test\CIUnitTestCase` for integration tests
- Use `CodeIgniter\Test\FeatureTestTrait` for HTTP testing

## Environment Setup

1. Copy `env` to `.env` and configure:
   - Set `CI_ENVIRONMENT` (development, production, testing)
   - Configure database credentials
   - Set base URL
   - Add API keys (e.g., `ANTHROPIC_API_KEY`)

2. Install dependencies:
```bash
cd public_html
composer install
```

3. Set up database:
```bash
cd public_html
php spark migrate
php spark db:seed <SeederName>  # If seeders exist
```

## File Paths

When working with files in this project:
- Web root is `public_html/public/`
- Application files are relative to `public_html/app/`
- Use forward slashes for paths (GitBash on Windows)
- Writable directories are in `public_html/writable/`
