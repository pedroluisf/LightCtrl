<?php

class m150707_232458_regenerate_jsons extends CDbMigration
{
	public function up()
	{
        $areaList = Area::model()->findAll();
        /** @var Area $area */
        foreach ($areaList as $area) {
            $area->extractJson();
        }
	}

	public function down()
	{
		echo "m150707_232458_regenerate_jsons does not support migration down.\n";
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