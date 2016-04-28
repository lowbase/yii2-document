<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace lowbase\document\models;

use lowbase\document\models\Document;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Просмотры статьи
 *
 * @property integer $id
 * @property string $created_at
 * @property integer $document_id
 * @property string $ip
 * @property string $user_agent
 * @property integer $user_id
 *
 * @property LbDocument $document
 */
class Visit extends \yii\db\ActiveRecord
{
    public $count;  // Количество просмотров

    /**
     * Автозаполнение даты просмотра
     * документа
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => date('Y-m-d H:i:s'),
            ]];
    }

    /**
     * Наименование таблицы
     * @return string
     */
    public static function tableName()
    {
        return 'lb_visit';
    }

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['document_id', 'ip'], 'required'],    // Обязательные поля для заполнения
            [['document_id', 'user_id', 'count'], 'integer'],   // Целочисленные значения
            [['user_agent'], 'string'], // Текстовое значение
            [['ip'], 'string', 'max' => 20],    // Строка (максимум 20 символов)
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
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
            'created_at' =>  Yii::t('document', 'Создано'),
            'document_id' => Yii::t('document', 'Документ'),
            'ip' => Yii::t('document', 'IP'),
            'user_agent' => Yii::t('document', 'Данные браузера'),
            'user_id' => Yii::t('document', 'Прользователь'),
        ];
    }

    /**
     * Документ
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }

    /**
     * Фиксирум просмотр документа
     * Не более 1 раза с одного IP в день
     * @param $document_id - ID документа
     * @return bool
     */
    public static function check($document_id)
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        // Проверяем наличие просмотров за сегодня с этого IP
        $model = Visit::find()->where('document_id=:document_id && ip=:ip && created_at>=:created_at', [
            ':document_id' => $document_id,
            ':ip' => $ip,
            ':created_at' => date('Y-m-d'). ' 00:00:00',
        ])->count();
        // Сохраняем запись
        if (!$model) {
            $visit = new Visit();
            $visit->document_id = $document_id;
            $visit->ip = $ip;
            $visit->user_id = (Yii::$app->user->isGuest) ? null : Yii::$app->user->id;
            $visit->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $visit->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получить просмотры документа/ов
     * при shedule = flase - общее количество за все время
     * при shedule = true - количество просмотров, сгруппированные по дням
     *
     * @param null $document_ids - ID документа (-ов)
     * @param bool $shedule - включить расписание просмотров?
     * @return array|\yii\db\ActiveRecord[] - возвращает только дату, id документа, кол-во просмотров
     */
    public static function getAll($document_ids = null, $shedule = false)
    {
        $table = self::tableName();
        $group_by = ($shedule) ? 'DATE(created_at)' : 'document_id';
        if ($document_ids) {
            $ids = (is_array($document_ids)) ? implode(',', $document_ids) : $document_ids;
            $sql = 'SELECT date(created_at) as created_at , document_id, count(document_id) as count FROM ' . $table . ' where document_id IN ('.$ids.') GROUP BY ' . $group_by;
        } else {
            $sql = 'SELECT date(created_at) as created_at , document_id, count(document_id) as count FROM ' . $table . ' GROUP BY ' . $group_by;
        }
        $model = Visit::findBySql($sql)->all();

        return $model;
    }
}
