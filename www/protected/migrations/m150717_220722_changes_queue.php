<?php

class m150717_220722_changes_queue extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('tbl_command_queue', 'cmd', 'VARCHAR(512) NOT NULL');
        $this->alterColumn('tbl_command_queue', 'status', "ENUM('pending','processing','finished','failed','error') NOT NULL");
        $this->addColumn('tbl_command_queue', 'last_response', 'VARCHAR(512) NOT NULL after hash');
	}

	public function down()
	{
        $this->dropColumn('tbl_command_queue', 'last_response');
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