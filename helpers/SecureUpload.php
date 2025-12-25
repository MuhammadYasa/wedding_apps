<?php

namespace app\helpers;

use Yii;
use yii\web\UploadedFile;
use yii\base\InvalidConfigException;

/**
 * Secure file upload helper
 * Day 11: Security Hardening
 */
class SecureUpload
{
    /**
     * Validate and sanitize uploaded file
     * 
     * @param UploadedFile $file The uploaded file
     * @param array $options Validation options
     * @return bool|string Returns sanitized filename or false on error
     */
    public static function validate($file, $options = [])
    {
        if (!$file instanceof UploadedFile) {
            return false;
        }
        
        $security = require Yii::getAlias('@app/config/security.php');
        $uploadConfig = $security['upload'];
        
        // Merge default options with custom options
        $maxSize = $options['maxSize'] ?? $uploadConfig['maxSize'];
        $allowedExtensions = $options['allowedExtensions'] ?? $uploadConfig['allowedExtensions'];
        $allowedMimeTypes = $options['allowedMimeTypes'] ?? $uploadConfig['allowedMimeTypes'];
        $validateContent = $options['validateImageContent'] ?? $uploadConfig['validateImageContent'];
        
        // Check file size
        if ($file->size > $maxSize) {
            Yii::$app->session->setFlash('error', 'Ukuran file terlalu besar. Maksimal ' . self::formatBytes($maxSize));
            return false;
        }
        
        // Check file extension
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedExtensions)) {
            Yii::$app->session->setFlash('error', 'Ekstensi file tidak diperbolehkan. Hanya: ' . implode(', ', $allowedExtensions));
            return false;
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->tempName);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            Yii::$app->session->setFlash('error', 'Tipe file tidak valid.');
            return false;
        }
        
        // Validate image content if enabled
        if ($validateContent && strpos($mimeType, 'image/') === 0) {
            if (!self::validateImageContent($file->tempName)) {
                Yii::$app->session->setFlash('error', 'File gambar tidak valid atau rusak.');
                return false;
            }
        }
        
        // Generate safe filename
        $safeFilename = self::generateSafeFilename($file->baseName, $extension);
        
        return $safeFilename;
    }
    
    /**
     * Validate actual image content
     */
    protected static function validateImageContent($path)
    {
        try {
            $imageInfo = @getimagesize($path);
            if ($imageInfo === false) {
                return false;
            }
            
            // Check if it's a valid image type
            $validTypes = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];
            if (!in_array($imageInfo[2], $validTypes)) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Generate safe filename
     */
    protected static function generateSafeFilename($baseName, $extension)
    {
        // Remove special characters and sanitize
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '', $baseName);
        
        // Limit length
        $baseName = substr($baseName, 0, 50);
        
        // Add timestamp for uniqueness
        $timestamp = time();
        $random = substr(md5(uniqid()), 0, 8);
        
        return $baseName . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
    
    /**
     * Format bytes to human readable size
     */
    protected static function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Save uploaded file securely
     */
    public static function save($file, $directory, $options = [])
    {
        $safeFilename = self::validate($file, $options);
        
        if (!$safeFilename) {
            return false;
        }
        
        // Create directory if not exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filePath = $directory . '/' . $safeFilename;
        
        // Save file
        if ($file->saveAs($filePath)) {
            // Set proper permissions
            chmod($filePath, 0644);
            return $safeFilename;
        }
        
        return false;
    }
}
