<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 22-03-2014
 * Time: 0:11
 */

abstract class TransferAbstract implements ArrayAccess {

    protected $attributes = array();
    private $_data = array();

    public function __construct(array $data = null){
        foreach ($this->attributes as $attribute) {
            if ($data && isset($data[$attribute])){
                $this->_data[$attribute] = $data[$attribute];
            } else {
                $this->_data[$attribute] = null;
            }
        }
    }

    abstract public function getId();

    public function __get($name){
        if ($this->hasAttribute($name)){
            return $this->_data[$name];
        }
    }

    public function __set($name, $value){
        if ($this->hasAttribute($name)){
            $this->_data[$name] = $value;
        }
    }

    public function setData(array $data){
        foreach ($this->attributes as $attribute){
            if (isset($data[$attribute]) && is_array($data[$attribute])){
                foreach ($data[$attribute] as $key => $value) {
                    $this->_data[$attribute][$key] = $value;
                }
            } else if (isset($data[$attribute])) {
                $this->_data[$attribute] = $data[$attribute];
            }
        }
    }

    public function getAllAttributeNames(){
        return array_keys($this->_data);
    }

    public function hasAttribute($name){
        return array_key_exists($name, $this->_data);
    }

    public function toArray(){
        return array_filter($this->_data, function($entry) {
            if (is_array($entry)) return true;
            if ($entry !== null) return true;
            return false;
        });
    }

    public function toArrayWithEmptyValues(){
        return $this->_data;
    }

    public function isEmpty(){
        return empty($this->_data);
    }

    public function isEqual(TransferAbstract $transfer){
        $className = get_class($this);
        if (!$transfer instanceof $className){
            throw new InvalidArgumentException("An instance of $className cannot be compared to an instance of ".get_class($transfer));
        }
        return ($this->toArray() == $transfer->toArray());
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    public function offsetGet($offset)
    {
        return $this->_data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $this->_data[$offset] = null;
    }


}