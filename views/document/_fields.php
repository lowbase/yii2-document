<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

foreach ($model->fields as $field_id => $field) {
    echo "<div class='lb-document-module-field' id='field-" . $field_id . "'>";
    if ($field['max'] > 1) {
        echo "<p>" . Html::button(Yii::t('document', 'Добавить'), ['class' => 'add-item btn btn-default']) . "</p>";
    }
    foreach ($field['data'] as $data_id => $data) {
        echo $this->render('_field', [
            'model' => $model,
            'field' => $field,
            'field_id' => $field_id,
            'data' => $data,
            'data_id' => $data_id,
        ]);
    }
    echo "</div>";
}

$document_id = ($model->isNewRecord) ? 0 : $model->id;
$this->registerJs("
    var multi_new = 0;
    $('.add-item').click(function(){
        var field_id = $(this).parent().parent().attr('id').substr(6);
        $.ajax({
            url: '" . Url::to(['document/field']) . "',
            type: 'POST',
            data: {
                'field_id' : field_id,
                'document_id' : " . $document_id . ",
                'data_id' : multi_new
            },
            success: function(data){
                $('#field-'+field_id).append(data);
                multi_new++;
            }
        });
    });
");
