<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        /* Active menu item styling with red underline, white text, and bold */
        .navbar-nav .nav-link.active,
        .navbar-nav > li > a.active,
        .navbar-nav .nav-item.active > a,
        .navbar-dark .navbar-nav .nav-link.active,
        .navbar-dark .navbar-nav .nav-item.active > a {
            background-color: transparent !important;
            border-bottom: 3px solid #dc3545 !important;
            border-radius: 0 !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            padding-bottom: calc(0.5rem - 3px) !important;
        }
        
        /* Hover effect for ALL menu items - super important! */
        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-item > a:hover,
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-item > a:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border-radius: 4px !important;
            transition: background-color 0.3s ease;
        }
        
        /* Don't apply border-radius on active items when hover */
        .navbar-nav .nav-link.active:hover,
        .navbar-nav .nav-item.active > a:hover,
        .navbar-dark .navbar-nav .nav-link.active:hover,
        .navbar-dark .navbar-nav .nav-item.active > a:hover {
            border-radius: 0 !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Make sure padding is preserved on hover */
        .navbar-dark .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
        }
        
        /* Active dropdown menu item */
        .navbar-nav .dropdown-menu .dropdown-item.active {
            background-color: #0d6efd;
            font-weight: 500;
        }
        
        /* Hover effect for dropdown items */
        .navbar-nav .dropdown-menu .dropdown-item:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        /* Form label styling - make bold */
        .form-group label,
        .control-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        /* Input border styling - make more visible */
        .form-control,
        .form-select {
            border: 2px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
        
        /* Textarea border */
        textarea.form-control {
            border: 2px solid #ced4da;
        }
        
        textarea.form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    
    // Get current controller and action
    $controller = Yii::$app->controller->id;
    $action = Yii::$app->controller->action->id;
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'activateParents' => true,
        'items' => array_filter([
            ['label' => 'Home', 'url' => ['/site/index'], 'active' => ($controller === 'site' && $action === 'index') || $controller === 'invitation'],
            // Hide About and Contact for client users
            (Yii::$app->user->isGuest || Yii::$app->user->identity->isSuperUser()) ? ['label' => 'About', 'url' => ['/site/about'], 'active' => $controller === 'site' && $action === 'about'] : null,
            (Yii::$app->user->isGuest || Yii::$app->user->identity->isSuperUser()) ? ['label' => 'Contact', 'url' => ['/site/contact'], 'active' => $controller === 'site' && $action === 'contact'] : null,
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/auth/login'], 'active' => $controller === 'auth' && $action === 'login']
            ) : (
                // Super User - show horizontal menu items (no dropdown)
                Yii::$app->user->identity->isSuperUser() ? null : null
            ),
            // Super User menu items - horizontal
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-envelope-heart me-1"></i> Kelola Undangan', 'url' => ['/admin-invitation/index'], 'encode' => false, 'active' => $controller === 'admin-invitation'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-person-gear me-1"></i> Kelola User', 'url' => ['/admin-user/index'], 'encode' => false, 'active' => $controller === 'admin-user'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-people me-1"></i> Kelola Tamu', 'url' => ['/admin-guest/index'], 'encode' => false, 'active' => $controller === 'admin-guest'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-images me-1"></i> Kelola Gallery', 'url' => ['/admin-gallery/index'], 'encode' => false, 'active' => $controller === 'admin-gallery'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-bar-chart me-1"></i> Analytics', 'url' => ['/admin-analytics/index'], 'encode' => false, 'active' => $controller === 'admin-analytics'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-envelope-at me-1"></i> Email', 'url' => ['/admin-email/index'], 'encode' => false, 'active' => $controller === 'admin-email'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-calendar-check me-1"></i> Kelola RSVP', 'url' => ['/admin-rsvp/index'], 'encode' => false, 'active' => $controller === 'admin-rsvp'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isSuperUser()) ? 
                ['label' => '<i class="bi bi-box-arrow-right me-1"></i> Logout', 
                 'url' => ['/auth/logout'],
                 'linkOptions' => [
                     'data-method' => 'post',
                     'data-confirm' => 'Apakah Anda yakin ingin logout?',
                 ],
                 'encode' => false
                ] : null,
            // Client User - show horizontal menu items
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()) ? 
                ['label' => '<i class="bi bi-pencil-square me-1"></i> Edit Undangan', 'url' => ['/admin-invitation/update', 'id' => Yii::$app->user->identity->invitations[0]->id ?? null], 'encode' => false, 'visible' => !empty(Yii::$app->user->identity->invitations), 'active' => $controller === 'admin-invitation'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()) ? 
                ['label' => '<i class="bi bi-people me-1"></i> Kelola Tamu', 'url' => ['/admin-guest/index'], 'encode' => false, 'active' => $controller === 'admin-guest'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()) ? 
                ['label' => '<i class="bi bi-images me-1"></i> Gallery', 'url' => ['/admin-gallery/index'], 'encode' => false, 'active' => $controller === 'admin-gallery'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()) ? 
                ['label' => '<i class="bi bi-calendar-check me-1"></i> RSVP', 'url' => ['/admin-rsvp/index'], 'encode' => false, 'active' => $controller === 'admin-rsvp'] : null,
            (!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()) ? 
                ['label' => '<i class="bi bi-box-arrow-right me-1"></i> Logout', 
                 'url' => ['/auth/logout'],
                 'linkOptions' => [
                     'data-method' => 'post',
                     'data-confirm' => 'Apakah Anda yakin ingin logout?',
                 ],
                 'encode' => false
                ] : null,
        ])
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; Yadevs <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
