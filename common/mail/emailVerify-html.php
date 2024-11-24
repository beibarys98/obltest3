<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var $teacher */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p><?= Yii::t('app', 'Саламатсызба, {name}.', ['name' => Html::encode($teacher->name)]) ?></p>

    <p><?= Yii::t('app', 'Поштаңызды растау үшін төмендегі сілтемені басыңыз:') ?></p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
