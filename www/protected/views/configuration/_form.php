<?php
/* @var $this ConfigurationController */
/* @var $model Configuration */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'configuration-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php //echo $form->labelEx($model,'label'); ?>
        <?php echo $form->hiddenField($model,'type'); ?>
        <?php //echo $form->error($model,'label'); ?>
    </div>

    <?php switch ($model->attributes['type']):
        case 'textField': ?>
            <div class="row">
                <?php echo $form->labelEx($model,$model->attributes['label']); ?>
                <?php echo $form->textField($model,'value', array('size'=>60,'maxlength'=>128)); ?>
                <?php echo $form->error($model,'value'); ?>
            </div>
        <?php break; ?>

        <?php case 'fileField': ?>
            <div class="row">
                <?php echo $form->labelEx($model,$model->attributes['label']); ?>
                <?php echo $form->fileField($model,'value'); ?>
                <?php echo $form->textField($model,'value', array('id'=>'display','size'=>50,'disabled'=>true)); ?>
                <?php echo $form->error($model, 'value'); ?>
            </div>
        <?php break; ?>

    <?php endswitch; ?>

	<div class="row buttons" style="display: inline;">
        <?php
        if (($model->attributes['type'])== 'fileField') {
            echo CHtml::submitButton(
                'Clear',
                array(
                    'onClick'=>'$("<input />").attr("type", "hidden").attr("name", "Configuration[remove]").attr("value", 1).appendTo("#configuration-form"); return true;',
                    'style'=>'margin-right:10px !important'
                )
            );
        }
		echo CHtml::submitButton('Save');
        ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->