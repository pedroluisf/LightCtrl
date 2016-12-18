<?php

/**
 * This is the model class for table "tbl_role".
 *
 * The followings are the available columns in table 'tbl_role':
 * @property integer $id_role
 * @property string $name
 * @property string $desc
 *
 * The followings are the available model relations:
 * @property TblUser[] $role
 */
class Role extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_role';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, desc', 'required'),
			array('name', 'length', 'max'=>20),
			array('desc', 'length', 'max'=>128),
			// The following rule is used by search().
			array('id_role, name, desc', 'safe', 'on'=>'search'),
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
			'role' => array(self::HAS_MANY, 'User', 'fk_role'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_role' => 'ID',
			'name' => 'Name',
			'desc' => 'Desc',
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

		$criteria->compare('id_role',$this->id_role);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Role the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
