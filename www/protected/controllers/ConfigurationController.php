<?php

class ConfigurationController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

    /**
     * @return array
     */
    protected $redirects = array(
        'Configuration' => 'index',
        'Ethernet'=>'ethernetview',
        'Area'=>'areaview',
    );

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
            array('allow',  // allow authenticated users to perform 'list' and 'view' actions
                'actions'=>array(
                    'area',
                    'areaView',
                    'ethernet',
                    'ethernetView'
                ),
                'users'=>array('@'),
            ),
			array('allow', // allow admin/super users to perform maintenance actions
				'actions'=>array(
                    'index',
                    'update',
                    'areaCreate',
                    'areaUpdate',
                    'areaAdmin',
                    'areaDelete',
                    'ethernetCreate',
                    'ethernetUpdate',
                    'ethernetRestore',
                    'ethernetAdmin',
                    'ethernetDelete',
                ),
                'expression'=>'!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser())',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    /* ----------------------------- */
    /* Configuration Related Actions */
    /* ----------------------------- */

    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        $model = new Configuration('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Configuration']))
            $model->attributes=$_GET['Configuration'];

        $this->render('admin',array(
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
        $model=$this->loadConfigurationModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Configuration']))
        {
            $data = $_POST['Configuration'];
            $type = isset($data['type']) ? $data['type'] : null;
            unset($data['type']);

            if ($type == 'fileField') {
                if (!$data['value'] && empty($data['remove'])){
                    unset ($data['value']); // We unset it so it doesnt delete our previous value when no file is updated
                }
                if (!empty($data['remove'])) {
                    $model->deleteDeprecatedFile(); // Remove old file
                    unset ($data['remove']); // Not an actual attribute
                }
            }
            $model->attributes=$data;
            $this->_save($model);
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Configuration the loaded model
     * @throws CHttpException
     */
    public function loadConfigurationModel($id)
    {
        $model=Configuration::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /* -------------------- */
    /* Area Related Actions */
    /* -------------------- */

    /**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionAreaView($id)
	{
		$this->render('area/view',array(
			'model'=>$this->loadAreaModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAreaCreate()
	{
		$model=new Area('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Area'])) {
			$model->attributes=$_POST['Area'];
            $this->_save($model);
		}

		$this->render('area/create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionAreaUpdate($id)
	{
		$model=$this->loadAreaModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Area']))
		{
            $data = $_POST['Area'];
            if (!$data['plan']){
                unset ($data['plan']);
            }
            $model->attributes=$data;
            $this->_save($model);
		}

		$this->render('area/update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionAreaDelete($id)
	{
		$this->loadAreaModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('areaadmin'));
        } else {
            $user=Yii::app()->getUser();
            foreach($user->getFlashKeys() as $key) {
                if($user->hasFlash($key)) {
                    echo '<div class="flash-'.$key.'">'.$user->getFlash($key).'</div>';
                }
            }
        }
	}

	/**
	 * Lists all models.
	 */
	public function actionArea()
	{
		$dataProvider=new CActiveDataProvider('Area');
		$this->render('area/index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAreaAdmin()
	{
		$model=new Area('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Area']))
			$model->attributes=$_GET['Area'];

		$this->render('area/admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Area the loaded model
	 * @throws CHttpException
	 */
	public function loadAreaModel($id)
	{
		$model=Area::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /* ----------------------------------- */
    /* Ethernet Interfaces Related Actions */
    /* ----------------------------------- */

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionEthernetView($id)
    {
        $this->render('ethernet/view',array(
            'model'=>$this->loadEthernetModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionEthernetCreate()
    {
        $model=new Ethernet('create');

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Ethernet'])) {
            $model->attributes=$_POST['Ethernet'];
            $this->_save($model);
        }

        $this->render('ethernet/create',array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionEthernetUpdate($id)
    {
        $model=$this->loadEthernetModel($id);

        // Uncomment the following line if AJAX validation is needed
         $this->performAjaxValidation($model);

        if(isset($_POST['Ethernet']))
        {
            $data = $_POST['Ethernet'];
            $model->attributes=$data;
            if(isset($_POST['activateButton'])) {
                $model->inactive = !$model->inactive;
                $model->refresh_dependencies = true;
            }
            $this->_save($model);
        }

        $this->render('ethernet/update',array(
            'model'=>$model,
        ));
    }

    /**
     * Restores the json to its original value. The original json should be saved on configs folder
     * If restore is successful, the dependencies should be generated
     * @param integer $id the ID of the model to be restored
     */
    public function actionEthernetRestore($id)
    {
        $model=$this->loadEthernetModel($id);
        try {
            $model->restoreConfigs();
            Yii::app()->user->setFlash('success','Configs have been Successfully Restored.');
        } catch (Exception $e){
            Yii::app()->user->setFlash('error','There was an error restoring the original configs «'.$e->getMessage().'».');
        }

        // if AJAX request (triggered via grid dmin), we should not redirect the browser
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
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionEthernetDelete($id)
    {
        $model = $this->loadEthernetModel($id);
        try {
            $model->delete();
        } catch (FileParsingException $e) {
            Yii::app()->user->setFlash('error',$e->getMessage());
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('ethernetadmin'));
    }

    /**
     * Lists all models.
     */
    public function actionEthernet()
    {
        $dataProvider=new CActiveDataProvider('Ethernet');
        $this->render('ethernet/index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionEthernetAdmin()
    {
        $model=new Ethernet('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Ethernet']))
            $model->attributes=$_GET['Ethernet'];

        $this->render('ethernet/admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Ethernet the loaded model
     * @throws CHttpException
     */
    public function loadEthernetModel($id)
    {
        $model=Ethernet::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /* ----------------------------------- */
    /* General Use Methods */
    /* ----------------------------------- */

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

    protected function buildConfigurationMenu($model = null){

        return array_filter(array(
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'System Configuration', 'url'=>array('index')): null),
            (!Yii::app()->user->isGuest && !Yii::app()->user->isAdmin()  && !Yii::app()->user->isSuperUser() ?
                array('label'=>'List Building Areas', 'url'=>array('area')) : null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Create Building Area', 'url'=>array('areaCreate')): null),
            (isset($model['id_area']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Update Building Area', 'url'=>array('areaUpdate', 'id'=>$model['id_area'])): null),
            (isset($model['id_area']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Delete Building Area', 'url'=>'#', 'linkOptions'=>array(
                    'submit'=>array('areaDelete','id'=>$model['id_area']),
                    'confirm'=>'Are you sure you want to delete this item? All data related to this item (Ethernets, Reports, Schedules) will also be deleted',
                    'csrf' => true)
                ): null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Manage Building Areas', 'url'=>array('areaAdmin')): null),
            (!Yii::app()->user->isGuest && !Yii::app()->user->isAdmin() && !Yii::app()->user->isSuperUser() ?
                array('label'=>'List Ethernet Interfaces', 'url'=>array('ethernet')) : null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Create Ethernet Interface', 'url'=>array('ethernetCreate')) : null),
            (isset($model['id_ethernet']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Update Ethernet Interface', 'url'=>array('ethernetUpdate', 'id'=>$model['id_ethernet'])) : null),
            (isset($model['id_ethernet']) && !Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Delete Ethernet Interface', 'url'=>'#', 'linkOptions'=>array(
                    'submit'=>array('ethernetDelete','id'=>$model['id_ethernet']),
                    'confirm'=>'Are you sure you want to delete this item? All data related to this item (Reports, Schedules) will also be deleted',
                    'csrf' => true)
                ) : null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Manage Ethernet Interfaces', 'url'=>array('ethernetAdmin')) : null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Manage Components', 'url'=>array('component/index')): null),
            (!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser()) ?
                array('label'=>'Manage Energetic Classes', 'url'=>array('energeticClass/admin')): null)
        ));
    }

    private function _save($model) {
        try {
            if (!$model->save()){
                return;
            };
        } catch (FileParsingException $e) {
            Yii::app()->user->setFlash('error',$e->getMessage());
            return;
        }
        if ($model instanceof Configuration) {
            $this->redirect(array($this->redirects[get_class($model)], null));
        } else {
            $this->redirect(array($this->redirects[get_class($model)], 'id' => $model->getPrimaryKey()));
        }
    }
}
