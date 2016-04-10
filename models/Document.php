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

/**
 * This is the model class for table "lb_document".
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
 * @property FieldValue[] $lbFieldValues
 * @property Visit[] $lbVisits
 */
class Document extends \yii\db\ActiveRecord
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_document';
    }

    /**
     * @inheritdoc
     *  Автозаполнение полей created_at и update_at
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
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ]];
    }

    /**
     * Статусы документов
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias'], 'unique'],
            [['name', 'alias'], 'required'],
            [['meta_keywords', 'meta_description', 'annotation', 'content'], 'string'],
            [['status', 'is_folder', 'parent_id', 'template_id', 'position'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'alias', 'title', 'image'], 'string', 'max' => 255],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['parent_id' => 'id']],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],
            [['name', 'title', 'meta_keywords', 'meta_description', 'annotation', 'alias'], 'filter', 'filter' => 'trim'],
            [['title', 'meta_keywords', 'meta_description', 'annotation', 'content', 'image', 'parent_id', 'template_id', 'position'], 'default', 'value' => null],
            [['is_folder'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Document::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Document::className(), ['parent_id' => 'id'])->orderBy(['position' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLbFieldValues()
    {
        return $this->hasMany(FieldValue::className(), ['document_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLbVisits()
    {
        return $this->hasMany(Visit::className(), ['document_id' => 'id']);
    }
    
    /**
     * Получить список документов массивом
     * @param null $parent_id
     * @return array
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
     * Пометка или снятие докуента как папки
     * @param $id
     * @param bool $child_delete
     * @return bool
     */
    public static function folder($id, $child_delete = false)
    {
        $node = Document::findOne($id);
        $db = self::getDb();
        if ($node && $node->children && !$node->is_folder) {
            $db->createCommand()->update('lb_document', ['is_folder' => 1], ['id' => $node->id])->execute();
        }
        if (($node && !$node->children && $node->is_folder) ||
            ($node && count($node->children) === 1 && $node->is_folder && $child_delete)) {
            $db->createCommand()->update('lb_document', ['is_folder' => 0], ['id' => $node->id])->execute();
        }

        return true;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::folder($this->parent_id);
        if (isset($changedAttributes['parent_id'])) {
            self::folder($changedAttributes['parent_id']);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        parent::beforeDelete();
        self::folder($this->parent_id, true);
        return true;
    }
    
        public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && !$this->position) {
                $position = self::find()->select('position')->where(['parent_id' => $this->parent_id])->orderBy(['position' => SORT_DESC])->one();
                $this->position = ($position) ? $position : 0;
            }
            return true;
        }
        return false;
    }

}
