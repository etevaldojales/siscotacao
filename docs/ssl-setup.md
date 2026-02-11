# SSL Setup for Local Development in Laravel with Devilbox and VSCode

This guide explains how to set up SSL (HTTPS) for your Laravel project running in Devilbox, and how to configure VSCode for SSL connections if needed.

## 1. Generate Self-Signed SSL Certificates

You can generate self-signed SSL certificates using OpenSSL:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost.key -out localhost.crt -subj "/CN=localhost"
```

This will create `localhost.key` and `localhost.crt` files.

## 2. Configure Devilbox to Use SSL

- Place the generated `localhost.key` and `localhost.crt` files in the Devilbox SSL directory, usually at `./devilbox/data/ssl/`.
- Update Devilbox configuration to enable SSL for your project domain.
- Restart Devilbox containers.

Refer to Devilbox documentation for exact SSL setup details.

## 3. Update Laravel .env File

Set the `APP_URL` to use HTTPS:

```
APP_URL=https://localhost
```

## 4. Force HTTPS in Laravel

Create a middleware to redirect all HTTP requests to HTTPS.

Example middleware `app/Http/Middleware/ForceHttps.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
```

Register this middleware in `app/Http/Kernel.php` in the `$middleware` array or in the `web` middleware group.

## 5. Configure VSCode for Remote SSL Connection (Optional)

If you connect to a remote server via VSCode, ensure your SSH or remote connection settings use the correct SSL certificates.

Refer to VSCode Remote Development documentation for details.

---

This setup will enable SSL for your Laravel project in local development using Devilbox and allow you to work securely in VSCode.
