<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Wish model
 *
 * @property int $id
 * @property int $invitation_id
 * @property int $guest_id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property int $is_approved
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Invitation $invitation
 * @property Guest $guest
 */
class Wish extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wish}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invitation_id', 'name', 'message'], 'required'],
            [['invitation_id', 'guest_id', 'is_approved', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string'],
            [['message'], 'string', 'max' => 1000, 'message' => 'Pesan terlalu panjang (maksimal 1000 karakter)'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 191],
            [['email'], 'email'],
            [['name'], 'filter', 'filter' => 'trim'],
            [['message'], 'filter', 'filter' => 'trim'],
            [['invitation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invitation::class, 'targetAttribute' => ['invitation_id' => 'id']],
            [['guest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Guest::class, 'targetAttribute' => ['guest_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invitation_id' => 'Undangan',
            'guest_id' => 'Tamu',
            'name' => 'Nama',
            'email' => 'Email',
            'message' => 'Pesan & Ucapan',
            'is_approved' => 'Disetujui',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
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
     * Gets query for [[Guest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGuest()
    {
        return $this->hasOne(Guest::class, ['id' => 'guest_id']);
    }

    /**
     * Get approved wishes for an invitation
     * @param int $invitationId
     * @param int $limit
     * @return Wish[]
     */
    public static function getApprovedWishes($invitationId, $limit = null)
    {
        $query = static::find()
            ->where(['invitation_id' => $invitationId, 'is_approved' => 1])
            ->orderBy(['created_at' => SORT_DESC]);
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->all();
    }

    /**
     * Get pending wishes count for an invitation
     * @param int $invitationId
     * @return int
     */
    public static function getPendingCount($invitationId = null)
    {
        $query = static::find()->where(['is_approved' => 0]);
        
        if ($invitationId) {
            $query->andWhere(['invitation_id' => $invitationId]);
        }
        
        return $query->count();
    }

    /**
     * Approve this wish
     * @return bool
     */
    public function approve()
    {
        $this->is_approved = 1;
        return $this->save(false);
    }

    /**
     * Reject (delete) this wish
     * @return bool
     */
    public function reject()
    {
        return $this->delete();
    }

    /**
     * Get formatted date
     * @return string
     */
    public function getFormattedDate()
    {
        return Yii::$app->formatter->asDatetime($this->created_at, 'php:d M Y, H:i');
    }

    /**
     * Get short message (truncated)
     * @param int $length
     * @return string
     */
    public function getShortMessage($length = 100)
    {
        if (mb_strlen($this->message) <= $length) {
            return $this->message;
        }
        
        return mb_substr($this->message, 0, $length) . '...';
    }
}
