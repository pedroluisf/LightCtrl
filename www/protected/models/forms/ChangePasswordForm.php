<?php

class ChangePasswordForm extends CFormModel
{
    /**
     * @var user
     */
    public $user;

    /**
     * @var string
     */
    public $currentPassword;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var string
     */
    public $newPasswordRepeat;

    /**
     * Validation rules for this form.
     *
     * @return array
     */
    public function rules()
    {
        return array_filter(array(
            (!Yii::app()->user->isAdmin() ? array('currentPassword', 'required') : null),
            array('newPassword, newPasswordRepeat', 'required'),
            array('newPassword', 'compare', 'compareAttribute'=>'newPasswordRepeat', 'message'=>"Passwords don't match"),
            array('currentPassword', 'validateCurrentPassword'),
        ));
    }

    /**
     * Validates current password.
     * @param string $attribute the attribute to validate
     * @return bool Is password valid
     */
    public function validateCurrentPassword($attribute)
    {
        if (!$this->user->validatePassword($this->currentPassword) && !Yii::app()->user->isAdmin()){
            $this->addError($attribute, 'Wrong password!');
        };
    }

    /**
     * Saves the new password.
     */
    public function saveNewPassword()
    {
        $this->user->password = $this->newPassword;
        $this->user->hashPass = true;
        $this->user->update();
    }

}