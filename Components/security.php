<?php
/**
 * SECURITY.PHP
 * Siguraduhin na walang kahit anong space sa itaas ng <?php tag na ito.
 */

// 1. Simulan ang Output Buffering para pigilan ang "headers already sent"
if (ob_get_level() == 0) ob_start();

// 2. I-set ang session settings BAGO ang session_start
// Gagamit tayo ng error suppression (@) para hindi lumabas ang warning kung active na ang session
@ini_set('session.cookie_httponly', 1);
@ini_set('session.use_only_cookies', 1);
@ini_set('session.cookie_samesite', 'Lax');

// 3. Simulan ang session kung wala pa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Security Headers
if (!headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

// 5. CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function get_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

function get_csrf_input_field() {
    $token = get_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die('Security check failed: Invalid CSRF token. Please refresh the page.');
        }
    }
}