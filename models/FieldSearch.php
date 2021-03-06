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
 * Поиск по подполнительным полям шаблонов
 * Class FieldSearch
 * @package app\modules\document\models
 */
class FieldSearch extends Field
{
    const COUNT = 50; // количество полей на одной странице

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'template_id', 'type', 'min', 'max'], 'integer'],   // Целочисленные значения
            [['name', 'param'], 'safe'],    // Безопасные аттрибуты
        ];
    }

    /**
     * Сценарии
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Создает DataProvider на основе переданных данных
     * @param $params - параметры
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Field::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=> $this::COUNT,
            ],
        ]);

        $this->load($params);

        // Если валидация не пройдена, то ничего не выводить
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Фильтрация
        $query->andFilterWhere([
            'id' => $this->id,
            'template_id' => $this->template_id,
            'type' => $this->type,
            'min' => $this->min,
            'max' => $this->max,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'param', $this->param]);

        return $dataProvider;
    }
}
