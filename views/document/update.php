<?php
use lowbase\document\DocumentAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\Document */

$this->title = Yii::t('document', 'Редактирование документа');
$this->params['breadcrumbs'][] = ['label' => Yii::t('document', 'Документы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('document', 'Редактирование');
DocumentAsset::register($this);
?>

<div class="document-update">

    <div class="row">
        <div class="col-lg-12">
            <?=$this->render('../default/alert');?>
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
