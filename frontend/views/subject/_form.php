<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Subject $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="subject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'second')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'third')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fourth')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fifth')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
