<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\File $model */

$this->title = Yii::t('app', 'Create File');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
