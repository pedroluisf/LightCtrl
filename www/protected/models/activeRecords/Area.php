<?php

/**
 * This is the model class for table "tbl_area".
 *
 * The followings are the available columns in table 'tbl_area':
 * @property integer $id_area
 * @property string $name
 * @property string $desc
 * @property string $plan
 * @property string $tree_config
 * @property string $props_config
 *
 * The followings are the available model relations:
 * @property TblEthernet[] $area
 */
class Area extends CActiveRecord
{

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('name, desc, plan', 'required', 'on'=>'create'),
            array('name, desc', 'required', 'on'=>'update'),
			array('name', 'length', 'max'=>20),
            array('name', 'unique','className'=>'Area','attributeName'=>'name','message'=>"Name already exists"),
            array('tree_config', 'unique','className'=>'Area','attributeName'=>'tree_config','message'=>"File name already exists"),
            array('props_config', 'unique','className'=>'Area','attributeName'=>'props_config','message'=>"File name already exists"),
			array('desc', 'length', 'max'=>128),
			array('plan', 'file', 'types'=>'dwf', 'allowEmpty'=>false, 'on'=>'create'),
			array('plan', 'file', 'types'=>'dwf', 'allowEmpty'=>true, 'on'=>'update'),
            array('name, desc','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			array('name, desc, plan', 'safe', 'on'=>'search'),
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
			'area' => array(self::HAS_MANY, 'Ethernet', 'fk_area'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_area' => 'Id Area',
			'name' => 'Name',
			'desc' => 'Description',
			'plan' => 'Plan',
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

		$criteria->compare('id_area',$this->id_area);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('plan',$this->plan,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Area the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function beforeDelete(){
        $relatedEthernet = Ethernet::model()->findByAttributes(array('fk_area' => $this->id_area));
        if (isset($relatedEthernet)) {
            Yii::app()->user->setFlash('error', "Area $this->id_area cannot be deleted. It has Ethernet devices associated with it.");
            return false;
        }
        return true;
    }

    public function afterDelete(){
        $this->deleteDeprecatedFile();
        return true;
    }

    public function extractJson() {
        // Starts Parser
        $parser = new JsonAreaStores($this->id_area);

        // Saves config files
        $this->tree_config = json_encode($parser->getTree());
        $this->props_config = json_encode($parser->getProperties());
        $this->update();
    }

    protected function deleteDeprecatedFile() {
        if ($this->plan) {
            $deprecatedFile = File::getFullPath($this->plan);
            @unlink($deprecatedFile);
        }
    }

    public function save($runValidation=true,$attributes=null){
        if ($this->uploadPlanFile()) {
            return parent::save($runValidation, $attributes);
        } else {
            return false;
        }
    }

    protected function uploadPlanFile(){
        $uploadedFile = CUploadedFile::getInstance($this, 'plan');
        if ($uploadedFile instanceof CUploadedFile) {
            $filePath = File::getFullPath($uploadedFile->getName(), Yii::app()->params['plansFolder']);
            if (file_exists($filePath)) {
                $this->addError('plan', 'A file with that name already exists.');
                return false;
            }
            if (!$uploadedFile->saveAs($filePath)) {
                $this->addError('plan', 'An error occurred saving file.');
                return false;
            }
            $this->deleteDeprecatedFile();
            $this->plan = Yii::app()->params['plansFolder'] . $uploadedFile->getName();
        }
        return true;
    }

    public function getTreeForScheduling() {

        $treeConfig = json_decode($this->tree_config);

        foreach ($treeConfig as $ethKey => $ethernetConfig) {
            // Filter Light Controllers so only Virtual Light Controller and Emergency Lamps from other controllers shows in tree
            if (isset($treeConfig[$ethKey]->children)) {
                foreach ($treeConfig[$ethKey]->children as $lcKey => $lc) {
                    $lcAttributes = get_object_vars($lc);
                    if ($lcAttributes['data-type'] == 'ctr' && $lcAttributes['data-id'] != Dictionary::VIRTUAL_LIGHT_CONTROLLER) {
                        // Filter Devices so only Emergency Lamps show is tree
                        foreach ($treeConfig[$ethKey]->children[$lcKey]->children as $deviceKey => $device) {
                            $deviceAttributes = get_object_vars($device);
                            if ($deviceAttributes['data-type'] != '1') {
                                unset($treeConfig[$ethKey]->children[$lcKey]->children[$deviceKey]);
                            }
                        }
                        if (count($treeConfig[$ethKey]->children[$lcKey]->children) == 0) {
                            unset($treeConfig[$ethKey]->children[$lcKey]);
                        }
                    } else {
                        // Filter Devices so only Devices with data-drawing_id > 0 show is tree
                        foreach ($treeConfig[$ethKey]->children[$lcKey]->children as $deviceKey => $device) {
                            $deviceAttributes = get_object_vars($device);
                            if ($deviceAttributes['data-type'] == '130' && $deviceAttributes['data-drawing_id'] == Dictionary::INACTIVE_VIRTUAL_CLEAR_CONTACT_INPUT) {
                                unset($treeConfig[$ethKey]->children[$lcKey]->children[$deviceKey]);
                            }
                        }
                        $treeConfig[$ethKey]->children[$lcKey]->children = array_values($treeConfig[$ethKey]->children[$lcKey]->children);
                    }
                    if (isset($treeConfig[$ethKey]->children[$lcKey]->children)) {
                        $treeConfig[$ethKey]->children[$lcKey]->children = array_values($treeConfig[$ethKey]->children[$lcKey]->children);
                    }
                }
            }
            $treeConfig[$ethKey]->children = array_values($treeConfig[$ethKey]->children);
        }
        return $treeConfig;
    }
}
