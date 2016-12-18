<?php
/**
 * EnergeticClassConfigurationForm class.
 * EnergeticClassConfigurationForm is the data structure for saving Energetic Classes configuration from file
 */
class EnergeticClassConfigurationForm extends ImportConfigurationForm
{
    public $full_import;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
//            array('file', 'required'),
            array('full_import, file', 'safe'),
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
            'full_import'=>'Full Import',
            'file'=>'CSV Import File',
        );
    }

}