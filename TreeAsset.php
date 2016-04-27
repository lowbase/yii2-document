<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\document;

use yii\web\AssetBundle;

/**
 * Подключение CSS и JS для компонента JSTree
 * Class TreeAsset
 * @package lowbase\document
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
