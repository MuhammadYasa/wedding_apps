<?php

namespace app\controllers;

use Yii;
use app\models\Guest;
use app\models\Invitation;
use app\helpers\QrCodeHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CheckInController handles guest check-in functionality with QR codes
 */
class CheckInController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'scan', 'check-in', 'check-out', 'bulk-generate'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'scan', 'check-in', 'check-out', 'bulk-generate'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'check-in' => ['POST'],
                    'check-out' => ['POST'],
                    'bulk-generate' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Display check-in dashboard
     * @param int|null $id Invitation ID
     * @return string
     */
    public function actionIndex($id = null)
    {
        if ($id === null && !Yii::$app->user->identity->isSuperUser()) {
            // For client users, get their invitation
            $invitations = Yii::$app->user->identity->invitations;
            if (empty($invitations)) {
                throw new NotFoundHttpException('No invitation found for your account.');
            }
            $id = $invitations[0]->id;
        }

        $invitation = $id ? Invitation::findOne($id) : null;
        
        $query = Guest::find();
        
        if ($invitation) {
            $query->where(['invitation_id' => $invitation->id]);
        }
        
        $totalGuests = (clone $query)->count();
        $checkedInGuests = (clone $query)->where(['IS NOT', 'checked_in_at', null])->count();
        $pendingGuests = $totalGuests - $checkedInGuests;
        
        $guests = $query->orderBy(['checked_in_at' => SORT_DESC, 'name' => SORT_ASC])->all();
        
        $invitations = Invitation::find()->orderBy(['event_date' => SORT_DESC])->all();

        return $this->render('index', [
            'invitation' => $invitation,
            'invitations' => $invitations,
            'guests' => $guests,
            'totalGuests' => $totalGuests,
            'checkedInGuests' => $checkedInGuests,
            'pendingGuests' => $pendingGuests,
        ]);
    }

    /**
     * QR code scanner interface
     * @return string
     */
    public function actionScanner()
    {
        return $this->render('scanner');
    }

    /**
     * Scan QR code and lookup guest
     * @param string|null $qr QR code
     * @return Response
     */
    public function actionScan($qr = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!$qr) {
            return ['success' => false, 'message' => 'No QR code provided'];
        }
        
        // Try to decode QR data first
        $qrData = QrCodeHelper::decodeQrCodeData($qr);
        $guest = null;
        
        if ($qrData && isset($qrData['guest_id'])) {
            $guest = Guest::findOne($qrData['guest_id']);
        } else {
            // Try direct QR code lookup
            $guest = Guest::findOne(['qr_code' => $qr]);
        }
        
        if ($guest) {
            return [
                'success' => true,
                'guest' => [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                    'checked_in_at' => $guest->checked_in_at,
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid QR code or guest not found'];
    }

    /**
     * Check in a guest
     * @return Response
     */
    public function actionCheckIn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $guestId = Yii::$app->request->post('guest_id');
        $qrCode = Yii::$app->request->post('qr_code');
        
        $guest = null;
        
        if ($guestId) {
            $guest = Guest::findOne($guestId);
        } elseif ($qrCode) {
            // Decode QR code data
            $qrData = QrCodeHelper::decodeQrCodeData($qrCode);
            if ($qrData && isset($qrData['guest_id'])) {
                $guest = Guest::findOne($qrData['guest_id']);
            } else {
                // Try direct QR code lookup
                $guest = Guest::findOne(['qr_code' => $qrCode]);
            }
        }
        
        if (!$guest) {
            return ['success' => false, 'message' => 'Guest not found'];
        }
        
        if ($guest->checked_in_at) {
            return [
                'success' => false, 
                'message' => 'Guest already checked in at ' . Yii::$app->formatter->asDatetime($guest->checked_in_at)
            ];
        }
        
        $guest->checked_in_at = time();
        $guest->checked_in_by = Yii::$app->user->id;
        
        if ($guest->save(false)) {
            return [
                'success' => true,
                'message' => 'Successfully checked in: ' . $guest->name,
                'guest' => [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'checked_in_at' => Yii::$app->formatter->asDatetime($guest->checked_in_at),
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to check in guest'];
    }

    /**
     * Check out a guest (undo check-in)
     * @return Response
     */
    public function actionCheckOut()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $guestId = Yii::$app->request->post('guest_id');
        $guest = Guest::findOne($guestId);
        
        if (!$guest) {
            return ['success' => false, 'message' => 'Guest not found'];
        }
        
        if (!$guest->checked_in_at) {
            return ['success' => false, 'message' => 'Guest is not checked in'];
        }
        
        $guest->checked_in_at = null;
        $guest->checked_in_by = null;
        
        if ($guest->save(false)) {
            return [
                'success' => true,
                'message' => 'Successfully checked out: ' . $guest->name
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to check out guest'];
    }

    /**
     * Bulk generate QR codes for all guests without QR codes
     * @param int $id Invitation ID
     * @return Response
     */
    public function actionBulkGenerate($id)
    {
        $count = QrCodeHelper::bulkGenerateQrCodes($id);
        
        Yii::$app->session->setFlash('success', "Generated QR codes for {$count} guests");
        
        return $this->redirect(['index', 'id' => $id]);
    }
    
    /**
     * Get QR code image for a guest (AJAX)
     * @param int $id Guest ID
     * @return Response
     */
    public function actionGetQrCode($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $guest = Guest::findOne($id);
        if (!$guest) {
            return ['success' => false, 'message' => 'Guest not found'];
        }
        
        if (!$guest->qr_code) {
            // Generate QR code if it doesn't exist
            $guest->qr_code = QrCodeHelper::generateUniqueQrCodeToken();
            $guest->save(false);
        }
        
        try {
            $qrCodeDataUri = QrCodeHelper::generateGuestQrCode($guest);
            return [
                'success' => true,
                'qr_code' => $qrCodeDataUri
            ];
        } catch (\Exception $e) {
            Yii::error("Failed to generate QR code for guest {$id}: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to generate QR code'];
        }
    }
}
