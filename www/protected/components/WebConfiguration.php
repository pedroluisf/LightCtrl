<?php

class WebConfiguration extends CApplicationComponent
{
    static function get($key)
    {
        /* @var $model Configuration */
        $model = Configuration::model()->findByAttributes(array('key'=>$key));
        return $model->value;
    }
}