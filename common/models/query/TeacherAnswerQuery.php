<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\TeacherAnswer]].
 *
 * @see \common\models\TeacherAnswer
 */
class TeacherAnswerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\TeacherAnswer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\TeacherAnswer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
