<?php
/* @var $this ComponentController */
/* @var $model ComponentForm */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
    'Components Configuration',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Component Configuration</h1>

<div id="action_bar" class="columns">
    <?php
    $list = CHtml::listData(Area::model()->findAll(),'id_area', 'name');
    echo CHtml::dropDownList('area_select', Area::model()->id_area, $list,
        array(
            'empty' => '(Select an Area)',
        )
    );
    ?>
    <div id="properties"></div>
    <div id= "treeView_container" class="mtm">
        <?php
            // Dummy Tree for including libraries
            $this->widget('CTreeView');
        ?>
    </div>
</div>

<div id="attributes_container">

</div>
<div id="component_edit_form">
    <?php
    // Dummy Slider for including libraries
    $this->widget('zii.widgets.jui.CJuiSlider');
    ?>
</div>

<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
</script>