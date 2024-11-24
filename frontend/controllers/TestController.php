<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\File;
use common\models\Place;
use common\models\Purpose;
use common\models\Question;
use common\models\search\TeacherSearch;
use common\models\Test;
use common\models\search\TestSearch;
use DOMDocument;
use DOMXPath;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class TestController extends Controller
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
        $searchModel = new TestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['id' => $id]),
        ]);

        $questions = Question::find()
            ->andWhere(['test_id' => $id])
            ->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'questions' => $questions,
        ]);
    }

    public function actionReady($id)
    {
        $test = Test::findOne($id);
        $test->status = 'ready';
        $test->save(false);

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionPublish($id)
    {
        $test = Test::findOne($id);
        $test->status = 'public';
        $test->save(false);

        return $this->redirect(['participants', 'id' => $id]);
    }

    public function actionParticipants($id){

        $dataProvider2 = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['id' => $id]),
        ]);

        $searchModel = new TeacherSearch();
        $searchModel->test_id_2 = $id;
        $dataProvider = $searchModel->search($this->request->queryParams);

        $test = Test::findOne($id);

        return $this->render('participants', [
            'dataProvider2' => $dataProvider2,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'test' => $test
        ]);
    }

    public function actionEnd($id)
    {
        $test = Test::findOne($id);
        $test->status = 'finished';
        $test->save(false);

        return $this->redirect(['participants', 'id' => $id]);
    }

    public function actionCreate()
    {
        $model = new Test();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $file = UploadedFile::getInstance($model, 'file');
                if ($file) {
                    $filePath = 'uploads/' . Yii::$app->security->generateRandomString(8)
                        . '.' . $file->extension;

                    $file->saveAs($filePath);
                }

                $model->status = 'new';
                $model->save(false);

                $linesArray = $this->parseWordDocument($filePath);
                $this->processAndStoreQuestions($linesArray, $model->id);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    function parseWordDocument($filePath)
    {
        $newFilePath = $this->ignoreFormula($filePath);

        $phpWord = IOFactory::load($newFilePath);
        $lines = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof TextRun) {
                    $textLine = '';

                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof Text) {
                            $textLine .= $textElement->getText();
                        }
                    }

                    $lines[] = [
                        'text' => $textLine,
                    ];
                }
            }
        }

        return $lines;
    }

    public function ignoreFormula($filePath){
        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $xmlContent = '';
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (strpos($entry, 'word/document.xml') !== false) {
                    $xmlContent = $zip->getFromIndex($i);
                    break;
                }
            }
            $zip->close();
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadXML($xmlContent);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $nodes = $xpath->query('//m:*');

        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }
        $modifiedXmlContent = $dom->saveXML();

        $newFilePath = 'uploads/' . Yii::$app->security->generateRandomString(8) . '.docx';

        $newZip = new ZipArchive;
        if ($newZip->open($newFilePath, ZipArchive::CREATE) === TRUE) {
            $newZip->addFromString('word/document.xml', $modifiedXmlContent);

            $zip = new ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if ($entry !== 'word/document.xml') {
                        $newZip->addFromString($entry, $zip->getFromIndex($i));
                    }
                }
                $zip->close();
            }
            $newZip->close();
        }
        return $newFilePath;
    }

    public function processAndStoreQuestions($linesArray, $test_id)
    {
        $currentQuestion = null;
        $firstAnswerProcessed = false;

        foreach ($linesArray as $lineData) {
            $lineText = $lineData['text'];

            if (preg_match('/^\s*\d+\s*\.?\s*(.+)$/u', $lineText, $matches)) {
                $currentQuestion = new Question();
                $currentQuestion->test_id = $test_id;
                $currentQuestion->question = $matches[1];
                $currentQuestion->answer_id = '';

                $currentQuestion->save();
                $firstAnswerProcessed = false;

            } elseif (preg_match('/^\s*[a-zA-Zа-яА-ЯёЁ]\s*[.)]?\s*(.+)$/u', $lineText, $matches)) {
                if ($currentQuestion !== null) {
                    $answerText = $matches[1];
                    $answer = new Answer();
                    $answer->question_id = $currentQuestion->id;
                    $answer->answer = $answerText;
                    $answer->save();

                    if (!$firstAnswerProcessed) {
                        $currentQuestion->answer_id = $answer->id;
                        $firstAnswerProcessed = true;
                        $currentQuestion->save(false);
                    }
                }
            }
        }
    }

    public function actionUpdate($id)
    {
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Test::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionSettings()
    {
        $place = Place::find()->one();

        if ($place->load(Yii::$app->request->post()) && $place->save()) {
            return $this->redirect(['settings']);
        }

        $purpose = Purpose::find()->one();

        if ($purpose->load(Yii::$app->request->post()) && $purpose->save()) {
            return $this->redirect(['settings']);
        }

        return $this->render('settings', [
            'place' => $place,
            'purpose' => $purpose,
        ]);
    }
}
