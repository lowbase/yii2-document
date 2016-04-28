<?php

namespace lowbase\document\models;

use Yii;

/**
 * Текстовые значения дополнительных полей документов
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $field_id
 * @property integer $type
 * @property integer $position
 * @property string $value
 *
 * @property Field $field
 * @property Document $document
 */
class ValueText extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы базы данных
     * @return string
     */
    public static function tableName()
    {
        return 'lb_value_text';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['document_id', 'field_id', 'type'], 'required'],  // Обязательно для заполнения
            [['document_id', 'field_id', 'type', 'position'], 'integer'],   // Целочисленные значения
            [['value'], 'string'],  // Текстовые значения
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => LbField::className(), 'targetAttribute' => ['field_id' => 'id']],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => LbDocument::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['position', 'value'], 'default', 'value' => null],  // По умолчанию = null
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
            'document_id' => Yii::t('document', 'Документ'),
            'field_id' => Yii::t('document', 'Поле'),
            'type' => Yii::t('document', 'Тип'),
            'position' => Yii::t('document', 'Позиция'),
            'value' => Yii::t('document', 'Значение'),
        ];
    }

    /**
     * Поле
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    /**
     * Документ
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }
}
