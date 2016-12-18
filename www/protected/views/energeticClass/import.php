<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClassConfigurationForm */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
    'Import Energetic Classes',
);

$this->menu=$this->buildConfigurationMenu($model);
?>

<h1 xmlns="http://www.w3.org/1999/html">Import Configuration</h1>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'energetic_class_configuration-form',
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
        <?php echo $form->labelEx($model,'file'); ?>
        <?php echo $form->fileField($model,'file'); ?>
        <?php echo $form->error($model, 'file'); ?>
        <div class="mtm mbm">
            <span class="hint">This should be a .csv file with the following headers "<?php
                echo implode(',',
                    CsvEnergeticClassConfigurationUpdater::getAllowedHeaders()
                );
                ?>"</span>
        </div>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'full_import'); ?>
        <?php echo $form->checkBox($model,'full_import'); ?>
        <span class="hint">Setting this option will delete all previous data existing for this table</span>
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
