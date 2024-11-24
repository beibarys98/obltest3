<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var ResetPasswordForm $model */

use frontend\models\ResetPasswordForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = Yii::t('app', 'Құпия сөзді жаңарту'); // Translatable title
?>
<div class="site-reset-password">
    <h1 class="text-center mb-3"><?= Html::encode($this->title) ?></h1>

    <div style="margin: 0 auto; width: 500px;">

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'Жаңа құпия сөз')])->label(false) ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('app', 'Жаңарту'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
