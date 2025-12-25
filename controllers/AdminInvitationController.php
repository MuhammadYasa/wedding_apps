<?php

namespace app\controllers;

use Yii;
use app\models\Invitation;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminInvitationController extends Controller
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
     * Lists all Invitation models.
     * @return string
     */
    public function actionIndex()
    {
        $query = Invitation::find();

        // Filter by user ownership for client users
        if (!Yii::$app->user->identity->isSuperUser()) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
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
     * Displays a single Invitation model.
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
     * Creates a new Invitation model.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // Only super_user can create new invitations
        if (!Yii::$app->user->identity->isSuperUser()) {
            throw new NotFoundHttpException('You are not allowed to create invitations.');
        }

        $model = new Invitation();

        if ($model->load(Yii::$app->request->post())) {
            // Assign to selected user or current user
            if (empty($model->user_id)) {
                $model->user_id = Yii::$app->user->id;
            }
            
            if ($model->save()) {
                Yii::$app->session->addFlash('success', 'Invitation created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Invitation model.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $originalUserId = $model->user_id; // Store original user_id

        if ($model->load(Yii::$app->request->post())) {
            // Prevent client from changing user_id
            if (!Yii::$app->user->identity->isSuperUser()) {
                $model->user_id = $originalUserId;
            }
            
            if ($model->save()) {
                Yii::$app->session->addFlash('success', 'Data pernikahan berhasil diupdate!');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // Show validation errors
                $errors = $model->getErrors();
                $errorMsg = 'Gagal menyimpan data: ';
                foreach ($errors as $field => $messages) {
                    $errorMsg .= implode(', ', $messages) . '; ';
                }
                Yii::$app->session->addFlash('error', $errorMsg);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Invitation model.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // Only super_user can delete invitations
        if (!Yii::$app->user->identity->isSuperUser()) {
            throw new NotFoundHttpException('You are not allowed to delete invitations.');
        }

        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'Invitation deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Toggle status (active/inactive) of an Invitation.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        
        // Check if invitation belongs to a super user
        if ($model->user && $model->user->role === User::ROLE_SUPER_USER) {
            Yii::$app->session->setFlash('error', 'Status undangan super user tidak dapat diubah.');
            return $this->redirect(['index']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Toggle the invitation status
            $model->is_active = !$model->is_active;
            
            if ($model->save(false)) {
                // Also update user status if this is the only invitation for the user
                if ($model->user_id) {
                    $user = User::findOne($model->user_id);
                    if ($user && $user->role === User::ROLE_CLIENT) {
                        // Sync user status with invitation status
                        $user->is_active = $model->is_active;
                        $user->save(false);
                    }
                }
                
                $transaction->commit();
                $statusText = $model->is_active ? 'aktif' : 'non-aktif';
                Yii::$app->session->setFlash('success', "Status undangan berhasil diubah menjadi {$statusText}. Status user terkait juga telah di{$statusText}kan.");
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal mengubah status undangan.');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * @param int $id ID
     * @return Invitation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Invitation::find()->where(['id' => $id]);

        // Filter by user ownership for client users
        if (!Yii::$app->user->identity->isSuperUser()) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        $model = $query->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested invitation does not exist.');
    }
}
