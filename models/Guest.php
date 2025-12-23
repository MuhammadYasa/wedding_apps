<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Url;

/**
 * Guest model
 *
 * @property int $id
 * @property int $invitation_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $whatsapp
 * @property string $slug
 * @property string $token
 * @property int $token_created_at
 * @property int $viewed_at
 * @property int $created_at
 *
 * @property Invitation $invitation
 */
class Guest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%guest}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'immutable' => false,
                'ensureUnique' => true,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invitation_id', 'name'], 'required'],
            [['invitation_id', 'token_created_at', 'viewed_at', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 191],
            ['email', 'email'],
            [['phone', 'whatsapp'], 'string', 'max' => 50],
            [['slug', 'token'], 'string', 'max' => 128],
            ['token', 'unique'],
            [['invitation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invitation::class, 'targetAttribute' => ['invitation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invitation_id' => 'Invitation ID',
            'name' => 'Nama Tamu',
            'email' => 'Email',
            'phone' => 'No. Telepon',
            'whatsapp' => 'No. WhatsApp',
            'slug' => 'Slug',
            'token' => 'Token',
            'token_created_at' => 'Token Created At',
            'viewed_at' => 'Viewed At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Invitation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitation()
    {
        return $this->hasOne(Invitation::class, ['id' => 'invitation_id']);
    }

    /**
     * Generate unique token
     */
    public function generateToken()
    {
        $this->token = Yii::$app->security->generateRandomString(32);
        $this->token_created_at = time();
    }

    /**
     * Get personalized invitation URL
     */
    public function getPersonalUrl()
    {
        return Url::to(['invitation/view', 'slug' => $this->invitation->slug, 'token' => $this->token], true);
    }

    /**
     * Get WhatsApp invitation link (manual distribution)
     * 
     * Generates a pre-filled WhatsApp Web/App link that admin can copy and use manually.
     * This method does NOT send messages automatically - it only creates the link.
     * 
     * How to use:
     * 1. Admin opens guest list in admin panel
     * 2. System generates WhatsApp link for each guest
     * 3. Admin clicks "Copy Link" or "Open WhatsApp"
     * 4. Admin manually sends to guest via WhatsApp Web/App
     * 
     * @return string|null WhatsApp link or null if no phone number
     */
    public function getWhatsAppLink()
    {
        $invitation = $this->invitation;
        
        // Format pesan undangan yang personal
        $message = "Kepada Yth.\n";
        $message .= "*{$this->name}*\n\n";
        $message .= "Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami:\n\n";
        $message .= "💑 *{$invitation->bride_name} & {$invitation->groom_name}*\n\n";
        $message .= "📅 " . date('d F Y', $invitation->event_date) . "\n";
        $message .= "⏰ {$invitation->event_time}\n";
        $message .= "📍 {$invitation->venue}\n\n";
        $message .= "Untuk informasi lengkap dan konfirmasi kehadiran, silakan klik link berikut:\n";
        $message .= $this->getPersonalUrl() . "\n\n";
        $message .= "Merupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.\n\n";
        $message .= "Terima kasih 🙏";
        
        $phone = $this->whatsapp ?: $this->phone;
        if ($phone) {
            // Remove non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $phone);
            // Add country code if not present (Indonesia)
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }
            
            // Return WhatsApp Web/App link with pre-filled message
            // Admin will manually open this link and send the message
            return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
        }
        
        return null;
    }

    /**
     * Get just the WhatsApp message text (without link)
     * Useful for copying message only
     */
    public function getWhatsAppMessage()
    {
        $invitation = $this->invitation;
        
        $message = "Kepada Yth.\n";
        $message .= "*{$this->name}*\n\n";
        $message .= "Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami:\n\n";
        $message .= "💑 *{$invitation->bride_name} & {$invitation->groom_name}*\n\n";
        $message .= "📅 " . date('d F Y', $invitation->event_date) . "\n";
        $message .= "⏰ {$invitation->event_time}\n";
        $message .= "📍 {$invitation->venue}\n\n";
        $message .= "Untuk informasi lengkap dan konfirmasi kehadiran, silakan klik link berikut:\n";
        $message .= $this->getPersonalUrl() . "\n\n";
        $message .= "Merupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.\n\n";
        $message .= "Terima kasih 🙏";
        
        return $message;
    }

    /**
     * Mark as viewed
     */
    public function markAsViewed()
    {
        if (!$this->viewed_at) {
            $this->viewed_at = time();
            $this->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && !$this->token) {
                $this->generateToken();
            }
            return true;
        }
        return false;
    }
}
