<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Test;

/**
 * TestSearch represents the model behind the search form of `common\models\Test`.
 */
class TestSearch extends Test
{
    public $subject;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'subject_id'], 'integer'],
            [['language', 'version', 'status', 'duration', 'subject'], 'safe'],
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
        $query = Test::find()->joinWith(['subject']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'subject' => [
                        'asc' => ['subject.title' => SORT_ASC],
                        'desc' => ['subject.title' => SORT_DESC],
                    ],
                    'language',
                    'version',
                    'status',
                    'duration',
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'test.id' => $this->id,
            'duration' => $this->duration,
        ]);

        $query->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'subject.title', $this->subject]);

        return $dataProvider;
    }
}
