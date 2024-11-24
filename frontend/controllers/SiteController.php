<?php

namespace frontend\controllers;

use common\models\File;
use common\models\Purpose;
use common\models\Teacher;
use common\models\Test;
use common\models\User;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
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

        $dataProvider3 = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['id' => $teacher->test_id]),
        ]);

        $dataProvider4 = new ActiveDataProvider([
            'query' => File::find()
                ->andWhere(['teacher_id' => $teacher->id])
                ->andWhere(['type' => 'certificate'])
        ]);

        $purpose = Purpose::find()->one();
        $receipt = File::findOne(['teacher_id' => $teacher->id, 'type' => 'receipt']);

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

    /**
     * Logs in a user.
     *
     * @return mixed
     */
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

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        $teacher = new Teacher();

        if ($model->load(Yii::$app->request->post())
            && $teacher->load(Yii::$app->request->post())
            && $model->signup($teacher)) {

            Yii::$app->session->setFlash('success',
                Yii::t('app',
                    'Поштаңызға (спамда болуы мүмкін) жіберілген сілтемеге басыңыз!'));


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
