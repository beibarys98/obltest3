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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Мұғалімдер'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->params['breadcrumbs'][] = Yii::t('app', 'Өзгерту');
?>
<div class="teacher-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($user, 'username')->textInput()->label(Yii::t('app', 'ЖСН')) ?>

    <?= $form->field($user, 'email')->textInput()->label(Yii::t('app', 'Пошта')) ?>

    <?= $form->field($user, 'newPassword')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'school')->textInput(['maxlength' => true]) ?>

    <?php
    $subjectField = (Yii::$app->language === 'ru') ? 'title_ru' : 'title';
    $subjects = ArrayHelper::map(Subject::find()->all(), 'id', $subjectField);
    ?>

    <?= $form->field($model, 'subject_id')
        ->widget(Select2::classname(),
            [
                'data' => $subjects,
                'options' => [
                    'placeholder' => '',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]
        ); ?>

    <?php
    if(User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin'])):
    $tests = ArrayHelper::map(
        Test::find()->all(),
        'id',
        function ($model) {
            return $model->subject->title . '_' . $model->language . '_' . $model->version;
        }
    );
    ?>

    <?=
    $form->field($model, 'test_id')
        ->widget(Select2::classname(),
            [
                'data' => $tests,
                'options' => [
                    'placeholder' => '',
                    'style' => ['width' => '100%'],
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'dropdownAutoWidth' => true,
                    'maximumInputLength' => 20,
                ],
            ]);
    endif;
    ?>

    <?php
    $languages = [
        'kz' => Yii::t('app', 'қазақша'),
        'ru' => Yii::t('app', 'орысша'),
    ];

    echo $form->field($model, 'language')->widget(Select2::classname(), [
        'data' => $languages,
        'options' => [
            'placeholder' => '',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
    ?>

    <?= $form->field($model, 'payment_time')->textInput() ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'result')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сақтау'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
