<?php

class Factory {

    // Constants for the names of the available Command models
    const COMMAND_STATUS_REQUEST    = 'StatusRequestCommand';
    const COMMAND_DALI              = 'DaliCommand';
    const COMMAND_EMERGENCY_TRIGGER = 'EmergencyTriggerCommand';
    const CLEAR_CONTACT_INPUT       = 'ClearContactInputCommand';
    const ATTRIBUTE_CHANGE          = 'AttributeChangeCommand';

    const CONFIGURATION_TYPE_COMPONENT = 'ComponentConfigurationUpdater';
    const CONFIGURATION_TYPE_ENERGETIC_CLASS = 'EnergeticClassConfigurationUpdater';

    /**
     * @return IConnection | null
     */
    public static function getDefaultConnectionModel() {
        $connectionName = Yii::app()->params['communications_class_name'];
        if (class_exists($connectionName) && in_array('IConnection', class_implements($connectionName))) {
            return new $connectionName();
        }
        return null;
    }

    /**
     * @param String $commandName
     * @return Command | null
     */
    public static function getCommandModelByName($commandName) {
        if (class_exists($commandName) && in_array('Command', class_parents($commandName))) {
            return new $commandName();
        }
        return null;
    }

    /**
     * @param $configurationType
     * @param $fileExtension
     * @return IConfigurationUpdater
     * @throws Exception
     */
    public static function getConfigurationUpdateModelByTypeAndFileExtension($configurationType, $fileExtension) {
        if (!in_array($configurationType, array(self::CONFIGURATION_TYPE_COMPONENT, self::CONFIGURATION_TYPE_ENERGETIC_CLASS))) {
            throw new Exception('Configuration Type not supported');
        }

        $className = ucfirst($fileExtension . $configurationType);
        if (class_exists($className) && in_array('IConfigurationUpdater', class_implements($className))) {
            return new $className();
        }

        throw new Exception('Unable to find the correct Configuration Updater');
    }

}