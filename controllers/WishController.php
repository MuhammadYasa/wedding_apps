<?php

namespace app\controllers;

use Yii;
use app\models\Wish;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * WishController handles public wish/guestbook submissions
 */
class WishController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Display wishes gallery for an invitation
     * @param int $id Invitation ID
     * @return string
     */
    public function actionIndex($id)
    {
        $invitation = $this->findInvitation($id);
        
        $dataProvider = new ActiveDataProvider([
            'query' => Wish::find()
                ->where(['invitation_id' => $id, 'is_approved' => 1])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'invitation' => $invitation,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create a new wish
     * @param int $id Invitation ID
     * @return mixed
     */
    public function actionCreate($id)
    {
        $invitation = $this->findInvitation($id);
        $wish = new Wish();
        $wish->invitation_id = $id;

        if ($wish->load(Yii::$app->request->post())) {
            // Auto-approve if settings allow, otherwise needs moderation
            $wish->is_approved = Yii::$app->params['autoApproveWishes'] ?? 0;
            
            if ($wish->save()) {
                Yii::$app->session->setFlash('success', 
                    $wish->is_approved 
                        ? 'Terima kasih! Ucapan Anda telah dipublikasikan.' 
                        : 'Terima kasih! Ucapan Anda akan ditampilkan setelah disetujui.'
                );
                
                // Log activity
                Yii::info("New wish from {$wish->name} for invitation #{$id}", __METHOD__);
                
                return $this->redirect(['index', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', 'Gagal menyimpan ucapan. Silakan coba lagi.');
            }
        }

        return $this->render('create', [
            'wish' => $wish,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Finds the Invitation model
     * @param int $id
     * @return Invitation
     * @throws NotFoundHttpException
     */
    protected function findInvitation($id)
    {
        if (($model = Invitation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Undangan tidak ditemukan.');
    }
}
