<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 01-05-2015
 * Time: 01:13
 */

$this->breadcrumbs=array(
    'Map'=>array('/map'),
    'Consumption (Watts) / Floor'
);
?>

<h1><?php echo $title; ?></h1>
<?php
$this->renderPartial(
    'partial/_filterModal',
    array(
        'showTreeview' => false,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'filters' => $filters
    )
);
?>
<div id="actions">
    <input type="button" id="show_pie_chart" title="Chart Format" class="chart_button" onclick="changeFormat('chart')"/>
    <input type="button" id="show_table" title="Table format" class="chart_button" onclick="changeFormat('html')"/>
    <input type="button" id="filter_open" title="Set Filters" class="chart_button"/>
</div>
<div id="chart_div"></div>

<script type="text/javascript">

    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    var chartData = <?php echo json_encode($dataProvider); ?>;

    chart = AmCharts.makeChart("chart_div", {
        "type": "pie"
        , "dataProvider": chartData
        , "titleField": 'name'
        , "valueField": 'consumption'
        , "outlineColor": '#FFFFFF'
        , "outlineAlpha": 0.8
        , "outlineThickness": 2
        , "depth3D": 30
        , "angle": 30
        , "colors": ['#308DCD']
        , "startDuration": 0
       <?php
        // Add Common configs on constructor
        $this->renderPartial(
            'partial/_chartCommon',
            array(
                'export' => $export,
                'legend' => true
            )
        );
        ?>
    });

    var filterUrl = '<?php echo Yii::app()->controller->action->id; ?>Data';

    filterCallback = function(response) {
        chart.dataProvider = JSON.parse(response);
        chart.validateData();
    }

</script>
