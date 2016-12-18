<?php

/**
 * This is the model class for table "vw_emergency_current".
 *
 * The followings are the available columns in table 'tbl_status_hist' not present in 'tbl_status':
 * @property integer $id_emergency_hist
 */
class EmergencyHist extends Emergency
{
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
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tbl_emergency_hist';
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

}
