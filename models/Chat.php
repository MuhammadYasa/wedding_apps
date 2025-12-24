<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%chat}}".
 *
 * @property int $id
 * @property int $invitation_id
 * @property string $guest_name
 * @property string $message
 * @property int $created_at
 *
 * @property Invitation $invitation
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%chat}}';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invitation_id', 'guest_name', 'message'], 'required'],
            [['invitation_id', 'created_at'], 'integer'],
            [['message'], 'string'],
            [['guest_name'], 'string', 'max' => 255],
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
            'guest_name' => 'Guest Name',
            'message' => 'Message',
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
     * Format created_at timestamp to readable time
     * 
     * @return string
     */
    public function getFormattedTime()
    {
        $now = time();
        $diff = $now - $this->created_at;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' menit lalu';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' jam lalu';
        } else {
            return date('d M Y, H:i', $this->created_at);
        }
    }
}
