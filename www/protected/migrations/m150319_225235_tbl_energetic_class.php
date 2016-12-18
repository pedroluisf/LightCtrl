<?php

class m150319_225235_tbl_energetic_class extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_energetic_class',array(
            'id_energetic_class' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
            'class_key' => 'varchar(30) COLLATE latin1_general_ci UNIQUE NOT NULL',
            'description' => 'varchar(64) COLLATE latin1_general_ci NOT NULL',
            'consumption_watts' => 'int(5) unsigned NOT NULL DEFAULT 0',
            'created_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
            'PRIMARY KEY (`id_energetic_class`)'
        ));
    }

	public function down()
	{
        $this->dropTable('tbl_energetic_class');
        return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}