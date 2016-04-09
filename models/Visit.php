<?php

namespace lowbase\document\models;

use lowbase\document\models\Document;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lb_visit".
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
    public $count;
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
                'updatedAtAttribute' => null,
                'value' => date('Y-m-d H:i:s'),
            ]];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_visit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'ip'], 'required'],
            [['document_id', 'user_id', 'count'], 'integer'],
            [['user_agent'], 'string'],
            [['ip'], 'string', 'max' => 20],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
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
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(LbDocument::className(), ['id' => 'document_id']);
    }

    /**
     * Фиксируем посещение документа
     * не более 1 раза в день с одного IP
     * @param $docment_id
     * @return bool
     */
    public static function check($docment_id)
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $model = Visit::find()->where('document_id=:document_id && ip=:ip && created_at>=:created_at', [
            ':document_id' => $docment_id,
            ':ip' => $ip,
            ':created_at' => date('Y-m-d'). ' 00:00:00',
        ])->count();
        if (!$model) {
            $visit = new Visit();
            $visit->document_id = $docment_id;
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
     * @param null $document_ids
     * @param bool $shedule
     * @return array|\yii\db\ActiveRecord[] - возвращает только дату, id документа, кол-во просмотров
     */
    public static function getAll($document_ids = null, $shedule = false)
    {
        $table = Visit::tableName();
        $group_by = ($shedule) ? 'DATE(created_at)' : 'document_id';
        if ($document_ids) {
            $ids = implode(',', $document_ids);
            $sql = 'SELECT date(created_at) as created_at , document_id, count(document_id) as count FROM ' . $table . ' where document_id IN ('.$ids.') GROUP BY ' . $group_by;
        } else {
            $sql = 'SELECT date(created_at) as created_at , document_id, count(document_id) as count FROM ' . $table . ' GROUP BY ' . $group_by;
        }
        $model = Visit::findBySql($sql)->all();

        return $model;
    }

}
