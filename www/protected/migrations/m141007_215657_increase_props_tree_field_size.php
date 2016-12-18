<?php

class m141007_215657_increase_props_tree_field_size extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('tbl_area', 'tree_config', 'MEDIUMTEXT');
        $this->alterColumn('tbl_area', 'props_config', 'MEDIUMTEXT');
	}

	public function down()
	{
		echo "m141007_215657_increase_props_tree_field_size does not need migration down.\n";
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