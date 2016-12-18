<?php
/* @var $this ConfigurationController */
/* @var $data Area */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_area')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_area), array('areaview', 'id'=>$data->id_area)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('desc')); ?>:</b>
	<?php echo CHtml::encode($data->desc); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('plan')); ?>:</b>
	<?php echo CHtml::encode($data->plan); ?>
	<br />


</div>