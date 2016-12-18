<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 22-03-2014
 * Time: 0:11
 */

/**
 * @property mixed ethernet_id
 * @property mixed draw_id
 * @property mixed description
 * @property mixed ethernet_status
 */

class EthernetTransfer extends TransferAbstract {

    protected $attributes = array(
        'draw_id',
        'ethernet_id',
        'description',
        'ethernet_status',
    );

    public function getId() {
        return $this->ethernet_id.'::';
    }
}