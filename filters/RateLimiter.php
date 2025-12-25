<?php

namespace app\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\TooManyRequestsHttpException;

/**
 * Rate Limiter filter for protecting endpoints from abuse
 * Day 11: Security Hardening
 */
class RateLimiter extends ActionFilter
{
    /**
     * @var string Rate limit type (login, rsvp, api)
     */
    public $type = 'api';
    
    /**
     * @var int Maximum number of requests allowed
     */
    public $maxAttempts;
    
    /**
     * @var int Time window in seconds
     */
    public $duration;
    
    /**
     * @var string Cache key prefix
     */
    public $cacheKeyPrefix = 'rate_limit_';
    
    public function init()
    {
        parent::init();
        
        $security = require Yii::getAlias('@app/config/security.php');
        $config = $security['rateLimit'][$this->type] ?? $security['rateLimit']['api'];
        
        if ($this->maxAttempts === null) {
            $this->maxAttempts = $config['maxAttempts'];
        }
        
        if ($this->duration === null) {
            $this->duration = $config['duration'];
        }
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $identifier = $this->getIdentifier();
        $cacheKey = $this->cacheKeyPrefix . $this->type . '_' . $identifier;
        
        $cache = Yii::$app->cache;
        $attempts = $cache->get($cacheKey) ?: 0;
        
        if ($attempts >= $this->maxAttempts) {
            throw new TooManyRequestsHttpException(
                "Terlalu banyak percobaan. Silakan coba lagi dalam " . 
                ceil($this->duration / 60) . " menit."
            );
        }
        
        // Increment attempts
        $cache->set($cacheKey, $attempts + 1, $this->duration);
        
        return parent::beforeAction($action);
    }
    
    /**
     * Get unique identifier for rate limiting (IP + User ID if logged in)
     */
    protected function getIdentifier()
    {
        $ip = Yii::$app->request->userIP;
        $userId = Yii::$app->user->id ?? 'guest';
        
        return md5($ip . '_' . $userId);
    }
    
    /**
     * Reset rate limit for an identifier
     */
    public static function reset($type, $identifier = null)
    {
        if ($identifier === null) {
            $ip = Yii::$app->request->userIP;
            $userId = Yii::$app->user->id ?? 'guest';
            $identifier = md5($ip . '_' . $userId);
        }
        
        $cacheKey = 'rate_limit_' . $type . '_' . $identifier;
        Yii::$app->cache->delete($cacheKey);
    }
}
