<?php
/* @var $this ConfigurationController */
/* @var $data Ethernet */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_ethernet')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_ethernet), array('ethernetview', 'id'=>$data->id_ethernet)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('desc')); ?>:</b>
	<?php echo CHtml::encode($data->desc); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('host')); ?>:</b>
	<?php echo CHtml::encode($data->host); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fk_area')); ?>:</b>
	<?php echo CHtml::encode($data->area->name); ?>
	<br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
    <?php echo CHtml::encode($data->translated_inactive); ?>
    <br />

</div>