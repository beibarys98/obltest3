<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\File;
use common\models\Place;
use common\models\Purpose;
use common\models\Question;
use common\models\search\TeacherSearch;
use common\models\Teacher;
use common\models\Test;
use common\models\search\TestSearch;
use common\models\User;
use DOMDocument;
use DOMXPath;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Smalot\PdfParser\Parser;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\HttpException;
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
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $searchModel = new TestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id, $switch = 'participant')
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['id' => $id]),
        ]);

        $questions = Question::find()
            ->andWhere(['test_id' => $id])
            ->all();

        $searchModel = new TeacherSearch();
        $searchModel->test_id_2 = $id;
        $dataProvider2 = $searchModel->search($this->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'questions' => $questions,
            'searchModel' => $searchModel,
            'dataProvider2' => $dataProvider2,
            'switch' => $switch,
        ]);
    }

    public function actionReady($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $test = Test::findOne($id);
        $test->status = 'ready';
        $test->save(false);

        return $this->redirect(['view', 'id' => $id, 'switch' => 'participant']);
    }

    public function actionPublish($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $test = Test::findOne($id);
        $test->status = 'public';
        $test->save(false);

        return $this->redirect(['view', 'id' => $id, 'switch' => 'participant']);
    }

    public function actionEnd($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $test = Test::findOne($id);
        $test->status = 'finished';
        $test->save(false);

        return $this->redirect(['view', 'id' => $id, 'switch' => 'participant']);
    }

    public function actionResult($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        //check names in receipts
        $files = File::find()->andWhere(['test_id' => $id])->andWhere(['type' => 'receipt'])->all();
        $fullNames = [];
        foreach ($files as $file) {
            $path = Yii::getAlias('@webroot/' . $file->path);
            if (!file_exists($path)) {
                throw new NotFoundHttpException('The file does not exist.');
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            $normalizedText = preg_replace('/\s+/', ' ', $text);
            $normalizedText = trim($normalizedText);
            $normalizedText = str_replace(["\n", "\r", "\t"], ' ', $normalizedText);
            $normalizedText = str_replace(['—', '"'], ['-', ''], $normalizedText);

            $fullNameSection = '';
            if (preg_match('/образование(.*?)Платеж/su', $normalizedText, $matches)) {
                $fullNameSection = trim($matches[1]);
            } else {
                echo 'No text found between "образование" and "платеж" in ' . $path . '<br>';
            }
            $searchString = 'Актюбинский областной научно-практический центр - г. Актобе, ул. Тынышбаева 43а';
            $flag = (strpos($normalizedText, $searchString) !== false && strpos($normalizedText, '5 000,00 ₸') !== false) ? '1' : '0';

            $fullNames[] = [
                'fullName' => $fullNameSection ?: 'N/A',
                'flag' => $flag
            ];
        }

        //save results in xlsx
        $teachers = Teacher::find()->andWhere(['test_id' => $id])->all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Есімі');
        $sheet->setCellValue('B1', 'Мекеме');
        $sheet->setCellValue('C1', 'Нәтиже');
        $sheet->setCellValue('D1', 'Төленді');

        $row = 2;
        foreach ($teachers as $teacher) {
            $sheet->setCellValue('A' . $row, $teacher->name);
            $sheet->setCellValue('B' . $row, $teacher->school);
            $sheet->setCellValue('C' . $row, $teacher->result);

            $foundFlag = '0';
            foreach ($fullNames as $fullNameData) {
                $normalizedText = strtr($fullNameData['fullName'], ['Ə' => 'Ә']);
                if($normalizedText == $teacher->name){
                    $foundFlag = $fullNameData['flag'];
                    break;
                }
            }

            $sheet->setCellValue('D' . $row, $foundFlag);
            $row++;
        }

        $filePath = 'uploads/result.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $test = Test::findOne($id);
        $filename = $test->subject->title . '_' . $test->language . '_' . $test->version . '_нәтиже.xlsx';

        return Yii::$app->response->sendFile($filePath, $filename);
    }

    public function actionPresent($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $test = Test::findOne($id);
        $test->status = 'certificated';
        $test->save(false);

        //send certificates
        $topResults = Teacher::find()->andWhere(['test_id' => $id])->orderBy(['result' => SORT_DESC])->all();

        $firstPlace = [];
        $secondPlace = [];
        $thirdPlace = [];
        $goodResults = [];
        $certificateResults = [];

        $percentage = Place::find()->one();

        foreach ($topResults as $result) {
            if ($result->result >= $percentage->first) {
                $firstPlace[] = $result;
            }
            else if ($result->result >= $percentage->second) {
                $secondPlace[] = $result;
            }
            else if ($result->result >= $percentage->third) {
                $thirdPlace[] = $result;
            }
            else if ($result->result >= $percentage->fourth) {
                $goodResults[] = $result;
            }
            else if ($result->result >= $percentage->fifth) {
                $certificateResults[] = $result;
            }
        }

        foreach ($firstPlace as $result) {
            $this->certificate(Teacher::findOne($result->id), Test::findOne($id), 'first');
        }
        foreach ($secondPlace as $result) {
            $this->certificate(Teacher::findOne($result->id), Test::findOne($id), 'second');
        }
        foreach ($thirdPlace as $result) {
            $this->certificate(Teacher::findOne($result->id), Test::findOne($id), 'third');
        }
        foreach ($goodResults as $result) {
            $this->certificate(Teacher::findOne($result->id), Test::findOne($id), 'fourth');
        }
        foreach ($certificateResults as $result) {
            $this->certificate(Teacher::findOne($result->id), Test::findOne($id), 'fifth');
        }

        return $this->redirect(['view', 'id' => $id, 'switch' => 'participant']);
    }

    function certificate($teacher, $test, $place)
    {
        $imgPath = Yii::getAlias("@webroot/templates/{$test->subject->title}/{$place}.jpg");
        $image = imagecreatefromjpeg($imgPath);
        $textColor = imagecolorallocate($image, 227, 41, 29);
        $fontPath = Yii::getAlias('@frontend/fonts/times.ttf');

        //writing name
        $averageCharWidth = 9.5;
        $numChars = strlen($teacher->name);
        $textWidth = $numChars * $averageCharWidth;
        $cx = 1700;
        $x = (int)($cx - ($textWidth / 2));
        imagettftext($image, 50, 0, $x, 1600, $textColor, $fontPath, $teacher->name);

        //writing number
        $formattedId = str_pad($teacher->id, 5, '0', STR_PAD_LEFT);
        imagettftext($image, 50, 0, 3100, 2300, $textColor, $fontPath, $formattedId);

        $directoryPath = 'certificates/' . $test->subject->title;
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $newPath = $directoryPath . '/' . $teacher->name . '.jpg';
        imagejpeg($image, $newPath);
        imagedestroy($image);

        //send emails
        $email = $teacher->user->email;
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'sendCertificate-html'],
                ['teacher' => $teacher]
            )
            ->setFrom(['beibarys.mukhammedyarov@alumni.nu.edu.kz' => Yii::t('app', Yii::$app->name)])
            ->setTo($email)
            ->setSubject(Yii::t('app', Yii::$app->name))
            ->attach(Yii::getAlias("@webroot/{$newPath}"))
            ->send();

        $certificate = new File();
        $certificate->teacher_id = $teacher->id;
        $certificate->test_id = $test->id;
        $certificate->type = 'certificate';
        $certificate->path = $newPath;
        $certificate->save(false);
    }

    public function actionCertificates($id)
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

        $filePaths = File::find()->andWhere(['test_id' => $id])->andWhere(['type' => 'certificate'])->all();

        $zip = new ZipArchive();
        $zipFilePath = Yii::getAlias('@webroot/uploads/certificates.zip');
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new HttpException(500, 'Could not create ZIP file.');
        }

        foreach ($filePaths as $filePath) {
            if (file_exists($filePath->path)) {
                $zip->addFile($filePath->path, basename($filePath->path));
            } else {
                Yii::error("File not found: $filePath->path");
            }
        }

        $zip->close();

        $test = Test::findOne($id);
        $filename = $test->subject->title . '_' . $test->language . '_' . $test->version . '.zip';

        $response = Yii::$app->response->sendFile($zipFilePath, $filename);
        $response->send();
        unlink($zipFilePath);

        return $response;
    }

    public function actionJournal($id)
    {
        $teachers = Teacher::find()->andWhere(['test_id' => $id])->all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header row
        $sheet->setCellValue('A1', 'Есімі')
            ->setCellValue('B1', '1 орын сериясы')
            ->setCellValue('C1', '2 орын сериясы')
            ->setCellValue('D1', '3 орын сериясы')
            ->setCellValue('E1', 'Алғыс хат сериясы')
            ->setCellValue('F1', 'Серитификаттың сериясы');

        // Populate data
        $row = 2;
        foreach ($teachers as $teacher) {
            $teacherName = $teacher->name ?? '---';
            $place = Place::find()->one();

            $sheet->setCellValue("A{$row}", $teacherName);

            // Calculate serials based on thresholds
            $serial = $teacher ? str_pad($teacher->id, 5, '0', STR_PAD_LEFT) : '';

            $first = $teacher->result >= $place->first;
            $second = $teacher->result >= $place->second && $teacher->result < $place->first;
            $third = $teacher->result >= $place->third && $teacher->result < $place->second;
            $fourth = $teacher->result >= $place->fourth && $teacher->result < $place->third;
            $fifth = $teacher->result >= $place->fifth;

            $sheet->setCellValue("B{$row}",  $first ? $serial : '');
            $sheet->setCellValue("C{$row}", $second ? $serial : '');
            $sheet->setCellValue("D{$row}", $third ? $serial : '');
            $sheet->setCellValue("E{$row}", $fourth ? $serial : '');
            $sheet->setCellValue("F{$row}", $fifth ? $serial : '');

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filePath = Yii::getAlias("@webroot/uploads/journal.xlsx");
        $writer->save($filePath);

        $test = Test::findOne($id);
        $filename = $test->subject->title . '_' . $test->language . '_' . $test->version . '_журнал.xlsx';

        return Yii::$app->response->sendFile($filePath, $filename);
    }

    public function actionCreate()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

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

                unlink($filePath);

                return $this->redirect(['view', 'id' => $model->id, 'switch' => 'test']);
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

        unlink($newFilePath);

        return $lines;
    }

    function ignoreFormula($filePath){
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

    function processAndStoreQuestions($linesArray, $test_id)
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
        if (($model = Test::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionSettings()
    {
        if(!User::findOne(['id' => Yii::$app->user->id, 'username' => 'admin']) && Yii::$app->user->isGuest){
            return $this->redirect('/site/login');
        }

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
