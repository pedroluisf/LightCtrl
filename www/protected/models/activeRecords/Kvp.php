<?php

/**
 * This is the model class for table "tbl_kvp".
 * KVP is an acronym for Key Value Pair.
 * A key-value pair (KVP) is a set of two linked data items:
 * 1- a key, which is a unique identifier for some item of data,
 * 2- the value, which is either the data that is identified or a pointer to the location of that data
 *
 * The followings are the available columns in table 'tbl_kvp':
 * @property string $key
 * @property string $value
 */
class Kvp extends CActiveRecord
{
    // This key represents the last time the tbl_consolidated was updated
    const LAST_CONSOLIDATED_STATUS_DATETIME = 'lastConsolidatedStatusDateTime';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_kvp';
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

		$criteria->compare('key',$this->key);
        $criteria->compare('value', $this->value, true );

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
     * Returns the value of the provided key if exists
     *
     * @param $key
     * @return string - the stored value
     * @throws Exception
     */
    public static function get($key, $defaultValue = null)
    {
        /** @var Kvp $kvp */
        $kvp = self::model()->findByPk($key);
        if (!$kvp) {
            return $defaultValue;
        }
        return $kvp->value;
    }

    /**
     * Saves the value of the provided key
     *
     * @param $key
     * @param $value
     * @param $create - Create key if it does not exist
     * @return boolean - true if successful, false otherwise
     * @throws Exception
     */
    public static function set($key, $value)
    {
        /** @var Kvp $kvp */
        $kvp = self::model()->findByPk($key);
        if (!$kvp) {
            $kvp = new Kvp('create');
            $kvp->key = $key;
        }

        $kvp->value = $value;
        return $kvp->save();

    }

}
