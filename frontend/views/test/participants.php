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
/** @var $dataProvider2 */
/** @var common\models\search\TeacherSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

/** @var $test */

$this->title = $test->subject->title . '_' . $test->language . '_' . $test->version;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тесттер'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="teacher-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider2,
        'tableOptions' => ['class' => 'table table-hover'],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'subject',
                'value' => 'subject.title',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'language',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'version',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'status',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'duration',
                'enableSorting' => false,
            ],
        ],
    ]); ?>

    <div style="margin: 0 auto; width: 700px;" class="p-3 mb-3">
        <div class="row">
            <div class="col-4 mt-auto">
                <?php
                if($test->status == 'new'){
                    echo Html::a(Yii::t('app', 'Дайын') ,
                        ['/test/ready', 'id' => $test->id],
                        ['class' => 'btn btn-success w-100']);
                }else if($test->status == 'ready'){
                    echo Html::a(Yii::t('app', 'Жариялау'),
                        ['test/publish', 'id' => $test->id],
                        [
                            'class' => 'btn btn-success w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }else if($test->status == 'public'){
                    echo Html::a(Yii::t('app', 'Аяқтау') ,
                        ['test/end', 'id' => $test->id],
                        [
                            'class' => 'btn btn-success w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }else if($test->status == 'finished'){
                    echo Html::a(Yii::t('app', 'Қайта жариялау') ,
                        ['test/publish', 'id' => $test->id],
                        [
                            'class' => 'btn btn-warning w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Марапаттау') ,
                        ['test/present', 'id' => $test->id],
                        [
                            'class' => 'btn btn-success w-100 mt-1',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }else if($test->status == 'certificated'){
                    echo Html::a(Yii::t('app', 'Қайта марапаттау') ,
                        ['test/present', 'id' => $test->id],
                        [
                            'class' => 'btn btn-warning w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                    echo '<br>';
                }
                ?>
            </div>
            <div class="col-4 mt-auto">
                <?php
                if($test->status == 'public'){
                    echo Html::a(Yii::t('app', 'Тест') ,
                        ['/test/view', 'id' => $test->id],
                        ['class' => 'btn btn-primary w-100']);

                }else if($test->status == 'finished'){
                    echo Html::a(Yii::t('app', 'Нәтиже') ,
                        ['test/result', 'id' => $test->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Тест') ,
                        ['/test/view', 'id' => $test->id],
                        ['class' => 'btn btn-primary w-100']);
                }else if($test->status == 'certificated'){
                    echo Html::a(Yii::t('app', 'Сертификаттар') ,
                        ['test/download-zip', 'id' => $test->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Нәтиже') ,
                        ['test/result', 'id' => $test->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Тест') ,
                        ['/test/view', 'id' => $test->id],
                        ['class' => 'btn btn-primary w-100']);
                }
                ?>
            </div>
            <div class="col-4 mt-auto">
                <?= Html::a(Yii::t('app', 'Өшіру'),
                    ['test/delete', 'id' => $test->id],
                    [
                        'class' => 'btn btn-danger w-100',
                        'data' => [
                            'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            'method' => 'post',
                        ],
                    ]) ?>
            </div>
        </div>
    </div>

    <div>
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

</div>
