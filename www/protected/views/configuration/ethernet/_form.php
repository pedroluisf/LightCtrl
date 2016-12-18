<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ethernet-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if (!$model->isNewRecord) :?>
        <div class="row">
            <h2><span style="color: <?php echo ($model->inactive ? 'red' : 'green'); ?>"><?php echo $model->translated_inactive; ?></span></h2>
        </div>
    <?php endif; ?>

    <div class="row">
		<?php echo $form->labelEx($model,'id_ethernet'); ?>
		<?php echo $form->textField($model,'id_ethernet', array('disabled' =>!$model->isNewRecord)); ?>
		<?php echo $form->error($model,'id_ethernet'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'fk_area'); ?>
        <?php $list = CHtml::listData(Area::model()->findAll(array('order' => 'id_area')), 'id_area', 'name'); ?>
        <?php echo $form->dropDownList($model,'fk_area', $list, array('style'=>'min-width: 150px;','disabled' =>!$model->isNewRecord)); ?>
        <?php echo $form->error($model,'fk_area'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'desc'); ?>
		<?php echo $form->textField($model,'desc',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'desc'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'host'); ?>
		<?php echo $form->textField($model,'host',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'host'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'config_file'); ?>
        <?php echo ($model->isNewRecord) ? '': $form->textField($model,'config_filename',array('size'=>50,'disabled'=>true)); ?>
        <?php echo $form->fileField($model,'config_file', array('onchange' => '
            if ($(this).val()) {
                $("#Ethernet_clear_data").prop("disabled", false);
            } else {
                $("#Ethernet_clear_data").prop("disabled", true);
                $("#Ethernet_clear_data").attr("checked", false);
            }
        ')); ?>
        <?php if (!$model->isNewRecord): ?>
            <div>
                <?php echo $form->labelEx($model,'clear_data'); ?>
                <?php echo $form->checkBox($model,'clear_data', array('disabled'=>'disabled')); ?>
                <span class="hint">Selecting this option will delete data related to old devices of this Caneth in Reports and Scheduling</span>
            </div>
        <?php endif; ?>
		<?php echo $form->error($model,'config_file'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
        <?php if (!$model->isNewRecord) :?>
            <?php echo CHtml::submitButton($model->inactive ? 'Save & Activate' : 'Save & Inactivate', array('name'=>'activateButton')); ?>
        <?php endif; ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->