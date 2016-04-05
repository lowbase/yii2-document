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
class TreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lowbase/jstree';

    public $css = [
        'dist/themes/default/style.css'
    ];

    public $js = [
        'dist/jstree.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
