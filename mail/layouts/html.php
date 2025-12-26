<?php
use yii\helpers\Html;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #d4a574, #8b7355);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
            letter-spacing: 2px;
        }
        .email-body {
            padding: 40px 30px;
            line-height: 1.8;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            margin: 20px 0;
            background: linear-gradient(135deg, #d4a574, #8b7355);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #d4a574, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="email-container">
        <div class="email-header">
            <h1><?= Yii::$app->name ?></h1>
        </div>
        <div class="email-body">
            <?= $content ?>
        </div>
        <div class="email-footer">
            <p>&copy; <?= date('Y') ?> <?= Yii::$app->name ?>. All rights reserved.</p>
            <p style="margin: 5px 0;">
                <a href="<?= \yii\helpers\Url::to(['site/index'], true) ?>" style="color: #d4a574; text-decoration: none;">
                    Visit Our Website
                </a>
            </p>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
