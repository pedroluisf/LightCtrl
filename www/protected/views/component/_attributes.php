<?php
/* @var TransferAbstract $component */
/* @var $form CActiveForm */
$attributes = $component->getAllAttributeNames();
$changeableAttributes = ComponentForm::getChangeableAttributes($component);
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'component-form',
    'action'=>'update',
    'htmlOptions' => array('enctype' => 'multipart/form-data')
)); ?>

<?php

    foreach ($attributes as $attribute) {
    $label = Dictionary::translateAttributeDescription($attribute);
    if (is_array($component[$attribute])) {
?>
        <div class="expandable_container">
            <div class="allow_expand">
                <?php echo CHtml::label($label, false); ?>
            </div>
            <div class="expandable_area">
<?php
        $i = 0;
        $class = 'text '. (isset($changeableAttributes[strtolower($attribute)]) ? 'editable' : '');
        foreach ($component[$attribute] as $key => $value) {
            echo '<div class="row indent">';
            $i++;
            $subLabel = $i;
            $translatedValue = ( $value==255 ? 'Disabled' : $value);
            echo CHtml::label($subLabel, $attribute.'_'.$key);
            echo CHtml::textField($attribute.'['.$key.']', $translatedValue, array(
                'readonly' => true,
                'class' => $class
            ));
            echo '</div>';
        }
?>
            </div>
        </div>

<?php
    } else {
        if ($component[$attribute] !== null){
            echo '<div class="row">';
            echo CHtml::label($label, $attribute);
            echo CHtml::textField($attribute, $component[$attribute], array(
                'readonly' => true,
                'class' => 'text '. (isset($changeableAttributes[strtolower($attribute)]) ? 'editable' : '')
            ));
            echo '</div>';
        }
    }
}
?>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Save', array('id' => 'component_submit', 'title' => 'Save the changes to this component')); ?>
    </div>

<?php $this->endWidget(); ?>

