<?php
/* @var $this NotificationController */
/* @var $data Notification */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_notification')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_notification), array('view', 'id'=>$data->id_notification)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fk_user')); ?>:</b>
	<?php echo CHtml::encode($data->fk_user); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('level')); ?>:</b>
	<?php echo CHtml::encode($data->level); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('message')); ?>:</b>
	<?php echo CHtml::encode($data->message); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('new')); ?>:</b>
	<?php echo CHtml::encode(($data->new ? 'Yes' : 'No')); ?>
	<br />


</div>