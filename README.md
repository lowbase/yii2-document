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
       'admin/template/<action:(index|create|update|delete|view|multidelete)>' => 'document/template/<action>',
       //Взаимодействия с документами в панели администрирования
       //Правила для документов лучше не менять, т.к. на них завязан js скрипт компонента дерево документов
       'admin/document/<action:(index|create|update|delete|view|multidelete|multiactive|multiblock|move)>' => 'document/document/<action>',
       // Лайк документа
       'like/<id:\d+>' => 'document/document/like'
        //Отображение документов
       '<alias>' => 'document/document/show'
       //Взаимодействия с дополнительными полями шаблонов
       'admin/field/<action:(create|update|delete|multidelete)>' => 'document/field/<action>',
   ],
],

//-----------------------------
// Активируем файловый менеджер
//-----------------------------

'controllerMap' => [
    'elfinder' => [
        'class' => 'mihaildev\elfinder\PathController',
        'access' => ['@'],
        'root' => [
            'baseUrl'=>'',
            'basePath'=>'@app/web',
            'path' => '/attach/document',
            'name' =>'Файлы'
        ],
    ]
],

//-----------------------
// Подключаем сами модули
//-----------------------

'modules' => [
   'gridview' =>  [
       'class' => '\kartik\grid\Module'
   ],
   'document' => [
       'class' => '\lowbase\document\Module',
   ],
],
```
Создание таблиц БД
------------------
Запускаем миграции командой:
```
php yii migrate/up --migrationPath=@vendor/lowbase/yii2-document/migrations
```
