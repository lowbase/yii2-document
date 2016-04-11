<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace lowbase\document\models;

use Yii;

/**
 * This is the model class for table "lb_template".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $path
 *
 * @property string $option_1_name
 * @property integer $option_1_type
 * @property integer $option_1_require
 * @property string $option_1_param
 * @property string $option_2_name
 * @property integer $option_2_type
 * @property integer $option_2_require
 * @property string $option_2_param
 * @property string $option_3_name
 * @property integer $option_3_type
 * @property integer $option_3_require
 * @property string $option_3_param
 * @property string $option_4_name
 * @property integer $option_4_type
 * @property integer $option_4_require
 * @property string $option_4_param
 * @property string $option_5_name
 * @property integer $option_5_type
 * @property integer $option_5_require
 * @property string $option_5_param
 *
 * @property Document[] $lbDocuments
 * @property Field[] $lbFields
 */
class Template extends \yii\db\ActiveRecord
{

    const OPTIONS_COUNT = 5; //полей в базе
    /**
     * @inheritdoc
     */

    /**
     * @return array
     */
    public static function getTypesField()
    {
        return [
            '1' => 'Целое число',
            '2' => 'Число',
            '3' => 'Строка',
            '4' => 'Выключатель',
            '5' => 'Текст',
            '6' => 'Файл (выбор)',
            '7' => 'Список дочерних документов',
            '8' => 'Регулярное выражение'
        ];
    }

    public static function tableName()
    {
        return 'lb_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules =  [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['path'], 'pathValidate', 'skipOnEmpty' => false],
            [['name', 'path'], 'string', 'max' => 255],
            [['name', 'description', 'path'], 'filter', 'filter' => 'trim'],
            [['path', 'description'], 'default', 'value' => null],
        ];

        $options_name = [];
        $options_type = [];
        $options_param = [];
        $options_require = [];
        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $options_name[] = 'option_' . $i . '_name';
            $options_type[] = 'option_' . $i . '_type';
            $options_param[] = 'option_' . $i . '_param';
            $options_require[] = 'option_' . $i . '_require';
        }
        $rules[] = [array_merge($options_name, $options_param), 'string', 'max' => 255];
        $rules[] = [array_merge($options_type, $options_require), 'integer'];
        $rules[] = [$options_name, 'filter', 'filter' => 'trim'];
        $rules[] = [array_merge($options_type, $options_name, $options_param), 'default', 'value' => null];
        $rules[] = [$options_require, 'default','value' => 0];


        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' => Yii::t('document', 'ID'),
            'name' => Yii::t('document', 'Наименование'),
            'description' => Yii::t('document', 'Описание'),
            'path' => Yii::t('document', 'Путь к файлу'),
        ];

        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $labels['option_' . $i . '_name'] = Yii::t('document', 'Название поля') .' '. $i;
            $labels['option_' . $i . '_type'] = Yii::t('document', 'Тип поля') .' '. $i;
            $labels['option_' . $i . '_require'] = Yii::t('document', 'Обязательность поля') .' '. $i;
            $labels['option_' . $i . '_param'] = Yii::t('document', 'Параметр поля') .' '. $i;
        }

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLbDocuments()
    {
        return $this->hasMany(Document::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLbFields()
    {
        return $this->hasMany(Field::className(), ['template_id' => 'id']);
    }

    /**
     * Проверка на существование файла
     * @return bool
     */
    public function pathValidate()
    {
        $ext = substr($this->path, -4);
        $file = ($ext === '.php') ? $this->path : $this->path.'.php';
        if ($this->path && !file_exists(Yii::getAlias($file))) {
            $this->addError('path', Yii::t('document', 'Файл шаблона не найден.'));
        }
        return true;
    }

    /**
     * Список всех шаблонов массивом
     * @return array
     */
    public static function getAll()
    {
        $templates = [];
        $model = Template::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $templates[$m->id] = $m->name;
            }
        }
        return $templates;
    }

    /**
     * Формирование массива аттрибутов согласно количеству
     * "быстрых" расширенный полей.
     * @param $attr - аттрибут (name|type|require|param)
     * @return mixed
     */
    public static function getOptionArray($attr)
    {
        $option = [];
        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $option[] = 'option_' . $i . '_' . $attr;
        }
        return $option;
    }
}
