<?php
require_once( dirname(__FILE__) . '/../../mpdf/mpdf.php');

class ReportController extends Controller
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
            array('allow', // allow admin user to perform 'index', 'view' and 'download' actions
                'actions'=>array(
                    'index',
                    'currentStatus',
                    'currentStatusCsv',
                    'currentStatusPdf',
                    'status',
                    'statusCsv',
                    'statusPdf',
                    'failure',
                    'failureCsv',
                    'failurePdf',
                    'emergency',
                    'emergencyCsv',
                    'emergencyPdf',
                    'emergencyCurrent',
                    'emergencyCurrentCsv',
                    'emergencyCurrentPdf',
                    'emergencyScheduledList',
                    'emergencyScheduled',
                    'emergencyScheduledCsv',
                    'emergencyScheduledPdf',
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

    public function actionCurrentStatus()
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('Status_sort'=>Yii::app()->request->getParam('Status_sort')));

        $model = $this->getStatusModel();
        $this->render('status_list', array(
            'title'=>'Current Status',
            'model'=>$model
        ));
    }

    public function actionCurrentStatusCsv()
    {
        $model = $this->getStatusModel();
        $this->exportNormal('csv', 'Current Status', $model, $model->attributeLabels(), $_GET['Status']);
    }

    public function actionCurrentStatusPdf()
    {
        $model = $this->getStatusModel();
        $this->exportNormal('pdf', 'Current Status', $model, $model->attributeLabels(), $_GET['Status']);
    }

    public function actionStatus()
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('StatusHist_sort'=>Yii::app()->request->getParam('StatusHist_sort')));

        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getStatusHistModel($dateFrom, $dateTo);

        $this->render('status_list', array(
            'title'=>'Status History',
            'dateFrom'=>$dateFrom,
            'dateTo'=>$dateTo,
            'model'=>$model
        ));
    }

    public function actionStatusCsv()
    {
        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getStatusHistModel($dateFrom, $dateTo);

        $this->exportNormal('csv', 'Status History', $model, $model->attributeLabels(), $_GET['StatusHist']);
    }

    public function actionStatusPdf()
    {
        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getStatusHistModel($dateFrom, $dateTo);

        $this->exportNormal('pdf', 'Status History', $model, $model->attributeLabels(), $_GET['StatusHist']);
    }

    public function actionFailure()
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('Failure_sort'=>Yii::app()->request->getParam('Failure_sort')));

        $model = $this->getFailureModel();
        $this->render('failure_list', array(
            'title'=>'Failure',
            'model'=>$model
        ));
    }

    public function actionFailureCsv()
    {
        $model = $this->getFailureModel();
        $this->exportNormal('csv', 'Failures', $model, $model->attributeLabelsForReport(), $_GET['Failure']);
    }

    public function actionFailurePdf()
    {
        $model = $this->getFailureModel();
        $this->exportNormal('pdf', 'Failures', $model, $model->attributeLabelsForReport(), $_GET['Failure']);
    }

    public function actionEmergency()
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('EmergencyHist_sort'=>Yii::app()->request->getParam('EmergencyHist_sort')));

        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getEmergencyHistModel($dateFrom, $dateTo);

        $this->render('emergency_list', array(
            'title'=>'Emergency Test History',
            'dateFrom'=>$dateFrom,
            'dateTo'=>$dateTo,
            'model'=>$model
        ));
    }

    public function actionEmergencyCsv()
    {
        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getEmergencyHistModel($dateFrom, $dateTo);

        $this->exportNormal('csv', 'Emergency History', $model, $model->attributeLabels(), $_GET['Emergency']);
    }

    public function actionEmergencyPdf()
    {
        $this->initializeDates($dateFrom, $dateTo);
        $model = $this->getEmergencyHistModel($dateFrom, $dateTo);

        $this->exportNormal('pdf', 'Emergency History', $model, $model->attributeLabels(), $_GET['Emergency']);
    }

    public function actionEmergencyCurrent()
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('Emergency_sort'=>Yii::app()->request->getParam('Emergency_sort')));

        $model = $this->getEmergencyModel();
        $this->render('emergency_list', array(
            'title'=>'Current Emergency',
            'model'=>$model
        ));
    }

    public function actionEmergencyCurrentCsv()
    {
        $model = $this->getEmergencyModel();
        $this->exportNormal('csv', 'Current Emergency', $model, $model->attributeLabels(), $_GET['Emergency']);
    }

    public function actionEmergencyCurrentPdf()
    {
        $model = $this->getEmergencyModel();
        $this->exportNormal('pdf', 'Current Emergency', $model, $model->attributeLabels(), $_GET['Emergency']);
    }

    public function actionEmergencyScheduledList()
    {
        $this->initReportGrid();

        $model = new ExecutedSchedule('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ExecutedSchedule']))
            $model->attributes=$_GET['ExecutedSchedule'];

        $this->render('emergency_scheduled_list', array(
            'model'=>$model
        ));
    }

    public function actionEmergencyScheduled($id_exec_schedule)
    {
        $this->initReportGrid();
        Yii::app()->user->setState('sort', array('ExecutedEmergency_sort'=>Yii::app()->request->getParam('ExecutedEmergency_sort')));

        $model = $this->getExecutedEmergencyModel();
        $executedSchedule = $this->getExecutedSchedule($id_exec_schedule);
        $model->setExecutedSchedule($executedSchedule);
        $this->render('emergency_scheduled', array(
            'model'=>$model,
            'execScheduled'=>$executedSchedule
        ));
    }

    public function actionEmergencyScheduledCsv($id_exec_schedule)
    {
        $this->exportScheduled('csv', $id_exec_schedule);
    }

    public function actionEmergencyScheduledPdf($id_exec_schedule)
    {
        $this->exportScheduled('pdf', $id_exec_schedule);
    }

    protected function initializeDates(&$dateFrom, &$dateTo) {
        $dateFromParam = $_GET['dateFrom'];
        $dateToParam = $_GET['dateTo'];

        if ($dateFromParam == null && $dateToParam == null) {
            $dateTo = new DateTime();
            $dateFrom = clone $dateTo;
            $dateFrom->modify('-1 day');
        } elseif ($dateFromParam == null) {
            try {
                $dateTo = new DateTime($dateToParam);
            } catch(Exception $e) {
                $dateTo = new DateTime();
            }
            $dateFrom = clone $dateTo;
            $dateFrom->modify('-1 day');
        } elseif ($dateToParam == null) {
            try {
                $dateFrom = new DateTime($dateFromParam);
            } catch(Exception $e) {
                $dateFrom = new DateTime();
            }
            $dateTo = clone $dateFrom;
            $dateTo->modify('+1 day');
        } else {
            try {
                $dateFrom = new DateTime($dateFromParam);
            } catch(Exception $e) {
                $dateFrom = new DateTime();
            }
            try {
                $dateTo = new DateTime($dateToParam);
                if ($dateFrom > $dateTo) {
                    throw new Exception('Invalid date');
                }
            } catch(Exception $e) {
                $dateTo = clone $dateFrom;
                $dateTo->modify('+1 day');
            }
        }
    }

    protected function initReportGrid() {
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/report.js', CClientScript::POS_END);

        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }
    }

    protected function getStatusModel(){
        $model = new Status('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Status']))
            $model->attributes=$_GET['Status'];

        $model->dbCriteria->order='lc_id, dvc_id ASC';

        return $model;
    }

    protected function getStatusHistModel(DateTime $dateFrom, DateTime $dateTo){
        $model = new StatusHist('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['StatusHist']))
            $model->attributes=$_GET['StatusHist'];

        $model->dbCriteria->addBetweenCondition('created_at', $dateFrom->format('Y-m-d 00:00:00'), $dateTo->format('Y-m-d 23:59:59'));
        $model->dbCriteria->order='created_at DESC';

        return $model;
    }

    protected function getFailureModel(){
        $model = new Failure('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Failure']))
            $model->attributes=$_GET['Failure'];

        $model->dbCriteria->order='created_at DESC';

        return $model;
    }

    protected function getEmergencyModel(){
        $model = new Emergency('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Emergency']))
            $model->attributes=$_GET['Emergency'];

        $model->dbCriteria->order='created_at DESC';

        return $model;
    }

    protected function getEmergencyHistModel(DateTime $dateFrom, DateTime $dateTo){
        $model = new EmergencyHist('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['EmergencyHist']))
            $model->attributes=$_GET['EmergencyHist'];

        $model->dbCriteria->addBetweenCondition('created_at', $dateFrom->format('Y-m-d 00:00:00'), $dateTo->format('Y-m-d 23:59:59'));

        $model->dbCriteria->order='created_at DESC';


        return $model;
    }

    protected function getExecutedEmergencyModel(){
        $model = new ExecutedEmergency('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ExecutedEmergency']))
            $model->attributes=$_GET['ExecutedEmergency'];

        return $model;
    }

    /**
     * Gets a ExecutedSchedule if id is valid, otherwise redirect
     * @param $id_exec_schedule
     * @return ExecutedSchedule
     */
    protected function getExecutedSchedule($id_exec_schedule) {
        $execScheduled = ExecutedSchedule::model()->findByPk($id_exec_schedule);
        if ($execScheduled) {
            return $execScheduled;
        }
        Yii::app()->user->setFlash('error','Schedule ID Not found');
        $this->redirect(array('index'));
    }

    protected function exportNormal($format, $name, $model, $labels, $filter) {
        // Force last sort to match previous request
        $sort = Yii::app()->user->getState('sort');
        if (!empty($sort)) {
            $_GET[key($sort)] = current($sort);
        }
        $rows = $model->search(false)->getData();
        $fields = $model->getReportFields();
        // Needed for CSV
        $labels = array_combine($fields, array_intersect_key($labels, array_flip ($fields)));

        $function = 'export'.ucfirst($format);
        $this->$function($name, $rows, $labels, $fields, $filter);
    }

    protected function exportScheduled($format, $id_exec_schedule) {
        // Force last sort to match previous request
        $sort = Yii::app()->user->getState('sort');
        if (!empty($sort)) {
            $_GET[key($sort)] = current($sort);
        }
        $model = $this->getExecutedEmergencyModel();
        $execScheduled = $this->getExecutedSchedule($id_exec_schedule);
        $model->setExecutedSchedule($execScheduled);
        $rows = $model->search(false)->getData();
        $fields = $model->getReportFields();
        $labels = array_combine($fields, array_intersect_key($model->attributeLabels(), array_flip ($fields)));
        $customHeader = array();
        $customHeader[] = $execScheduled->description;
        $customHeader[] = $execScheduled->getAttributeLabel('periodicity').': '.$execScheduled->getPeriodicityForDisplay();
        $customHeader[] = $execScheduled->getAttributeLabel('start_date').': '.$execScheduled->start_date;
        $customHeader[] = $execScheduled->getAttributeLabel('type').': '.$execScheduled->getTypeForDisplay();
        $customHeader[] = $execScheduled->getAttributeLabel('fk_ethernet').': '.$execScheduled->fk_ethernet.
            ' '.$execScheduled->getAttributeLabel('lc_id').': '.$execScheduled->lc_id.
            ' '.$execScheduled->getAttributeLabel('dvc_id').': '.$execScheduled->dvc_id;
        if ($execScheduled->group !== null) {
            $customHeader[] = $execScheduled->getAttributeLabel('group').': '.$execScheduled->group;
        }

        $function = 'export'.ucfirst($format);
        $this->$function('Scheduled Emergency', $rows, $labels, $fields, null, $customHeader);
    }

    /**
     * Displays a particular report in PDF format.
     * @param $name
     * @param array $rows
     * @param $labels
     * @param $fields
     * @param $filters
     */
    protected function exportPdf($name, $rows, $labels, $fields, $filters=null, $customHeader=null)
    {
        $filename = str_replace(' ', '_', $name).'_report_'.date('Y-m-d_H-i-s').'.pdf';

        $mpdf = new mPDF('c','A4');
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setHeader($filename.'||');
        $mpdf->setFooter('Page: {PAGENO}');
        $mpdf->WriteHTML($this->render('pdf', array(
            'reportName' => $name,
            'labels' => $labels,
            'rows' => $rows,
            'fields' => $fields,
            'filters' => $filters,
            'customHeader' => $customHeader,
            'digitalSignature' => $this->generateDigitalSignature($rows)
        ), true));
        $mpdf->Output($filename, 'D');

        Yii::app()->end();
    }

    /**
     * Displays a particular report in CSV format.
     * @param $name
     * @param array $rows
     * @param $labels
     * @param $fields
     * @param $filters
     */
    public function exportCsv($name, $rows, $labels, $fields, $filters=null, $customHeader=null)
    {
        $filename = str_replace(' ', '_', $name).'_report_'.date('Y-m-d_H-i-s').'.csv';

        $reportHeader = $this->renderPartial('csvHeader', array(
            'reportName' => $name,
            'labels' => $labels,
            'rowCount' => count($rows),
            'filters' => $filters,
            'customHeader' => $customHeader,
            'digitalSignature' => $this->generateDigitalSignature($rows)
        ), true);

        $csvExport = new CsvExport();
        $csvExport->filename = $filename;
        $csvExport->reportHeader = $reportHeader;
        $csvExport->columnHeaders = $labels;
        $csvExport->delimiter = ';';
        $csvExport->fieldsToExport = $fields;
        $csvExport->export($rows);

        Yii::app()->end();
    }

    /**
     * Generated hash to ensure content validity when exported
     * The hash will be generated with the concatenation of three values (private_key + nr_rows + content)
     * @param $content
     * @return string
     */
    protected function generateDigitalSignature($content) {
        $privateKey = Yii::app()->configuration->get('hashKey');
        $nrRows = count($content);
        $strContent = serialize($content);
        return md5($privateKey.$nrRows.$strContent);
    }

}