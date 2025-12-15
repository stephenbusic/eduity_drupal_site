# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Drupal 11 project built using the `drupal/recommended-project` Composer template. The project uses a relocated document root (`web/`) and follows Drupal best practices for composer-based site management.

## Development Environment

### DDEV (Recommended for Local Development)

DDEV is the primary local development environment configured for this project:

- **Project name**: test-site
- **URL**: https://test-site.ddev.site
- **PHP version**: 8.3
- **Database**: MariaDB 10.11
- **Web server**: nginx-fpm
- **Composer**: v2

**Common DDEV commands**:
```bash
# Start the environment
ddev start

# Stop the environment
ddev stop

# SSH into web container
ddev ssh

# Run Composer commands
ddev composer install
ddev composer require drupal/[module_name]
ddev composer update

# Run Drush commands
ddev drush status
ddev drush cr                    # Clear cache
ddev drush updb                  # Run database updates
ddev drush cim                   # Import configuration
ddev drush cex                   # Export configuration
ddev drush uli                   # Generate one-time login link

# Database operations
ddev import-db --file=dump.sql.gz
ddev export-db > backup.sql.gz
ddev snapshot                    # Create database snapshot
ddev snapshot --name=before-feature
ddev restore-snapshot [snapshot-name]

# View logs
ddev logs
ddev logs -f                     # Follow logs

# Access database
ddev mysql

# Restart services
ddev restart

# Describe project configuration
ddev describe
```

### Docker Compose (Production/Swarm Deployment)

A standalone Docker Compose configuration is also available for production-like deployments:

**Services**:
- `drupal`: Drupal 11 with PHP-FPM (image: `drupal:11-fpm`)
- `nginx`: Nginx web server with custom config
- `db`: MariaDB 10.6 database

**Configuration**:
- Traefik labels configured for reverse proxy routing
- Persistent volumes: `drupal_data` and `db_data`
- Database credentials in `docker-compose.yml` (environment variables)

**Commands**:
```bash
# Start services
docker compose up -d

# View logs
docker compose logs -f

# Stop services
docker compose down

# Access containers
docker compose exec drupal bash
docker compose exec db mysql -u drupal -p
```

## Project Structure

```
.
├── .ddev/                    # DDEV configuration and customizations
│   ├── config.yaml          # Main DDEV configuration
│   ├── commands/            # Custom DDEV commands (web/host/db)
│   └── nginx_full/          # Nginx configuration overrides
├── web/                     # Document root (Drupal codebase)
│   ├── core/                # Drupal core (managed by Composer)
│   ├── modules/
│   │   ├── contrib/         # Contributed modules (managed by Composer)
│   │   └── custom/          # Custom modules (version controlled)
│   ├── themes/
│   │   ├── contrib/         # Contributed themes (managed by Composer)
│   │   └── custom/          # Custom themes (version controlled)
│   ├── profiles/            # Installation profiles
│   ├── sites/               # Multisite configuration
│   │   └── default/
│   │       ├── settings.php      # Main settings file
│   │       ├── settings.ddev.php # DDEV-specific settings (auto-generated)
│   │       └── files/            # User-uploaded files
│   └── index.php
├── recipes/                 # Drupal recipes (managed by Composer)
├── vendor/                  # Composer dependencies (not version controlled)
├── composer.json            # PHP dependency management
├── composer.lock            # Locked dependency versions
├── docker-compose.yml       # Standalone Docker Compose configuration
├── Dockerfile               # Custom Nginx image (if needed)
└── nginx.conf              # Nginx server configuration
```

## Architecture Notes

### Composer-Based Workflow

This project uses Composer to manage all Drupal code:

- **Core and contributed modules/themes** are installed via Composer into `web/core`, `web/modules/contrib`, and `web/themes/contrib`
- **Custom code** lives in `web/modules/custom`, `web/themes/custom`, and `web/profiles/custom`
- **Never edit** files in `web/core` or `web/modules/contrib` directly - changes will be lost on updates

**Drupal Scaffold**: The `drupal/core-composer-scaffold` plugin manages core files like `.htaccess`, `robots.txt`, and `index.php`

**Installer Paths**: Composer is configured to place packages in the correct Drupal directories automatically

### Configuration Management

Drupal 11 uses Configuration Management for site settings:

- Configuration is typically stored in a `config/sync` directory (not yet created in this project)
- Use `drush cex` to export configuration from database to files
- Use `drush cim` to import configuration from files to database
- Configuration should be version controlled for deployment workflows

### Database Credentials

**DDEV** (automatic):
- Host: `db`
- Database: `db`
- Username: `db`
- Password: `db`
- Port: 3306

**Docker Compose**:
- Host: `db`
- Database: `drupal`
- Username: `drupal`
- Password: `drupalpass`
- Root password: `rootpass`

### Settings Files

- `web/sites/default/settings.php` - Main settings file (may contain DDEV includes)
- `web/sites/default/settings.ddev.php` - Auto-generated by DDEV (do not edit manually)
- `web/sites/example.settings.local.php` - Template for local development settings

## Testing

To run Drupal tests, use PHPUnit within the DDEV container:

```bash
# SSH into container
ddev ssh

# Run PHPUnit tests
cd web/core
../vendor/bin/phpunit -c core/phpunit.xml.dist [path-to-test]

# Run specific test group
../vendor/bin/phpunit -c core/phpunit.xml.dist --group [group-name]
```

## Common Development Workflows

### Adding a Contributed Module

```bash
ddev composer require drupal/[module_name]
ddev drush en [module_name]
ddev drush cr
```

### Creating a Custom Module

```bash
# Generate module scaffold using Drush
ddev drush generate module

# Or manually create in web/modules/custom/[module_name]/
```

Custom modules should include:
- `[module_name].info.yml` - Module metadata
- `[module_name].module` - Module hooks and functions (optional)
- `src/` - Object-oriented code (Controllers, Forms, Plugins, etc.)

### Updating Drupal Core

```bash
ddev composer update drupal/core "drupal/core-*" --with-all-dependencies
ddev drush updb  # Run database updates
ddev drush cr    # Clear cache
```

### Working with Recipes

Drupal recipes (stored in `recipes/`) can be applied using:

```bash
ddev composer require drupal/core-recipe-unpack
# Recipe application is handled automatically via Composer
```

## Deployment Considerations

When deploying to production:

1. Run `composer install --no-dev --optimize-autoloader` to install dependencies without dev packages
2. Ensure `web/sites/default/files` is writable and persistent
3. Set up configuration sync directory and import configuration with `drush cim`
4. Run database updates with `drush updb`
5. Clear cache with `drush cr`
6. For Docker Swarm, ensure `docker-compose.yml` is properly configured with secrets and environment-specific settings
