<?php
use yii\helpers\Html;
use lowbase\document\DocumentAsset;

$this->title = $model->title;
DocumentAsset::register($this);
?>
<div class="lb-document-module-post">
    <h1><?=Html::decode($model->name)?></h1>

    <?php
    if ($model->annotation) {
        echo "<div class='annotation'>";
        echo Html::decode($model->annotation);
        echo "</div>";
    }
    ?>

    <?php
    if ($model->image) {
        echo Html::img($model->image);
    }
    ?>

    <?=Html::decode($model->content)?>
</div>