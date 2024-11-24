<?php

use common\models\Teacher;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\Teacher $model */
/** @var $dataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
<div class="teacher-view">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            'user_id',
            'name',
            'school',
            'subject_id',
            [
                'attribute' => 'language',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'test_id',
                'value' => function ($model) {
                    return empty($model->test_id) ? '---' : $model->test_id;
                },
            ],
            //'start_time',
            //'end_time',
            [
                'attribute' => 'result',
                'headerOptions' => ['style' => 'width: 5%;'],
                'value' => function ($model) {
                    return empty($model->result) ? '---' : $model->result;
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update}',
                'urlCreator' => function ($action, Teacher $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
