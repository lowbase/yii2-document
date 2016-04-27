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
 * Шаблоны документов
 * Используются для применения макетов 
 * отображения данных, а также закрепления
 * за документом дополнительных полей
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
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_template';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'required'], // Обязательно для заполнения
            [['name'], 'unique'],   // Уникальное значение
            [['description'], 'string'],    // Текстовое поле
            [['path'], 'pathValidate', 'skipOnEmpty' => false], // Проверка на существование файла шаблона         
            [['name', 'path'], 'string', 'max' => 255], // Строковое значение (максимум 255 символов)
            [['name', 'description', 'path'], 'filter', 'filter' => 'trim'],    // Обрезаем строки по краям
            [['path', 'description'], 'default', 'value' => null],  // По умолчанию = null
        ];
    }

    /**
     * Наименование полей аттрибутов
     * @return array
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
     * Документы с текущим шаблоном
     * @return \yii\db\ActiveQuery
     */
    public function getLbDocuments()
    {
        return $this->hasMany(Document::className(), ['template_id' => 'id']);
    }

    /**
     * Проверка на существование файла
     */
    public function pathValidate()
    {
        // Определяем расширение файла
        $ext = substr($this->path, -4);
        $file = ($ext === '.php') ? $this->path : $this->path.'.php';
        if ($this->path && !file_exists(Yii::getAlias($file))) {
            // Выводим ошибку если файл не найден
            $this->addError('path', Yii::t('document', 'Файл шаблона не найден.'));
        }
    }

    /**
     * Список шаблонов массивом
     * @return array
     */
    public static function getAll()
    {
        $templates = [];
        $model = self::find()->orderBy(['name' => SORT_ASC])->all();
        if ($model) {
            foreach ($model as $m) {
                $templates[$m->id] = $m->name;
            }
        }
        
        return $templates;
    }
}
