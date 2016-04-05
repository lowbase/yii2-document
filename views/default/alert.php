<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use kartik\widgets\Alert;
?>

<div class="lb-document-module-alert">

<?php
$delay = 4000;

if (Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => Yii::t('document', 'Ошибка'),
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => $delay
    ]);
}
if (Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => Yii::t('document', 'Результат'),
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => $delay
    ]);
}
?>

</div>