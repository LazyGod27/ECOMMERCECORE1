<?php
// Components/security.php

// 1. Session Security
if (session_status() === PHP_SESSION_NONE) {
    // Set strict cookie parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Enable secure cookies if running on HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// 2. Security Headers (Global)
if (!headers_sent()) {
    // Prevent Clickjacking
    header("X-Frame-Options: SAMEORIGIN"); // Allow same origin framing (e.g. iframes within the app)
    // XSS Protection
    header("X-XSS-Protection: 1; mode=block");
    // Prevent MIME-sniffing
    header("X-Content-Type-Options: nosniff");
    // Referrer Policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    // Content Security Policy (Basic - allows scripts for now to avoid breaking things)
    // header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:;");
}

// 3. CSRF Protection Helper Functions
if (empty($_SESSION['csrf_token'])) {
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

function get_csrf_token() {
    return $_SESSION['csrf_token'];
}

function get_csrf_input_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Log the attempt here if logging is implemented
            die('Security check failed: Invalid CSRF token. Please refresh the page and try again.');
        }
    }
}
?>
