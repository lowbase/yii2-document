<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use lowbase\document\DocumentAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\document\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('document', 'Менеджер шаблонов');
$this->params['breadcrumbs'][] = $this->title;
$assets = DocumentAsset::register($this);
?>
<div class="template-index">
 
    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class'=>'kartik-sheet-style'],
            'width' => '30px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        [
            'attribute' => 'id',
            'width' => '70px',
        ],
        'name',
        'description',
        'path',
        [
            'template' => '{view} {update} {delete}',
            'class'=>'kartik\grid\ActionColumn',
        ],
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'headerOptions'=>['class'=>'kartik-sheet-style'],
        ],
    ];

    echo GridView::widget([
        'layout'=>"{items}\n{summary}\n{pager}",
        'dataProvider'=> $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'containerOptions' => ['style'=>'overflow: auto'],
        'headerRowOptions' => ['class'=>'kartik-sheet-style'],
        'filterRowOptions' => ['class'=>'kartik-sheet-style'],
        'pjax' => false,
        'panel' => [
            'heading' => '<i class="glyphicon glyphicon-book"></i> '.Yii::t('document', 'Шаблоны'),
            'type' => GridView::TYPE_PRIMARY,
            'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('document', 'Добавить'), [
                'template/create'], ['class' => 'btn btn-success']),
            'after' => "<div class='text-right'><b>".Yii::t('document', 'Выбранные').":</b> ".
                Html::button('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('document', 'Удалить'), [
                    'class' => 'btn btn-danger delete-all']).
                "</div>"
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'persistResize' => false,
        'hover' => true,
        'responsive' => true,
    ]);
    ?>

</div>

<?php
$this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "' . Url::to(['template/multidelete']) . '",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>
