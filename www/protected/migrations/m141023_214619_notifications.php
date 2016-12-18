<?php

class m141023_214619_notifications extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_notification',array(
            'id_notification' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            'fk_user' => 'INT(10) UNSIGNED NULL',
            'level' => 'ENUM (\'Info\', \'Warning\', \'Error\')',
            'message' => 'VARCHAR(1024) NOT NULL',
            'new' => 'INT(1) NOT NULL DEFAULT 1',
            'created_at' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'PRIMARY KEY (`id_notification`)'
        ));

        $this->addForeignKey('fk_notification_user_id', 'tbl_notification', 'fk_user',
            'tbl_user', 'id_user', 'CASCADE', 'CASCADE');

	}

	public function down()
	{
        $this->dropForeignKey('fk_notification_user_id', 'tbl_notification');
		$this->dropTable('tbl_notification');
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