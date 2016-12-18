<?php

class m140924_223745_minify_old_json_files extends CDbMigration
{
	public function up()
	{
        $ethernetList = Ethernet::model()->findAll();
        /** @var Ethernet $ethernet */
        foreach ($ethernetList as $ethernet) {
            $jsonMinifier = new JsonComponentsMinifier($ethernet->id_ethernet);
            $jsonConfig = json_decode($ethernet->config);
            if (isset($jsonConfig->{JsonComponentsParser::SOURCE_ETHERNET_ID})){ // This validates if it was already minified
                $ethernet->config = $jsonMinifier->minifyJsonConfig(json_decode($ethernet->config));
                $ethernet->refresh_dependencies = true;
                $ethernet->save();
            }
        }
	}

	public function down()
	{
		echo "m140924_223745_minify_old_json_files does not support migration down.\n";
		return false;
	}

}