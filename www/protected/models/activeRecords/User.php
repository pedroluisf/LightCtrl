<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id_user
 * @property string $username
 * @property string $password
 * @property integer $fk_role
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 *
 * The followings are the available model relations:
 * @property TblRole $role
 */
class User extends CActiveRecord
{
    public $role_search;
    public $repeat_password;
    public $hashPass;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, repeat_password, email, first_name, last_name', 'required', 'on'=>'create'),
            array('username, email, first_name, last_name', 'required', 'on'=>'update'),
            array('username', 'unique','className'=>'User','attributeName'=>'username','message'=>"Username already exists"),
            array('repeat_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match",'on'=>'create'),
			array('username, password, repeat_password, email, first_name, last_name', 'length', 'max'=>128),
            array('email','email'),
            array('username, email, first_name, last_name','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			array('id_user, username, email, role_search, first_name, last_name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'role' => array(self::BELONGS_TO, 'Role', 'fk_role'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_user' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
            'repeat_password' => 'Repeat Password',
            'fk_role' => 'Role',
			'role_search' => 'Role',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
        $criteria->with = array( 'role' );

		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('username',$this->username,true);
        $criteria->compare('fk_role', $this->role_search, true );
		$criteria->compare('email',$this->email,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'attributes'=>array(
                    'role_search'=>array(
                        'asc'=>'role.name',
                        'desc'=>'role.name DESC',
                    ),
                    '*',
                ),
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Provides an easy method for validating user passwords
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return CPasswordHelper::verifyPassword($password,$this->password);
    }

    public function beforeDelete(){
        //Admin integrity Validation rules
        if ($this->id_user == 1){
            Yii::app()->user->setFlash('error','User "admin" cannot be deleted.');
            return false;
        }
        return true;
    }

    /**
     * Overrides the username to a lower cased username and the password to save with an hashed one
     * * @return bool
     */
    public function beforeSave() {

        //Admin integrity Validation rules
        if ($this->id_user == 1){
            if (strtolower($this->username) != 'admin') {
                Yii::app()->user->setFlash('error','User "admin" cannot change username.');
                $this->username = 'admin';
                return false;
            }
            if ($this->fk_role != 1) {
                Yii::app()->user->setFlash('error','User "admin" cannot change role.');
                $this->fk_role = 1;
                return false;
            }
        }

        if (!empty($this->username)){
            $this->username = strtolower($this->username);
        }
        if (!empty($this->password) && $this->hashPass){
            $this->password=CPasswordHelper::hashPassword($this->password);
        }
        return true;
    }
}
