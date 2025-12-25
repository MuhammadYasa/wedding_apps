<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\web\Response;

/**
 * Security headers component for enhancing application security
 * Day 11: Security Hardening
 */
class SecurityHeaders extends Component
{
    public function init()
    {
        parent::init();
        
        // Apply security headers to every response
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, [$this, 'addSecurityHeaders']);
    }
    
    /**
     * Add security headers to response
     */
    public function addSecurityHeaders()
    {
        $headers = Yii::$app->response->headers;
        $security = Yii::$app->params['security'] ?? require Yii::getAlias('@app/config/security.php');
        
        // Add security headers
        foreach ($security['headers'] as $name => $value) {
            $headers->set($name, $value);
        }
        
        // Add Content Security Policy
        if (!empty($security['csp']['enabled'])) {
            $cspDirectives = [];
            foreach ($security['csp']['directives'] as $directive => $value) {
                $cspDirectives[] = "$directive $value";
            }
            $headers->set('Content-Security-Policy', implode('; ', $cspDirectives));
        }
        
        // Add Strict-Transport-Security for HTTPS
        if (!YII_DEBUG && Yii::$app->request->isSecureConnection) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}
