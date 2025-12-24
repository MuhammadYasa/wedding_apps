<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Rsvp model
 *
 * @property int $id
 * @property int $invitation_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $attendance
 * @property int $guests_count
 * @property string $message
 * @property string $token
 * @property int $created_at
 *
 * @property Invitation $invitation
 */
class Rsvp extends ActiveRecord
{
    const ATTENDANCE_ATTENDING = 'attending';
    const ATTENDANCE_NOT_ATTENDING = 'not_attending';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rsvp}}';
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
            [['invitation_id', 'name', 'email', 'attendance'], 'required', 'message' => '{attribute} wajib diisi'],
            [['invitation_id', 'guests_count', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 191],
            ['email', 'email', 'message' => 'Format email tidak valid'],
            [['phone'], 'string', 'max' => 50],
            [['message'], 'string'],
            [['attendance'], 'in', 'range' => [self::ATTENDANCE_ATTENDING, self::ATTENDANCE_NOT_ATTENDING]],
            [['guests_count'], 'integer', 'min' => 1, 'max' => 2],
            [['guests_count'], 'required', 'when' => function($model) {
                return $model->attendance === self::ATTENDANCE_ATTENDING;
            }, 'whenClient' => "function (attribute, value) {
                return $('input[name=\"Rsvp[attendance]\"]:checked').val() === 'attending';
            }", 'message' => 'Jumlah tamu wajib diisi jika Anda hadir'],
            [['token'], 'string', 'max' => 128],
            [['invitation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invitation::class, 'targetAttribute' => ['invitation_id' => 'id']],
            // Unique constraint: satu nama hanya bisa RSVP sekali per invitation
            [['name'], 'unique', 'targetAttribute' => ['invitation_id', 'name'], 
                'message' => 'Anda sudah melakukan konfirmasi kehadiran untuk undangan ini.'],
            // Unique constraint: satu email hanya bisa RSVP sekali per invitation
            [['email'], 'unique', 'targetAttribute' => ['invitation_id', 'email'], 
                'message' => 'Email ini sudah digunakan untuk melakukan konfirmasi kehadiran.'],
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
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'phone' => 'No. Telepon / WhatsApp',
            'attendance' => 'Kehadiran',
            'guests_count' => 'Jumlah Tamu',
            'message' => 'Pesan & Doa',
            'token' => 'Token',
            'created_at' => 'Dibuat Pada',
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
     * Get attendance label
     */
    public function getAttendanceLabel()
    {
        return $this->attendance === self::ATTENDANCE_ATTENDING ? 'Hadir' : 'Tidak Hadir';
    }

    /**
     * Get attendance options for dropdown
     */
    public static function getAttendanceOptions()
    {
        return [
            self::ATTENDANCE_ATTENDING => 'Ya, saya akan hadir',
            self::ATTENDANCE_NOT_ATTENDING => 'Maaf, saya tidak bisa hadir',
        ];
    }

    /**
     * Generate unique token
     */
    public function generateToken()
    {
        $this->token = Yii::$app->security->generateRandomString(32);
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
