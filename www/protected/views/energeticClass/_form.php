<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'energetic-class-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'class_key'); ?>
		<?php echo $form->textField($model,'class_key',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'class_key'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textField($model,'description',array('size'=>64,'maxlength'=>64)); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model,'consumption_watts'); ?>
		<?php echo $form->textField($model,'consumption_watts',array('size'=>5,'maxlength'=>5)); ?>
		<?php echo $form->error($model,'consumption_watts'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->