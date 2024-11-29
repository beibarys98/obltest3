<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teacher".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $school
 * @property int $subject_id
 * @property int|null $test_id
 * @property string $language
 * @property string|null $payment_time
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int|null $result
 *
 * @property Subject $subject
 * @property Test $test
 * @property User $user
 */
class Teacher extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required', 'message' => Yii::t('app', 'Толық аты-жөні толтырылмаған!')],
            ['school', 'required', 'message' => Yii::t('app', 'Мекеме толтырылмаған!')],
            ['subject_id', 'required', 'message' => Yii::t('app', 'Пән толтырылмаған!')],
            ['language', 'required', 'message' => Yii::t('app', 'Тест тапсыру тілі толтырылмаған!')],
            ['user_id', 'required'],
            [['user_id', 'subject_id', 'test_id', 'result'], 'integer'],
            [['payment_time', 'start_time', 'end_time'], 'safe'],
            [['name', 'school'], 'string', 'max' => 255],
            ['name', 'match', 'pattern' => '/^[А-ЯЁӘІҢҒҮҰҚӨҺа-яёәіңғүұқөһ\s-]+$/u', 'message' => Yii::t('app', 'Аты-жөніңіз кириллица болуы тиіс!')],
            ['name', 'match', 'pattern' => '/^[^\s]/', 'message' => Yii::t('app', 'Аты-жөніңіз бос орыннан бастала алмайды!')],
            ['name', 'match', 'pattern' => '/\s/', 'message' => Yii::t('app', 'Аты-жөніңіз ең кемінде екі сөзден тұруы тиіс!')],
            [['language'], 'string', 'max' => 50],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::class, 'targetAttribute' => ['test_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'name' => Yii::t('app', 'Name'),
            'school' => Yii::t('app', 'School'),
            'subject_id' => Yii::t('app', 'Subject ID'),
            'test_id' => Yii::t('app', 'Test ID'),
            'language' => Yii::t('app', 'Language'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
            'result' => Yii::t('app', 'Result'),
        ];
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TestQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::class, ['id' => 'test_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TeacherQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TeacherQuery(get_called_class());
    }
}
