<?php
/**
 * This file contains interfaces for the Intsys application.
 *

/**
 * Interface IConnection is the interface that states the necessary steps for a communication to take place
 */
interface IConnection {

    /**
     * @param String $host
     * @return void
     * @throws Exception
     */
    public function startConnection($host);

    /**
     * @return bool
     */
    public function closeConnection();

    /**
     * @param $command
     * @return mixed
     */
    public function sendAndReceive($command);

}

/**
 * Interface IConfigurationUpdater is the interface that states the necessary steps to upload a Configuration Update file
 */
interface IConfigurationUpdater {

    /**
     * @return array
     */
    public static function getAllowedHeaders();

    /**
     * @param string $filePath
     * @return array
     */
    public function processFile($filePath);

    /**
     * @return array
     */
    public function getErrors();
}

/**
 * Interface IComponentConfigurationUpdater is the interface that defines the update for component configurations
 */
interface IComponentConfigurationUpdater extends IConfigurationUpdater {

    /**
     * @param int $ethernetId
     */
    public function setEthernetId($ethernetId);

}

/**
 * Interface IEnergeticClassConfigurationUpdater is the interface that defines the update for Energy Class configurations
 */
interface IEnergeticClassConfigurationUpdater extends IConfigurationUpdater {

    /**
     * @param bool $fullImport
     */
    public function isFullImport($fullImport);

}
