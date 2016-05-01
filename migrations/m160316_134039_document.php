<?php

use lowbase\document\models\Template;
use yii\db\Migration;
use yii\db\Schema;

class m160316_134039_document extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //Таблица шаблонов template
        $this->createTable('{{%lb_template}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'path' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ] , $tableOptions);

        //Индексы и ключи таблицы шаблонов template
        $this->createIndex('template_name_index', '{{%lb_template}}', 'name');

        //Таблица документов document
        $this->createTable('{{%lb_document}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'alias' => Schema::TYPE_STRING . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'meta_keywords' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'annotation' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'content' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'image' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'status' =>Schema::TYPE_SMALLINT. ' NOT NULL DEFAULT 1',
            'is_folder' =>Schema::TYPE_SMALLINT. ' NOT NULL DEFAULT 0',
            'parent_id' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'template_id' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'created_at' =>Schema::TYPE_DATETIME. ' NOT NULL',
            'updated_at' =>Schema::TYPE_DATETIME. ' NULL DEFAULT NULL',
            'created_by' =>Schema::TYPE_INTEGER. ' NOT NULL',
            'updated_by' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'position' => Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
        ] , $tableOptions);

        //Индексы и ключи таблицы документов document
        $this->addForeignKey('document_parent_id_fk', '{{%lb_document}}', 'parent_id', '{{%lb_document}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('document_template_id_fk', '{{%lb_document}}', 'template_id', '{{%lb_template}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('document_name_index', '{{%lb_document}}', 'name');
        $this->createIndex('document_alias_index', '{{%lb_document}}', 'alias');
        $this->createIndex('document_status_index', '{{%lb_document}}', 'status');

        //Дополнительные поля
        $this->createTable('{{%lb_field}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'template_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'param' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'min' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'max' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
        ], $tableOptions);

        //Индексы и ключи таблицы полей field
        $this->addForeignKey('field_template_id_fk', '{{%lb_field}}', 'template_id', '{{%lb_template}}', 'id', 'CASCADE', 'CASCADE');

        //Числовые значения дополнительных полей
        $this->createTable('{{%lb_value_numeric}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы числовых значений дополнительных полей
        $this->addForeignKey('value_numeric_document_id_fk', '{{%lb_value_numeric}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_numeric_field_id_fk', '{{%lb_value_numeric}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_numeric_name_index', '{{%lb_value_numeric}}', 'value');

        //Строковые значения дополнительных полей
        $this->createTable('{{%lb_value_string}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы строковых значений дополнительных полей
        $this->addForeignKey('value_string_document_id_fk', '{{%lb_value_string}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_string_field_id_fk', '{{%lb_value_string}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_string_name_index', '{{%lb_value_string}}', 'value');

        //Текстовые значения дополнительных полей
        $this->createTable('{{%lb_value_text}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы текстовых значений дополнительных полей
        $this->addForeignKey('value_text_document_id_fk', '{{%lb_value_text}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_text_field_id_fk', '{{%lb_value_text}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');

        //Значения дат дополнительных полей
        $this->createTable('{{%lb_value_date}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы значений дат дополнительных полей
        $this->addForeignKey('value_date_document_id_fk', '{{%lb_value_date}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_date_field_id_fk', '{{%lb_value_date}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_date_name_index', '{{%lb_value_date}}', 'value');

        //Таблица просмотров документов visit
        $this->createTable('{{%lb_visit}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NOT NULL',
            'user_agent' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы таблицы просмотров документов visit
        $this->addForeignKey('visit_document_id_fk', '{{%lb_visit}}', 'document_id', '{{%lb_document}}', 'id', 'CASCADE', 'CASCADE');

        //Таблица просмотров документов visit
        $this->createTable('{{%lb_like}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NOT NULL',
            'user_agent' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы таблицы просмотров документов visit
        $this->addForeignKey('like_document_id_fk', '{{%lb_like}}', 'document_id', '{{%lb_document}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%lb_like}}');
        $this->dropTable('{{%lb_visit}}');
        $this->dropTable('{{%lb_value_numeric}}');
        $this->dropTable('{{%lb_value_string}}');
        $this->dropTable('{{%lb_value_text}}');
        $this->dropTable('{{%lb_value_date}}');
        $this->dropTable('{{%lb_field}}');
        $this->dropTable('{{%lb_document}}');
        $this->dropTable('{{%lb_template}}');
    }
}
