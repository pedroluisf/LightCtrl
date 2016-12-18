<?php
/* @var $this ComponentController */
/* @var $model ComponentConfigurationForm */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
    'Import Components',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Import Configuration</h1>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'component_configuration-form',
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
        <?php echo $form->labelEx($model,'ethernet_id'); ?>
        <?php echo $form->dropDownList($model,'ethernet_id', $ethernetDevices, array('prompt'=>'select ethernet interface')); ?>
        <?php echo $form->error($model,'ethernet_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'file'); ?>
        <?php echo $form->fileField($model,'file'); ?>
        <?php echo $form->error($model, 'file'); ?>
        <div class="mtm mbm">
            <span class="hint">This should be a .csv file with the following headers "<?php
                $allowedHeaders = CsvComponentConfigurationUpdater::getAllowedHeaders();
                echo implode(',', $allowedHeaders[0]);
                echo '" or "';
                echo implode(',',$allowedHeaders[1]);
                ?>"</span>
        </div>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Upload'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<?php if (!empty($errors)): ?>
<div class="file_updater">
    <div class="expandable_container">
        <div class="allow_expand">
            Some errors occurred. Expand for details:
        </div>
        <div class="expandable_area">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
