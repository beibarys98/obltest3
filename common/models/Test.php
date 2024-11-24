<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property int $id
 * @property int $subject_id
 * @property string $language
 * @property string $version
 * @property string $status
 * @property string $duration
 *
 * @property Subject $subject
 * @property Teacher[] $teachers
 */
class Test extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => 'docx'],

            [['subject_id', 'language', 'version', 'status', 'duration'], 'required'],
            [['subject_id'], 'integer'],
            [['duration'], 'safe'],
            [['language', 'version', 'status'], 'string', 'max' => 50],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject_id' => Yii::t('app', 'Subject ID'),
            'language' => Yii::t('app', 'Language'),
            'version' => Yii::t('app', 'Version'),
            'status' => Yii::t('app', 'Status'),
            'duration' => Yii::t('app', 'Duration'),
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
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TeacherQuery
     */
    public function getTeachers()
    {
        return $this->hasMany(Teacher::class, ['test_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TestQuery(get_called_class());
    }
}
