<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\document\controllers;

//use yii\filters\AccessControl;

class PathController extends \mihaildev\elfinder\PathController {

    public $root = [
        'baseUrl'=>'',
        'basePath'=>'@app/web',
        'path' => 'attach/document',
        'name' => 'Файлы',
    ];

    public function behaviors()
    {
        return [
            // Ограничение доступа к операциям, связанным с файловым менеджером
            // Активировать при подключении пользователей и разделений прав
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['manager', 'connect'],
//                'rules' => [
//                    [
//                    ],
//                ],
//            ],
        ];
    }

}
