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
 * Поиск среди шаблоново документов
 * Class TemplateSearch
 * @package lowbase\document\models
 */
class TemplateSearch extends Template
{
    const COUNT = 50; // количество шаблонов на одной странице

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],    // Целочисленные значения
            [['name', 'description', 'path'], 'safe'],  // Безопасные аттрибуты
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
        $query = Template::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'path', $this->path]);

        return $dataProvider;
    }
}
