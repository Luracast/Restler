# Security Policy

## Supported Versions

| Version | Supported          | PHP Version | Status |
| ------- | ------------------ | ----------- | ------ |
| 6.0.x   | :white_check_mark: | 8.0+        | Current |
| 5.0.x   | :white_check_mark: | 7.4 - 8.2   | Maintenance |
| 4.0.x   | :x:                | 7.1 - 7.4   | End of Life |
| 3.0.x   | :x:                | 5.4 - 7.4   | End of Life |

---

## Security Features in v6

### 1. Secure Session Handling

v6 replaces unsafe `unserialize()` with JSON serialization:

```php
// v5 (vulnerable to object injection)
$data = unserialize($sessionData);

// v6 (safe)
$data = json_decode($sessionData, true);
```

### 2. JSONP Callback Validation

Prevents XSS attacks through JSONP callbacks:

```php
// Validates callback names against safe patterns
// Only allows: letters, numbers, dots, underscores, $
// Example: myCallback, jQuery.callback, callbacks[0]
```

### 3. Template Rendering Protection

Uses `EXTR_SKIP` flag to prevent variable overwriting:

```php
// Prevents malicious data from overwriting critical variables
extract($data, EXTR_SKIP);
```

### 4. Input Validation

Automatic type validation and coercion:

```php
/**
 * @param int $id User ID {@min 1}{@max 999999}
 * @param string $email {@type email}
 */
public function updateUser(int $id, string $email): array
{
    // $id and $email are automatically validated
}
```

---

## Reporting a Vulnerability

**DO NOT** open public GitHub issues for security vulnerabilities.

### How to Report

1. **Email**: Send details to **arul@luracast.com**
2. **Subject**: "Security Vulnerability Report - Restler"
3. **Include**:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Your suggested fix (if any)

### What to Expect

* **Initial Response**: Within 48 hours
* **Status Update**: Within 7 days
* **Fix Timeline**: Depends on severity
  - **Critical**: 1-7 days
  - **High**: 7-30 days
  - **Medium**: 30-90 days
  - **Low**: Next release

### After Reporting

* We will investigate and confirm the vulnerability
* We will develop and test a fix
* We will prepare a security advisory
* We will release a patched version
* We will credit you (if desired) in the security advisory

---

## Security Best Practices

### 1. Always Use HTTPS

```apache
# .htaccess
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 2. Enable Production Mode

```php
$r = new Restler(true); // Disables debug info
```

### 3. Implement Authentication

```php
$r->addAuthenticationClass('YourAuth');
```

### 4. Use Rate Limiting

```php
$r->addFilterClass('RateLimit');
```

### 5. Validate All Input

```php
/**
 * @param string $email {@type email}
 * @param string $password {@min 8}{@max 100}
 */
public function register(string $email, string $password): array
{
    // Input is pre-validated by Restler
}
```

### 6. Sanitize Output

```php
public function getHtml(): string
{
    $userInput = $this->getUserInput();
    return htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
}
```

### 7. Configure CORS Properly

```php
use Luracast\Restler\Defaults;

// Be specific with allowed origins
Defaults::$accessControlAllowOrigin = 'https://yourdomain.com';

// Don't use '*' with credentials
Defaults::$accessControlAllowCredentials = true;
```

### 8. Protect Sensitive Files

```apache
# .htaccess
<FilesMatch "^(composer\.(json|lock)|\.env|\.git|cache)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 9. Keep Dependencies Updated

```bash
composer update
composer audit
```

### 10. Use Environment Variables

```php
// Don't hardcode credentials
$dbPassword = getenv('DB_PASSWORD');

// Use .env files (never commit them!)
```

---

## Common Vulnerabilities and Mitigations

### SQL Injection

**Vulnerable:**
```php
public function getUser(int $id): array
{
    $sql = "SELECT * FROM users WHERE id = $id";
    return $db->query($sql);
}
```

**Secure:**
```php
public function getUser(int $id): array
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
```

### XSS (Cross-Site Scripting)

**Vulnerable:**
```php
public function display(string $name): string
{
    return "<h1>Hello $name</h1>";
}
```

**Secure:**
```php
public function display(string $name): string
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    return "<h1>Hello $safeName</h1>";
}
```

### CSRF (Cross-Site Request Forgery)

**Protect forms with tokens:**
```php
/**
 * @class Forms {@csrf}
 */
class MyAPI
{
    public function postForm(array $data): array
    {
        // CSRF token automatically validated
    }
}
```

### Authentication Bypass

**Always check authentication:**
```php
/**
 * @access protected
 */
public function deleteUser(int $id): bool
{
    // Only authenticated users can access
}
```

### Information Disclosure

**Hide sensitive data:**
```php
public function getUser(int $id): array
{
    $user = $this->db->getUser($id);

    // Remove sensitive fields
    unset($user['password']);
    unset($user['salt']);
    unset($user['reset_token']);

    return $user;
}
```

---

## Security Checklist

Before deploying to production:

- [ ] HTTPS enabled
- [ ] Production mode enabled
- [ ] Authentication implemented
- [ ] Rate limiting configured
- [ ] CORS properly configured
- [ ] Input validation on all endpoints
- [ ] Output sanitization for HTML
- [ ] SQL parameterization
- [ ] CSRF protection on forms
- [ ] Sensitive files protected
- [ ] Error messages don't leak info
- [ ] Dependencies updated
- [ ] Security headers configured
- [ ] File upload validation (if applicable)
- [ ] API key/token storage secure

---

## Security Headers

Configure your web server to send security headers:

### Apache

```apache
# .htaccess or httpd.conf
Header set X-Frame-Options "DENY"
Header set X-Content-Type-Options "nosniff"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
```

### Nginx

```nginx
add_header X-Frame-Options "DENY";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
add_header Referrer-Policy "strict-origin-when-cross-origin";
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()";
```

### PHP

```php
use Luracast\Restler\Defaults;

Defaults::$headerSecurity = [
    'X-Frame-Options' => 'DENY',
    'X-Content-Type-Options' => 'nosniff',
    'X-XSS-Protection' => '1; mode=block',
];
```

---

## Penetration Testing

We encourage responsible security research. If you want to test:

1. **Set up locally** - Don't test on production
2. **Use the examples** - Test the included example APIs
3. **Report findings** - Email arul@luracast.com
4. **Don't be malicious** - No DoS, data destruction, etc.

---

## Security Resources

* [OWASP Top 10](https://owasp.org/www-project-top-ten/)
* [OWASP API Security](https://owasp.org/www-project-api-security/)
* [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
* [Web Security Testing Guide](https://owasp.org/www-project-web-security-testing-guide/)

---

## Disclosure Policy

When we receive a security report:

1. We confirm the vulnerability
2. We develop a fix
3. We release a patched version
4. We publish a security advisory
5. We credit the reporter (unless they prefer anonymity)

**Responsible Disclosure Timeline:**
* Day 0: Report received
* Day 1-2: Initial response sent
* Day 3-7: Investigation and confirmation
* Day 7-30: Patch development and testing
* Day 30-90: Public disclosure (after patch release)

---

## Credits

We thank the following security researchers:

* (Your name could be here - report responsibly!)

---

**Last Updated**: November 2024
**Next Review**: March 2025
