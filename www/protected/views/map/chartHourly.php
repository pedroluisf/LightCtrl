<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 01-05-2015
 * Time: 01:13
 */

$this->breadcrumbs=array(
    'Map'=>array('/map'),
    'Watts / Hour'
);
?>

<h1><?php echo $title; ?></h1>
<?php
$this->renderPartial(
    'partial/_filterModal',
    array(
        'showTreeview' => true,
        'dateFrom' => $date,
        'dateTo' => null,
        'timeFrom' => $timeFrom,
        'timeTo' => $timeTo,
        'filters' => $filters
    )
);
?>
<div id="actions">
    <input type="button" id="show_area_chart" title="Area Chart" class="chart_button" onclick="changeFormat('line')"/>
    <input type="button" id="show_bar_chart" title="Bar Chart" class="chart_button" onclick="changeFormat('column')"/>
    <input type="button" id="show_table" title="Table format" class="chart_button" onclick="changeFormat('html')"/>
    <input type="button" id="filter_open" title="Set Filters" class="chart_button"/>
</div>
<div id="chart_div"></div>

<script type="text/javascript">

    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    var chartData = <?php echo json_encode($dataProvider); ?>;

    chart = AmCharts.makeChart("chart_div", {
        "type": "serial"
        , "dataProvider": chartData
        , "categoryField": "hour"
        , "numberFormatter": {
            "precision": 2,
            "decimalSeparator": ",",
            "thousandsSeparator": ""
        }
        , "depth3D": 20
        , "angle": 30
        , "categoryAxis": {
            "title": "Time (Hour)",
            "minorGridEnabled": true,
            "gridAlpha": 0.1,
            "startOnAxis": true
        }
        , "valueAxes": [{
            "gridAlpha": 0.1,
            "title": "Watts"
        }]
        , "graphs": [
            {
                "type": "<?php echo $format; ?>",
                "id": "chart_1",
                "connect": false,
                "valueField": "value",
            <?php if ($format == 'line'): ?>
                "lineThickness": 2,
                "lineColor": '#308DCD',
                "bullet": "round",
                "bulletSize": 8,
                "bulletBorderColor": "#FFFFFF",
                "bulletBorderThickness": 2,
                "bulletBorderAlpha": 1
            <?php else: ?>
                "lineAlpha": 0,
                "fillAlphas": 0.6,
                "fillColors": '#308DCD'
            <?php endif; ?>
            }
        ]

        <?php // Add Common configs on constructor
        $this->renderPartial(
            'partial/_chartCommon',
            array(
                'chartId' => 'chart_1', // Needed for mini-chart on scrollbar
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
