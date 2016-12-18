<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'userpass-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php echo $form->hiddenField($model->user, 'id_user'); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'current password'); ?>
        <?php echo $form->passwordField($model,'currentPassword',array('size'=>60,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'currentPassword'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'new password'); ?>
		<?php echo $form->passwordField($model,'newPassword',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'newPassword'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'repeat password'); ?>
        <?php echo $form->passwordField($model,'newPasswordRepeat',array('size'=>60,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'newPasswordRepeat'); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->