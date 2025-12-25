<?php

namespace app\controllers;

use Yii;
use app\models\Rsvp;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * AdminRsvpController implements admin RSVP management
 */
class AdminRsvpController extends Controller
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
        ];
    }

    /**
     * Lists all RSVP models with filtering
     * @return mixed
     */
    public function actionIndex($attendance = null, $invitation_id = null)
    {
        // Get from GET parameters (untuk support pagination)
        $attendance = Yii::$app->request->get('attendance', $attendance);
        $invitation_id = Yii::$app->request->get('invitation_id', $invitation_id);
        
        $query = Rsvp::find()->with('invitation');

        // Filter by user ownership for client users
        if (!Yii::$app->user->identity->isSuperUser()) {
            $query->joinWith('invitation')
                ->andWhere(['invitation.user_id' => Yii::$app->user->id]);
        }

        // Normalize 'all' or empty to null
        if ($attendance === 'all' || $attendance === '' || $attendance === null) {
            $attendance = null;
        }
        if ($invitation_id === 'all' || $invitation_id === '' || $invitation_id === null) {
            $invitation_id = null;
        }

        // Filter by attendance
        if ($attendance !== null && in_array($attendance, ['attending', 'not_attending'])) {
            $query->andWhere(['attendance' => $attendance]);
        }

        // Filter by invitation
        if ($invitation_id !== null) {
            $query->andWhere(['rsvp.invitation_id' => $invitation_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
                'attributes' => [
                    'name',
                    'email',
                    'created_at',
                    'attendance',
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'params' => array_merge(
                    Yii::$app->request->getQueryParams(),
                    [
                        'attendance' => $attendance,
                        'invitation_id' => $invitation_id,
                    ]
                ),
            ],
        ]);

        // Get statistics
        $statsQuery = Rsvp::find();
        if (!Yii::$app->user->identity->isSuperUser()) {
            $statsQuery->joinWith('invitation')
                ->andWhere(['invitation.user_id' => Yii::$app->user->id]);
        }
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'attending' => (clone $statsQuery)->where(['attendance' => Rsvp::ATTENDANCE_ATTENDING])->count(),
            'not_attending' => (clone $statsQuery)->where(['attendance' => Rsvp::ATTENDANCE_NOT_ATTENDING])->count(),
            'total_guests' => (clone $statsQuery)->where(['attendance' => Rsvp::ATTENDANCE_ATTENDING])->sum('guests_count') ?: 0,
        ];

        // Get invitations for filter dropdown
        $invitationsQuery = Invitation::find();
        if (!Yii::$app->user->identity->isSuperUser()) {
            $invitationsQuery->andWhere(['user_id' => Yii::$app->user->id]);
        }
        $invitations = $invitationsQuery->orderBy(['title' => SORT_ASC])->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'stats' => $stats,
            'invitations' => $invitations,
            'currentAttendance' => $attendance,
            'currentInvitation' => $invitation_id,
        ]);
    }

    /**
     * Displays a single RSVP model
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing RSVP model
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'RSVP berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Export RSVPs to CSV
     * @return mixed
     */
    public function actionExport($attendance = null, $invitation_id = null)
    {
        $query = Rsvp::find()->with('invitation');

        // Filter by user ownership for client users
        if (!Yii::$app->user->identity->isSuperUser()) {
            $query->joinWith('invitation')
                ->andWhere(['invitation.user_id' => Yii::$app->user->id]);
        }

        // Apply same filters as index
        if ($attendance !== null && in_array($attendance, [Rsvp::ATTENDANCE_ATTENDING, Rsvp::ATTENDANCE_NOT_ATTENDING])) {
            $query->andWhere(['attendance' => $attendance]);
        }

        if ($invitation_id !== null) {
            $query->andWhere(['rsvp.invitation_id' => $invitation_id]);
        }

        $rsvps = $query->orderBy(['created_at' => SORT_DESC])->all();

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rsvp_export_' . date('Y-m-d_His') . '.csv"');

        // Create file pointer
        $output = fopen('php://output', 'w');

        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add CSV headers
        fputcsv($output, [
            'ID',
            'Undangan',
            'Nama',
            'Email',
            'Telepon',
            'Kehadiran',
            'Jumlah Tamu',
            'Pesan',
            'Tanggal RSVP'
        ]);

        // Add data rows
        foreach ($rsvps as $rsvp) {
            fputcsv($output, [
                $rsvp->id,
                $rsvp->invitation ? $rsvp->invitation->title : '-',
                $rsvp->name,
                $rsvp->email,
                $rsvp->phone ?: '-',
                $rsvp->getAttendanceLabel(),
                $rsvp->guests_count ?: '-',
                $rsvp->message ?: '-',
                date('Y-m-d H:i:s', $rsvp->created_at),
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Finds the RSVP model based on its primary key value
     * @param integer $id
     * @return Rsvp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Rsvp::find()->where(['rsvp.id' => $id]);

        // Filter by user ownership for client users
        if (!Yii::$app->user->identity->isSuperUser()) {
            $query->joinWith('invitation')
                ->andWhere(['invitation.user_id' => Yii::$app->user->id]);
        }

        $model = $query->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('RSVP tidak ditemukan.');
    }
}
