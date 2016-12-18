<?php

class m141126_235000_schedule_priority extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tbl_command_schedule', 'priority', 'INT(1) NOT NULL DEFAULT 1 AFTER `group`');
	}

	public function down()
	{
        $this->dropColumn('tbl_command_schedule', 'priority');
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