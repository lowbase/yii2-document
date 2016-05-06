<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\document\models\Field */

$this->title = Yii::t('document', 'Новое поле');
$this->params['breadcrumbs'][] = ['label' => Yii::t('document', 'Шаблоны'), 'url' => ['template/index']];
$this->params['breadcrumbs'][] = ['label' => $template->name, 'url' => ['template/view', 'id' => $template->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-create">

    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'template' => $template
    ]) ?>

</div>
