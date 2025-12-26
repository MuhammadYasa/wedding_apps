<?php

namespace app\controllers;

use Yii;
use app\models\Wish;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * AdminWishController handles wish moderation and management
 */
class AdminWishController extends Controller
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
                    'approve' => ['POST'],
                    'bulk-approve' => ['POST'],
                    'bulk-delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Wish models
     * @param int|null $id Invitation ID filter
     * @return string
     */
    public function actionIndex($id = null)
    {
        if ($id === null && !Yii::$app->user->identity->isSuperUser()) {
            // For client users, show only their invitation's wishes
            $invitations = Yii::$app->user->identity->invitations;
            if (!empty($invitations)) {
                $id = $invitations[0]->id;
            }
        }

        $query = Wish::find()->with(['invitation', 'guest']);
        
        if ($id) {
            $query->where(['invitation_id' => $id]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['is_approved' => SORT_ASC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $invitations = Yii::$app->user->identity->isSuperUser() 
            ? Invitation::find()->orderBy(['event_date' => SORT_DESC])->all()
            : Yii::$app->user->identity->invitations;

        $selectedInvitation = $id ? Invitation::findOne($id) : null;
        
        $totalWishes = Wish::find()->count();
        $approvedWishes = Wish::find()->where(['is_approved' => 1])->count();
        $pendingWishes = Wish::find()->where(['is_approved' => 0])->count();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'invitations' => $invitations,
            'selectedInvitation' => $selectedInvitation,
            'totalWishes' => $totalWishes,
            'approvedWishes' => $approvedWishes,
            'pendingWishes' => $pendingWishes,
        ]);
    }

    /**
     * Displays a single Wish model
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing Wish model
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ucapan berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Approve a wish
     * @param int $id
     * @return Response
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        
        if ($model->approve()) {
            Yii::$app->session->setFlash('success', 'Ucapan telah disetujui.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menyetujui ucapan.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Wish model
     * @param int $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Ucapan telah dihapus.');

        return $this->redirect(['index']);
    }

    /**
     * Bulk approve wishes
     * @return Response
     */
    public function actionBulkApprove()
    {
        $ids = Yii::$app->request->post('selection', []);
        
        if (empty($ids)) {
            Yii::$app->session->setFlash('warning', 'Tidak ada ucapan yang dipilih.');
            return $this->redirect(['index']);
        }

        $count = Wish::updateAll(['is_approved' => 1], ['id' => $ids]);
        
        Yii::$app->session->setFlash('success', "{$count} ucapan telah disetujui.");
        
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete wishes
     * @return Response
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('selection', []);
        
        if (empty($ids)) {
            Yii::$app->session->setFlash('warning', 'Tidak ada ucapan yang dipilih.');
            return $this->redirect(['index']);
        }

        $count = Wish::deleteAll(['id' => $ids]);
        
        Yii::$app->session->setFlash('success', "{$count} ucapan telah dihapus.");
        
        return $this->redirect(['index']);
    }

    /**
     * Get wish statistics (AJAX)
     * @param int|null $id Invitation ID
     * @return Response
     */
    public function actionStats($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Wish::find();
        if ($id) {
            $query->where(['invitation_id' => $id]);
        }
        
        return [
            'total' => (clone $query)->count(),
            'approved' => (clone $query)->where(['is_approved' => 1])->count(),
            'pending' => (clone $query)->where(['is_approved' => 0])->count(),
        ];
    }

    /**
     * Finds the Wish model based on its primary key value
     * @param int $id
     * @return Wish
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Wish::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Ucapan tidak ditemukan.');
    }
}
