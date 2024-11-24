<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var PasswordResetRequestForm $model */

use frontend\models\PasswordResetRequestForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = Yii::t('app', 'Құпия сөзді жаңарту');
?>
<div class="site-request-password-reset">
    <h1 class="text-center mb-3"><?= Html::encode($this->title) ?></h1>

    <div style="margin: 0 auto; width: 500px;">
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'Пошта')])->label(false) ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('app', 'Жаңарту'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
