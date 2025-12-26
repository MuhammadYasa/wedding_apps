<?php

namespace app\controllers;

use Yii;
use app\models\Invitation;
use app\models\Guest;
use app\helpers\EmailHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * AdminEmailController handles sending invitation emails to guests
 */
class AdminEmailController extends Controller
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
                    'send-single' => ['POST'],
                    'send-batch' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all invitations for email management
     * @return string
     */
    public function actionIndex()
    {
        $invitations = Invitation::find()
            ->with(['guests'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'invitations' => $invitations,
        ]);
    }

    /**
     * Manage emails for a specific invitation
     * @param integer $id Invitation ID
     * @return string
     */
    public function actionManage($id)
    {
        $invitation = $this->findModel($id);
        
        $guests = Guest::find()
            ->where(['invitation_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('manage', [
            'invitation' => $invitation,
            'guests' => $guests,
        ]);
    }

    /**
     * Send invitation email to a single guest
     * @param integer $id Guest ID
     * @return \yii\web\Response
     */
    public function actionSendSingle($id)
    {
        $guest = Guest::findOne($id);
        if (!$guest) {
            throw new NotFoundHttpException('Guest not found.');
        }

        $invitation = $guest->invitation;
        if (!$invitation) {
            throw new NotFoundHttpException('Invitation not found.');
        }

        if (!$guest->email) {
            Yii::$app->session->setFlash('error', 'Guest does not have an email address.');
            return $this->redirect(['manage', 'id' => $invitation->id]);
        }

        $sent = EmailHelper::sendInvitation($invitation, $guest);
        
        if ($sent) {
            Yii::$app->session->setFlash('success', "Invitation email sent successfully to {$guest->name} ({$guest->email})");
        } else {
            Yii::$app->session->setFlash('error', "Failed to send invitation email to {$guest->name}");
        }

        return $this->redirect(['manage', 'id' => $invitation->id]);
    }

    /**
     * Send invitation emails to all guests with email addresses
     * @param integer $id Invitation ID
     * @return \yii\web\Response
     */
    public function actionSendBatch($id)
    {
        $invitation = $this->findModel($id);
        
        $guests = Guest::find()
            ->where(['invitation_id' => $id])
            ->andWhere(['IS NOT', 'email', null])
            ->andWhere(['<>', 'email', ''])
            ->all();

        if (empty($guests)) {
            Yii::$app->session->setFlash('warning', 'No guests with email addresses found.');
            return $this->redirect(['manage', 'id' => $id]);
        }

        $result = EmailHelper::sendBatchInvitations($invitation, $guests);

        if ($result['sent'] > 0) {
            Yii::$app->session->setFlash('success', 
                "Successfully sent {$result['sent']} out of {$result['total']} emails. Failed: {$result['failed']}");
        } else {
            Yii::$app->session->setFlash('error', 
                "Failed to send all emails. Sent: {$result['sent']}, Failed: {$result['failed']}");
        }

        return $this->redirect(['manage', 'id' => $id]);
    }

    /**
     * Send RSVP reminder to guest who hasn't responded
     * @param integer $id Guest ID
     * @return \yii\web\Response
     */
    public function actionSendReminder($id)
    {
        $guest = Guest::findOne($id);
        if (!$guest) {
            throw new NotFoundHttpException('Guest not found.');
        }

        $invitation = $guest->invitation;
        
        // Check if guest already has RSVP
        $hasRsvp = \app\models\Rsvp::find()
            ->where(['invitation_id' => $invitation->id])
            ->andWhere(['or',
                ['email' => $guest->email],
                ['name' => $guest->name]
            ])
            ->exists();

        if ($hasRsvp) {
            Yii::$app->session->setFlash('info', "{$guest->name} has already submitted an RSVP.");
            return $this->redirect(['manage', 'id' => $invitation->id]);
        }

        $sent = EmailHelper::sendRsvpReminder($invitation, $guest);
        
        if ($sent) {
            Yii::$app->session->setFlash('success', "Reminder sent to {$guest->name}");
        } else {
            Yii::$app->session->setFlash('error', "Failed to send reminder to {$guest->name}");
        }

        return $this->redirect(['manage', 'id' => $invitation->id]);
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * @param integer $id
     * @return Invitation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invitation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested invitation does not exist.');
    }
}
