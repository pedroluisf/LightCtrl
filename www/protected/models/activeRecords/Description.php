<?php

/**
 * This is the model class for table "tbl_description".
 *
 * The followings are the available columns in table 'tbl_description':
 * @property integer $id_description
 * @property string $description
 */
class Description extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_description';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			array('id_description, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_emergency' => 'Id',
            'description' => 'Description',
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

		$criteria->compare('id_description',$this->id_description);
        $criteria->compare('description', $this->description, true );

		return new CActiveDataProvider($this, array('criteria'=>$criteria));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Emergency the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Saves a new description or gets the fk of an existing one
     *
     * @param TransferAbstract $component
     * @return int
     */
    public static function getComponentDescriptionId(TransferAbstract $component) {
        // Figure out what is to save
        if (isset($component['custom_description']) && $component['custom_description']) {
            $description2Save = $component['custom_description'];
        } elseif (isset($component['description']) && $component['description']) {
            $description2Save = $component['description'];
        } else {
            $description2Save = '';
        }

        // Look for existing
        $description = Description::model()->find('`description` = :desc', array(':desc'=>$description2Save));
        if (!$description) {
            $description = new Description('create');
            $description->description = $description2Save;
            $description->save();
        }

        // Return Key
        return $description->id_description;
    }


}
