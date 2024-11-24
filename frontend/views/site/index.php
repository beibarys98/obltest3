<?php

/** @var yii\web\View $this */
/** @var $teacher */
/** @var $dataProvider */
/** @var $dataProvider2 */
/** @var $dataProvider3 */
/** @var $dataProvider4 */
/** @var $purpose */
/** @var $receipt */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = $teacher->name;
?>
<div class="site-index">
    <h1><?= Yii::t('app', 'Қатысушы') ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            [
                'label' => Yii::t('app', 'ЖСН'),
                'attribute' => 'user_id',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Толық аты-жөні'),
                'attribute' => 'name',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Мекеме'),
                'attribute' => 'school',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Пән'),
                'attribute' => 'subject_id',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Тест тапсыру тілі'),
                'attribute' => 'language',
                'headerOptions' => ['style' => 'width: 5%;'],
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Нәтиже'),
                'attribute' => 'result',
                'headerOptions' => ['style' => 'width: 5%;'],
                'value' => function ($model) {
                    return empty($model->result) ? '---' : $model->result;
                },
                'enableSorting' => false,
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div',
                        Html::a(Yii::t('app', 'Өзгерту'), [''], ['class' => 'btn btn-primary']),
                        ['style' => 'text-align: right;']
                    );
                }
            ]
        ],
    ]); ?>

    <br>

    <h1><?= Yii::t('app', 'Төлем') ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider2,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'showHeader' => false,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'path',
                'value' => function ($model) {
                    return empty($model->path) ? Yii::t('app', 'Квитанция жүктеңіз!') : $model->path;
                },
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div',
                        Html::button(Yii::t('app', 'Жүктеу'), [
                            'class' => 'btn btn-success',
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#exampleModal',
                        ]),
                        ['style' => 'text-align: right;']
                    );
                },
            ],
        ],
    ]); ?>

    <br>

    <h1>Тест</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider3,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            [
                'label' => Yii::t('app', 'Пән'),
                'attribute' => 'subject_id',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Тест тапсыру тілі'),
                'attribute' => 'language',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Статус'),
                'attribute' => 'status',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Ұзақтығы'),
                'attribute' => 'duration',
                'enableSorting' => false,
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div',
                        Html::a(Yii::t('app', 'Бастау'), [''], ['class' => 'btn btn-success']),
                        ['style' => 'text-align: right;']
                    );
                },
            ],
        ],
    ]); ?>

    <br>

    <h1><?= Yii::t('app', 'Марапат') ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider4,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'showHeader' => false,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'path',
                'value' => function ($model) {
                    return empty($model->path) ? '---' : $model->path;
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div',
                        Html::a(Yii::t('app', 'Жүктеп алу'), [''], ['class' => 'btn btn-success']),
                        ['style' => 'text-align: right;']
                    );
                }
            ]
        ],
    ]); ?>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel" style="text-align: center; width: 100%;">
                    <?= Yii::t('app', 'Төлем жасау') ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="text-align: center;">
                    <img src="/images/qr.jpg" alt="" width="200px" style="border: 1px solid black; border-radius: 10px;" class="shadow-sm p-1">
                </div>

                <div class="mt-1" style="margin: 0 auto;">
                    <div class="shadow-sm p-3" style="border: 1px solid black; border-radius: 10px;">
                        <label for="student-name"><?= Yii::t('app', 'ФИО учащегося') ?></label>
                        <input id="student-name" type="text" value="<?= $teacher->name ?>" class="form-control" disabled>
                        <label class="mt-1" for="payment-purpose"><?= Yii::t('app', 'Назначение платежа') ?></label>
                        <input id="payment-purpose" type="text" value="<?= $purpose->purpose ?>" class="form-control" disabled>
                        <label class="mt-1" for="payment-amount"><?= Yii::t('app', 'Сумма') ?></label>
                        <input id="payment-amount" type="text" value="<?= $purpose->cost ?>" class="form-control" disabled>
                    </div>

                    <div class="mt-1">
                        <?php
                        $form = ActiveForm::begin([
                            'options' => ['enctype' => 'multipart/form-data'],
                        ]); ?>

                        <div class="shadow-sm p-3" style="border: 1px solid black; border-radius: 10px;">
                            <?= $form->field($receipt, 'file')->fileInput()->label(false) ?>
                            <?= Html::submitButton(Yii::t('app', 'Жүктеу'), ['class' => 'btn btn-success w-100']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

