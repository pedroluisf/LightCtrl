<?php

class m141006_223427_retry_counter_commands extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tbl_command_queue', 'retry_counter', 'INT(1) NOT NULL DEFAULT 0 AFTER `status`');
	}

	public function down()
	{
        $this->dropColumn('tbl_command_queue', 'retry_counter');
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