<?php

class m140923_213452_command_scheduling_add_column_cci_sw_num extends CDbMigration
{
    public function up()
    {
        $this->addColumn('tbl_command_schedule', 'cci_sw_num', 'ENUM( "1", "2", "3", "4", "5", "6", "7", "8") NULL DEFAULT NULL AFTER `type`');
    }

    public function down()
    {
        $this->dropColumn('tbl_command_schedule', 'cci_sw_num');
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