<?php
/**
 * @package   yii2-document
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace lowbase\document\components;

use yii\base\Widget;
use Yii;

/**
 * Отображение документов в виде дерева
 * Class TreeWidget
 * @package lowbase\document\components
 */
class TreeWidget extends Widget
{
    public $data = []; // маассив документов

    public function run()
    {
        $data = [];
        if ($this->data) {
            foreach ($this->data as $document) {
                $data[] = [
                    'id' => $document->id,
                    'text' => $document->name . ' <span class="hint">(' . $document->id . ')</span>',
                    'parent' => ($document->parent_id) ? $document->parent_id : '#',
                    'icon' => ($document->is_folder) ? 'glyphicon glyphicon-folder-open' : 'glyphicon glyphicon-file'
                ];
            }
        }
        // Преобразуем в JSON
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this->render('treeWidget', ['data' => $data]);
    }
}
