<?php

/**
 * Security configuration for Wedding Apps
 * Day 11: Security Hardening
 */

return [
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'cookieValidationKey' => getenv('SECURITY_KEY') ?: 'wedding_csrf_key_change_in_production',
    ],
    
    // Password Policy
    'password' => [
        'minLength' => 8,
        'requireUppercase' => true,
        'requireLowercase' => true,
        'requireNumbers' => true,
        'requireSpecialChars' => false,
        'maxAge' => 90, // days
    ],
    
    // Session Security
    'session' => [
        'timeout' => 3600, // 1 hour
        'cookieParams' => [
            'httpOnly' => true,
            'secure' => !YII_DEBUG, // HTTPS only in production
            'sameSite' => 'Lax',
        ],
    ],
    
    // Rate Limiting
    'rateLimit' => [
        'login' => [
            'maxAttempts' => 5,
            'duration' => 300, // 5 minutes
        ],
        'rsvp' => [
            'maxAttempts' => 10,
            'duration' => 3600, // 1 hour
        ],
        'api' => [
            'maxAttempts' => 100,
            'duration' => 3600, // 1 hour
        ],
    ],
    
    // File Upload Security
    'upload' => [
        'maxSize' => 5 * 1024 * 1024, // 5 MB
        'allowedExtensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'allowedMimeTypes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
        ],
        'validateImageContent' => true,
    ],
    
    // Security Headers
    'headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    ],
    
    // Content Security Policy
    'csp' => [
        'enabled' => !YII_DEBUG,
        'directives' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://maps.googleapis.com",
            'style-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            'img-src' => "'self' data: https:",
            'font-src' => "'self' https://fonts.gstatic.com",
            'connect-src' => "'self' https://maps.googleapis.com",
            'frame-ancestors' => "'self'",
            'base-uri' => "'self'",
            'form-action' => "'self'",
        ],
    ],
];
