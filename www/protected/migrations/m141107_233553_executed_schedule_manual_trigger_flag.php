<?php

class m141107_233553_executed_schedule_manual_trigger_flag extends CDbMigration
{
    public function up()
    {
        $this->addColumn('tbl_executed_schedule', 'manual_trigger', 'INT(1) NOT NULL DEFAULT 0 AFTER `fk_user`');
    }

    public function down()
    {
        $this->dropColumn('tbl_executed_schedule', 'manual_trigger');
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