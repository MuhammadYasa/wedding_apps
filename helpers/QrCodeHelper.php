<?php

namespace app\helpers;

use Yii;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;

/**
 * QR Code Helper for generating QR codes for guests
 */
class QrCodeHelper
{
    /**
     * Generate QR code for a guest
     * 
     * @param \app\models\Guest $guest
     * @param bool $returnDataUri Return as data URI for inline display
     * @return string QR code image data or data URI
     */
    public static function generateGuestQrCode($guest, $returnDataUri = true)
    {
        // Generate unique QR code data
        $qrData = self::getGuestQrCodeData($guest);
        
        // Create QR code
        $qrCode = QrCode::create($qrData)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(139, 115, 85)) // Secondary color
            ->setBackgroundColor(new Color(255, 255, 255));

        // Add label
        $label = Label::create($guest->name)
            ->setTextColor(new Color(51, 51, 51));

        $writer = new PngWriter();
        $result = $writer->write($qrCode, null, $label);

        if ($returnDataUri) {
            return $result->getDataUri();
        }

        return $result->getString();
    }

    /**
     * Get QR code data for guest
     * 
     * @param \app\models\Guest $guest
     * @return string
     */
    public static function getGuestQrCodeData($guest)
    {
        if (!$guest->qr_code) {
            $guest->qr_code = self::generateUniqueQrCodeToken();
            $guest->save(false);
        }

        // Create JSON data with guest information
        $data = [
            'type' => 'wedding_guest',
            'qr_code' => $guest->qr_code,
            'guest_id' => $guest->id,
            'name' => $guest->name,
            'invitation_id' => $guest->invitation_id,
            'timestamp' => time(),
        ];

        return json_encode($data);
    }

    /**
     * Generate unique QR code token
     * 
     * @return string
     */
    public static function generateUniqueQrCodeToken()
    {
        return 'QR-' . strtoupper(bin2hex(random_bytes(16)));
    }

    /**
     * Validate and decode QR code data
     * 
     * @param string $qrData
     * @return array|null
     */
    public static function decodeQrCodeData($qrData)
    {
        try {
            $data = json_decode($qrData, true);
            
            if (!isset($data['type']) || $data['type'] !== 'wedding_guest') {
                return null;
            }
            
            if (!isset($data['qr_code']) || !isset($data['guest_id'])) {
                return null;
            }
            
            return $data;
        } catch (\Exception $e) {
            Yii::error("Failed to decode QR code data: " . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Save QR code to file
     * 
     * @param \app\models\Guest $guest
     * @param string $directory
     * @return string|false File path or false on failure
     */
    public static function saveQrCodeToFile($guest, $directory = '@webroot/uploads/qr-codes')
    {
        try {
            $dir = Yii::getAlias($directory);
            
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $fileName = 'guest-' . $guest->id . '-qr.png';
            $filePath = $dir . '/' . $fileName;

            $qrCodeData = self::generateGuestQrCode($guest, false);
            file_put_contents($filePath, $qrCodeData);

            return $filePath;
        } catch (\Exception $e) {
            Yii::error("Failed to save QR code to file: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Generate QR code for check-in URL
     * 
     * @param \app\models\Guest $guest
     * @param bool $returnDataUri
     * @return string
     */
    public static function generateCheckInUrlQrCode($guest, $returnDataUri = true)
    {
        $checkInUrl = \yii\helpers\Url::to(
            ['check-in/scan', 'qr' => $guest->qr_code],
            true
        );

        $qrCode = QrCode::create($checkInUrl)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->setSize(250)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(212, 165, 116)) // Primary color
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        if ($returnDataUri) {
            return $result->getDataUri();
        }

        return $result->getString();
    }

    /**
     * Bulk generate QR codes for all guests of an invitation
     * 
     * @param int $invitationId
     * @return int Number of QR codes generated
     */
    public static function bulkGenerateQrCodes($invitationId)
    {
        $guests = \app\models\Guest::find()
            ->where(['invitation_id' => $invitationId])
            ->andWhere(['qr_code' => null])
            ->all();

        $count = 0;
        foreach ($guests as $guest) {
            $guest->qr_code = self::generateUniqueQrCodeToken();
            if ($guest->save(false)) {
                $count++;
            }
        }

        return $count;
    }
}
