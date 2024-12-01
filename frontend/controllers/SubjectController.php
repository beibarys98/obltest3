<?php

namespace frontend\controllers;

use common\models\Subject;
use common\models\search\SubjectSearch;
use common\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class SubjectController extends Controller
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

        $searchModel = new SubjectSearch();
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

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function saveFile($model){
        $folderPath = 'templates/' . $model->title;
        if (!is_dir($folderPath)) {
            FileHelper::createDirectory($folderPath, 0775, true);
        }

        foreach (['first', 'second', 'third', 'fourth', 'fifth'] as $attribute) {
            $file = UploadedFile::getInstance($model, $attribute);
            if ($file) {
                $filePath = $folderPath . '/' . $attribute . '.' . $file->extension;
                if ($file->saveAs($filePath)) {
                    $model->$attribute = $filePath;
                }
            }
        }

        $model->save();
    }

    public function actionCreate()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = new Subject();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $this->saveFile($model);

                return $this->redirect(['index',]);
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
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            $this->saveFile($model);

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Subject::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
