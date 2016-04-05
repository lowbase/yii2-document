<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
use lowbase\document\components\TreeWidget;
use lowbase\document\models\Document;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\DocumentSearch */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
    'header' => '<h1 class="text-center">'.Yii::t('document', 'Дерево документов').'</h1>',
    'toggleButton' => false,
    'id' => 'tree',
    'options' => [
        'tabindex' => false
    ],
]);
?>

<div class="lb-document-module-tree">
    <?= TreeWidget::widget(['data' => Document::find()->orderBy(['position' => SORT_ASC])->all()])?>
</div>

<?php Modal::end(); ?>
