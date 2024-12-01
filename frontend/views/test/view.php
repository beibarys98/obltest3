<?php

use common\models\Answer;
use common\models\File;
use common\models\Teacher;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\Test $model */
/** @var $dataProvider */
/** @var $questions */
/** @var $searchModel */
/** @var $dataProvider2 */
/** @var $switch */

$this->title = $model->subject->title . '_' . $model->language . '_' . $model->version;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тесттер'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
<div class="test-view">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div',
                        Html::a(Yii::t('app', 'Өзгерту'), ['test/update', 'id' => $model->id],
                            ['class' => 'btn btn-primary']),
                        ['style' => 'text-align: right;']
                    );
                }
            ]
        ],
    ]); ?>

    <div style="margin: 0 auto; width: 700px;" class="p-3 mb-3">
        <div class="row">
            <div class="col-4 mt-auto">
                <?php
                if($model->status == 'new'){
                    echo Html::a(Yii::t('app', 'Дайын') ,
                        ['/test/ready', 'id' => $model->id],
                        ['class' => 'btn btn-success w-100']);
                }
                if($model->status == 'ready'){
                    echo Html::a(Yii::t('app', 'Жариялау'),
                        ['test/publish', 'id' => $model->id],
                        [
                            'class' => 'btn btn-success w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }
                if($model->status == 'public'){
                    echo Html::a(Yii::t('app', 'Аяқтау') ,
                        ['test/end', 'id' => $model->id],
                        [
                            'class' => 'btn btn-success w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }
                if($model->status == 'finished'){
                    echo Html::a(Yii::t('app', 'Қайта жариялау') ,
                        ['test/publish', 'id' => $model->id],
                        [
                            'class' => 'btn btn-warning w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Марапаттау') ,
                        ['test/present', 'id' => $model->id],
                        [
                            'class' => 'btn btn-success w-100 mt-1',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }
                if($model->status == 'certificated'){
                    echo Html::a(Yii::t('app', 'Қайта марапаттау') ,
                        ['test/present', 'id' => $model->id],
                        [
                            'class' => 'btn btn-warning w-100',
                            'data' => [
                                'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            ]
                        ]);
                }
                ?>
            </div>
            <div class="col-4 mt-auto">
                <?php
                if($model->status == 'certificated'){
                    echo Html::a(Yii::t('app', 'Сертификаттар') ,
                        ['test/certificates', 'id' => $model->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                    echo '<br>';
                    echo Html::a(Yii::t('app', 'Журнал') ,
                        ['test/journal', 'id' => $model->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                }
                if (in_array($model->status, ['finished', 'certificated'])) {
                    echo Html::a(Yii::t('app', 'Нәтиже'),
                        ['test/result', 'id' => $model->id],
                        [
                            'class' => 'btn btn-info w-100 mb-1',
                        ]);
                    echo '<br>';
                }
                if($switch == 'test'){
                    echo Html::a(Yii::t('app', 'Қатысушылар'),
                        ['/test/view', 'id' => $model->id, 'switch' => 'participant'],
                        ['class' => 'btn btn-primary w-100']);
                }elseif ($switch == 'participant'){
                    echo Html::a(Yii::t('app', 'Тест'),
                        ['/test/view', 'id' => $model->id, 'switch' => 'test'],
                        ['class' => 'btn btn-primary w-100']);
                } ?>
            </div>
            <div class="col-4 mt-auto">
                <?= Html::a(Yii::t('app', 'Өшіру'),
                    ['test/delete', 'id' => $model->id],
                    [
                        'class' => 'btn btn-danger w-100 disabled',
                        'data' => [
                            'confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                            'method' => 'post',
                        ],
                    ]) ?>
            </div>
        </div>
    </div>

    <?php if($switch == 'test'): ?>
    <div style="font-size: 24px;">
        <?php $number = 1; ?>
        <?php foreach ($questions as $q): ?>
            <?= Html::a('+', ['add-formula', 'id' => $q->id, 'type' => 'question'], [
                'class' => 'btn btn-primary',
            ]) ?>
            <?= $number++ . '. '; ?>
            <?php if ($q->formula_path): ?>
                <?= Html::img(Url::to('@web/' . $q->formula_path)) ?>
            <?php else: ?>
                <?= $q->question; ?>
            <?php endif; ?>
            <br>
            <?php
            $answers = Answer::find()
                ->andWhere(['question_id' => $q->id])
                ->all();
            $alphabet = range('A', 'Z');
            $index = 0;
            ?>
            <?php foreach ($answers as $a): ?>
                <?= Html::a('+', ['add-formula', 'id' => $a->id, 'type' => 'answer'], [
                    'class' => 'btn btn-secondary',
                ]) ?>
                <?php if ($a->formula_path): ?>
                    <?php if ($a->id == $q->answer_id): ?>
                        <strong><?= $alphabet[$index++] . '. '?></strong>
                        <?= Html::img(Url::to('@web/' . $a->formula_path)) ?>
                        <br>
                    <?php else: ?>
                        <?= $alphabet[$index++] . '. ' ?>
                        <?= Html::img(Url::to('@web/' . $a->formula_path)) ?>
                        <br>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($a->id == $q->answer_id): ?>
                        <strong><?= $alphabet[$index++] . '. ' . $a->answer; ?></strong><br>
                    <?php else: ?>
                        <?= $alphabet[$index++] . '. ' . $a->answer; ?><br>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <br>
        <?php endforeach; ?>
    </div>

    <?php elseif ($switch == 'participant'): ?>

        <div>
            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider2,
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
                            $receipt = File::find()
                                ->where(['teacher_id' => $model->id, 'type' => 'receipt'])
                                ->orderBy(['id' => SORT_DESC])
                                ->one();

                            $certificate = File::find()
                                ->where(['teacher_id' => $model->id, 'type' => 'certificate'])
                                ->orderBy(['id' => SORT_DESC])
                                ->one();


                            $receiptLink = $receipt->path ? Html::a('Квитанция', [$receipt->path], ['target' => '_blank', 'data-pjax' => '0']) : '---';
                            $certificateLink = $certificate ? Html::a('Марапат', [$certificate->path], ['target' => '_blank', 'data-pjax' => '0']) : '---';

                            return $receiptLink . '<br>' . $certificateLink;
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{update}<span style="margin: 10px;"></span>{delete}',
                        'urlCreator' => function ($action, Teacher $model, $key, $index, $column) {
                            return Url::toRoute(['teacher/' . $action, 'id' => $model->id]);
                        }
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>

    <?php endif;?>

</div>
