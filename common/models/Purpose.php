<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "purpose".
 *
 * @property int $id
 * @property string|null $purpose
 * @property int|null $cost
 */
class Purpose extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purpose';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost'], 'integer'],
            [['purpose'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'purpose' => Yii::t('app', 'Purpose'),
            'cost' => Yii::t('app', 'Cost'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\PurposeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PurposeQuery(get_called_class());
    }
}
