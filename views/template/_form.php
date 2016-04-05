<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12">
            <p>
                <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> '.Yii::t('document', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
                <?php
                if (!$model->isNewRecord) {
                    echo Html::a('<i class="glyphicon glyphicon-trash"></i> '.Yii::t('document', 'Удалить'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('document', 'Вы уверены, что хотите удалить шаблон?'),
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
            <?= $form->field($model, 'path')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
