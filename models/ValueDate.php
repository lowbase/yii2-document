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
 * Значения дат дополнительных полей документов
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
class ValueDate extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы базы данных
     * @return string
     */
    public static function tableName()
    {
        return 'lb_value_date';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['document_id', 'field_id', 'type'], 'required'],  // Обязательные поля для заполнения
            [['document_id', 'field_id', 'type', 'position'], 'integer'],   // Только целочисленные значения
            [['value'], 'safe'],    // Безопасные аттрибуты
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
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
