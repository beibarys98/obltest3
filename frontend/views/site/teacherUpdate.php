<?php

use common\models\Subject;
use common\models\Test;
use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Teacher $model */
/** @var $user */

$this->title = Yii::t('app', 'Өзгерту');
?>

<style>
    input::placeholder {
        color: #999999 !important;
    }
</style>

<div class="teacher-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($user, 'username')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'ЖСН')])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'Толық аты-жөні')])->label(false) ?>

    <?= $form->field($model, 'school')->textInput(['placeholder' => Yii::t('app', 'Мекеме')])->label(false) ?>

    <?php
    $subjectField = (Yii::$app->language === 'ru') ? 'title_ru' : 'title';
    $subjects = ArrayHelper::map(Subject::find()->all(), 'id', $subjectField);
    ?>

    <?= $form->field($model, 'subject_id')
        ->widget(Select2::classname(),
            [
                'data' => $subjects,
                'options' => [
                    'placeholder' => 'Пән',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]
        )->label(false); ?>

    <?php
    $languages = [
        'kz' => Yii::t('app', 'қазақша'),
        'ru' => Yii::t('app', 'орысша'),
    ];

    echo $form->field($model, 'language')->widget(Select2::classname(), [
        'data' => $languages,
        'options' => [
            'placeholder' => 'Тест тапсыру тілі',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label(false);
    ?>

    <?= $form->field($user, 'email')->input('email', ['placeholder' => Yii::t('app', 'Пошта'),])->label(false) ?>

    <?= $form->field($user, 'newPassword')->textInput(['placeholder' => Yii::t('app', 'Жаңа құпия сөз')])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сақтау'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
