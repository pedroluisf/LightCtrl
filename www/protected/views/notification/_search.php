<?php
/* @var $this NotificationController */
/* @var $model Notification */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id_notification'); ?>
		<?php echo $form->textField($model,'id_notification',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fk_user'); ?>
		<?php echo $form->textField($model,'fk_user',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'level'); ?>
		<?php echo $form->textField($model,'level',array('size'=>7,'maxlength'=>7)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'message'); ?>
		<?php echo $form->textField($model,'message',array('size'=>60,'maxlength'=>1024)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'new'); ?>
		<?php echo $form->textField($model,'new'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->