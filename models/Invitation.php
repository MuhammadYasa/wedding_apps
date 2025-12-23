<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Url;

/**
 * Invitation model
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $bride_name
 * @property string $groom_name
 * @property string $bride_father
 * @property string $bride_mother
 * @property string $groom_father
 * @property string $groom_mother
 * @property int $event_date
 * @property string $event_time
 * @property string $venue
 * @property string $venue_address
 * @property string $venue_map_url
 * @property float $venue_lat
 * @property float $venue_lng
 * @property string $cover_image
 * @property string $story
 * @property string $description
 * @property string $theme
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Gallery[] $galleries
 * @property Rsvp[] $rsvps
 * @property Guest[] $guests
 */
class Invitation extends ActiveRecord
{
    const THEME_DEFAULT = 'default';
    const THEME_ELEGANT = 'elegant';
    const THEME_RUSTIC = 'rustic';
    const THEME_MODERN = 'modern';

    public $coverFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%invitation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
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
            [['title', 'bride_name', 'groom_name', 'event_date'], 'required'],
            [['title', 'bride_name', 'groom_name', 'bride_father', 'bride_mother', 
              'groom_father', 'groom_mother'], 'string', 'max' => 255],
            [['slug', 'event_time'], 'string', 'max' => 50],
            [['venue', 'venue_address', 'story', 'description'], 'string'],
            [['venue_map_url'], 'string', 'max' => 500],
            [['venue_lat', 'venue_lng'], 'number'],
            [['event_date', 'created_at', 'updated_at'], 'integer'],
            [['is_active'], 'boolean'],
            [['theme'], 'in', 'range' => [
                self::THEME_DEFAULT, 
                self::THEME_ELEGANT, 
                self::THEME_RUSTIC, 
                self::THEME_MODERN
            ]],
            ['slug', 'unique'],
            [['coverFile'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize' => 2048000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Judul Undangan',
            'slug' => 'Slug',
            'bride_name' => 'Nama Mempelai Wanita',
            'groom_name' => 'Nama Mempelai Pria',
            'bride_father' => 'Ayah Mempelai Wanita',
            'bride_mother' => 'Ibu Mempelai Wanita',
            'groom_father' => 'Ayah Mempelai Pria',
            'groom_mother' => 'Ibu Mempelai Pria',
            'event_date' => 'Tanggal Acara',
            'event_time' => 'Waktu Acara',
            'venue' => 'Tempat Acara',
            'venue_address' => 'Alamat Lengkap',
            'venue_map_url' => 'URL Google Maps',
            'venue_lat' => 'Latitude',
            'venue_lng' => 'Longitude',
            'cover_image' => 'Cover Image',
            'story' => 'Cerita Kami',
            'description' => 'Deskripsi',
            'theme' => 'Tema',
            'is_active' => 'Aktif',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diupdate Pada',
        ];
    }

    /**
     * Gets query for [[Gallery]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGalleries()
    {
        return $this->hasMany(Gallery::class, ['invitation_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);
    }

    /**
     * Gets query for [[Rsvp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRsvps()
    {
        return $this->hasMany(Rsvp::class, ['invitation_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Gets attending RSVPs count
     */
    public function getAttendingCount()
    {
        return $this->getRsvps()
            ->where(['attendance' => 'attending'])
            ->sum('guests_count') ?? 0;
    }

    /**
     * Gets query for [[Guest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGuests()
    {
        return $this->hasMany(Guest::class, ['invitation_id' => 'id']);
    }

    /**
     * Get public URL
     */
    public function getUrl()
    {
        return Url::to(['invitation/view', 'slug' => $this->slug], true);
    }

    /**
     * Get WhatsApp share URL
     */
    public function getWhatsAppUrl($guestName = null)
    {
        $message = "Hai " . ($guestName ?? 'teman') . "! 🎉\n\n";
        $message .= "Dengan senang hati kami mengundang Anda di pernikahan kami:\n\n";
        $message .= "💑 *{$this->bride_name} & {$this->groom_name}*\n";
        $message .= "📅 " . date('d F Y', $this->event_date) . "\n";
        $message .= "📍 {$this->venue}\n\n";
        $message .= "Lihat undangan lengkap:\n";
        $message .= $this->getUrl();
        
        return 'https://wa.me/?text=' . urlencode($message);
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrl()
    {
        if ($this->cover_image) {
            return Yii::getAlias('@web/uploads/invitations/' . $this->id . '/' . $this->cover_image);
        }
        return Yii::getAlias('@web/img/default-cover.jpg');
    }

    /**
     * Get days until event
     */
    public function getDaysUntilEvent()
    {
        $now = time();
        $diff = $this->event_date - $now;
        return max(0, floor($diff / 86400)); // 86400 seconds in a day
    }

    /**
     * Check if event has passed
     */
    public function isPastEvent()
    {
        return time() > $this->event_date;
    }
}
