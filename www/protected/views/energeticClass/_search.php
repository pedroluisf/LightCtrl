<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id_energetic_class'); ?>
		<?php echo $form->textField($model,'id_energetic_class',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'consumption_watts'); ?>
		<?php echo $form->textField($model,'consumption_watts',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created_at'); ?>
		<?php echo $form->textField($model,'created_at'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->