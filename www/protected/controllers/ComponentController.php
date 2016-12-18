<?php

class ComponentController extends Controller
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
            array('allow',  // allow all users to perform 'error' and 'contact' actions
                'actions'=>array('error'),
                'users'=>array('*'),
            ),
            array('allow',  // allow only authenticated users to perform 'index' actions
                'actions'=>array('index', 'areaTree', 'areaTreeData', 'getAttributes', 'getForm', 'update', 'import'),
                'expression'=>'!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser())',
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
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/component.js', CClientScript::POS_END);

        $model = new ComponentForm();
        $this->render('index', array ('model' => $model));
    }

    public function actionUpdate()
    {
        $ethernetId = Yii::app()->request->getPost('ethernet_id');
        $lc_id = Yii::app()->request->getPost('lc_id');
        $dvc_id = Yii::app()->request->getPost('dvc_id');

        if (!isset($ethernetId)) {
            $this->sendClientResponse(false, 'Error Saving Data: Mandatory fields not supplied');
        }

        $componentParser = new JsonComponentsParser($ethernetId);
        if ($lc_id || $dvc_id) {
            $component = $componentParser->getDeviceByLcIdAndDvcId($lc_id, $dvc_id);
        } else {
            $component = $componentParser->getEthernetComponent();
        }

        if (!$component) {
            $this->sendClientResponse(false, 'Component not found');
        }

        $purifier = new CHtmlPurifier();
        $values = array();
        foreach ($_POST as $key=>$value){
            $values[$key] = $purifier->purify($value);
        }

        $model = new ComponentForm();
        $success = $model->save($ethernetId, $component, $values);

        if ($success === true) {
            $html = $this->renderPartial(
                '_attributes',
                array(
                    'component' => $component
                ),
                true
            );
            Yii::app()->getClientScript()->renderBodyBegin($html);
            Yii::app()->getClientScript()->renderBodyEnd($html);
            $this->sendClientResponse(true, 'Saved successfully', $html);
        }

        $this->sendClientResponse(false, 'Error Saving Data');
    }

    public function actionImport()
    {
        // This is to allow expanding div for errors
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/component.js', CClientScript::POS_END);

        $model = new ComponentConfigurationForm();
        $errors = array();

        if(isset($_POST['ComponentConfigurationForm'])) {
            $model->attributes = $_POST['ComponentConfigurationForm'];
            if ($model->validate() && $model->uploadAndValidateConfigFile()) {
                /** @var IComponentConfigurationUpdater $configurationUpdater */
                $configurationUpdater = Factory::getConfigurationUpdateModelByTypeAndFileExtension(
                    Factory::CONFIGURATION_TYPE_COMPONENT,
                    $model->getFileExtension()
                );
                try {
                    $configurationUpdater->setEthernetId($model->ethernet_id);
                    $configurationUpdater->processFile($model->getFilePath());
                    $errors = $configurationUpdater->getErrors();
                    $msg = 'Import completed Successfully';
                    if (!empty($errors)) {
                        $msg .= '... Although some errors occurred! See bellow for more detail';
                    }
                    Yii::app()->user->setFlash('success', $msg);
                } catch (FileParsingException $e) {
                    Yii::app()->user->setFlash('error','Import failed: '.$e->getMessage());
                }
                @unlink($model->getFilePath());
            }
        }

        $this->render('import', array(
            'model' => $model,
            'errors' => $errors,
            'ethernetDevices' => Ethernet::model()->getListForDropDown()
        ));
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
        echo $area->tree_config;
        Yii::app()->end();
    }

    public function actionGetAttributes($ethernetId, $drawId)
    {
        $parser = new JsonComponentsParser($ethernetId);

        $component = $parser->getDeviceByDrawId($drawId);

        if (!$component) {
            $this->sendClientResponse(false, 'Component Not Found');
        }

        $html = $this->renderPartial(
            '_attributes',
            array(
                'component' => $component
            ),
            true
        );
        Yii::app()->getClientScript()->renderBodyBegin($html);
        Yii::app()->getClientScript()->renderBodyEnd($html);
        $this->sendClientResponse(true, '', $html);

    }
    public function actionGetForm($ethernetId, $drawId, $attributeName, $value)
    {
        $parser = new JsonComponentsParser($ethernetId);

        $component = $parser->getDeviceByDrawId($drawId);

        if (!$component) {
            $this->sendClientResponse(false, 'Component Not Found');
        }

        $attributes = ComponentForm::getChangeableAttributes($component);
        parse_str($attributeName, $output);
        $attrName = array_keys($output)[0];
        $html = $this->renderPartial(
            $attributes[$attrName]['view'],
            array(
                'attribute' => $attrName,
                'subAttribute' => is_array(current($output)) ? array_keys(current($output))[0] : null,
                'component' => $component,
                'value' => $value,
                'validator' => isset($attributes[$attrName]['validator']) ? $attributes[$attrName]['validator'] : null
            ),
            true
        );
        Yii::app()->getClientScript()->renderBodyBegin($html);
        Yii::app()->getClientScript()->renderBodyEnd($html);
        $this->sendClientResponse(true, '', $html);
    }

    /**
     * Sends feedback to client
     * @param bool $success
     * @param string $message
     * @param mixed $data
     */
    protected function sendClientResponse($success, $message, $data = null){
        $result = array(
            'success' => $success,
            'message' => $message,
            'data' => $data
        );
        echo json_encode($result);
        Yii::app()->end();
    }


    protected function buildConfigurationMenu(){
        return array_filter(array(
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Component Configuration', 'url'=>array('component/index')): null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Import Configuration', 'url'=>array('component/import')): null),
        ));
    }
}
