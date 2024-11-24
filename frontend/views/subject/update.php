<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Subject $model */

$this->title = Yii::t('app', 'Өзгерту');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пәндер'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Өзгерту');
?>
<div class="subject-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first')->fileInput() ?>

    <?= $form->field($model, 'second')->fileInput() ?>

    <?= $form->field($model, 'third')->fileInput() ?>

    <?= $form->field($model, 'fourth')->fileInput() ?>

    <?= $form->field($model, 'fifth')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сақтау'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
