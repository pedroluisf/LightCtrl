<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
	'Energetic Classes'=>array('admin'),
	'Manage',
);

$this->menu=$this->buildConfigurationMenu($model);
?>

<h1>Energetic Classes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'energetic-class-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id_energetic_class',
		'description',
		'consumption_watts',
		'created_at',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
