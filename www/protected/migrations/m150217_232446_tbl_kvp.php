<?php

class m150217_232446_tbl_kvp extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_kvp',array(
            'key' => 'VARCHAR(64) NOT NULL',
            'value' => 'VARCHAR(128) NOT NULL',
            'PRIMARY KEY (`key`)'
        ));
    }

    public function down()
    {
        $this->dropTable('tbl_kvp');
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