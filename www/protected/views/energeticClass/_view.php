<?php
/* @var $this EnergeticClassController */
/* @var $data EnergeticClass */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_energetic_class')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_energetic_class), array('view', 'id'=>$data->id_energetic_class)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('class_key')); ?>:</b>
	<?php echo CHtml::encode($data->class_key); ?>
	<br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
    <?php echo CHtml::encode($data->description); ?>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('consumption_watts')); ?>:</b>
	<?php echo CHtml::encode($data->consumption_watts); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />


</div>