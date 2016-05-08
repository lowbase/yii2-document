Модуль документов
=================

Yii2-document - независимый модуль из комплекта lowBase с панелью администрирования и полным функционалом возможностей для
организации статей, новостей и прочих сущностей контентного содержания.

* Древовидная иерархия документов (категории, разделы являются самими документами)
* WYSIWYG редактор с загрузкой файлов на сервер
* Дополнительные поля документов и мультиполя документов
* Настраиваемые шаблоны отображения
* Ведение статистики посещений
* Лайки докментов
* Удобный Tree-виджет с поддержкой Drag n Drop
* Расширенный поиск по документам и их дополнительным полям

Установка
---------
```
php composer.phar require --prefer-dist lowbase/yii2-document "*"
```
или
```
"lowbase/yii2-document": "*"
```

Настройка конфигурационного файла
---------------------------------

```
//-------------------------------------------------
// Прописываем правила роутинга для соответствующих
// действий с модулем в приложении.
//-------------------------------------------------

'urlManager' => [
   'enablePrettyUrl' => true,
   'showScriptName' => false,
   'rules' => [
       //Взаимодействия с шаблонами в панели администрирования
       'admin/template/<action:(index|create|update|delete|view|multidelete)>' => 'lowbase-document/template/<action>',
       //Взаимодействия с документами в панели администрирования
       //Правила для документов лучше не менять, т.к. на них завязан js скрипт компонента дерево документов
       'admin/document/<action:(index|create|update|delete|view|multidelete|multiactive|multiblock|move|change|field)>' => 'lowbase-document/document/<action>',
        //Взаимодействия с файловым менеджеромч
       'elfinder/<action(connect|manager)>' => 'lowbase-document/path/<action>',
       // Лайк документа
       'like/<id:\d+>' => 'lowbase-document/document/like',
        //Отображение документов
       '<alias>' => 'lowbase-document/document/show',
       //Взаимодействия с дополнительными полями шаблонов
       'admin/field/<action:(create|update|delete|multidelete)>' => 'lowbase-document/field/<action>',
   ],
],

//-----------------------
// Подключаем сами модули
//-----------------------

'modules' => [
   'gridview' =>  [
       'class' => '\kartik\grid\Module'
   ],
   'lowbase-document' => [
       'class' => '\lowbase\document\Module',
   ],
],
```
Внимание!!!
-----------
Рекомендуем не изменять название модуля lowbase-document в конфигурационном файле. А также не изменять роуты на создание, удаление, редактирование, просмотр и перемещение документов. В компоненте JStree (виджет вывода документов в виде дерева) указаны абсолютные пути.

Создание таблиц БД
------------------
Запускаем миграции командой:
```
php yii migrate/up --migrationPath=@vendor/lowbase/yii2-document/migrations
```
Работа с документами
--------------------
Значения дополнительных полей документа хранятся в массиве `$document->fields`

После получения самого документа массив не заполняется:

```
$document = app\models\Document::findOne($id);
print_r($document->fields);     //Array() - массив пуст
```

Для заполнения дополнительных полей документа используйте метод `fillFields()`

```
$document = app\models\Document::findOne($id);
$document->fillFields();
print_r($document->fields);     //Array([1] => ['name' => 'Теги', 'type' => 4, 'param' => '', 'min' => 0, 'max' => 2, 'data' => [[1] => ['value' => 'Тег_1', 'position' => ''], [2] => ...]], [2] => ...)

    /**
     * Значения дополнительных полей
     * Массив должен иметь следующую структуру:
     *
     * [$field_id] => [
     *                  'name' => 'Название дополнительного поля',
     *                  'type' => 'Тип дополнительного поля',
     *                  'param' => 'Параметр дополнительного поля',
     *                  'min' => 'Минимум значений',
     *                  'max' => 'Максимум значений',
     *                  'data' => [ $data_id => [
     *                                            'value' => 'Значение дополнительного поля'
     *                                            'position' => 'Позиция дополнительного поля'
     *                                             ],
     *                                           ...
     *                          ]
     *              ],
     * ...
     *
     * $field_id - ID дополнительного поля из БД, $data_id - ID записи значения дополнительного поля из БД
     * Если необходимо прикрепить новое значение 'data' к документу, то в качестве ключа используем 'new_'.$i, где
     * $i - идентификатор нового значения
     */
```
После сохранения документа
```
$document->save();
```
значения дополнительных полей будут сохранены в соответствующие таблицы.

Можно также получить значения дополнительных полей запросами к соответствующим таблицам (в зависимости от типа поля) БД напрямую

```
// Получение значений дополнительного поля $field_id строкового типа
$data_values = \lowbase\document\models\ValueString::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all();
// Получение значений дополнительного поля $field_id числового типа
$data_values = \lowbase\document\models\ValueNumeric::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all();
// Получение значений дополнительного поля $field_id типа Текст
$data_values = \lowbase\document\models\ValueText::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all();
// Получение значений дополнительного поля $field_id типа Дата
$data_values = \lowbase\document\models\ValueDate::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all();
```

Запуск виджетов
---------------
```
use lowbase\document\components\TreeWidget;

// Виджет отображения документов в виде деревва (используется компонент jstree) 
<?= TreeWidget::widget(['data' => Document::find()->orderBy(['position' => SORT_ASC])->all()]) ?>

```
