<?php

class m150305_231721_inactive_caneths extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tbl_ethernet', 'inactive', 'TINYINT NOT NULL DEFAULT 0');
        $this->dropIndex('id_ethernet', 'tbl_ethernet');
        $this->dropIndex('host', 'tbl_ethernet');
	}

	public function down()
	{
        $this->dropColumn('tbl_ethernet', 'inactive');
        $this->createIndex('host', 'tbl_ethernet', 'host', true);
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