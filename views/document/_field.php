<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use lowbase\document\models\Document;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;

$attr_name = $field['name'];    // Надпись над полем
$attr_class = "form-group";     // Класс аттрибута
$attr_pos_class = "form-group";     // Класс позиции аттрибута
$attr_value = 'fields[' . $field_id . '][data][' . $data_id . '][value]'; // Значение аттрибута
$attr_pos = 'fields[' . $field_id . '][data][' . $data_id . '][position]'; // Позиция аттрибута
$attr_id = "field-document-fields-" . $field_id . "-data-" . $data_id . "-value";   // Id аттрибута
$attr_pos_id = "field-document-fields-" . $field_id . "-data-" . $data_id . "-position";   // Id позиции аттрибута
$attr_error = null;             // Ошибки аттрибута
$attr_pos_error = null;             // Ошибки позиции аттрибута
// Пометка обязательных аттрибутов
if ($field['min']) {    // Поле обязательно для заполнения
    if ($field['min'] > 1) {    // Поле с несколькими значениями
        $attr_class .= " multiple-required";
        $attr_name .= " <span class='lb-document-module-require'>минимум: " . $field['min'] . "</span>";
    } else {    // Поле с одним значением
        $attr_class .= " required";
        $attr_name .= " <span class='lb-document-module-require'>обязательно</span>";
    }
}
if ($field['max'] > 1) {
    $attr_name .= " <span class='lb-document-module-require'>максимум: " . $field['max'] . "</span>";
}
// Пометка ошибочных аттрибутов
if (in_array($attr_value, array_keys($model->errors))) {
    $attr_class .= ' has-error';
    $attr_error = $model->errors[$attr_value][0];
}
// Пометка ошибочных позиций
if (in_array($attr_pos, array_keys($model->errors))) {
    $attr_pos_class .= ' has-error';
    $attr_pos_error = $model->errors[$attr_pos][0];
}
?>

<div class="row">
    <div class="col-lg-6">
        <div class="<?=$attr_class?>">
            <div>
                <label class='control-label' for='<?=$attr_id?>' ><?=$attr_name?></label>
            </div>
            <div>
            <?php
            // Вывод дополнительного поля
            if (isset($field['type'])) {
                switch ($field['type']) {
                    case 1:     // Целое число
                    case 2:     // Число
                    case 4:     // Строка
                    case 9:     // Регулярное выражение
                        echo Html::activeInput('text', $model, $attr_value, ['class' => 'form-control', 'id' => $attr_id]);
                        break;
                    case 3:     // Флажок
                        echo Html::activeCheckbox($model, $attr_value, ['class' => 'form-control', 'id' => $attr_id, 'label' => null]);
                        break;
                    case 5:     // Текст
                        echo Html::activeTextarea($model, $attr_value, ['class' => 'form-control', 'id' => $attr_id]);
                        break;
                    case 6:     // Список (дочерние документы
                        echo Select2::widget(
                            [
                                'model' => $model,
                                'attribute' => $attr_value,
                                'data' => Document::getAll($field['param']),
                                'options' => [
                                    'id' => $attr_id,
                                    'placeholder' => '',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'class' => 'form-control',
                                    'id' => $attr_id,
                                ]
                            ]
                        );
                        break;
                    case 8:     // Файл (выбор с сервера)
                        echo InputFile::widget([
                            'controller' => 'elfinder',
                            'filter' => 'image',
                            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                            'options' => ['class' => 'form-control', 'id' => $attr_id],
                            'buttonOptions' => ['class' => 'btn btn-default'],
                            'buttonName' => 'Выбрать файл',
                            'multiple' => false,       // возможность выбора нескольких файлов
                            'name' => 'Document',
                            'value' => $model->$attr_value,
                        ]);
                        break;
                    case 7:    // Дата
                        echo DatePicker::widget([
                            'model' => $model,
                            'attribute' => $attr_value,
                            'options' => [
                                'id' => $attr_id,
                                'placeholder' => '',
                            ],
                            'pluginOptions' => [
                                'format' => 'dd.mm.yyyy',
                                'class' => 'form-control',
                                'id' => $attr_id,
                            ]
                        ]);
                        break;
                }
            }
            ?>
            </div>
            <div class="help-block">
                <?=$attr_error?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="<?=$attr_pos_class?>">
            <div>
                <label class='control-label' for='<?=$attr_pos_id?>'><?=Yii::t('document', 'Позиция')?></label>
            </div>
            <div>
                <?=Html::activeInput('text', $model, $attr_pos, ['class' => 'form-control', 'id' => $attr_pos_id])?>
            </div>
            <div class="help-block">
                <?=$attr_pos_error?>
            </div>
        </div>
    </div>
</div>
