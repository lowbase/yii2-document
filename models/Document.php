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
     * Значения дополнительных полей
     * Массив должен иметь следующую структуру:
     *
     * [field_id] => [
     *                  'name' => ...,
     *                  'type' => ...,
     *                  'param' => ...,
     *                  'min' => ...,
     *                  'max' => ...,
     *                  'data' => [ id_value_i => [
     *                                            'value' => ...
     *                                            'position' => ...
     *                                             ],
     *                                           ...
     *                          ]
     *              ],
     * ...
     *
     * Для новых значений в качестве ключа
     * значений использется прставка new_i, где
     * i - идентификатор нового значения
     */
    public $fields = [];

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
            [['fields'], 'fieldsValidate', 'skipOnEmpty' => true], // Валидация дополнительных полей
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
     * Числовые значения дополнительных полей документа
     * @return \yii\db\ActiveQuery
     */
    public function getValueNumeric()
    {
        return $this->hasMany(ValueNumeric::className(), ['document_id' => 'id']);
    }

    /**
     * Строковые значения дополнительных полей документа
     * @return \yii\db\ActiveQuery
     */
    public function getValueString()
    {
        return $this->hasMany(ValueString::className(), ['document_id' => 'id']);
    }

    /**
     * Текстовые значения дополнительных полей документа
     * @return \yii\db\ActiveQuery
     */
    public function getValueText()
    {
        return $this->hasMany(ValueText::className(), ['document_id' => 'id']);
    }

    /**
     * Значения дат дополнительных полей документа
     * @return \yii\db\ActiveQuery
     */
    public function getValueDate()
    {
        return $this->hasMany(ValueDate::className(), ['document_id' => 'id']);
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
        $this->fieldsSave();
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
            // При смене шаблона удаляем значения полей от старого шаблона
            if (!$this->isNewRecord && $this->getOldAttribute('template_id') != $this->template_id) {
                ValueNumeric::deleteAll(['document_id' => $this->id]);
                ValueString::deleteAll(['document_id' => $this->id]);
                ValueText::deleteAll(['document_id' => $this->id]);
                ValueDate::deleteAll(['document_id' => $this->id]);
            }
            return true;
        }
        return false;
    }

    /**
     * Инициализация дополнительных полей
     * документа.
     * Заполнение знчаениями из базы.
     * Формирование необходимых полей для
     * заполнения
     */
    public function fillFields()
    {
        /**
         * Заполение field из уже имеющихся
         * значений полей из БД
         * Второе условие обеспечивает возможность сброса полей
         * при динамическом изменении шаблона документа
         */
        if (!$this->isNewRecord && $this->getOldAttribute('template_id') == $this->template_id) {
            $fieldsValue = array_merge($this->valueNumeric, $this->valueString, $this->valueText, $this->valueDate);
            if ($fieldsValue) {
                foreach ($fieldsValue as $fv) {
                    // Преобразум дату в понятный формат
                    if ($fv->type == 7) {
                        $date = new \DateTime($fv->value);
                        $value = $date->format('d.m.Y');
                    } else {
                        $value = $fv->value;
                    }
                    // Значения мультиполя
                    if (in_array($fv->field_id, array_keys($this->fields))) {
                            $this->fields[$fv->field_id]['data'][$fv->id] = [
                         'value' => $value,
                         'position' => $fv->position
                     ];
                    } else {
                        if (isset($fv->field)) {
                            $field = $fv->field;
                            $this->fields[$fv->field_id] = [
                                'name' => $field->name,
                                'type' => $field->type,
                                'param' => $field->param,
                                'min' => $field->min,
                                'max' => $field->max,
                                'data' => [
                                        $fv->id => [
                                            'value' => $value,
                                            'position' => $fv->position
                                        ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
        /*
         * Добавление к массиву полей $fields новые аттрибуты
         * согласно данных текущего шаблона документа
         */
        if ($this->template_id) {
            // Получаем поля текущего шаблона
            $templateFields = Field::find()->where(['template_id' => $this->template_id])->all();
            $new_index = 0;
            foreach ($templateFields as $tf) {
                // Проверяем присутствие хотя бы одного значения поля
                if (in_array($tf->id, array_keys($this->fields))) {
                    $field_count = count($this->fields[$tf->id]['data']); // Количество значений
                    while ($field_count < $tf->min) {
                        $this->fields[$tf->id]['data']['new_'.$new_index] = [
                            'value' => '',
                            'position' => ''
                        ];
                        $new_index++;
                        $field_count++;
                    }
                } else {
                    // Значений этого поля нет
                    $field_count = 0;
                    $this->fields[$tf->id] = [
                        'name' => $tf->name,
                        'type' => $tf->type,
                        'param' => $tf->param,
                        'min' => $tf->min,
                        'max' => $tf->max,
                        'data' => []
                    ];
                    do {
                        $this->fields[$tf->id]['data']['new_'.$new_index] = [
                            'value' => '',
                            'position' => ''
                        ];
                        $new_index++;
                        $field_count++;
                    } while ($field_count < $tf->min);
                }
            }
        }
    }

    /**
     * Валидация дополнительных полей
     * документа
     */
    public function fieldsValidate()
    {
        if ($this->fields) {
            $access_field_ids = []; // ID доступных полей
            // Получаем поля текущего шаблона
            $templateFields = Field::find()->where(['template_id' => $this->template_id])->all();
            if ($templateFields) {
                foreach ($templateFields as $tf) {
                    $access_field_ids[] = $tf->id;
                    // Заполняем недостающие необходимые данных, которые
                    // не приходят с формы заполнения документа, но необходимы
                    // для валидации
                    if (in_array($tf->id, array_keys($this->fields))) {
                        $this->fields[$tf->id]['name'] = $tf->name;
                        $this->fields[$tf->id]['type'] = $tf->type;
                        $this->fields[$tf->id]['param'] = $tf->param;
                        $this->fields[$tf->id]['min'] = $tf->min;
                        $this->fields[$tf->id]['max'] = $tf->max;
                    }
                }
            }
            // Определяем необходимую модель для валидации
            foreach ($this->fields as $field_id => $field) { // Перебираем дополнительные поля
                if (in_array($field_id, $access_field_ids)) { // Защита от подмены данных в массиве других полей
                    if (isset($field['data']) && count($field['data']) && in_array($field['type'], array_keys(Field::getTypes()))) {
                    foreach ($field['data'] as $data_id => $data) { // Перебираем все значения дополнительных полей
                        if (in_array($field['type'], array_keys(ValueNumeric::getTypes()))) {   // Числовой тип
                            $item = (substr_count($data_id, 'new')) ? new ValueNumeric() : ValueNumeric::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueString::getTypes()))) {  // Строковый тип
                            $item = (substr_count($data_id, 'new')) ? new ValueString() : ValueString::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueText::getTypes()))) {    // Текстовый тип
                            $item = (substr_count($data_id, 'new')) ? new ValueText() : ValueText::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueDate::getTypes()))) {    // Тип дата
                            $item = (substr_count($data_id, 'new')) ? new ValueDate() : ValueDate::findOne($data_id);
                        }
                        $item->document_id = $this->id;
                        $item->field_id = $field_id;
                        $item->type = $field['type'];
                        $item->value = isset($data['value']) ? $data['value'] : null;
                        $item->position = isset($data['position']) ? $data['position'] : null;
                        if (!$item->validate()) {
                            // Ошибка значения дополнительного поля
                            if (isset($item->errors['value'][0])) {
                                $this->addError('fields['.$field_id.'][data]['.$data_id.'][value]', $item->errors['value'][0]);
                            }
                            // Ошибка позиции дополнительного поля
                            if (isset($item->errors['position'][0])) {
                                $this->addError('fields['.$field_id.'][data]['.$data_id.'][position]', $item->errors['position'][0]);
                            }
                        }
                    }
                }
                } else {
                    // Попытка сохранить  поля не присущие текущему шаблону
                    $this->addError('fields['.$field_id.']', Yii::t('document', 'Ошибка сохранения поля'));
                }
            }
            $this->requreValidate();
        }
    }

    /**
     * Валидация на обязательность заполнения
     * полей
     */
    protected function requreValidate()
    {
        $count_fields_value = []; // Количество заполненныех значений в каждом поле
        // Определяем кол-во заполненных значений, заполняя массив
        foreach($this->fields as $field_id => $field) {
            $count_fields_value[$field_id] = 0;
            foreach($field['data'] as $data) {
                if (isset($data['value']) && $data['value'] != '') {
                    if (isset($count_fields_value[$field_id])) {
                        $count_fields_value[$field_id]++;
                    } else {
                        $count_fields_value[$field_id] = 1;
                    }
                }
            }
        }
        // Добавляем ошибки при необходимости
        foreach ($this->fields as $field_id => $field) {
            foreach ($field['data'] as $data_id => $data) {
                if ($count_fields_value[$field_id] > $field['max']) {
                    $this->addError('fields['.$field_id.'][data]['.$data_id.'][value]', Yii::t('document', 'Должно быть максимум') . ' ' . $field['max'] . ' ' . Yii::t('document', 'значений'));
                }
                if ($count_fields_value[$field_id] < $field['min']) {
                    $this->addError('fields['.$field_id.'][data]['.$data_id.'][value]', Yii::t('document', 'Должно быть минимум') . ' ' . $field['min'] . ' ' . Yii::t('document', 'значений'));
                }
            }
        }
    }

    /**
     * Сохранение дополнительных полей
     * документа
     */
    protected function fieldsSave()
    {
        if ($this->fields) {
            $field_count = 0;
            foreach ($this->fields as $field_id => $field) {
                if (isset($field['data']) && count($field['data']) && in_array($field['type'], array_keys(Field::getTypes()))) {
                    foreach ($field['data'] as $data_id => $data) { // Перебираем все значения дополнительных полей
                        if (in_array($field['type'], array_keys(ValueNumeric::getTypes()))) {   // Числовой тип
                            $item[$field_count] = (substr_count($data_id, 'new')) ? new ValueNumeric() : ValueNumeric::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueString::getTypes()))) {  // Строковый тип
                            $item[$field_count] = (substr_count($data_id, 'new')) ? new ValueString() : ValueString::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueText::getTypes()))) {    // Текстовый тип
                            $item[$field_count] = (substr_count($data_id, 'new')) ? new ValueText() : ValueText::findOne($data_id);
                        } elseif (in_array($field['type'], array_keys(ValueDate::getTypes()))) {    // Тип дата
                            $item[$field_count] = (substr_count($data_id, 'new')) ? new ValueDate() : ValueDate::findOne($data_id);
                        }
                        $item[$field_count]->document_id = $this->id;
                        $item[$field_count]->field_id = $field_id;
                        $item[$field_count]->type = $field['type'];
                        // Преобразуем дату в формат для хранения в БД
                        if ($field['type'] == 7) {
                            if (isset($data['value']) && $data['value'] != '') {
                                $date = new \DateTime($data['value']);
                                $value = $date->format('Y-m-d');
                            } else {
                                $value = null;
                            }
                        } else {
                            $value = isset($data['value']) ? $data['value'] : null;
                        }
                        $item[$field_count]->value = $value;
                        $item[$field_count]->position = isset($data['position']) ? $data['position'] : null;
                        if ($item[$field_count]->value != '') {
                            // Сохраняем если значение не пустое
                            $item[$field_count]->save();
                        } else {
                            // Удаляем если значение пустое, но оно есть в базе данных
                            if (!$item[$field_count]->isNewrecord) {
                                $item[$field_count]->delete();
                            }
                        }
                        $field_count++;
                    }
                }
            }
        }
    }
}
