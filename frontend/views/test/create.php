<?php

use common\models\Subject;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Test $model */

$this->title = Yii::t('app', 'Жаңа');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тесттер'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?= $form->field($model, 'subject_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Subject::find()->all(), 'id', 'title'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <?= $form->field($model, 'language')->widget(Select2::classname(), [
        'data' => [
            'kz' => Yii::t('app', 'қазақша'),
            'ru' => Yii::t('app', 'орысша'),
        ],
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duration')->input('time');?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сақтау'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
