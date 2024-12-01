<?php

namespace frontend\controllers;

use common\models\File;
use common\models\Purpose;
use common\models\Question;
use common\models\Teacher;
use common\models\TeacherAnswer;
use common\models\Test;
use common\models\User;
use DateTime;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        if(User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin'])){
            return $this->redirect('/teacher/index');
        }

        $teacher = Teacher::findOne(['user_id' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => Teacher::find()->andWhere(['user_id' => Yii::$app->user->id]),
        ]);

        $dataProvider2 = new ActiveDataProvider([
            'query' => File::find()
                ->andWhere(['teacher_id' => $teacher->id])
                ->andWhere(['type' => 'receipt'])
        ]);

        //assign test
        if(!$teacher->test_id){
            $tests = Test::find()
                ->andWhere(['status' => 'public'])
                ->andWhere(['subject_id' => $teacher->subject_id])
                ->andWhere(['language' => $teacher->language])
                ->all();

            if (!empty($tests)) {
                $teacher->test_id = $tests[array_rand($tests)]->id;
                $teacher->save(false);
            }
        }

        $dataProvider3 = new ActiveDataProvider([
            'query' => Test::find()
                ->andWhere(['status' => 'public'])
                ->andWhere(['id' => $teacher->test_id]),
        ]);

        $dataProvider4 = new ActiveDataProvider([
            'query' => File::find()
                ->andWhere(['teacher_id' => $teacher->id])
                ->andWhere(['type' => 'certificate'])
                ->orderBy(['id' => SORT_DESC])
                ->limit(1),
            'pagination' => false,
        ]);

        $purpose = Purpose::find()->one();
        $receipt = File::findOne(['teacher_id' => $teacher->id, 'type' => 'receipt']);

        //receipt is uploaded?
        if (Yii::$app->request->isPost) {
            $receipt->file = UploadedFile::getInstance($receipt, 'file');

            if ($receipt->file) {
                $directoryPath = 'receipts/' . $teacher->subject->title;
                if (!is_dir($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }

                $filePath = $directoryPath . '/'
                    . $teacher->name . '.'
                    . $receipt->file->extension;

                if ($receipt->file->saveAs($filePath)) {
                    $receipt->path = $filePath;
                    $receipt->save(false);

                    $teacher->payment_time = date('Y-m-d H:i:s');
                    $teacher->save(false);

                    return $this->redirect(['site/index']);
                }
            }
        }

        //return
        return $this->render('index', [
            'teacher' => $teacher,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            'dataProvider4' => $dataProvider4,
            'purpose' => $purpose,
            'receipt' => $receipt,
        ]);
    }

    public function actionDownload($path)
    {
        $filePath = Yii::getAlias('@webroot/') . $path;

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new NotFoundHttpException('The requested file does not exist.');
        }
    }

    public function actionTeacherUpdate($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = Teacher::findOne($id);
        $user = User::findOne($model->user_id);

        if ($this->request->isPost
            && $model->load($this->request->post())
            && $user->load($this->request->post())) {

            if($user->newPassword){
                $user->setPassword($user->newPassword);
            }

            $model->save(false);
            $user->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('teacherUpdate', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionTest($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $question = Question::findOne([$id]);
        $test = Test::findOne($question->test_id);
        $teacher = Teacher::findone(['test_id' => $test->id]);

        if(!$teacher->start_time){
            $receipt = File::findOne(['teacher_id' => $teacher->id, 'type' => 'receipt']);
            $receipt->test_id = $test->id;
            $receipt->save(false);

            $teacher->start_time = date('Y-m-d H:i:s');
            $teacher->save(false);
        }

        return $this->render('/site/test', [
            'test' => $test,
            'question' => $question,
            'teacher' => $teacher,
        ]);
    }

    public function actionSubmit()
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $answerId = Yii::$app->request->get('answer_id');
        $questionId = Yii::$app->request->get('question_id');
        $teacherId = Teacher::findOne(['user_id' => Yii::$app->user->id])->id;

        $teacherAnswer = TeacherAnswer::findOne([
            'teacher_id' => $teacherId,
            'question_id' => $questionId,
        ]);

        if (!$teacherAnswer) {
            $teacherAnswer = new TeacherAnswer();
            $teacherAnswer->teacher_id = $teacherId;
            $teacherAnswer->question_id = $questionId;
        }
        $teacherAnswer->answer_id = $answerId;
        $teacherAnswer->save(false);

        $nextQuestion = Question::find()
            ->andWhere(['test_id' => Question::findOne($questionId)->test_id])
            ->andWhere(['>', 'id', $questionId])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if (!$nextQuestion) {
            $nextQuestion = Question::findOne(['test_id' => Question::findOne($questionId)->test_id]);
        }

        return $this->redirect(['site/test', 'id' => $nextQuestion->id]);
    }

    public function actionEnd($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $test = Test::findOne($id);
        $questions = Question::find()->andWhere(['test_id' => $test->id])->all();
        $teacher = Teacher::findOne(['user_id' => Yii::$app->user->id]);

        //unanswered questions? return to test
        $now = new DateTime();
        $startTime = new DateTime($teacher->start_time);
        $testDuration = new DateTime($test->duration);

        $h = (int)$testDuration->format('H') * 3600;
        $i = (int)$testDuration->format('i') * 60;
        $s = (int)$testDuration->format('s');

        $durationInSeconds = $h + $i + $s;
        $timeElapsed = $now->getTimestamp() - $startTime->getTimestamp();

        if ($timeElapsed < $durationInSeconds) {
            $question = Question::find()
                ->joinWith('teacherAnswer')
                ->andWhere(['question.test_id' => $id])
                ->andWhere(['teacher_answer.answer_id' => null])
                ->one();

            if ($question) {
                Yii::$app->session->setFlash('warning', Yii::t('app', 'Барлық сұрақтарға жауап беріңіз!'));
                return $this->redirect(['site/test', 'id' => $question->id]);
            }
        }

        //save end time
        $teacher->end_time = (new \DateTime())->format('Y-m-d H:i:s');
        $teacher->save(false);

        //save results in db
        $score = 0;
        foreach ($questions as $q) {
            $teacherAnswer = TeacherAnswer::findOne([
                'teacher_id' => $teacher->id,
                'question_id' => $q->id]);

            if ($teacherAnswer !== null) {;
                if ($teacherAnswer->answer_id == $q->answer_id) {
                    $score++;
                }
            }
        }

        $teacher->result = $score;
        $teacher->save(false);

        return $this->redirect(['/site/index']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        $teacher = new Teacher();

        if ($model->load(Yii::$app->request->post())
            && $teacher->load(Yii::$app->request->post())
            && $model->signup($teacher)) {

            Yii::$app->session->setFlash('success',
                Yii::t('app',
                    'Тіркелу сәтті өтті!'));


            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
            'teacher' => $teacher,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success',
                    Yii::t('app',
                        'Поштаңызға (спамда болуы мүмкін) жіберілген сілтемеге басыңыз!'));

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', Yii::t('app', 'Поштаңызды дұрыс енгізіңіз!'));
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Құпия сөз жаңартылды!'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Поштаңыз расталды!'));
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', Yii::t('app', 'Поштаңыз расталмады!'));
        return $this->goHome();
    }

    public function actionLanguage($view)
    {
        if(Yii::$app->language == 'kz'){
            Yii::$app->session->set('language', 'ru');
        }else{
            Yii::$app->session->set('language', 'kz');
        }
        return $this->redirect([$view]);
    }
}
