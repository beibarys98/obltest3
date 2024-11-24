<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $first
 * @property string $second
 * @property string $third
 * @property string $fourth
 * @property string $fifth
 *
 * @property Teacher[] $teachers
 * @property Test[] $tests
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first', 'second', 'third', 'fourth', 'fifth'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg'],

            [['title', 'title_ru',], 'required'],
            [['title', 'title_ru', 'first', 'second', 'third', 'fourth', 'fifth'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'title_ru' => Yii::t('app', 'Title Ru'),
            'first' => Yii::t('app', 'First'),
            'second' => Yii::t('app', 'Second'),
            'third' => Yii::t('app', 'Third'),
            'fourth' => Yii::t('app', 'Fourth'),
            'fifth' => Yii::t('app', 'Fifth'),
        ];
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TeacherQuery
     */
    public function getTeachers()
    {
        return $this->hasMany(Teacher::class, ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Tests]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TestQuery
     */
    public function getTests()
    {
        return $this->hasMany(Test::class, ['subject_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SubjectQuery(get_called_class());
    }
}
