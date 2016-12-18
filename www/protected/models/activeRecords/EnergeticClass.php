<?php

/**
 * This is the model class for table "tbl_energetic_class".
 *
 * The followings are the available columns in table 'tbl_energetic_class':
 * @property string $id_energetic_class
 * @property string $class_key
 * @property string $description
 * @property string $consumption_watts
 * @property string $created_at
 */
class EnergeticClass extends CActiveRecord
{
    protected static $cachedClassKeys;
    protected static $cachedDefaultWattageValue;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_energetic_class';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('class_key, description, consumption_watts', 'required'),
			array('class_key', 'length', 'max'=>30),
            array('description', 'length', 'max'=>64),
			array('consumption_watts', 'length', 'max'=>5),
            array('description, consumption_watts', 'safe'),
            // The following rule is used by search().
			array('id_energetic_class, description, consumption_watts, created_at', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_energetic_class' => 'Id Energetic Class',
            'class_key' => 'Key',
			'description' => 'Description',
			'consumption_watts' => 'Watts / Hour',
			'created_at' => 'Created At',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_energetic_class',$this->id_energetic_class);
        $criteria->compare('class_key',$this->class_key,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('consumption_watts',$this->consumption_watts,true);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnergeticClass the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Gets a cached wattage value for a given classKey. If no cache is present it will be created
     * @param $classKey
     * @return EnergeticClass | null
     */
    public static function getClassByKey($classKey) {
        if (!is_array(self::$cachedClassKeys)){
            self::$cachedClassKeys = array();
        }

        if (!array_key_exists($classKey, self::$cachedClassKeys)) {
            self::$cachedClassKeys[$classKey] = EnergeticClass::model()->find(
                'class_key=:class_key',
                array(':class_key'=>$classKey)
            );
        }

        return self::$cachedClassKeys[$classKey];
    }

    /**
     * Gets a cached wattage value for a given classKey. If no class exists it will return the default wattage (config)
     * @param $classKey
     * @return integer | null
     */
    public static function getWattageByClass($classKey) {
        $energeticClass = self::getClassByKey($classKey);
        if ($energeticClass){
            $wattage = $energeticClass->consumption_watts;
        } else {
            $wattage = self::getDefaultWattageValue();
        }

        return $wattage;
    }

    /**
     * Gets a cached default Wattage value. If no cache exists, it will populate it from application config
     * @return int
     */
    public static function getDefaultWattageValue() {
        if (self::$cachedDefaultWattageValue === null){
            self::$cachedDefaultWattageValue = (int) Yii::app()->params['default_wattage'];
        }
        return self::$cachedDefaultWattageValue;
    }

}
