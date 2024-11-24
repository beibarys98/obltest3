<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\models\Teacher;
use common\models\User;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <link rel="icon" href="<?= Yii::getAlias('@web') ?>/images/adort2.png" type="image/jpg">

    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/images/adort2.png', ['alt' => 'Logo', 'style' => 'height:30px; margin-right:10px;']) . Yii::t('app', Yii::$app->name),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-light bg-light fixed-top shadow-sm'
        ],
    ]);
    $menuItems = [];

    if(User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin'])){
        $menuItems[] = ['label' => 'Мұғалімдер', 'url' => ['/teacher/index']];
        $menuItems[] = ['label' => 'Пәндер', 'url' => ['/subject/index']];
        $menuItems[] = ['label' => 'Тесттер', 'url' => ['/test/index']];
        $menuItems[] = ['label' => 'Баптаулар', 'url' => ['/test/settings/']];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto'],
        'items' => $menuItems,
    ]);

    if (!Yii::$app->user->isGuest){
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                Yii::t('app', 'Шығу ({username})', ['username' => Yii::$app->user->identity->username]),
                ['class' => 'btn btn-link logout text-decoration-none', 'style' => 'color: black;']
            )
            . Html::endForm();
    }

    if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin'])){
        echo Html::tag('div', Html::a( Html::img(
            Yii::$app->language == 'kz' ? '/images/kz.png' : '/images/ru.png',
            ['style' => 'width: 32px; height: 32px;', 'class' => 'rounded']
        ), ['/site/language', 'view' => '/site/index']));
    }

    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => 'Басты бет', // Replace with your desired label
                'url' => Yii::$app->homeUrl, // Keeps the link to the homepage
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-start">&copy; <?= Yii::t('app', Yii::$app->name) ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
