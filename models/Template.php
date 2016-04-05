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
 * @property Document[] $lbDocuments
 * @property Field[] $lbFields
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['path'], 'pathValidate', 'skipOnEmpty' => false],
            [['name', 'path'], 'string', 'max' => 255],
            [['name', 'description', 'path'], 'filter', 'filter' => 'trim'],
            [['path', 'description'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('document', 'ID'),
            'name' => Yii::t('document', 'Наименование'),
            'description' => Yii::t('document', 'Описание'),
            'path' => Yii::t('document', 'Путь к файлу'),
        ];
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
}
