<?php

use app\modules\document\models\Document;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\document\DocumentAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\Document */

$this->title = Yii::t('document', 'Просмотр документа');
$this->params['breadcrumbs'][] = ['label' => Yii::t('document', 'Документы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
DocumentAsset::register($this);
?>
<div class="document-view">

    <div class="row">
        <div class="col-lg-12">
            <?=$this->render('../default/alert');?>
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '.Yii::t('document', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('document', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('document', 'Вы уверены, что хотите удалить документ?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('document', 'Отмена'), ['index'], [
            'class' => 'btn btn-default',
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'alias',
            'title',
            'meta_keywords:ntext',
            'meta_description:ntext',
            'annotation:ntext',
            'content:ntext',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => ($model->image)?'<img src="'.$model->image.'" class="thumbnail lb-document-module-thumb">':null
            ],
            [
                'attribute' => 'status',
                'value' => Document::getStatusArray()[$model->status],
            ],
            [
                'attribute' => 'is_folder',
                'value' => ($model->is_folder) ? Yii::t('document', 'Да') : Yii::t('document', 'Нет'),
            ],
            [
                'attribute' => 'parent_id',
                'format' => 'raw',
                'value' => ($model->parent_id && $model->parent) ? Html::a($model->parent->name, ['document/view', 'id' => $model->parent_id]) : null,
            ],
            [
                'attribute' => 'template_id',
                'format' => 'raw',
                'value' => ($model->template_id && $model->template) ? Html::a($model->template->name, ['template/view', 'id' => $model->template_id]) : null,
            ],
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            'position',
            'created_by',
            'updated_by',
        ],
    ]) ?>

</div>
