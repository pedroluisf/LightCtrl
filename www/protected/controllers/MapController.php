<?php
require_once( dirname(__FILE__) . '/../../mpdf/mpdf.php');

class MapController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column1';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow registered user to perform 'index', and maps actions
                'actions'=>array(
                    'index',
                    'dailyHours',
                    'dailyHoursData',
                    'dailyWatts',
                    'dailyWattsData',
                    'wattsFloor',
                    'wattsFloorData',
                    'hourlyWatts',
                    'hourlyWattsData'
                ),
                'users'=>array('@'),
                //'expression'=>'!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser())',
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionDailyHours()
    {
        $this->prepareDailyMap('hours');
    }

    public function actionDailyHoursData()
    {
        echo json_encode($this->getDailyChartData('hours'));
        Yii::app()->end();
    }

    public function actionDailyWatts()
    {
        $this->prepareDailyMap('watts');
    }

    public function actionDailyWattsData()
    {
        echo json_encode($this->getDailyChartData('watts'));
        Yii::app()->end();
    }

    public function actionWattsFloor()
    {
        $this->initializeDates($dateFrom, $dateTo);

        $format = $_GET['format'];
        if (!in_array($format, array('chart', 'html', 'pdf', 'csv'))) {
            $format = 'chart';
        }

        $title = 'Consumption (Watts) / Floor';

        $model = new Consumption('search');

        $this->initializeCommonLibraries();
        $filtersForm = $this->initializeFilters();

        // Get Data
        $rows = $model->getWattsConsumptionByFloor($dateFrom, $dateTo, $filtersForm);
        if ($format == 'html') {
            $rows = new CArrayDataProvider($rows, array(
                'pagination'=>array(
                    'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
                ),
            ));
        }

        switch ($format) {
            case 'html':
                $view = 'tableWattsFloor';
                break;

            case 'chart':
                $this->initializeChartJs('pie');
                $view = 'chartWattsFloor';
                break;

            default:
                // filters description for export
                $filters = array(
                    'Date From' => $dateFrom->format('Y-m-d'),
                    'Date To' => $dateTo->format('Y-m-d'),
                );
                $filters = array_merge($filters, $filtersForm->filters);
                $filtersDisplay = array();
                foreach ($filters as $label => $value) {
                    $label = $model->getAttributeLabel($label);
                    $filtersDisplay[$label] = $value;
                }

                // Fields to show on export
                $fields = $this->initializeFieldsForExport($model, $rows);

                // Export
                $formatFunction = 'export'.ucfirst($format);
                $this->$formatFunction(
                    $title,
                    $rows,
                    $fields,
                    $filtersDisplay
                );
                break;
        }

        $params = array(
            'title' => $title,
            'model' => $model,
            'dataProvider' => $rows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filters' => $filtersForm,
            'export' => BrowserDetector::usingCompatibleInternetExplorerForChartExport()
        );
        $this->render($view, $params);
    }

    public function actionWattsFloorData()
    {
        echo json_encode($this->getFloorChartData());
        Yii::app()->end();
    }

    public function actionHourlyWatts()
    {
        $this->initializeDateTime($date, $timeFrom, $timeTo);

        $format = Yii::app()->request->getParam('format');
        if (!in_array($format, array('line', 'column', 'html', 'pdf', 'csv'))) {
            $format = 'line';
        }

        $title = 'Consumption (Watts) / Hour';

        $model = new StatusHist('search');

        $this->initializeCommonLibraries();
        $filtersForm = $this->initializeFilters();

        // Get Data
        if (in_array($format, array('line', 'column'))) {
            $rows = $model->getHourlyConsumption($date, $timeFrom, $timeTo, $filtersForm);
        } else {
            $rows = $model->getHourlyConsumptionByDevice($date, $timeFrom, $timeTo, $filtersForm);
        }
        if ($format == 'html') {
            $rows = new CArrayDataProvider($rows, array(
                'pagination'=>array(
                    'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
                ),
            ));
        }

        switch ($format) {
            case 'html':
                $view = 'tableHourly';
                break;

            case 'column':
            case 'line':
                $this->initializeChartJs('serial');
                $view = 'chartHourly';
                break;

            default:
                // filters description for export
                $filters = array(
                    'Date' => $date->format('Y-m-d'),
                    'Time From' => $timeFrom,
                    'Time To' => $timeTo,
                );
                $filters = array_merge($filters, $filtersForm->filters);
                $filtersDisplay = array();
                foreach ($filters as $label => $value) {
                    $label = $model->getAttributeLabel($label);
                    $filtersDisplay[$label] = $value;
                }

                // Fields to show on export
                $fields = $this->initializeFieldsForExport($model, $rows, array('fk_ethernet'));

                // Export
                $formatFunction = 'export'.ucfirst($format);
                $this->$formatFunction(
                    $title,
                    $rows,
                    $fields,
                    $filtersDisplay
                );
                break;
        }

        $params = array(
            'title' => $title,
            'model' => $model,
            'dataProvider' => $rows,
            'format' => $format,
            'date' => $date,
            'timeFrom' => $timeFrom,
            'timeTo' => $timeTo,
            'filters' => $filtersForm,
            'export' => BrowserDetector::usingCompatibleInternetExplorerForChartExport()
        );
        $this->render($view, $params);
    }

    public function actionHourlyWattsData()
    {
        $this->initializeDateTime($date, $timeFrom, $timeTo);

        $model = new StatusHist('search');

        // Get Data
        $filtersForm = $this->initializeFilters();
        $data = $model->getHourlyConsumption($date, $timeFrom, $timeTo, $filtersForm);

        echo json_encode($data);
        Yii::app()->end();
    }

    /**
     * This aggregates the needed info for generating daily maps (hours and watts) both in table or chart format
     * @param $type
     * @throws CException
     */
    protected function prepareDailyMap($type)
    {
        $this->initializeDates($dateFrom, $dateTo);

        $format = $_GET['format'];
        if (!in_array($format, array('column', 'line', 'html', 'pdf', 'csv'))) {
            $format = ($type == 'hours' ? 'column' : 'line');
        }

        $title = 'Daily Consumption ('.ucfirst($type).')';
        $model = new Consumption('search');

        $this->initializeCommonLibraries();
        $filtersForm = $this->initializeFilters();

        if (in_array($format, array('column', 'line'))) {
            $rows = $model->getDailyConsumptionByDate($dateFrom, $dateTo, $filtersForm, $type);
        } else {
            $rows = $model->getDailyConsumption($dateFrom, $dateTo, $filtersForm);
            if ($format == 'html') {
                $rows = new CArrayDataProvider($rows, array(
                    'pagination'=>array(
                        'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
                    ),
                ));
            }
        }

        switch ($format) {
            case 'html':
                $view = 'tableDaily';
                break;

            case 'column':
            case 'line':
                $this->initializeChartJs('serial');
                $view = 'chartDaily';
                break;

            default:
                // filters description for export
                $filters = array(
                    'Date From' => $dateFrom->format('Y-m-d'),
                    'Date To' => $dateTo->format('Y-m-d'),
                );
                $filters = array_merge($filters, $filtersForm->filters);
                $filtersDisplay = array();
                foreach ($filters as $label => $value) {
                    $label = $model->getAttributeLabel($label);
                    $filtersDisplay[$label] = $value;
                }

                // Fields to show on export
                $exceptions = array(
                    'fk_ethernet',
                    'fk_description',
                    'consumption_minutes_',
                );
                if ($type == 'hours') {
                    $exceptions[] = 'consumption_watts_';
                } else {
                    $exceptions[] = 'consumption_hours_';
                }
                $fields = $this->initializeFieldsForExport($model, $rows, $exceptions);

                // Export
                $formatFunction = 'export'.ucfirst($format);
                $this->$formatFunction(
                    $title,
                    $rows,
                    $fields,
                    $filtersDisplay
                );
                break;
        }

        $params = array(
            'title' => $title,
            'type' => $type,
            'model' => $model,
            'dataProvider' => $rows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filters' => $filtersForm,
            'chartType' => $format,
            'export' => BrowserDetector::usingCompatibleInternetExplorerForChartExport()
        );
        $this->render($view, $params);
    }

    protected function getDailyChartData($type) {
        // Validate the requested data type
        if (!in_array($type, array('hours', 'watts'))) {
            return array();
        }

        $this->initializeDates($dateFrom, $dateTo);
        $model = new Consumption('search');
        $filtersForm = $this->initializeFilters();
        return $model->getDailyConsumptionByDate($dateFrom, $dateTo, $filtersForm, $type);
    }

    protected function getFloorChartData()
    {
        $this->initializeDates($dateFrom, $dateTo);
        $model = new Consumption('search');
        $filtersForm = $this->initializeFilters();
        return $model->getWattsConsumptionByFloor($dateFrom, $dateTo, $filtersForm);
    }

    protected function initializeCommonLibraries() {
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/filter.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/report.js', CClientScript::POS_END);

        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }
    }

    protected function initializeChartJs($type)
    {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/amcharts.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/'.$type.'.js', CClientScript::POS_HEAD);
        /*
            scripts for exporting chart as an image
            Exporting to image works on all modern browsers except IE9 (IE10 works fine)
            Note, the exporting will work only if you view the file from web server
        */
        if (BrowserDetector::usingCompatibleInternetExplorerForChartExport()) {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/plugins/export/export.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/plugins/export/export.css');
        }
    }

    protected function initializeDates(&$dateFrom, &$dateTo) {
        $dateFromParam = Yii::app()->request->getParam('dateFrom');
        $dateToParam = Yii::app()->request->getParam('dateTo');

        if ($dateFromParam == null && $dateToParam == null) {
            $dateTo = new DateTime();
            $dateFrom = clone $dateTo;
            $dateFrom->modify('-1 month');
        } elseif ($dateFromParam == null) {
            try {
                $dateTo = new DateTime($dateToParam);
            } catch(Exception $e) {
                $dateTo = new DateTime();
            }
            $dateFrom = clone $dateTo;
            $dateFrom->modify('-1 month');
        } elseif ($dateToParam == null) {
            try {
                $dateFrom = new DateTime($dateFromParam);
            } catch(Exception $e) {
                $dateFrom = new DateTime();
            }
            $dateTo = clone $dateFrom;
            $dateTo->modify('+1 month');
        } else {
            try {
                $dateFrom = new DateTime($dateFromParam);
            } catch(Exception $e) {
                $dateFrom = new DateTime();
            }
            try {
                $dateTo = new DateTime($dateToParam);
            } catch(Exception $e) {
                $dateTo = clone $dateFrom;
                $dateTo->modify('+1 month');
            }
        }
    }

    protected function initializeDateTime(&$date, &$timeFrom, &$timeTo) {
        $dateParam = Yii::app()->request->getParam('dateFrom');
        $timeFromParam = Yii::app()->request->getParam('timeFrom');
        $timeToParam = Yii::app()->request->getParam('timeTo');

        if ($dateParam == null) {
            $date = (new DateTime());
        } else {
            $date = new DateTime($dateParam);
        }

        if ($timeFromParam == null) {
            $fromHour = 0;
            $fromMinute = 0;
        } else {
            $timeFromParamArray = explode(':', $timeFromParam);
            if (isset($timeFromParamArray[0]) && ((int)$timeFromParamArray[0]) <= 23) {
                $fromHour = (int)$timeFromParamArray[0];
            } else {
                $fromHour = 0;
            }
            if (isset($timeFromParamArray[1]) && ((int)$timeFromParamArray[1]) <= 59) {
                $fromMinute = (int)$timeFromParamArray[1];
            } else {
                $fromMinute = 0;
            }
        }

        if ($timeToParam == null) {
            $toHour = 23;
            $toMinute = 59;
        } else {
            $timeToParamArray = explode(':', $timeToParam);
            if (isset($timeToParamArray[0]) && ((int)$timeToParamArray[0]) <= 23) {
                $toHour = (int)$timeToParamArray[0];
            } else {
                $toHour = 23;
            }
            if (isset($timeToParamArray[1]) && ((int)$timeToParamArray[1]) <= 59) {
                $toMinute = (int)$timeToParamArray[1];
            } else {
                $toMinute = 59;
            }
        }

        if ($toHour >= $fromHour || ($toHour = $fromHour && $toMinute >= $fromMinute)) {
            $timeFrom = sprintf('%02d', $fromHour).':'.sprintf('%02d', $fromMinute);
        } else {
            $timeFrom = '00:00';
        }
        $timeTo = sprintf('%02d', $toHour).':'.sprintf('%02d', $toMinute);
    }

    protected function initializeFilters() {
        $filtersForm=new FiltersForm;
        if ($filters = Yii::app()->request->getParam('FiltersForm')) {
            $filtersForm->filters=$filters;
        }
        return $filtersForm;
    }

    protected function initializeFieldsForExport(CActiveRecord $model, array $rows, array $exceptions = array()) {
        $fields = array();

        if (empty($rows)) {
            return $fields;
        }
        $singleLine = $rows[0];
        foreach (array_keys($singleLine) as $field) {
            $isException = array_map( function($exception) use($field) {
                    $matches = preg_match("/$exception/", $field);
                    return (!empty($matches));
                }, $exceptions);
            if (in_array(true, $isException)){
                continue;
            }
            if (preg_match('/consumption_[A-Za-z]*_/', $field)) {
                $fields[$field] = str_replace('_', '-', preg_replace('/consumption_[A-Za-z]*_/', '', $field));
            } else {
                $fields[$field] = $model->getAttributeLabel($field);
            }
        }

        return $fields;
    }

    /**
     * Displays a particular report in PDF format.
     * @param $title
     * @param array $rows
     * @param $fields
     * @param $filters
     * @param $customHeader
     */
    protected function exportPdf($title, $rows, $fields, $filters=null, $customHeader=null)
    {
        $filename = str_replace(' ', '_', $title).'_map_'.date('Y-m-d_H-i-s').'.pdf';

        if (count($fields) > 10) {
            $mpdf = new mPDF('c','A4-L'); // Landscape
        } else {
            $mpdf = new mPDF('c','A4'); // Portrait
        }
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setHeader($filename.'||');
        $mpdf->setFooter('Page: {PAGENO}');
        $mpdf->WriteHTML($this->render('pdf', array(
            'reportName' => $title,
            'rows' => $rows,
            'fields' => $fields,
            'filters' => $filters,
            'customHeader' => $customHeader,
        ), true));
        $mpdf->Output($filename, 'D');

        Yii::app()->end();
    }

    /**
     * Displays a particular report in CSV format.
     * @param $name
     * @param array $rows
     * @param $fields
     * @param $filters
     */
    public function exportCsv($title, $rows, $fields, $filters=null, $customHeader=null)
    {
        $filename = str_replace(' ', '_', $title).'_report_'.date('Y-m-d_H-i-s').'.csv';

        $reportHeader = $this->renderPartial('csvHeader', array(
            'reportName' => $title,
            'rowCount' => count($rows),
            'filters' => $filters,
            'customHeader' => $customHeader,
        ), true);

        $csvExport = new CsvExport();
        $csvExport->filename = $filename;
        $csvExport->reportHeader = $reportHeader;
        $csvExport->columnHeaders = $fields;
        $csvExport->delimiter = ';';
        $csvExport->fieldsToExport = array_keys($fields);
        $csvExport->export($rows);

        Yii::app()->end();
    }

}