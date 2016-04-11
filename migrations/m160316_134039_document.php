<?php

use lowbase\document\models\Template;
use yii\db\Migration;
use yii\db\Schema;

class m160316_134039_document extends Migration
{
    const OPTIONS_FIELD = Template::OPTIONS_COUNT;

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $fileds = [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'path' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ];

        for ($i = 1; $i <= self::OPTIONS_FIELD; $i++) {
            $fields['option_' . $i . '_name'] =  Schema::TYPE_STRING . ' NULL DEFAULT NULL';
            $fields['option_' . $i . '_type'] = Schema::TYPE_SMALLINT .' NULL DEFAULT NULL';
            $fields['option_' . $i . '_require'] = Schema::TYPE_SMALLINT .' NOT NULL DEFAULT 0';
            $fields['option_' . $i . '_param'] = Schema::TYPE_STRING . ' NULL DEFAULT NULL';
        }

        //Таблица шаблонов template
        $this->createTable('{{%lb_template}}', $fileds , $tableOptions);

        //Индексы и ключи таблицы шаблонов template
        $this->createIndex('template_name_index', '{{%lb_template}}', 'name');

        //Таблица дополнительных полей документов field
        $this->createTable('{{%lb_field}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'type' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'param' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'require' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'multiple' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'template_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы дополнительных полей документов field_value
        $this->addForeignKey('field_template_id_fk', '{{%lb_field}}', 'template_id', '{{%lb_template}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('field_name_index', '{{%lb_field}}', 'name');

        $fields = [
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
        ];

        for ($i = 1; $i <= self::OPTIONS_FIELD; $i++) {
            $fields['option_' . $i] = Schema::TYPE_TEXT . ' NULL DEFAULT NULL';
        }

        //Таблица документов document
        $this->createTable('{{%lb_document}}', $fields , $tableOptions);

        //Индексы и ключи таблицы документов document
        $this->addForeignKey('document_parent_id_fk', '{{%lb_document}}', 'parent_id', '{{%lb_document}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('document_template_id_fk', '{{%lb_document}}', 'template_id', '{{%lb_template}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('document_name_index', '{{%lb_document}}', 'name');
        $this->createIndex('document_alias_index', '{{%lb_document}}', 'alias');
        $this->createIndex('document_status_index', '{{%lb_document}}', 'status');
        $this->createIndex('document_position_index', '{{%lb_document}}', 'position');

        //Таблица значений дополнительных полей документов field_value
        $this->createTable('{{%lb_field_value}}', [
            'id' => Schema::TYPE_PK,
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы значений дополнительных полей документов field_value
        $this->addForeignKey('field_value_field_id_fk', '{{%lb_field_value}}', 'field_id', '{{%lb_field}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('field_value_document_id_fk', '{{%lb_field_value}}', 'document_id', '{{%lb_document}}', 'id', 'CASCADE', 'CASCADE');

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

    }

    public function down()
    {
        $this->dropTable('{{%lb_visit}}');
        $this->dropTable('{{%lb_field_value}}');
        $this->dropTable('{{%lb_field}}');
        $this->dropTable('{{%lb_document}}');
        $this->dropTable('{{%lb_template}}');
    }
}
