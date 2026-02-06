# iMarket Security Documentation

This document outlines the security features and protocols implemented in the iMarket platform to ensure user data protection and system integrity.

## 1. Authentication & Authorization

*   **Password Hashing**: User passwords are securely hashed using `password_hash()` (Bcrypt algorithm) before storage. We never store plain-text passwords.
*   **Session Management**:
    *   **HTTPOnly Cookies**: Session cookies are set with the `HttpOnly` flag to prevent access via JavaScript (mitigating XSS attacks).
    *   **Secure Cookies**: On HTTPS connections, the `Secure` flag is enabled.
    *   **Session Timeouts**: Automatic logout occurs after 30 minutes of inactivity to prevent unauthorized access on shared devices.
*   **Role-Based Access**: Critical admin functions are restricted to authorized accounts only.

## 2. Web Attacks Mitigation

*   **Cross-Site Request Forgery (CSRF)**:
    *   All state-changing forms (Login, Register, Checkout) are protected using unique, per-session CSRF tokens.
    *   The system validates these tokens on every POST request to ensure the request originated from our application.

*   **Cross-Site Scripting (XSS)**:
    *   **Output escaping**: User-generated content is escaped using `htmlspecialchars()` before being displayed in the browser.
    *   **Security Headers**: The application sends the `X-XSS-Protection: 1; mode=block` header.

*   **SQL Injection**:
    *   Database queries use prepared statements or proper `mysqli_real_escape_string` sanitization to separate code from data.

*   **Clickjacking**:
    *   The `X-Frame-Options: UPSTREAM` (or `SAMEORIGIN`) header is set to prevent the site from being maliciously embedded in iframes on other domains.

## 3. Data Protection

*   **MIME Sniffing Prevention**: `X-Content-Type-Options: nosniff` header is implemented to force browsers to respect the declared content type.
*   **Referrer Policy**: `Referrer-Policy: strict-origin-when-cross-origin` ensures we don't leak sensitive URL parameters to third-party sites.

## 4. Implementation Details

The core security logic is centralized in `Components/security.php`. This file is responsible for:
1.  Starting the session with secure parameters.
2.  Setting standard security HTTP headers.
3.  Generating and verifying CSRF tokens.

### How to use in code:
```php
require_once 'Components/security.php';

// In forms:
echo get_csrf_input_field();

// In POST handlers:
verify_csrf_token();
```
