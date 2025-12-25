<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use yii\authclient\ClientInterface;

/**
 * AuthController handles authentication actions
 */
class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'callback' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Success callback for OAuth authentication
     */
    public function onAuthSuccess(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        
        $googleId = $attributes['id'] ?? null;
        $email = $attributes['email'] ?? null;
        $name = $attributes['name'] ?? null;
        
        if (!$googleId || !$email) {
            Yii::$app->session->setFlash('error', 'Gagal mendapatkan informasi dari Google.');
            return $this->redirect(['login']);
        }
        
        // Check if user exists with this email (added by super_user)
        /** @var User|null $user */
        $user = User::find()
            ->where(['email' => $email])
            ->one();
        
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Email Anda (' . $email . ') belum terdaftar. Silakan hubungi administrator untuk menambahkan akun Anda.');
            return $this->redirect(['login']);
        }
        
        // Update google_id if not set
        if (!$user->google_id) {
            $user->google_id = $googleId;
            $user->save(false);
        }
        
        // Login the user
        Yii::$app->user->login($user, 3600 * 24 * 30); // 30 days
        
        Yii::$app->session->setFlash('success', 'Login berhasil! Selamat datang, ' . $user->username);
        
        // Redirect based on role
        if ($user->isSuperUser()) {
            return $this->redirect(['/admin-invitation/index']);
        }
        return $this->redirect(['/admin-guest/index']);
    }

    /**
     * Login action.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            /** @var User $identity */
            $identity = Yii::$app->user->identity;
            // Redirect based on role
            if ($identity->isSuperUser()) {
                return $this->redirect(['/admin-invitation/index']);
            }
            return $this->redirect(['/admin-guest/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            /** @var User $identity */
            $identity = Yii::$app->user->identity;
            Yii::$app->session->setFlash('success', 'Login berhasil! Selamat datang, ' . $identity->username);
            
            // Redirect based on role
            if ($identity->isSuperUser()) {
                return $this->redirect(['/admin-invitation/index']);
            }
            return $this->redirect(['/admin-guest/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'Anda telah berhasil logout.');
        
        return $this->redirect(['login']);
    }
}
