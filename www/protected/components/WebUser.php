<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 05-01-2014
 * Time: 22:55
 */

class WebUser extends CWebUser {

    // Store model to not repeat query.
    private $_model;

    // Return first name.
    // access it by Yii::app()->user->first_name
    function getFirst_Name(){
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->first_name;
    }

    // Return last name.
    // access it by Yii::app()->user->first_name
    function getLast_Name(){
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->last_name;
    }

    // This is a function that checks the field 'role'
    // in the User model to be equal to 1, that means it's admin
    // access it by Yii::app()->user->isAdmin()
    function isAdmin(){
        $user = $this->loadUser(Yii::app()->user->id);
        if ($user){
            return intval($user->fk_role) == 1;
        }
        return false;
    }

    // This is a function that checks the field 'role'
    // in the User model to be equal to 1, that means it's admin
    // access it by Yii::app()->user->isAdmin()
    function isSuperUser(){
        $user = $this->loadUser(Yii::app()->user->id);
        if ($user){
            return intval($user->fk_role) == 2;
        }
        return false;
    }

    // Load user model.
    protected function loadUser($id=null)
    {
        if($this->_model===null)
        {
            if($id!==null)
                $this->_model=User::model()->findByPk($id);
        }
        return $this->_model;
    }

    /**
     * @return array flash message keys array
     */
    public function getFlashKeys()
    {
        $counters=$this->getState(self::FLASH_COUNTERS);
        if(!is_array($counters)) return array();
        return array_keys($counters);
    }
}
?>