<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var $teacher */

?>
<div class="verify-email">
    <p><?= Yii::t('app', 'Саламатсызба, {name}.', ['name' => Html::encode($teacher->name)]) ?></p>

    <p><?= Yii::t('app', 'Құттықтаймыз! Сіздің марапатыңыз:') ?></p>
</div>
