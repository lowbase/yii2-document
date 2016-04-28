<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use kartik\grid\GridView;
use lowbase\document\models\Field;
use yii\helpers\Html;
use yii\helpers\Url;

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
    [
        'attribute' => 'type',
        'value' => function ($model) {
            return Field::getTypes()[$model->id];
        },
        'filter' => Field::getTypes(),
    ],
    'min',
    'max',
    [
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url, $model, $key) {
                $options = [
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                ];
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                    'field/update', 'id' => $key], $options);
            },
            'delete' => function ($url, $model, $key) {
                $options = [
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post'
                ];
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['field/delete', 'id' => $key], $options);
            }
        ],
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
        'heading' => '<i class="glyphicon glyphicon-th-list"></i> '.Yii::t('document', 'Дополнительные поля'),
        'type' => GridView::TYPE_PRIMARY,
        'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('document', 'Добавить'), [
            'field/create', 'template_id' => $model->id], ['class' => 'btn btn-success']),
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

<?php
$this->registerJs('
            $(".delete-all").click(function(){
            var keys = $(".grid-view").yiiGridView("getSelectedRows");
            $.ajax({
                url: "' . Url::to(['field/multidelete']) . '",
                type:"POST",
                data:{keys: keys},
                success: function(data){
                    location.reload();
                }
                });
            });
        ');
?>
