This guide walks you through installing the OpenFGA PHP SDK and setting up the necessary dependencies for your application.

## Requirements

Before installing the SDK, ensure your environment meets these requirements:

- **PHP 8.3** or higher
- **Composer** (PHP dependency manager)
- **JSON extension** (usually enabled by default)
- **OpenSSL extension** (for HTTPS connections)

## Installing the SDK

Install the OpenFGA PHP SDK using [Composer](https://getcomposer.org/):

```bash
composer require evansims/openfga-php
```

This command installs the SDK and its core dependencies. However, the SDK relies on [PSR standards](https://www.php-fig.org/psr/) for HTTP communication, which you'll need to provide.

## PSR Dependencies

The OpenFGA SDK is built on [PHP Standards Recommendations (PSRs)](https://www.php-fig.org/psr/) for maximum compatibility with your existing application stack. It requires implementations of:

- [PSR-7](https://www.php-fig.org/psr/psr-7): HTTP message interfaces (requests and responses)
- [PSR-17](https://www.php-fig.org/psr/psr-17): HTTP factory interfaces (creating PSR-7 objects)
- [PSR-18](https://www.php-fig.org/psr/psr-18): HTTP client interface (sending HTTP requests)

If your application already uses a framework like Laravel, Symfony, or Slim, you likely have these implementations installed. If not, you'll need to install them.

### Installing PSR Implementations

If your application is missing one or more of these implementations, you'll need to install dependencies to fill those gaps. [Guzzle](https://github.com/guzzle/guzzle) is a popular choice, and provides all three PSR implementations you'll need:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

### Alternative PSR Implementations

You can choose different implementations based on your needs:

#### Lightweight Option: Nyholm

For a more lightweight solution:

```bash
composer require nyholm/psr7 php-http/curl-client
```

#### Symfony Components

If you're using Symfony or prefer Symfony components:

```bash
composer require symfony/http-client nyholm/psr7
```

#### Laravel Projects

Laravel projects typically have Guzzle pre-installed. If not:

```bash
composer require guzzlehttp/guzzle
```

## Common Installation Issues

### Missing PSR Implementation

**Error:** `No PSR-18 HTTP Client implementation found`

**Solution:** Install a PSR-18 compatible HTTP client:

```bash
composer require guzzlehttp/guzzle
```

### PHP Version Too Old

**Error:** `Your PHP version (8.x.x) does not satisfy requirements`

**Solution:** Upgrade to PHP 8.3 or higher (e.g. `sudo apt update && sudo apt install php8.3` on Ubuntu or `brew install php@8.3` on macOS).

### Memory Limit Exceeded

**Error:** `Allowed memory size exhausted`

**Solution:** Increase Composer's memory limit:

```bash
COMPOSER_MEMORY_LIMIT=-1 composer require evansims/openfga-php
```

## Development Installation

For contributing to the SDK or running tests:

```bash
git clone https://github.com/evansims/openfga-php.git
cd openfga-php
composer install
composer test
```

## Docker Installation

If you prefer using Docker, create a `Dockerfile`:

```dockerfile
FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy application code
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

CMD ["php", "your-script.php"]
```

Build and run:

```bash
docker build -t openfga-php-app .
docker run --rm openfga-php-app
```

## Production Considerations

When installing for production use:

1. **Use Composer's** **`--no-dev`** **--no-dev** **flag** to exclude development dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

2. **Consider using a PSR-6 cache** for better performance:

```bash
composer require symfony/cache
```

## Next Steps

Now that you have the SDK installed:

1. [Set up authentication](Authentication.md) for secure API access
2. [Read the introduction](Introduction.md) to understand core concepts
3. [Create your first store](Introduction.md) to begin using OpenFGA

## Troubleshooting

If you encounter issues during installation:

1. **Clear Composer's cache:**

```bash
composer clear-cache
```

2. **Update Composer itself:**

```bash
composer self-update
```

3. **Check for conflicts:**

```bash
composer diagnose
```

4. **Enable verbose output:**

```bash
composer require evansims/openfga-php -vvv
```
