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
 * @property mixed dvc_id
 * @property mixed dev_type
 * @property mixed description
 * @property mixed groups
 * @property mixed scenes
 * @property mixed sensitivity
 * @property mixed timeout
 * @property mixed firmware_version
 * @property mixed custom_location
 * @property mixed custom_description
 * @property mixed target_lux
 * @property mixed energetic_class
 */

class DeviceTransfer extends TransferAbstract {

    protected $attributes = array(
        'draw_id',
        'ethernet_id',
        'lc_id',
        'dvc_id',
        'dev_type',
        'description',
        'groups',
        'scenes',
        'sensitivity',
        'timeout',
        'firmware_version',
        'custom_location',
        'custom_description',
        'target_lux',
        'energetic_class'
    );

    public function getId() {
        return $this->ethernet_id.':'.$this->lc_id.':'.$this->dvc_id;
    }
} 