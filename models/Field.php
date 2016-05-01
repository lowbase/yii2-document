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
 * Дополнительные поля шаблона
 *
 * @property integer $id
 * @property string $name
 * @property integer $template_id
 * @property integer $type
 * @property string $param
 * @property integer $min
 * @property integer $max
 *
 * @property Template $template
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_field';
    }

    /**
     * Типы дополнительных полей
     * @return array
     */
    public static function getTypes()
    {
        return ValueNumeric::getTypes() + ValueString::getTypes() + ValueText::getTypes() + ValueDate::getTypes();
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'template_id', 'type'], 'required'],  // Обязательны для заполнения
            [['template_id', 'type', 'min', 'max'], 'integer'], // Целочисленные значения
            [['name', 'param'], 'string', 'max' => 255],    // Текстовая строка (максимум 255 символов)
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['name', 'param'], 'filter', 'filter' => 'trim'],    // Обрезаем строки по краям
            [['max'], 'maxValidate'],
            [['param'], 'default', 'value' => null],   // По умолчанию = null
            [['min'], 'default', 'value' => 0],   // По умолчанию = 0
            [['max'], 'default', 'value' => 1],   // По умолчанию = 1
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
            'name' =>  Yii::t('document', 'Наименование'),
            'template_id' => Yii::t('document', 'Шаблон'),
            'type' => Yii::t('document', 'Тип'),
            'param' => Yii::t('document', 'Параметры'),
            'min' => Yii::t('document', 'Минимум значений'),
            'max' => Yii::t('document', 'Максимум значений'),
        ];
    }

    /**
     * Шаблон, которому принадлежит поле
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /*
     * Валидация количества максимальных значений
     * Должно быть больше 0 (иначе нет смысла в данном поле)
     * Должно быть больше количества минимальных значений
     * иначе всегда будет ошибка валидации
     */
    public function maxValidate()
    {
        if ($this->max < 1) {
            $this->addError('max', Yii::t('document', 'Значений должно быть больше 0'));
        }
        if ($this->max < $this->min) {
            $this->addError('max', Yii::t('document', 'Значений должно быть больше чем минимальное количество'));
        }
    }
}
