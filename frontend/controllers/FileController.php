<?php

namespace frontend\controllers;

use common\models\File;
use common\models\search\FileSearch;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{
    /**
     * @inheritDoc
     */
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

        $searchModel = new FileSearch();
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

    public function actionCreate()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = new File();

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
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        if (($model = File::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
