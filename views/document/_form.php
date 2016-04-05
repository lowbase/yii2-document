<?php

use lowbase\document\models\Document;
use lowbase\document\models\Template;
use yii\helpers\Html;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use lowbase\document\DocumentAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\Document */
/* @var $form yii\widgets\ActiveForm */
DocumentAsset::register($this);
?>

<div class="document-form">

    <?php $form = ActiveForm::begin([
        'id' => 'document',
        'enableClientValidation' => false,
        'method' => 'POST',
        'options' => [
            'enctype'=>'multipart/form-data'
        ]
    ]); ?>

    <div class="row">
        <div class="col-lg-12">
            <p>
                <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> '.Yii::t('document', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
                <?php
                if (!$model->isNewRecord) {
                    echo Html::a('<i class="glyphicon glyphicon-level-up"></i> '.Yii::t('document', 'Создать дочерний'), ['create', 'parent_id' => $model->id], [
                        'class' => 'btn btn-default',
                    ])." ";
                    echo Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('document', 'Удалить'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('document', 'Вы уверены, что хотите удалить документ?'),
                            'method' => 'post',
                        ],
                    ]);
                }
                ?>
                <?= Html::a('<i class="glyphicon glyphicon-menu-left"></i> '.Yii::t('document', 'Отмена'), ['index'], [
                    'class' => 'btn btn-default',
                ]) ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'title', [
                'addon' => [
                    'append' => [
                        'content'=> Html::a(Yii::t('document', 'Повторить название'), '#', [
                            'class' =>['btn btn-default repeat-name']]),
                        'asButton'=>true,
                    ],
                    'groupOptions' => [
                        'id' => 'title-btn'
                    ]
                ]
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'alias', [
                'addon' => [
                    'append' => [
                        'content' => ButtonDropdown::widget([
                            'label' => 'Сформировать',
                            'dropdown' => [
                                'items' => [
                                    ['label' => Yii::t('document', 'Из названия'), 'url' => '#', 'options' => ['class'=>'translate-name']],
                                    ['label' => Yii::t('document', 'Из заголовка'), 'url' => '#', 'options' => ['class'=>'translate-title']],
                                ],
                            ],
                            'options' => ['class'=>'btn-default']
                        ]),
                        'asButton' => true
                    ],
                    'groupOptions' => [
                        'id' => 'alias-btn'
                    ]
                ]
            ]); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'status')->dropDownList(Document::getStatusArray()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
                'data' => Document::getAll(),
                'options' => [
                    'placeholder' => '',
                    'class' => 'parent_d'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'template_id')->widget(Select2::classname(), [
                'data' => Template::getAll(),
                'options' => [
                    'placeholder' => '',
                    'class' => 'template_id'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'meta_keywords')->textarea(['rows' => 2]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'meta_description')->textarea(['rows' => 2]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'annotation')->textarea(['rows' => 2]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                'editorOptions' => ElFinder::ckeditorOptions('elfinder', []),
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'image')->widget(InputFile::className(), [
                'controller'    => 'elfinder',
                'filter'        => 'image',
                'template'      => '<div class="input-group">
                                                {input}<span class="input-group-btn">{button}</span>
                                            </div>',
                'options'       => ['class' => 'form-control'],
                'buttonName'    => Yii::t('document', 'Выбрать файл'),
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple'      => false
            ]);
            ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs("
    $('.repeat-name').click(function(){
    var text = $('#document-name').val();
    $('#document-title').val(text);
    });
    $('.translate-name').click(function(){
    var text = $('#document-name').val().toLowerCase();
    result = translit(text);
    $('#document-alias').val(result);
    });
    $('.translate-title').click(function(){
    var text = $('#document-title').val().toLowerCase();
    result = translit(text);
    $('#document-alias').val(result);
    });
");
?>
