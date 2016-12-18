<?php

class NotificationController extends Controller
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
			array('allow',  // allow authenticated user to perform 'index', 'delete' and 'view' actions
				'actions'=>array('index','view','viewMany','delete','deleteMany'),
				'users'=>array('@'),
			),
            array('allow', // allow admin user to perform 'listAll' actions
                'actions'=>array('listAll'),
                'expression'=>'!Yii::app()->user->isGuest && Yii::app()->user->isAdmin()',
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    /**
     * Lists all Notifications for the current user
     */
    public function actionIndex()
    {
        $model=new Notification('search');
        $model->unsetAttributes();  // clear any default values
        $model->fk_user = Yii::app()->user->getId();
        if(isset($_GET['Notification']))
            $model->attributes=$_GET['Notification'];

        $this->render('index',array(
            'showUser'=>false,
            'model'=>$model,
        ));
    }

    /**
     * Lists all Notifications if user is Admin.
     */
    public function actionListAll()
    {
        $model=new Notification('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Notification']))
            $model->attributes=$_GET['Notification'];

        $this->render('index',array(
            'showUser'=>true,
            'model'=>$model,
        ));
    }

    /**
	 * Reads a Notification
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model=$this->loadModel($id);

        if ($model->fk_user != Yii::app()->user->getId() && !Yii::app()->user->isAdmin()) {
            $this->redirect(array('notification/index'));
        }

        $model->new=false;
        $model->save();

        $this->render('view',array(
			'model'=>$model
		));
	}

    /**
     * Reads Notifications
     */
    public function actionViewMany()
    {
        $ids = Yii::app()->request->getParam('ids');
        if (!empty($ids)) {
            Notification::model()->updateAll(array('new'=>0), "id_notification IN ($ids)");
        }
    }

    /**
     * Deletes a particular Notification.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);

        if ($model->fk_user != Yii::app()->user->getId() && !Yii::app()->user->isAdmin()) {
            $this->redirect(array('notification/index'));
        }

        $model->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    /**
     * Deletes Notifications.
     */
    public function actionDeleteMany()
    {
        $ids = Yii::app()->request->getParam('ids');
        if (!empty($ids)) {
            Notification::model()->deleteAll("id_notification IN ($ids)");
        }
    }

    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Notification the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Notification::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Notification $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='notification-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    protected function buildNotificationsMenu($id = null){
        return array_filter(array(
            (!Yii::app()->user->isGuest ? array('label'=>'List My Notifications', 'url'=>array('index')) : null),
            (!Yii::app()->user->isGuest && Yii::app()->user->isAdmin() ? array('label'=>'List All Notifications', 'url'=>array('listAll')) : null),
            ($id && !Yii::app()->user->isGuest ? array('label'=>'Delete Notification', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$id),'confirm'=>'Are you sure you want to delete this Notification?', 'csrf' => true)) : null)
        ));
    }

}
