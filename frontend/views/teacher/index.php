<?php

use common\models\File;
use common\models\Teacher;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\search\TeacherSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Мұғалімдер');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'username',
                'value' => 'user.username'
            ],
            'name',
            [
                'attribute' => 'test_id',
                'value' => function ($model) {
                    return empty($model->test_id) ? '---' : $model->test_id;
                },
            ],
            [
                'label' => 'Times',
                'attribute' => 'start_time',
                'format' => 'raw',
                'value' => function ($model) {
                    $paymentTime = $model->payment_time ?? '---';
                    $startTime = $model->start_time ?? '---';
                    $endTime = $model->end_time ?? '---';

                    return $paymentTime . '<br>' . $startTime . '<br>' . $endTime;
                }
            ],
            [
                'attribute' => 'result',
                'headerOptions' => ['style' => 'width: 5%;'],
                'value' => function ($model) {
                    return empty($model->result) ? '---' : $model->result;
                },
            ],
            [
                'label' => 'Files',
                'format' => 'raw',
                'value' => function ($model) {
                    $receipt = File::find()->where(['teacher_id' => $model->id, 'type' => 'receipt'])->one();
                    $certificate = File::find()->where(['teacher_id' => $model->id, 'type' => 'certificate'])->one();

                    $receiptLink = $receipt->path ? Html::a('Квитанция', [$receipt->path], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                    $certificateLink = $certificate ? Html::a('Марапат', [$certificate->path], ['target' => '_blank', 'data-pjax' => '0']) : '---';

                    return $receiptLink . '<br>' . $certificateLink;
                }
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update}<span style="margin: 10px;"></span>{delete}',
                'urlCreator' => function ($action, Teacher $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
