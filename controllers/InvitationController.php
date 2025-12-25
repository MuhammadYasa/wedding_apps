<?php

namespace app\controllers;

use Yii;
use app\models\Invitation;
use app\models\Guest;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\filters\RateLimiter;

/**
 * InvitationController handles public invitation pages
 */
class InvitationController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
                'only' => ['rsvp', 'send-message'],
                'type' => 'rsvp',
            ],
        ];
    }

    /**
     * Display invitation by slug
     * 
     * @param string $slug Invitation slug (friendly URL)
     * @param string|null $token Guest token for personalized view
     * @param string|null $to Guest name for WhatsApp shared invitation
     * @param string|null $preview_theme Theme to preview (for testing purposes)
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $token = null, $to = null, $preview_theme = null)
    {
        // Find invitation by slug with eager loading
        $invitation = Invitation::find()
            ->where(['slug' => $slug])
            ->with(['galleries' => function($query) {
                $query->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);
            }])
            ->with(['rsvps' => function($query) {
                $query->orderBy(['created_at' => SORT_DESC])->limit(10);
            }])
            ->one();

        if (!$invitation) {
            throw new NotFoundHttpException('Undangan tidak ditemukan.');
        }

        // Check if invitation is active
        if (!$invitation->is_active) {
            throw new NotFoundHttpException('Undangan tidak ditemukan atau sudah tidak aktif.');
        }

        // Theme preview for testing (override actual theme)
        if ($preview_theme && in_array($preview_theme, ['default', 'elegant', 'rustic', 'modern'])) {
            $invitation->theme = $preview_theme;
        }

        // Guest personalization (if token or 'to' parameter provided)
        $guest = null;
        if ($token) {
            // Find guest by token
            $guest = Guest::findOne(['token' => $token, 'invitation_id' => $invitation->id]);
        } elseif ($to) {
            // Find guest by name (from WhatsApp share link)
            $guest = Guest::findOne(['name' => $to, 'invitation_id' => $invitation->id]);
        }

        // Mark invitation as viewed by this guest
        if ($guest) {
            $guest->markAsViewed();
        }

        // Use eager loaded galleries and RSVPs
        $galleries = $invitation->galleries;
        $recentRsvps = $invitation->rsvps;

        // Check if guest already submitted RSVP
        $existingRsvp = null;
        $guestNameFromUrl = null;
        if ($guest) {
            // Check by both name and email to be sure
            $existingRsvp = \app\models\Rsvp::find()
                ->where(['invitation_id' => $invitation->id])
                ->andWhere(['or',
                    ['email' => $guest->email],
                    ['name' => $guest->name]
                ])
                ->one();
            $guestNameFromUrl = $guest->name;
        }

        // Set page title and meta tags
        $this->view->title = $invitation->title . ' - Wedding Invitation';
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => $invitation->description ?? 'Undangan pernikahan ' . $invitation->bride_name . ' & ' . $invitation->groom_name
        ]);
        
        // Open Graph tags for social sharing
        $this->view->registerMetaTag(['property' => 'og:title', 'content' => $invitation->title]);
        $this->view->registerMetaTag(['property' => 'og:description', 'content' => $invitation->description ?? '']);
        $this->view->registerMetaTag(['property' => 'og:type', 'content' => 'website']);
        $this->view->registerMetaTag(['property' => 'og:url', 'content' => $invitation->getUrl()]);
        
        if ($invitation->cover_image) {
            $this->view->registerMetaTag(['property' => 'og:image', 'content' => $invitation->getCoverImageUrl()]);
        }

        return $this->render('view', [
            'invitation' => $invitation,
            'guest' => $guest,
            'galleries' => $galleries,
            'recentRsvps' => $recentRsvps,
            'existingRsvp' => $existingRsvp,
            'guestNameFromUrl' => $guestNameFromUrl,
        ]);
    }

    /**
     * Handle RSVP form submission
     * 
     * @param string $slug Invitation slug
     * @param string|null $token Guest token
     * @param string|null $to Guest name
     * @return \yii\web\Response|string
     */
    public function actionRsvp($slug, $token = null, $to = null)
    {
        $invitation = $this->findInvitationBySlug($slug);

        // Get guest name from POST data (submitted form)
        $postData = Yii::$app->request->post('Rsvp');
        $guestNameFromForm = isset($postData['name']) ? $postData['name'] : null;

        // Guest personalization - try multiple sources
        $guest = null;
        if ($token) {
            $guest = Guest::findOne(['token' => $token, 'invitation_id' => $invitation->id]);
        } elseif ($to) {
            $guest = Guest::findOne(['name' => $to, 'invitation_id' => $invitation->id]);
        } elseif ($guestNameFromForm) {
            // Get from form data when POST request
            $guest = Guest::findOne(['name' => $guestNameFromForm, 'invitation_id' => $invitation->id]);
        }
        
        // Redirect if no guest (not authorized)
        if (!$guest) {
            Yii::$app->session->setFlash('error', 'Anda harus menggunakan link undangan yang valid untuk melakukan konfirmasi kehadiran.');
            return $this->redirect(['view', 'slug' => $slug]);
        }
        
        // Check if already submitted RSVP (by email or name)
        $existingRsvp = \app\models\Rsvp::find()
            ->where(['invitation_id' => $invitation->id])
            ->andWhere(['or',
                ['email' => $guest->email],
                ['name' => $guest->name]
            ])
            ->one();
        
        if ($existingRsvp) {
            Yii::$app->session->setFlash('info', 'Anda sudah melakukan konfirmasi kehadiran sebelumnya. Terima kasih!');
            return $this->redirect(['view', 'slug' => $slug, 'to' => $guest->name]);
        }

        $model = new \app\models\Rsvp();
        $model->invitation_id = $invitation->id;
        $model->name = $guest->name;
        $model->email = $guest->email;
        $model->phone = $guest->phone;

        if ($model->load(Yii::$app->request->post())) {
            // Jika tidak hadir, set guests_count ke 0
            if ($model->attendance === \app\models\Rsvp::ATTENDANCE_NOT_ATTENDING) {
                $model->guests_count = 0;
            } else {
                // Jika hadir, set default 1 orang (diri sendiri)
                $model->guests_count = 1;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Terima kasih! Konfirmasi kehadiran Anda telah kami terima.');
                // Redirect with 'to' parameter using guest name
                return $this->redirect(['view', 'slug' => $invitation->slug, 'to' => $guest->name]);
            } else {
                // Check for specific errors
                if ($model->hasErrors('email')) {
                    $errors = $model->getErrors('email');
                    if (isset($errors[0]) && strpos($errors[0], 'sudah melakukan RSVP') !== false) {
                        Yii::$app->session->setFlash('error', 'Email Anda sudah terdaftar. Anda hanya bisa melakukan RSVP satu kali untuk undangan ini.');
                    } else {
                        Yii::$app->session->setFlash('error', 'Email tidak valid. ' . $errors[0]);
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Terjadi kesalahan. Mohon periksa kembali form Anda.');
                }
            }
        }

        // Redirect back to invitation page
        return $this->redirect(['view', 'slug' => $invitation->slug, '#' => 'rsvp']);
    }

    /**
     * Get chat messages for invitation (AJAX)
     * 
     * @param string $slug
     * @return array
     */
    public function actionGetMessages($slug)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $invitation = $this->findInvitationBySlug($slug);
        
        $messages = \app\models\Chat::find()
            ->where(['invitation_id' => $invitation->id])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(100)
            ->all();

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->id,
                'guest_name' => $message->guest_name,
                'message' => $message->message,
                'created_at' => $message->created_at,
                'formatted_time' => $message->getFormattedTime(),
            ];
        }

        return ['success' => true, 'messages' => $result];
    }

    /**
     * Send chat message (AJAX)
     * 
     * @param string $slug
     * @return array
     */
    public function actionSendMessage($slug)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $invitation = $this->findInvitationBySlug($slug);
        
        $guestName = Yii::$app->request->post('guest_name');
        $message = Yii::$app->request->post('message');

        // Log for debugging
        Yii::info('Send Message Request - Guest: ' . $guestName . ', Message: ' . $message, __METHOD__);

        if (empty($guestName) || empty($message)) {
            return ['success' => false, 'error' => 'Nama dan pesan harus diisi'];
        }

        // Verify that guest exists in invitation
        $guest = \app\models\Guest::findOne([
            'name' => $guestName,
            'invitation_id' => $invitation->id
        ]);

        if (!$guest) {
            return ['success' => false, 'error' => 'Anda tidak memiliki akses untuk menggunakan live chat'];
        }

        $chat = new \app\models\Chat();
        $chat->invitation_id = $invitation->id;
        $chat->guest_name = $guestName;
        $chat->message = $message;

        if ($chat->save()) {
            return [
                'success' => true,
                'message' => [
                    'id' => $chat->id,
                    'guest_name' => $chat->guest_name,
                    'message' => $chat->message,
                    'created_at' => $chat->created_at,
                    'formatted_time' => $chat->getFormattedTime(),
                ]
            ];
        }

        // Return detailed validation errors
        $errors = [];
        foreach ($chat->getErrors() as $attribute => $attributeErrors) {
            $errors[$attribute] = $attributeErrors;
        }
        
        Yii::error('Chat save failed: ' . json_encode($errors), __METHOD__);
        
        return [
            'success' => false, 
            'error' => 'Gagal mengirim pesan',
            'validation_errors' => $errors
        ];
    }

    /**
     * Find invitation by slug
     * 
     * @param string $slug
     * @return Invitation
     * @throws NotFoundHttpException
     */
    protected function findInvitationBySlug($slug)
    {
        if (($model = Invitation::findOne(['slug' => $slug])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Undangan yang Anda cari tidak ditemukan.');
    }
}
