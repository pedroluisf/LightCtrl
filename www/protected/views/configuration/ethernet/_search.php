<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id_ethernet'); ?>
		<?php echo $form->textField($model,'id_ethernet'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'desc'); ?>
		<?php echo $form->textField($model,'desc',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'host'); ?>
		<?php echo $form->textField($model,'host',array('size'=>32,'maxlength'=>32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fk_area'); ?>
		<?php echo $form->textField($model,'fk_area'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->