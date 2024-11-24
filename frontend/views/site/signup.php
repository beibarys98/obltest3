<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var SignupForm $model */
/** @var $teacher */

use common\models\Subject;
use frontend\models\SignupForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Тіркелу'); // Translated title
?>

<style>
    input::placeholder {
        color: #999999 !important;
    }
</style>

<div class="site-signup">
    <h1 class="text-center mb-3"><?= Html::encode($this->title) ?></h1>

    <div style="margin: 0 auto; width: 500px;">
        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'ЖСН')])->label(false) ?>

        <?= $form->field($teacher, 'name')->textInput(['placeholder' => Yii::t('app', 'Толық аты-жөні')])->label(false) ?>

        <?= $form->field($teacher, 'school')->textInput(['placeholder' => Yii::t('app', 'Мекеме')])->label(false) ?>

        <?php
        $subjectField = (Yii::$app->language === 'ru') ? 'title_ru' : 'title';
        $subjects = ArrayHelper::map(Subject::find()->all(), 'id', $subjectField);
        ?>

        <?= $form->field($teacher, 'subject_id')
            ->widget(Select2::classname(),
                [
                    'data' => $subjects,
                    'options' => [
                        'placeholder' => Yii::t('app', 'Пән'),
                        'style' => ['width' => '100%'],
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

        echo $form->field($teacher, 'language')->widget(Select2::classname(), [
            'data' => $languages,
            'options' => [
                'placeholder' => Yii::t('app', 'Тест тапсыру тілі'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label(false);
        ?>

        <?= $form->field($model, 'email')->input('email', [
            'placeholder' => Yii::t('app', 'Пошта'),
        ])->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app', 'Құпия сөз')])->label(false) ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('app', 'Тіркелу'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
