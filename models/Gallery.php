<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Gallery model
 *
 * @property int $id
 * @property int $invitation_id
 * @property string $filename
 * @property string $caption
 * @property int $sort_order
 * @property int $created_at
 *
 * @property Invitation $invitation
 */
class Gallery extends ActiveRecord
{
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%gallery}}';
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
            [['invitation_id', 'filename'], 'required'],
            [['invitation_id', 'sort_order', 'created_at'], 'integer'],
            [['filename', 'caption'], 'string', 'max' => 255],
            [['invitation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invitation::class, 'targetAttribute' => ['invitation_id' => 'id']],
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize' => 5120000],
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
            'filename' => 'Filename',
            'caption' => 'Caption',
            'sort_order' => 'Urutan',
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
     * Get image URL
     */
    public function getImageUrl()
    {
        return Yii::getAlias('@web/uploads/invitations/' . $this->invitation_id . '/gallery/' . $this->filename);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl()
    {
        $pathInfo = pathinfo($this->filename);
        $thumbName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        return Yii::getAlias('@web/uploads/invitations/' . $this->invitation_id . '/gallery/' . $thumbName);
    }

    /**
     * Delete image file when model is deleted
     */
    public function afterDelete()
    {
        parent::afterDelete();
        
        $imagePath = Yii::getAlias('@webroot/uploads/invitations/' . $this->invitation_id . '/gallery/' . $this->filename);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Also delete thumbnail
        $pathInfo = pathinfo($this->filename);
        $thumbName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        $thumbPath = Yii::getAlias('@webroot/uploads/invitations/' . $this->invitation_id . '/gallery/' . $thumbName);
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }
}
