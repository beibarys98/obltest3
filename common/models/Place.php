<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "place".
 *
 * @property int $id
 * @property int|null $first
 * @property int|null $second
 * @property int|null $third
 * @property int|null $fourth
 * @property int|null $fifth
 */
class Place extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'place';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first', 'second', 'third', 'fourth', 'fifth'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first' => Yii::t('app', 'First'),
            'second' => Yii::t('app', 'Second'),
            'third' => Yii::t('app', 'Third'),
            'fourth' => Yii::t('app', 'Fourth'),
            'fifth' => Yii::t('app', 'Fifth'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\PlaceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PlaceQuery(get_called_class());
    }
}
