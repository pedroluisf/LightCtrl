<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 05-01-2014
 * Time: 22:06
 */

class IndexController extends Controller
{

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
            array('allow',  // allow all users to perform 'error' and 'contact' actions
                'actions'=>array('error','noViewer'),
                'users'=>array('*'),
            ),
            array('allow',  // allow only authenticated users to perform 'index' actions
                'actions'=>array('index', 'areaSchema', 'areaTree', 'areaTreeData', 'areaEthernetStores'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page'=>array(
                'class'=>'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // Redirect when the conditions are not met to display Viewer
        if (!BrowserDetector::usingCompatibleInternetExplorerForAutoDesk()){
            $this->render('noie');
            Yii::app()->end();
        }

        // If we have a Pre-selected device to show
        $selectDevice = array();
        if(isset($_POST['selectDevice']))
        {
            $selectDevice = $_POST['selectDevice'];
            /** @var Ethernet $ethernet */
            $ethernet = Ethernet::model()->findByPk($selectDevice['fk_ethernet']);
            if ($ethernet){
                $selectDevice['fk_area'] = $ethernet->fk_area;
            }
        }

        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/viewer.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/index.js', CClientScript::POS_END);

        $this->render('index', array(
            'selectDevice' => $selectDevice
        ));

        //Render The scripts for the events on viewer
        $this->renderPartial('viewerEvents');

        //Render The Stores for info on client
        $this->renderPartial('stores', array());
    }

    /**
     * This action serves an Ajax request with an area Schema
     */
    public function actionAreaSchema($id_area)
    {
        // Prepare Area Schema
        $area = Area::model()->findByPk($id_area);
        if ($area instanceof Area) {
            $areaPlan = (is_file(File::getFullPath($area->plan)) ? File::getURL($area->plan) : '');
            echo $areaPlan;
        } else {
            echo "";
        }
        Yii::app()->end();
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
            echo $area->tree_config;
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
        if ($area instanceof Area) {
            $response['props'] = json_decode($area->props_config);
            // Status Stores
            $response['status'] = Status::getReadableStatusForAllDevicesByArea($id_area);
            echo json_encode($response);
        } else {
            echo "";
        }
        Yii::app()->end();
    }

    /**
     * This is the action to show page reporting Viewer missing.
     */
    public function actionNoViewer()
    {
        $this->render('noviewer');
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
}