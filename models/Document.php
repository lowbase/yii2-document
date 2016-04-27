<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace lowbase\document\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\validators\Validator;

/**
 * Документы (универсальные сущности)
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property string $title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $annotation
 * @property string $content
 * @property string $image
 * @property integer $status
 * @property integer $is_folder
 * @property integer $parent_id
 * @property integer $template_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $position
 *
 * @property Template $template
 * @property Document $parent
 * @property Document[] $documents
 * @property Visit[] $lbVisits
 */
class Document extends \yii\db\ActiveRecord
{
    const STATUS_BLOCKED = 0;   //  Скрыт
    const STATUS_ACTIVE = 1;    // Активен
    const STATUS_WAIT = 2;      // На модерации

    /**
     * Наименование таблицы документа
     * @return string
     */
    public static function tableName()
    {
        return 'lb_document';
    }

    /**
     * Автозаполнение даты создания и редактирования
     * Автозаполнение пользоватлея, кто создал и кто отредактировал
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => date('Y-m-d H:i:s'),
            ],[
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => (Yii::$app->user->isGuest) ? null : 'created_by',
                'updatedByAttribute' => (Yii::$app->user->isGuest) ? null : 'updated_by',
            ]];
    }

    /**
     * Статусы документов
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('document', 'Опубликован'),
            self::STATUS_BLOCKED => Yii::t('document', 'Скрыт'),
            self::STATUS_WAIT =>  Yii::t('document', 'На модерации'),
        ];
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['alias'], 'unique'], // Уникальное значение
            [['name', 'alias'], 'required'],    // Обязательные значения
            [['meta_keywords', 'meta_description', 'annotation', 'content'], 'string'], // Текстовые значения
            [['status', 'is_folder', 'parent_id', 'template_id', 'position'], 'integer'],   // Целочисленные значения
            [['created_at', 'updated_at'], 'safe'], // Безопасные аттрибуты
            [['name', 'alias', 'title', 'image'], 'string', 'max' => 255],  // Строковое значение (максимум 255 символов)
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['parent_id' => 'id']],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],    // Статус должен быть из списка статусов
            [['name', 'title', 'meta_keywords', 'meta_description', 'annotation', 'alias'], 'filter', 'filter' => 'trim'],  // Обрезаем строки по краям
            [['title', 'meta_keywords', 'meta_description', 'annotation', 'content', 'image', 'parent_id', 'template_id', 'position'], 'default', 'value' => null], // По умолчанию = null
            [['is_folder'], 'default', 'value' => 0],   // По умолчанию не папка, а документ
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],    // По умолчанию статус "Опубликован"
        ];
    }

    /**
     * Наименования полей аттрибутов
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('document', 'ID'),
            'name' => Yii::t('document', 'Наименование'),
            'alias' => Yii::t('document', 'Алиас'),
            'title' => Yii::t('document', 'Заголовок'),
            'meta_keywords' => Yii::t('document', 'Мета ключи'),
            'meta_description' => Yii::t('document', 'Мета описание'),
            'annotation' => Yii::t('document', 'Аннотация'),
            'content' => Yii::t('document', 'Содержание'),
            'image' => Yii::t('document', 'Изображение'),
            'status' => Yii::t('document', 'Статус'),
            'is_folder' => Yii::t('document', 'Папка?'),
            'parent_id' => Yii::t('document', 'Родитель'),
            'template_id' => Yii::t('document', 'Шаблон'),
            'created_at' => Yii::t('document', 'Создан'),
            'updated_at' => Yii::t('document', 'Редактирован'),
            'created_by' => Yii::t('document', 'Создал'),
            'updated_by' => Yii::t('document', 'Редактировал'),
            'position' => Yii::t('document', 'Позиция'),
            'file' => Yii::t('document', 'Изображения'),

        ];
    }

    /**
     * Шаблон документа
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * Родительский документ
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Document::className(), ['id' => 'parent_id']);
    }

    /**
     * Дочерние документы
     * @return $this
     */
    public function getChildren()
    {
        return $this->hasMany(Document::className(), ['parent_id' => 'id'])->orderBy(['position' => SORT_ASC]);
    }

    /**
     * Просмотры документа
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::className(), ['document_id' => 'id']);
    }

    /**
     * Получить список документов массивом
     * @param null $parent_id - родительский документ
     * @return array [ID => Название]
     */
    public static function getAll($parent_id = null)
    {
        $documents = [];
        if ($parent_id) {
            $model = Document::find()->where(['parent_id' => $parent_id])->all();
        } else {
            $model = Document::find()->all();
        }
        if ($model) {
            foreach ($model as $m) {
                $documents[$m->id] = $m->name;
            }
        }
        return $documents;
    }

    /**
     * Пометка или снятие документа как папки
     * @param $id - ID документа
     * @param bool $child_delete - дочерние документы удаляются?
     * @return bool
     */
    public static function folder($id, $child_delete = false)
    {
        $model = Document::findOne($id);
        $db = self::getDb();
        // Помечаем документ как папку если имеются дочерние документы
        if ($model && $model->children && !$model->is_folder) {
            $db->createCommand()->update('lb_document', ['is_folder' => 1], ['id' => $model->id])->execute();
        }
        // Помечаем папку как документ если нет дочерних документов или
        // имеется один дочерний докуемнт, который будет удален
        if (($model && !$model->children && $model->is_folder) ||
            ($model && count($model->children) === 1 && $model->is_folder && $child_delete)) {
            $db->createCommand()->update('lb_document', ['is_folder' => 0], ['id' => $model->id])->execute();
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // Пометка "Папкой" текущего документа при необходимости
        self::folder($this->parent_id);
        // Пометка "Папкой" родительского документа при необходимости
        if (isset($changedAttributes['parent_id'])) {
            self::folder($changedAttributes['parent_id']);
        }

        return true;
    }

    /**
     * Перед удалением проверяем количество дочерних
     * документов у родительского документа.
     * Если это был единственный документ, то у родителя
     * снимаем значение "Папка"
     * @return bool
     */
    public function beforeDelete()
    {
        parent::beforeDelete();
        // Снятие значения "Папка" у родительского документа при необходимости
        self::folder($this->parent_id, true);
        return true;
    }


    /**
     * Перед сохранением документа выставляем
     * ему необходимую позицию, инкрементируя последнюю
     * позицию из текущей директории
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && !$this->position) {
                $model = self::find()
                    ->select(['position'])
                    ->where(['parent_id' => $this->parent_id])
                    ->orderBy(['position' => SORT_DESC])
                    ->one();
                $this->position = ($model && $model->position) ? $model->position+1 : 1;
            }
            return true;
        }
        return false;
    }

}
