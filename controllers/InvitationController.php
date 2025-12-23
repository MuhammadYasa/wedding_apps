<?php

namespace app\controllers;

use Yii;
use app\models\Invitation;
use app\models\Guest;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
        ];
    }

    /**
     * Display invitation by slug
     * 
     * @param string $slug Invitation slug (friendly URL)
     * @param string|null $token Guest token for personalized view
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $token = null)
    {
        // Find invitation by slug
        $invitation = $this->findInvitationBySlug($slug);

        // Check if invitation is active
        if (!$invitation->is_active) {
            throw new NotFoundHttpException('Undangan tidak ditemukan atau sudah tidak aktif.');
        }

        // Guest personalization (if token provided)
        $guest = null;
        if ($token) {
            $guest = Guest::findOne([
                'invitation_id' => $invitation->id,
                'token' => $token
            ]);

            // Mark invitation as viewed by this guest
            if ($guest) {
                $guest->markAsViewed();
            }
        }

        // Load galleries
        $galleries = $invitation->getGalleries()->all();

        // Load recent RSVPs (for display testimonials)
        $recentRsvps = $invitation->getRsvps()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

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
        ]);
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
