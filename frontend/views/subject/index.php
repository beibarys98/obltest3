<?php

use common\models\Subject;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\search\SubjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Пәндер');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Жаңа'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
            'id',
            'title',
            'title_ru',
            [
                'attribute' => 'first',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->first ? Html::a($model->first, [$model->first], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                },
            ],
            [
                'attribute' => 'second',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->second ? Html::a($model->second, [$model->second], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                },
            ],
            [
                'attribute' => 'third',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->third ? Html::a($model->third, [$model->third], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                },
            ],
            [
                'attribute' => 'fourth',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->fourth ? Html::a($model->fourth, [$model->fourth], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                },
            ],
            [
                'attribute' => 'fifth',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->fifth ? Html::a($model->fifth, [$model->fifth], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update}<span style="margin: 10px;"></span>{delete}',
                'urlCreator' => function ($action, Subject $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
