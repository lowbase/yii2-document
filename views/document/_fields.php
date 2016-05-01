<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

foreach ($model->fields as $field_id => $field) {
    echo "<div class='lb-document-module-field' id='field-" . $field_id . "'>";
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
