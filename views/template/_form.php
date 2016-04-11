<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use kartik\widgets\SwitchInput;
use lowbase\document\models\Template;
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

    <div class="row">
        <div class="col-lg-12">
            <?php
            for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
                $type_option = 'option_'.$i.'_type';
                if ($model->$type_option) {
                    echo "<span class='pointer ex label label-success off-ex' id='ex-f-".$i."'>".$i."</span>&nbsp;";
                } else {
                    echo "<span class='pointer ex label label-default on-ex' id='ex-f-".$i."'>".$i."</span>&nbsp;";
                }
            }
            ?>
            <div class="clear"></div>

            <p class="hint-block">
                Количество «быстрых» полей ограничено <?= Template::OPTIONS_COUNT?>.
                Нажмите на цифру для активации расширенного поля.
            </p>

            <?php
            for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {?>
                <div class="row hidden-block" id="ex-<?=$i?>">
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_type')->dropDownList([''] + Template::getTypesField(), [
                            'class'=>'ex-field-type form-control']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_param')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_require')->widget(SwitchInput::classname(), [
                            'pluginOptions' => [
                                'onText' => 'Да',
                                'offText' => 'Нет',
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('
        var exfields = $(".ex-field-type");
        $.each(exfields, function(index, value){
            var val = $(this).val();
            if (val && val !== "0"){
                    $(this).parent().parent().parent().show();
                }
        });

        $(".ex").click(function(){
            var id = $(this).attr("id").substr(5);
            var exid = $("#ex-"+id);
            var display = exid.is(":visible");
            if (!display) {
                $("#ex-f-"+id).removeClass("label-default")
                .removeClass("on-ex")
                .addClass("label-success")
                .addClass("off-ex");
                exid.show();
            }
            else {
                 $("#ex-f-"+id).removeClass("label-success")
                .removeClass("off-ex")
                .addClass("label-default")
                .addClass("on-ex");
                exid.hide();
                exid.find("input").val("");
                exid.find(".bootstrap-switch input").bootstrapSwitch("state", false);
                exid.find(".ex-field-type").val(0);

            }
        });
    ');
?>
