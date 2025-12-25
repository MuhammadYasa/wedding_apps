<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminUserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isSuperUser();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->with('invitations'),
            'sort' => [
                'defaultOrder' => [
                    'role' => SORT_DESC, // super_user first, then client
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Auto-create invitation for client user
                    if ($model->role === User::ROLE_CLIENT && !empty($model->bride_name) && !empty($model->groom_name)) {
                        $invitation = new \app\models\Invitation();
                        $invitation->title = $model->bride_name . ' & ' . $model->groom_name;
                        $invitation->bride_name = $model->bride_name;
                        $invitation->groom_name = $model->groom_name;
                        $invitation->bride_nickname = $model->bride_nickname;
                        $invitation->groom_nickname = $model->groom_nickname;
                        $invitation->user_id = $model->id;
                        $invitation->event_date = strtotime('+1 month'); // Default 1 bulan dari sekarang
                        $invitation->is_active = 1;
                        $invitation->theme = 'default';
                        
                        if (!$invitation->save()) {
                            throw new \Exception('Failed to create invitation: ' . json_encode($invitation->errors));
                        }
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->addFlash('success', 'User created successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', 'Failed to create user: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->addFlash('success', 'User updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // Prevent deleting own account
        if ($id == Yii::$app->user->id) {
            Yii::$app->session->addFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['index']);
        }

        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'User deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Toggle status (active/inactive) of a User.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        
        // Prevent toggling own account status
        if ($id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
            return $this->redirect(['index']);
        }
        
        // Prevent toggling super user status
        if ($model->role === User::ROLE_SUPER_USER) {
            Yii::$app->session->setFlash('error', 'Status super user tidak dapat diubah.');
            return $this->redirect(['index']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Toggle the user status
            $model->is_active = !$model->is_active;
            
            if ($model->save(false)) {
                // Also toggle all invitations owned by this user (for client users)
                if ($model->role === User::ROLE_CLIENT) {
                    \app\models\Invitation::updateAll(
                        ['is_active' => $model->is_active],
                        ['user_id' => $model->id]
                    );
                }
                
                $transaction->commit();
                $statusText = $model->is_active ? 'aktif' : 'non-aktif';
                Yii::$app->session->setFlash('success', "Status user berhasil diubah menjadi {$statusText}. Semua undangan milik user ini juga telah di{$statusText}kan.");
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal mengubah status user.');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }
}
