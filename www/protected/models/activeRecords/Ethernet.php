<?php

/**
 * This is the model class for table "tbl_ethernet".
 *
 * The followings are the available columns in table 'tbl_ethernet':
 * @property integer $id_ethernet
 * @property string $name
 * @property string $desc
 * @property string $host
 * @property string $config
 * @property string $config_filename
 * @property integer $fk_area
 * @property integer $lock
 * @property integer $locked_at
 * @property integer $locked_by
 * @property integer $inactive
 *
 * The followings are the available model relations:
 * @property Area $area
 */
class Ethernet extends CActiveRecord
{
    public $config_file;
    public $clear_data;
    public $refresh_dependencies;
    public $altered_config;
    public $area_search;

    protected $allowedFileFormats = array('json');


    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_ethernet';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_ethernet, name, desc, host, fk_area', 'required'),
			array('id_ethernet, fk_area', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
            array('name', 'unique','className'=>'Ethernet','attributeName'=>'name','message'=>"Name already exists"),
            array('id_ethernet', 'unique','className'=>'Ethernet','attributeName'=>'id_ethernet','message'=>"CANETH ID already exists"),
			array('desc', 'length', 'max'=>128),
			array('config_file', 'file', 'types'=>'json', 'allowEmpty'=>false, 'on'=>'create'),
			array('config_file', 'file', 'types'=>'json', 'allowEmpty'=>true, 'on'=>'update'),
			array('host', 'length', 'max'=>32),
            array('id_ethernet, name, host, fk_area','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
            array('fk_area', 'exist', 'className'=>'Area', 'attributeName'=>'id_area'),
            array('id_ethernet, fk_area', 'unsafe', 'on'=>'update'),
            array('clear_data, inactive', 'safe', 'on'=>'update'),
            // The following rule is used by search().
            array('id_ethernet, name, desc, host, area_search, inactive', 'safe', 'on'=>'search'),
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
			'area' => array(self::BELONGS_TO, 'Area', 'fk_area'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_ethernet' => 'CANETH ID',
			'name' => 'Name',
			'desc' => 'Description',
			'host' => 'Host',
			'config_file' => 'Configuration File',
            'config_filename' => 'Configuration File',
            'clear_data' => 'Clear Old Data?',
            'fk_area' => 'Area',
			'area_search' => 'Area',
            'inactive' => 'Inactive',
            'status' => 'Status',
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
        $criteria->with = array('area');

		$criteria->compare('id_ethernet',$this->id_ethernet);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('host',$this->host,true);
		$criteria->compare('fk_area',$this->area_search, true);
        $criteria->compare('inactive',$this->inactive);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'attributes'=>array(
                    'area_search'=>array(
                        'asc'=>'area.name',
                        'desc'=>'area.name DESC',
                    ),
                    '*',
                ),
            ),
		));
	}

    public function __get($name)
    {
        if ($name == 'translated_inactive'){
            return $this->inactive ? 'Inactive' : 'Active';
        } else {
            return parent::__get($name);
        }
    }

    public function getInactiveToGrid(){
        return $this->inactive ? Emergency::FAIL_ICON : Emergency::OK_ICON;
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Ethernet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function afterDelete(){
        if (file_exists(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder']))) {
            @unlink(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder']));
        }
        $this->refresh_dependencies = true;
        $this->generateDependencies();
        return true;
    }

    public function save($runValidation = true, $attributes = NULL){
        if ($this->uploadAndValidateConfigFile()) {
            return parent::save($runValidation, $attributes);
        } else {
            return false;
        }
    }

    public function afterSave(){
        $this->generateDependencies();
        return true;
    }

    public function restoreConfigs(){
        if (!$this->config_filename || !file_exists(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder']))) {
            throw new Exception('Missing original file');
        }
        $jsonMinifier = new JsonComponentsMinifier($this->id_ethernet);
        try {
            $json = json_decode(file_get_contents(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder'])));
            $this->config = $jsonMinifier->minifyJsonConfig($json);
            $this->refresh_dependencies = true;
            $this->save();
        } catch (FileParsingException $e) {
            $this->addError('config_file','The file "'.$this->config_filename.'" has the following error: ' . $e->getMessage());
            return false;
        }
    }

    protected function generateDependencies() {
        if (!$this->altered_config && !$this->refresh_dependencies){
            return;
        }

        /* @var Area $areaModel */
        $areaModel = Area::model()->findByPk($this->fk_area);
        if ($areaModel){
            $areaModel->extractJson();
        }

        if ($this->altered_config){
            Status::clearData($this->id_ethernet);
            Emergency::clearData($this->id_ethernet);
        }

        if ($this->clear_data) {
            $this->clearData();
        }
    }

    protected function clearData() {
        Status::clearData($this->id_ethernet);
        Emergency::clearData($this->id_ethernet);
        StatusHist::clearData($this->id_ethernet);
        EmergencyHist::clearData($this->id_ethernet);
        CommandQueue::clearData($this->id_ethernet);
        CommandSchedule::clearData($this->id_ethernet);
        ExecutedSchedule::clearData($this->id_ethernet);
    }

    public function isLocked() {
        $dateLocked = strtotime($this->locked_at);
        $dateNow = time();
        $mins = ($dateNow - $dateLocked) / 60;
        if ($this->lock) {
            if ($mins > 2) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function setLock($fk_user) {
        $this->lock = true;
        $this->locked_at = date('Y-m-d H:i:s');
        $this->locked_by = $fk_user;
        return $this->save();
    }

    public function unlock() {
        $this->lock = false;
        $this->locked_at = null;
        $this->locked_by = null;
        return $this->save();
    }

    public function uploadAndValidateConfigFile(){
        $uploadedFile = CUploadedFile::getInstance($this, 'config_file');
        if ($uploadedFile instanceof CUploadedFile) {
            $info = pathinfo($uploadedFile->getName());

            // Validate FileType
            if (!in_array(strtolower($info["extension"]), $this->allowedFileFormats)) {
                $this->addError('config_file','The file "'.$uploadedFile->getName().'" cannot be uploaded. Only files with these extensions are allowed: '.implode(', ',$this->allowedFileFormats).'.');
                return false;
            }

            // Save config
            $jsonMinifier = new JsonComponentsMinifier($this->id_ethernet);
            try {
                $json = json_decode(file_get_contents($uploadedFile->tempName));
                $this->config = $jsonMinifier->minifyJsonConfig($json);
                // Clear old file
                if (file_exists(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder']))) {
                    @unlink(File::getFullPath($this->config_filename, Yii::app()->params['configsFolder']));
                }
                // Upload new one
                $filePath = File::getFullPath($uploadedFile->getName(), Yii::app()->params['configsFolder']);
                if (!$uploadedFile->saveAs($filePath)) {
                    $this->addError('config_file', 'An error occurred saving file.');
                    return false;
                }
                $this->config_filename = $uploadedFile->getName();
            } catch (FileParsingException $e) {
                $this->addError('config_file','The file "'.$uploadedFile->getName().'" has the following error: ' . $e->getMessage());
                return false;
            }

            $this->altered_config = true;
        }

        return true;
    }

    public function getListForDropDown()
    {
        $rows = static::model()->findAll('inactive=0');
        foreach ($rows as $row) {
            $result[$row->id_ethernet] = '(' . $row->id_ethernet . ') ' . $row->name;
        }
        return $result;
    }

}
