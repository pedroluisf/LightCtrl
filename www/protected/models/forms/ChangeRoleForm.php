<?php

class ChangeRoleForm extends CFormModel
{
    /**
     * @var user
     */
    public $user;

    /**
     * @var int
     */
    public $fk_role;

    /**
     * Validation rules for this form.
     *
     * @return array
     */
    public function rules()
    {
        return array(
            array('fk_role', 'required'),
            array('fk_role', 'exist', 'className'=>'Role', 'attributeName'=>'id_role'),
        );
    }

    /**
     * Saves the new password.
     */
    public function saveNewRole()
    {
        $this->user->fk_role=$this->fk_role;
        $this->user->update();
    }

}