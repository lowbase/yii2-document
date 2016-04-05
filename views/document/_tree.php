<?php

use app\modules\document\components\TreeWidget;
use app\modules\document\models\Document;
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