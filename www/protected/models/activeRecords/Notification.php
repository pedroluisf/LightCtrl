<?php

/**
 * This is the model class for table "tbl_notification".
 *
 * The followings are the available columns in table 'tbl_notification':
 * @property string $id_notification
 * @property string $fk_user
 * @property string $level
 * @property string $message
 * @property integer $new
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Notification extends CActiveRecord
{
    const NOTIFICATION_LEVEL_INFO = 'Info';
    const NOTIFICATION_LEVEL_WARNING = 'Warning';
    const NOTIFICATION_LEVEL_ERROR = 'Error';

    protected static $allowedLevels = array(
        self::NOTIFICATION_LEVEL_INFO,
        self::NOTIFICATION_LEVEL_WARNING,
        self::NOTIFICATION_LEVEL_ERROR
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_notification';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('level, message', 'required'),
			array('id_notification, fk_user', 'length', 'max'=>10),
			array('level', 'length', 'max'=>7),
			array('message', 'length', 'max'=>1024),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_notification, fk_user, level, message, new', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'fk_user'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_notification' => 'Id',
			'fk_user' => 'User',
			'level' => 'Level',
			'message' => 'Message',
			'new' => 'New',
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

		$criteria->compare('id_notification',$this->id_notification,true);
		$criteria->compare('fk_user',$this->fk_user,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('new',$this->new);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Creates a new notification for a user
     *
     * @param $fk_user
     * @param $level
     * @param $msg
     * @throws Exception
     */
    public static function sendNotification($fk_user, $level, $message) {
        if (!in_array($level, self::$allowedLevels)) {
            throw new Exception("Unknown Level supplied for Notification '$level'");
        }

        $notification = new Notification('create');
        $notification->fk_user = $fk_user;
        $notification->level = $level;
        $notification->message = $message;
        if (!$notification->save()) {
            $msg = 'Error saving new Notification'.PHP_EOL;
            $msg .= print_r($notification, true).PHP_EOL;
            $msg .= print_r($notification->getErrors()).PHP_EOL;
            Yii::log($msg, CLogger::LEVEL_ERROR);
        }
    }

    public static function getUnreadForUser($fromDate=null) {
        $condition = ' new=1 ';
        $params = array();

        if ($fromDate){
            $condition .= ' AND created_at >= :created_at ';
            $params[':created_at'] = $fromDate;
        }
        if (!Yii::app()->user->isAdmin()) {
            $condition .= ' AND fk_user >= :fk_user ';
            $params[':fk_user'] = Yii::app()->user->getId();
        }

        return Notification::model()->findAll(array(
            'condition'=>$condition,
            'params'=>$params,
        ));
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Notification the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
