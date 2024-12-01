<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var $place */
/** @var $purpose */

$this->title = Yii::t('app', 'Баптаулар');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-index">

    <h1>Баптаулар</h1>

    <h3 class="text-center">Бағалау</h3>

    <div class="p-3 shadow-sm mb-3" style="border: 1px solid black; border-radius: 10px; margin: 0 auto; width: 500px;">

        <?php $form = ActiveForm::begin(); ?>

        <div class="d-flex">
            <div style="width: 20%" class="p-1">
                <?= $form->field($place, 'first')->textInput()->label('Бірінші') ?>
            </div>
            <div style="width: 20%" class="p-1">
                <?= $form->field($place, 'second')->textInput()->label('Екінші') ?>
            </div>
            <div style="width: 20%" class="p-1">
                <?= $form->field($place, 'third')->textInput()->label('Үшінші') ?>
            </div>
            <div style="width: 20%" class="p-1">
                <?= $form->field($place, 'fourth')->textInput()->label('Алғыс хат') ?>
            </div>
            <div style="width: 20%" class="p-1">
                <?= $form->field($place, 'fifth')->textInput()->label('Сертификат') ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Сақтау', ['class' => 'btn btn-secondary w-100',
                'style' => 'text-align: center;']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <h3 class="text-center">Төлем</h3>

    <div class="p-3 shadow-sm mb-3" style="border: 1px solid black; border-radius: 10px; margin: 0 auto; width: 500px;">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($purpose, 'purpose')->textInput()->label('Назначение платежа') ?>

        <?= $form->field($purpose, 'cost')->textInput()->label('Сумма') ?>

        <div class="form-group">
            <?= Html::submitButton('Сақтау',
                ['class' => 'btn btn-secondary w-100', 'style' => 'text-align: center;']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
