<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace lowbase\document\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DocumentSearch represents the model behind the search form about `common\modules\document\models\Document`.
 */
class DocumentSearch extends Document
{
    public $id_from;
    public $id_till;
    public $position_from;
    public $position_till;
    public $created_at_from;
    public $created_at_till;
    public $updated_at_from;
    public $updated_at_till;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position', 'id_from', 'id_till', 'position_from', 'position_till', 'status', 'is_folder', 'parent_id', 'template_id', 'created_by', 'updated_by'], 'integer'],
            [['name', 'alias', 'title', 'meta_keywords', 'meta_description', 'annotation', 'content', 'image', 'created_at', 'created_at_from', 'created_at_till',  'updated_at_from', 'updated_at_till', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['id_from'] = Yii::t('document', 'От Id');
        $label['id_till'] = Yii::t('document', 'До Id');
        $label['position_from'] = Yii::t('document', 'От позиции');
        $label['position_till'] = Yii::t('document', 'До позиции');
        $label['created_at_from'] = Yii::t('document', 'Создан с');
        $label['created_at_till'] = Yii::t('document', 'Создан до');
        $label['updated_at_from'] = Yii::t('document', 'Редактирован с');
        $label['updated_at_till'] = Yii::t('document', 'Редактирован до');
        return $label;
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
        $query = Document::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=>50,
            ],
            'sort' => array(
                'defaultOrder' => ['created_at' => SORT_DESC],
            ),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'position' => $this->position,
            'status' => $this->status,
            'is_folder' => $this->is_folder,
            'parent_id' => $this->parent_id,
            'template_id' => $this->template_id,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        if ($this->created_at) {
            $date = new \DateTime($this->created_at);
            $this->created_at = $date->format('Y-m-d');
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'meta_keywords', $this->meta_keywords])
            ->andFilterWhere(['like', 'meta_description', $this->meta_description])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);
        if ($this->id_from){
            $query->andFilterWhere(['>=', 'id', $this->id_from]);
        }
        if ($this->id_till){
            $query->andFilterWhere(['<=', 'id', $this->id_till]);
        }
        if ($this->position_from){
            $query->andFilterWhere(['>=', 'position', $this->position_from]);
        }
        if ($this->position_till){
            $query->andFilterWhere(['<=', 'position', $this->position_till]);
        }
        if ($this->created_at_from) {
            $date_from = new \DateTime($this->created_at_from);
            $this->created_at_from = $date_from->format('Y-m-d');
            $query->andFilterWhere(['>=', 'created_at', $this->created_at_from]);
            $this->created_at_from = $date_from->format('d.m.Y');
        }
        if ($this->created_at_till) {
            $date_till = new \DateTime($this->created_at_till);
            $this->created_at_till = $date_till->format('Y-m-d');
            $query->andFilterWhere(['<=', 'created_at', $this->created_at_till]);
            $this->created_at_till = $date_till->format('d.m.Y');
        }
        if ($this->updated_at_from) {
            $date_from = new \DateTime($this->updated_at_from);
            $this->updated_at_from = $date_from->format('Y-m-d');
            $query->andFilterWhere(['>=', 'updated_at', $this->updated_at_from]);
            $this->updated_at_from = $date_from->format('d.m.Y');
        }
        if ($this->updated_at_till) {
            $date_till = new \DateTime($this->updated_at_till);
            $this->updated_at_till = $date_till->format('Y-m-d');
            $query->andFilterWhere(['<=', 'updated_at', $this->updated_at_till]);
            $this->updated_at_till = $date_till->format('d.m.Y');
        }

        return $dataProvider;
    }
}
