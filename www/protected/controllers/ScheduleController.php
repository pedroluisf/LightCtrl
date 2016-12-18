<?php
require_once( dirname(__FILE__) . '/../../mpdf/mpdf.php');

class ScheduleController extends Controller
{

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
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
            array('allow',  // allow only authenticated users to perform actions
                'actions'=>array('index', 'listPrint', 'listPdf', 'executed', 'view', 'update', 'create', 'delete', 'run', 'areaTree', 'areaTreeData', 'areaEthernetStores'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }
        $model = new CommandSchedule('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['CommandSchedule']))
            $model->attributes=$_GET['CommandSchedule'];

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/schedule.js', CClientScript::POS_END);

        $this->render('index', array ('model' => $model));
    }

    /**
     * Shows all pending tasks in a printable friendly manner
     */
    public function actionListPrint()
    {
        $model = new CommandSchedule('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['CommandSchedule']))
            $model->attributes=$_GET['CommandSchedule'];

        $this->render('pdf', array(
            'labels' => $model->attributeLabels(),
            'rows' => $model->search(false)->getData(),
            'fields' => $model->getReportFields()
        ));
    }

    /**
     * Shows all pending tasks in a pdf
     */
    public function actionListPdf()
    {
        $model = new CommandSchedule('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['CommandSchedule']))
            $model->attributes=$_GET['CommandSchedule'];

        $filename = 'Task_list_report_'.date('Y-m-d_H-i-s').'.pdf';

        $mpdf = new mPDF('c','A4');
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setHeader($filename.'||');
        $mpdf->setFooter('Page: {PAGENO}');
        $mpdf->WriteHTML($this->render('pdf', array(
            'labels' => $model->attributeLabels(),
            'rows' => $model->search(false)->getData(),
            'fields' => $model->getReportFields()
        ), true));
        $mpdf->Output($filename, 'D');

        Yii::app()->end();
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Displays all executed scheduled commands
     */
    public function actionExecuted()
    {
        // page size drop down changed
        if (isset($_GET['pageSize'])) {
            Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
            unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
        }
        $model = new ExecutedSchedule('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ExecutedSchedule']))
            $model->attributes=$_GET['ExecutedSchedule'];

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/schedule.js', CClientScript::POS_END);

        $this->render('executed', array ('model' => $model));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     */
    public function actionCreate()
    {
        $this->layout='//layouts/column1';

        $model=new CommandSchedule();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->periodicity = CommandSchedule::UNIQUE_COMMAND;
        if(isset($_POST['CommandSchedule']))
        {
            $model->attributes=$_POST['CommandSchedule'];
            $model->scenario = strtolower($model->periodicity) . '_' . strtolower($model->type);

            if($model->save())
                $this->redirect(array('index','id'=>$model->id_schedule));
        }

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/schedule.js', CClientScript::POS_END);

        if (!isset($model->start_date)) {
            $model->start_date = date('Y-m-d');
        }

        $this->render('create', array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $this->layout='//layouts/column1';

        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['CommandSchedule']))
        {
            $model->attributes=$_POST['CommandSchedule'];
            $model->scenario = strtolower($model->periodicity) . '_' . strtolower($model->type);

            if($model->save())
                $this->redirect(array('index','id'=>$model->id_schedule));
        }

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/schedule.js', CClientScript::POS_END);

        $model->start_date = $model->getDateForDisplay();
        $model->event_time = $model->getTimeForDisplay();

        $this->render('update', array(
            'model'=>$model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $model->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    /**
     * Runs a particular Schedule task.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the task to be executed
     */
    public function actionRun($id)
    {
        $model = $this->loadModel($id);
        try {
            $model->execute(true);
            Yii::app()->user->setFlash('success','Schedule Task has been Queued for Execution.');
        } catch (Exception $e){
            Yii::app()->user->setFlash('error','Execute Schedule Task returned the following Error «'.$e->getMessage().'».');
        }

        // if AJAX request (triggered via grid view), we should not redirect the browser
        if(!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        } else {
            $user=Yii::app()->getUser();
            foreach($user->getFlashKeys() as $key) {
                if($user->hasFlash($key)) {
                    echo '<div class="flash-'.$key.'">'.$user->getFlash($key).'</div>';
                }
            }
            Yii::app()->end();
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return CommandSchedule the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=CommandSchedule::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * This action serves an Ajax request with the area Tree
     */
    public function actionAreaTree()
    {
        $html = $this->renderPartial('tree', array(), true);
        Yii::app()->getClientScript()->renderBodyBegin($html);
        Yii::app()->getClientScript()->renderBodyEnd($html);
        echo $html;
        Yii::app()->end();
    }

    /**
     * This action serves an Ajax request with the area Tree Data in a json format
     */
    public function actionAreaTreeData($id_area)
    {
        $area = Area::model()->findByPk($id_area);
        if ($area instanceof Area) {
            $treeConfig = $area->getTreeForScheduling();
            echo json_encode($treeConfig);
        } else {
            echo "";
        }
        Yii::app()->end();
    }

    /**
     * This action serves an Ajax request with all the area stores in a json format
     */
    public function actionAreaEthernetStores($id_area)
    {
        $response = array();

        // Properties store
        $area = Area::model()->findByPk($id_area);
        $response['props'] = json_decode($area->props_config);

        // Status Stores
        $response['status'] = Status::getReadableStatusForAllDevicesByArea($id_area);

        echo json_encode($response);
        Yii::app()->end();
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Performs the AJAX validation.
     * @param Area $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='ethernet-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    protected function buildCommandScheduleMenu($id = null){
        return array_filter(array(
            (!Yii::app()->user->isGuest ? array('label'=>'Waiting Tasks', 'url'=>array('index')) : null),
            array('label'=>'View Completed Tasks', 'url'=>array('executed')),
            array('label'=>'Add Task', 'url'=>array('create')),
            ($id ? array('label'=>'Update Task', 'url'=>array('update', 'id'=>$id)) : null),
            ($id && !Yii::app()->user->isGuest && Yii::app()->user->isAdmin() ? array('label'=>'Delete Task', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$id),'confirm'=>'Are you sure you want to delete this task?', 'csrf' => true)) : null),
        ));
    }

}