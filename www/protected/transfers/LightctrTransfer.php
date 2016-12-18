<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 22-03-2014
 * Time: 0:11
 */

/**
 * @property mixed draw_id
 * @property mixed ethernet_id
 * @property mixed lc_id
 * @property mixed description
 * @property mixed type
 * @property mixed type_description
 * @property mixed firmware_version
 * @property mixed custom_location
 * @property mixed custom_description
 */

class LightctrTransfer extends TransferAbstract {

    protected $attributes = array(
        'draw_id',
        'ethernet_id',
        'lc_id',
        'description',
        'type',
        'type_description',
        'firmware_version',
        'custom_location',
        'custom_description',
    );

    public function getId() {
        return $this->ethernet_id.':'.$this->lc_id.':';
    }
}