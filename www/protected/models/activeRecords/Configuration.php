<?php

/**
 * This is the model class for table "tbl_configuration".
 *
 * The followings are the available columns in table 'tbl_configuration':
 * @property integer $id_configuration
 * @property string $key
 * @property string $type
 * @property string $label
 * @property string $value
 */
class Configuration extends CActiveRecord
{

    protected $allowedImageFormats = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_configuration';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('key, type, label', 'required'),
			array('key, type, label, value', 'length', 'max'=>128),
            array('id_configuration, key, type, label', 'unsafe', 'on'=>'update'),
			// The following rule is used by search().
			array('id_configuration, key, type, label, value', 'safe', 'on'=>'search'),
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
			'id_configuration' => 'Id Configuration',
			'key' => 'Key',
            'type' => 'Type',
            'label' => 'Configuration',
			'value' => 'Value',
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

		$criteria->compare('id_configuration',$this->id_configuration);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Configuration the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function deleteDeprecatedFile() {
        $deprecatedFile = File::getFullPath($this->value);
        @unlink($deprecatedFile);
        $this->value = null;
    }

    public function save($runValidation=true,$attributes=null){
        if ($this->uploadConfigFile()) {
            return parent::save($runValidation, $attributes);
        } else {
            return false;
        }
    }

    protected function uploadConfigFile(){
        $uploadedFile = CUploadedFile::getInstance($this, 'value');
        if ($uploadedFile instanceof CUploadedFile) {
            $filePath = File::getFullPath($uploadedFile->getName(), Yii::app()->params['configsFolder']);
            if (file_exists($filePath)) {
                $this->addError('value', 'A file with that name already exists.');
                return false;
            }
            $info = pathinfo($filePath);
            if (!in_array(strtolower($info["extension"]), $this->allowedImageFormats)) {
                $this->addError('value','The file "'.$uploadedFile->getName().'" cannot be uploaded. Only files with these extensions are allowed: '.implode(', ',$this->allowedImageFormats).'.');
                return false;
            }
            if (!$uploadedFile->saveAs($filePath)) {
                $this->addError('value', 'An error occurred saving file.');
                return false;
            }

            $this->deleteDeprecatedFile();
            $this->value = Yii::app()->params['configsFolder'] . $uploadedFile->getName();
        }
        return true;
    }
}
