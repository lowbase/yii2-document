<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\document;

/**
 * document module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lowbase\document\controllers';

    public $documentPhotoPath = 'attach/document';

    /**
     * Инициализация
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::registerTranslations();
    }

    /**
     * Подключаем сообщения перевода
     */
    public static function registerTranslations()
    {
        if (!isset(\Yii::$app->i18n->translations['document']) && !isset(\Yii::$app->i18n->translations['document/*'])) {
            \Yii::$app->i18n->translations['document'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@lowbase/document/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'document' => 'document.php'
                ]
            ];
        }
    }

}
