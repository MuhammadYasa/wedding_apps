<?php

namespace app\controllers;

use Yii;
use app\models\Rsvp;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RsvpController handles RSVP form submission for wedding invitations
 */
class RsvpController extends Controller
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
                    'submit' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Display RSVP form
     * @param int $id Invitation ID
     * @return string
     */
    public function actionIndex($id)
    {
        $invitation = $this->findInvitation($id);
        $model = new Rsvp();
        $model->invitation_id = $invitation->id;

        return $this->render('index', [
            'model' => $model,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Submit RSVP form
     * @return string|\yii\web\Response
     */
    public function actionSubmit()
    {
        $model = new Rsvp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Terima kasih! RSVP Anda telah berhasil dikirim.');
            return $this->redirect(['confirmation', 'token' => $model->token]);
        }

        // If validation fails, redirect back to form
        $invitation = null;
        if ($model->invitation_id) {
            $invitation = Invitation::findOne($model->invitation_id);
        }

        if (!$invitation) {
            throw new NotFoundHttpException('Undangan tidak ditemukan.');
        }

        return $this->render('index', [
            'model' => $model,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Display confirmation page after successful RSVP
     * @param string $token RSVP token
     * @return string
     */
    public function actionConfirmation($token)
    {
        $model = Rsvp::findOne(['token' => $token]);

        if ($model === null) {
            throw new NotFoundHttpException('RSVP tidak ditemukan.');
        }

        return $this->render('confirmation', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * @param int $id
     * @return Invitation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInvitation($id)
    {
        if (($model = Invitation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Undangan tidak ditemukan.');
    }
}
