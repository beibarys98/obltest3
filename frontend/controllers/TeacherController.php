<?php

namespace frontend\controllers;

use common\models\File;
use common\models\Teacher;
use common\models\search\TeacherSearch;
use common\models\TeacherAnswer;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TeacherController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $searchModel = new TeacherSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Teacher::find()->andWhere(['id' => $id]),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = new Teacher();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = $this->findModel($id);
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

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionDelete($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $teacher = Teacher::findOne($id);
        $files = File::findAll(['teacher_id' => $id]);
        foreach ($files as $file) {
            if ($file->path) {
                unlink($file->path);
            }
        }

        File::deleteAll(['teacher_id' => $id]);
        TeacherAnswer::deleteAll(['teacher_id' => $teacher->id]);

        $teacher->delete();

        $user = User::findOne($teacher->user_id);
        if ($user !== null) {
            $user->delete();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Teacher::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
