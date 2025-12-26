<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Invitation;
use app\models\Guest;
use app\models\Rsvp;
use yii\db\Expression;

/**
 * AdminAnalyticsController handles analytics and reporting
 */
class AdminAnalyticsController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Analytics dashboard
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;

        // Get all invitations for the user
        $invitations = Invitation::find()
            ->where(['user_id' => $user->id])
            ->all();

        $invitationIds = array_map(function($inv) {
            return $inv->id;
        }, $invitations);

        // Overall statistics
        $stats = [
            'total_invitations' => count($invitations),
            'total_guests' => Guest::find()->where(['invitation_id' => $invitationIds])->count(),
            'total_rsvps' => Rsvp::find()->where(['invitation_id' => $invitationIds])->count(),
            'attending' => Rsvp::find()->where(['invitation_id' => $invitationIds, 'attendance' => 'yes'])->count(),
            'not_attending' => Rsvp::find()->where(['invitation_id' => $invitationIds, 'attendance' => 'no'])->count(),
            'maybe' => Rsvp::find()->where(['invitation_id' => $invitationIds, 'attendance' => 'maybe'])->count(),
        ];

        // Calculate response rate
        $stats['response_rate'] = $stats['total_guests'] > 0 
            ? round(($stats['total_rsvps'] / $stats['total_guests']) * 100, 1) 
            : 0;

        // Get RSVP trend data (last 30 days)
        $rsvpTrend = Rsvp::find()
            ->select([
                'DATE(FROM_UNIXTIME(created_at)) as date',
                'COUNT(*) as count'
            ])
            ->where(['invitation_id' => $invitationIds])
            ->andWhere(['>=', 'created_at', time() - (30 * 24 * 60 * 60)])
            ->groupBy('DATE(FROM_UNIXTIME(created_at))')
            ->orderBy('date ASC')
            ->asArray()
            ->all();

        // Get attendance distribution by invitation
        $invitationStats = [];
        foreach ($invitations as $invitation) {
            $invitationStats[] = [
                'id' => $invitation->id,
                'name' => $invitation->title,
                'total_guests' => Guest::find()->where(['invitation_id' => $invitation->id])->count(),
                'rsvp_count' => Rsvp::find()->where(['invitation_id' => $invitation->id])->count(),
                'attending' => Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'yes'])->count(),
                'not_attending' => Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'no'])->count(),
                'maybe' => Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'maybe'])->count(),
            ];
        }

        // Get recent RSVPs
        $recentRsvps = Rsvp::find()
            ->where(['invitation_id' => $invitationIds])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->with(['invitation', 'guest'])
            ->all();

        return $this->render('index', [
            'stats' => $stats,
            'rsvpTrend' => $rsvpTrend,
            'invitationStats' => $invitationStats,
            'recentRsvps' => $recentRsvps,
        ]);
    }

    /**
     * Get analytics data for specific invitation
     * @param int $id
     * @return string
     */
    public function actionInvitation($id)
    {
        $invitation = $this->findInvitationModel($id);

        // Guest statistics
        $stats = [
            'total_guests' => Guest::find()->where(['invitation_id' => $id])->count(),
            'total_rsvps' => Rsvp::find()->where(['invitation_id' => $id])->count(),
            'attending' => Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'yes'])->count(),
            'not_attending' => Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'no'])->count(),
            'maybe' => Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'maybe'])->count(),
            'pending' => 0,
        ];

        $stats['pending'] = $stats['total_guests'] - $stats['total_rsvps'];
        $stats['response_rate'] = $stats['total_guests'] > 0 
            ? round(($stats['total_rsvps'] / $stats['total_guests']) * 100, 1) 
            : 0;

        // RSVP timeline
        $rsvpTimeline = Rsvp::find()
            ->select([
                'DATE(FROM_UNIXTIME(created_at)) as date',
                'attendance',
                'COUNT(*) as count'
            ])
            ->where(['invitation_id' => $id])
            ->groupBy(['DATE(FROM_UNIXTIME(created_at))', 'attendance'])
            ->orderBy('date ASC')
            ->asArray()
            ->all();

        // Guests who haven't responded
        $respondedGuestIds = Rsvp::find()
            ->select('guest_id')
            ->where(['invitation_id' => $id])
            ->column();

        $pendingGuests = Guest::find()
            ->where(['invitation_id' => $id])
            ->andWhere(['NOT IN', 'id', $respondedGuestIds ?: [0]])
            ->all();

        return $this->render('invitation', [
            'invitation' => $invitation,
            'stats' => $stats,
            'rsvpTimeline' => $rsvpTimeline,
            'pendingGuests' => $pendingGuests,
        ]);
    }

    /**
     * Export analytics to PDF
     * @param int $id
     * @return mixed
     */
    public function actionExportPdf($id = null)
    {
        // TODO: Implement PDF export using TCPDF or similar
        Yii::$app->session->setFlash('info', 'PDF export will be implemented in the next iteration');
        return $this->redirect(['index']);
    }

    /**
     * Export analytics to Excel
     * @param int $id
     * @return mixed
     */
    public function actionExportExcel($id = null)
    {
        $user = Yii::$app->user->identity;
        
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        if ($id !== null) {
            // Export specific invitation
            $invitation = $this->findInvitationModel($id);
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('Wedding App')
                ->setTitle('Analytics Report - ' . $invitation->title)
                ->setSubject('RSVP Analytics')
                ->setDescription('Analytics report for ' . $invitation->title);
            
            // Add header
            $sheet->setCellValue('A1', 'Analytics Report: ' . $invitation->title);
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            
            // Summary section
            $stats = [
                'total_guests' => \app\models\Guest::find()->where(['invitation_id' => $id])->count(),
                'total_rsvps' => \app\models\Rsvp::find()->where(['invitation_id' => $id])->count(),
                'attending' => \app\models\Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'yes'])->count(),
                'not_attending' => \app\models\Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'no'])->count(),
                'maybe' => \app\models\Rsvp::find()->where(['invitation_id' => $id, 'attendance' => 'maybe'])->count(),
            ];
            
            $row = 3;
            $sheet->setCellValue('A' . $row, 'Summary Statistics');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Total Guests:');
            $sheet->setCellValue('B' . $row, $stats['total_guests']);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Total Responses:');
            $sheet->setCellValue('B' . $row, $stats['total_rsvps']);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Attending:');
            $sheet->setCellValue('B' . $row, $stats['attending']);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Not Attending:');
            $sheet->setCellValue('B' . $row, $stats['not_attending']);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Maybe:');
            $sheet->setCellValue('B' . $row, $stats['maybe']);
            $row += 2;
            
            // RSVP Details
            $sheet->setCellValue('A' . $row, 'RSVP Details');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $headers = ['Guest Name', 'Attendance', 'Message', 'Date'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $col++;
            }
            $row++;
            
            $rsvps = \app\models\Rsvp::find()
                ->where(['invitation_id' => $id])
                ->with('guest')
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            foreach ($rsvps as $rsvp) {
                $sheet->setCellValue('A' . $row, $rsvp->guest->name ?? 'Unknown');
                $sheet->setCellValue('B' . $row, ucfirst($rsvp->attendance));
                $sheet->setCellValue('C' . $row, $rsvp->message ?: '-');
                $sheet->setCellValue('D' . $row, date('Y-m-d H:i', $rsvp->created_at));
                $row++;
            }
            
            $filename = 'Analytics_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $invitation->title) . '_' . date('Y-m-d') . '.xlsx';
        } else {
            // Export all invitations summary
            $spreadsheet->getProperties()
                ->setCreator('Wedding App')
                ->setTitle('Analytics Report - All Invitations')
                ->setSubject('RSVP Analytics')
                ->setDescription('Overall analytics report');
            
            $sheet->setCellValue('A1', 'Overall Analytics Report');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            
            $invitations = \app\models\Invitation::find()
                ->where(['user_id' => $user->id])
                ->all();
            
            $row = 3;
            $headers = ['Invitation', 'Total Guests', 'Responses', 'Attending', 'Not Attending', 'Maybe', 'Response Rate'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $col++;
            }
            $row++;
            
            foreach ($invitations as $invitation) {
                $totalGuests = \app\models\Guest::find()->where(['invitation_id' => $invitation->id])->count();
                $rsvpCount = \app\models\Rsvp::find()->where(['invitation_id' => $invitation->id])->count();
                $attending = \app\models\Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'yes'])->count();
                $notAttending = \app\models\Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'no'])->count();
                $maybe = \app\models\Rsvp::find()->where(['invitation_id' => $invitation->id, 'attendance' => 'maybe'])->count();
                $responseRate = $totalGuests > 0 ? round(($rsvpCount / $totalGuests) * 100, 1) : 0;
                
                $sheet->setCellValue('A' . $row, $invitation->title);
                $sheet->setCellValue('B' . $row, $totalGuests);
                $sheet->setCellValue('C' . $row, $rsvpCount);
                $sheet->setCellValue('D' . $row, $attending);
                $sheet->setCellValue('E' . $row, $notAttending);
                $sheet->setCellValue('F' . $row, $maybe);
                $sheet->setCellValue('G' . $row, $responseRate . '%');
                $row++;
            }
            
            $filename = 'Analytics_All_Invitations_' . date('Y-m-d') . '.xlsx';
        }
        
        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * @param integer $id
     * @return Invitation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInvitationModel($id)
    {
        $user = Yii::$app->user->identity;
        
        if (($model = Invitation::findOne(['id' => $id, 'user_id' => $user->id])) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested invitation does not exist.');
    }
}
