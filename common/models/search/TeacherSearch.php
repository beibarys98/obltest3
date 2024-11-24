<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Teacher;

/**
 * TeacherSearch represents the model behind the search form of `common\models\Teacher`.
 */
class TeacherSearch extends Teacher
{
    public $username;
    public $test_id_2;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'subject_id', 'test_id', 'result', 'username', 'test_id_2'], 'integer'],
            [['name', 'school', 'language', 'start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Teacher::find()
            ->joinWith('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'username',
                    'name',
                    'test_id',
                    'start_time',
                    'result',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->test_id_2) {
            $query->andWhere(['test_id' => $this->test_id_2]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'teacher.id' => $this->id,
            'test_id' => $this->test_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'result' => $this->result,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'school', $this->school])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'user.username', $this->username]);

        return $dataProvider;
    }
}
