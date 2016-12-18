<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */

$this->breadcrumbs=array(
	'Ethernet Interfaces'=>array('ethernet'),
	'Manage',
);

$this->menu=$this->buildConfigurationMenu();

//Yii::app()->clientScript->registerScript('search', "
//$('.search-button').click(function(){
//	$('.search-form').toggle();
//	return false;
//});
//$('.search-form form').submit(function(){
//	$('#ethernet-grid').yiiGridView('update', {
//		data: $(this).serialize()
//	});
//	return false;
//});
//");
?>

<div id="statusMsg"></div>
<h1>Manage Ethernet Interfaces</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<!--<div class="search-form" style="display:none">-->
<?php /*$this->renderPartial('ethernet/_search',array(
	'model'=>$model,
));*/ ?>
<!--</div>--><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ethernet-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id_ethernet',
		'name',
		'desc',
		'host',
        array(
            'name'=>'area_search',
            'value'=>'$data->area->name',
            'filter'=>CHtml::listData(Area::model()->findAll(), 'id_area', 'name'),
        ),
        'config_filename',
        array(
            'type'=>'raw',
            'name'=>'inactive',
            'header'=>'Status',
            'value'=>'$data->getInactiveToGrid()',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'Active', true=>'Inactive')
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'{view}{update}{delete}{restore}',
            'deleteConfirmation'=>'Are you sure you want to delete this item? All data related to this item (Reports, Schedules) will also be deleted',
            'htmlOptions' => array('style' => 'width: 70px;'),
            'buttons' => array(
                'view' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/ethernetView",
                        array("id" => "$data->id_ethernet"))'
                ),
                'update' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/ethernetUpdate",
                        array("id" => "$data->id_ethernet"))'
                ),
                'delete' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/ethernetDelete",
                        array("id" => "$data->id_ethernet"))'
                ),
                'restore' => array(
                    'label' => 'Restore Json Config',
                    'visible'=>'$data->config_filename',
                    'url' => '$this->grid->controller->createUrl(
                        "configuration/ethernetRestore",
                        array("ajax"=>1, "id" => "$data->id_ethernet"))',
                    'imageUrl' => Yii::app()->request->baseUrl . '/themes/intsys/images/restore.png',
                    'options' => array( 'onclick' => '
                            event.preventDefault();
                            if (confirm("Are you sure you want to restore the default settings for this config?")) {
                                $.ajax({
                                    type:"get",
                                    url:$(this).attr("href"),
                                    success:function(data) {
                                        $("#statusMsg").html(data);
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        console.log(xhr.status + " " + thrownError);
                                    }
                                });
                            }'
                    )
                )
            )
		),
	),
)); ?>
