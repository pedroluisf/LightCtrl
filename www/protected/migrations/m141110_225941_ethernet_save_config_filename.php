<?php

class m141110_225941_ethernet_save_config_filename extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tbl_ethernet', 'config_filename', 'VARCHAR(128) NULL AFTER `config`');
	}

	public function down()
	{
        $this->dropColumn('tbl_ethernet', 'config_filename');
		return false;
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