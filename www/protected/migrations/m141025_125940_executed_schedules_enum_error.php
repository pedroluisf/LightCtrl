<?php

class m141025_125940_executed_schedules_enum_error extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('tbl_executed_schedule', 'type', "enum('normal','function','duration')");

	}

	public function down()
	{
		echo "m141025_125940_executed_schedules_enum_error does not support migration down.\n";
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