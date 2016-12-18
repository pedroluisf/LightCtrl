<?php

class m150218_221442_tbl_consumption extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_consumption',array(
            'id_consumption' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
            'fk_ethernet' => 'int(11) unsigned NOT NULL',
            'lc_id' => 'int(11) unsigned NOT NULL',
            'dvc_id' => 'int(11) unsigned NOT NULL',
            'date' => 'date NOT NULL',
            'type' => 'varchar(12) COLLATE latin1_general_ci NOT NULL',
            'fk_description' => 'int(5) unsigned DEFAULT NULL',
            'consumption_watts' => 'int(5) unsigned DEFAULT NULL',
            'consumption_minutes' => 'int(5) unsigned DEFAULT NULL',
            'PRIMARY KEY (`id_consumption`)'
        ));

        $this->addForeignKey('fk_consumption_ethernet_id', 'tbl_consumption', 'fk_ethernet',
            'tbl_ethernet', 'id_ethernet', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_consumption_description_id', 'tbl_consumption', 'fk_description',
            'tbl_description', 'id_description', 'SET NULL', 'CASCADE');
    }

	public function down()
	{
        $this->dropTable('tbl_consumption');
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