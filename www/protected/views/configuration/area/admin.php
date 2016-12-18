<?php
/* @var $this ConfigurationController */
/* @var $model Area */

$this->breadcrumbs=array(
	'Areas'=>array('area'),
	'Manage',
);

$this->menu=$this->buildConfigurationMenu();

//Yii::app()->clientScript->registerScript('search', "
//$('.search-button').click(function(){
//	$('.search-form').toggle();
//	return false;
//});
//$('.search-form form').submit(function(){
//	$('#area-grid').yiiGridView('areaupdate', {
//		data: $(this).serialize()
//	});
//	return false;
//});
//");
?>

<div id="statusMsg"></div>
<h1>Manage Building Areas</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<!--<div class="search-form" style="display:none">-->
<?php /*$this->renderPartial('area/_search',array(
	'model'=>$model,
)); */ ?>
<!--</div>--><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'area-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id_area',
		'name',
		'desc',
		'plan',
		array(
			'class'=>'CButtonColumn',
            'deleteConfirmation'=>'Are you sure you want to delete this item? All data related to this item (Ethernets, Reports, Schedules) will also be deleted',
            'buttons' => array(
                'view' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/areaView",
                        array("id" => "$data->id_area"))'
                ),
                'update' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/areaUpdate",
                        array("id" => "$data->id_area"))'
                ),
                'delete' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/areaDelete",
                        array("id" => "$data->id_area"))'
                )
            ),
            'afterDelete'=>'function(link,success,data){
                if(success) $("#statusMsg").html(data);}',
		),
	),
)); ?>
