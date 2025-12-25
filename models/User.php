<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $google_id
 * @property string $role
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_SUPER_USER = 'super_user';
    const ROLE_CLIENT = 'client';

    public $password;
    public $bride_name;
    public $groom_name;
    public $bride_nickname;
    public $groom_nickname;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['username', 'email', 'password', 'role', 'bride_name', 'groom_name', 'bride_nickname', 'groom_nickname'];
        $scenarios['update'] = ['username', 'email', 'password', 'role'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6, 'on' => ['create', 'update']],
            [['created_at', 'updated_at'], 'integer'],
            ['role', 'in', 'range' => [self::ROLE_SUPER_USER, self::ROLE_CLIENT]],
            ['role', 'default', 'value' => self::ROLE_CLIENT],
            ['username', 'string', 'max' => 255],
            ['username', 'unique'],
            ['email', 'email'],
            ['email', 'string', 'max' => 191],
            ['email', 'unique'],
            // Couple names required when creating client user
            [['bride_name', 'groom_name'], 'required', 'when' => function($model) {
                return $model->role === self::ROLE_CLIENT && $model->scenario === 'create';
            }, 'whenClient' => "function (attribute, value) {
                return $('#user-role').val() === 'client';
            }"],
            [['bride_name', 'groom_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
            'bride_name' => 'Nama Mempelai Wanita',
            'groom_name' => 'Nama Mempelai Pria',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Check if user is super user
     */
    public function isSuperUser()
    {
        return $this->role === self::ROLE_SUPER_USER;
    }

    /**
     * Check if user is client
     */
    public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    /**
     * Get invitations relation
     */
    public function getInvitations()
    {
        return $this->hasMany(Invitation::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
                $this->created_at = time();
            }
            $this->updated_at = time();

            // Hash password if set
            if (!empty($this->password)) {
                $this->setPassword($this->password);
            }

            return true;
        }
        return false;
    }
}
