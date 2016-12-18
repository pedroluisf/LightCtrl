<?php

class m150127_233757_status_emergency_battery_failure extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('tbl_status', 'battery_status', 'ENUM( "charged", "charging", "failure") NULL DEFAULT NULL');
        $this->alterColumn('tbl_status_hist', 'battery_status', 'ENUM( "charged", "charging", "failure") NULL DEFAULT NULL');
	}

	public function down()
	{
		echo "m150127_233757_status_emergency_battery_failure does not support migration down.\n";
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