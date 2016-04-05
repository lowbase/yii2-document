<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\modules\document;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class DocumentAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/document/assets';

    public $css = [
        'css/lb-document-module.css'
    ];

    public $js = [
        'js/translate.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
