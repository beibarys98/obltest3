<?php

use common\models\Answer;
use common\models\Question;
use common\models\Teacher;
use common\models\TeacherAnswer;
use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\YiiAsset;

/** @var $this */
/** @var $test */
/** @var $question*/
/** @var $teacher*/

$title = $test->language === 'ru'
    ? $test->subject->title_ru
    : $test->subject->title;
$this->title = $title;

YiiAsset::register($this);

$durationArray = explode(':', $test->duration);
$totalDurationInSeconds = ($durationArray[0] * 3600) + ($durationArray[1] * 60) + $durationArray[2];
$totalDurationInSeconds = max($totalDurationInSeconds, 0);

$startTime = new DateTime($teacher->start_time);
$currentTime = new DateTime('now');
$elapsedTimeInSeconds = $currentTime->getTimestamp() - $startTime->getTimestamp();
$remainingTimeInSeconds = $totalDurationInSeconds - $elapsedTimeInSeconds;
$remainingTimeInSeconds = max($remainingTimeInSeconds, 0);

$this->registerJs("
    function startTimer(duration, display) {
        var timer = duration, hours, minutes, seconds;
        
        var interval = setInterval(function () {
        
            hours = parseInt(timer / 3600, 10); // Calculate hours
            minutes = parseInt((timer % 3600) / 60, 10); // Calculate minutes
            seconds = parseInt(timer % 60, 10); // Calculate seconds
            
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            display.textContent = hours + ':' + minutes + ':' + seconds;
            
            if (--timer < 0) {
                timer = 0;
                clearInterval(interval);
                window.location = \"" . Url::to(['site/end', 'id' => $test->id]) . "\";
            }
        }, 1000);
    }

    window.onload = function () {
        var duration = $remainingTimeInSeconds; // Countdown duration in seconds
        var display = document.querySelector('#clock'); // Timer display element
        startTimer(duration, display);
    };
", View::POS_END);
?>

<div class="test-view">

    <?= Alert::widget() ?>

    <div class="d-flex">
        <div style="width: 70%;">
            <div style="font-size: 24px;
    user-select: none; -webkit-user-select: none; -moz-user-select: none;
    -ms-user-select: none;">
                <?php if ($question->formula_path): ?>
                    <?= Html::img(Url::to('@web/' . $question->formula_path)) ?>
                <?php else: ?>
                    <?= $question->question; ?>
                <?php endif; ?>

                <br>

                <?php
                $answers = Answer::find()
                    ->andWhere(['question_id' => $question->id])
                    ->orderBy('RAND()')
                    ->all();
                $alphabet = range('A', 'Z');
                $index = 0; ?>

                <form class="mt-5" id="answerForm" action="<?= Url::to(['site/submit']) ?>" method="get">
                    <?php
                    $teacherAnswer = TeacherAnswer::find()
                        ->andWhere(['teacher_id' => Teacher::findOne(['user_id' => Yii::$app->user->id])->id,
                            'question_id' => $question->id])->one();
                    $selectedAnswerId = $teacherAnswer ? $teacherAnswer->answer_id : null; ?>

                    <?php foreach ($answers as $a): ?>
                        <input type="radio" name="answer_id" value="<?= $a->id ?>"
                               class="form-check-input me-1"
                            <?= $selectedAnswerId == $a->id ? 'checked' : '' ?>>
                        <?php if ($a->formula_path): ?>
                            <?= $alphabet[$index++] . '. ' ?>
                            <?= Html::img(Url::to('@web/' . $a->formula_path)) ?><br>
                        <?php else: ?>
                            <?= $alphabet[$index++] . '. ' . $a->answer; ?><br>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <input type="hidden" name="question_id" value="<?= $question->id ?>">

                    <button type="submit" class="btn btn-primary mt-5" data-pjax="false">
                        <?= Yii::t('app', 'Сақтау') ?>
                    </button>
                </form>
            </div>
        </div>

        <?php
        $questions = Question::find()->andWhere(['test_id' => $test->id])->all();
        ?>

        <div style="width: 30%;">
            <div style="border-left: 1px solid black;">
                <div style="display: flex; flex-wrap: wrap; justify-content: center;">
                    <?php $index = 1; ?>
                    <?php foreach ($questions as $q): ?>
                        <?php
                        $teacherAnswer2 = TeacherAnswer::find()
                            ->andWhere(['teacher_id' => Teacher::findOne(['user_id' => Yii::$app->user->id])->id,
                                'question_id' => $q->id])->one();
                        $backgroundColor = $teacherAnswer2 && $teacherAnswer2->answer_id ? 'green' : 'red';
                        $borderStyle = ($q->id == $question->id) ? '5px solid black' : 'none'; ?>

                        <a href="<?= Url::to(['test', 'id' => $q->id]) ?>" style="text-decoration: none;">
                            <div style="width: 40px; height: 40px; display: flex; align-items: center;
                                justify-content: center; border: <?= $borderStyle ?>;
                                background-color: <?= $backgroundColor ?>;
                                color: white; font-size: 14px; margin: 2px; border-radius: 5px;">
                                <?= $index++ ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="jumbotron" style="text-align: center;">

                    <a href="<?= Url::to(['site/end', 'id' => $test->id]) ?>" class="btn btn-danger mt-5">
                        <?= Yii::t('app', 'Аяқтау') ?>
                    </a>

                    <div id="clock" class="mt-3" style="border-top: 1px solid black; font-size: 24px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>