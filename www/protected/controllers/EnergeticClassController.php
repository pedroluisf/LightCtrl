<?php

class EnergeticClassController extends Controller
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','update','import','admin','delete'),
                'expression'=>'!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser())',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new EnergeticClass;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['EnergeticClass']))
		{
			$model->attributes=$_POST['EnergeticClass'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_energetic_class));
		}

		$this->render('create',array(
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
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['EnergeticClass']))
		{
			$model->attributes=$_POST['EnergeticClass'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_energetic_class));
		}

		$this->render('update',array(
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
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('EnergeticClass');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new EnergeticClass('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['EnergeticClass']))
			$model->attributes=$_GET['EnergeticClass'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    public function actionImport()
    {
        // This is to allow expanding div for errors
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_'.APP_VERSION.'/component.js', CClientScript::POS_END);

        $model = new EnergeticClassConfigurationForm();
        $errors = array();

        if(isset($_POST['EnergeticClassConfigurationForm'])) {
            $model->attributes = $_POST['EnergeticClassConfigurationForm'];
            if ($model->validate() && $model->uploadAndValidateConfigFile()) {
                /** @var IEnergeticClassConfigurationUpdater $configurationUpdater */
                $configurationUpdater = Factory::getConfigurationUpdateModelByTypeAndFileExtension(
                    Factory::CONFIGURATION_TYPE_ENERGETIC_CLASS,
                    $model->getFileExtension()
                );
                try {
                    $configurationUpdater->isFullImport($model->full_import);
                    $configurationUpdater->processFile($model->getFilePath());
                    $errors = $configurationUpdater->getErrors();
                    $msg = 'Import completed Successfully';
                    if (!empty($errors)) {
                        $msg .= '... Although some errors occurred! See bellow for more detail';
                    }
                    Yii::app()->user->setFlash('success',$msg);
                } catch (FileParsingException $e) {
                    Yii::app()->user->setFlash('error','Import failed: '.$e->getMessage());
                }
                @unlink($model->getFilePath());
            }
        }

        $this->render('import', array(
            'model' => $model,
            'errors' => $errors,
        ));
    }

    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return EnergeticClass the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=EnergeticClass::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param EnergeticClass $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='energetic-class-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    protected function buildConfigurationMenu($model = null){

        return array_filter(array(
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Manage Energetic Class', 'url'=>array('admin')): null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
            array('label'=>'List Energetic Classes', 'url'=>array('index')) : null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
            array('label'=>'Create Energetic Class', 'url'=>array('create')): null),
            (isset($model['id_energetic_class']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
            array('label'=>'Update Energetic Class', 'url'=>array('update', 'id'=>$model['id_energetic_class'])): null),
            (isset($model['id_energetic_class']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
            array('label'=>'Delete Energetic Class', 'url'=>'#', 'linkOptions'=>array(
            'submit'=>array('delete','id'=>$model['id_energetic_class']),
            'confirm'=>'Are you sure you want to delete this item?',
            'csrf' => true)
            ): null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Import Energetic Classes', 'url'=>array('import')) : null),
        ));
    }

}
