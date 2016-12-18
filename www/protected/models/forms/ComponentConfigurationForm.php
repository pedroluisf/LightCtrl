<?php
/**
 * ComponentConfigurationForm class.
 * ComponentConfigurationForm is the data structure for saving component configuration from file
 */
class ComponentConfigurationForm extends ImportConfigurationForm
{
    public $ethernet_id;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('ethernet_id', 'required'),
            array('ethernet_id, file', 'safe'),
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'ethernet_id'=>'Ethernet Interface',
            'file'=>'CSV Import File',
        );
    }

}