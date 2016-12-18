<?php

class UserController extends Controller
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
			array('allow',  // allow all users to perform 'create' actions
				'actions'=>array('index', 'create', 'login', 'logout'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'index', 'logout', 'view', 'update' and 'changepass' actions
				'actions'=>array('view', 'update', 'changepass'),
                'expression' => array('UserController','allowOnlyOwnerOrAdmin')
			),
			array('allow', // allow admin user to perform 'list', 'admin' and 'delete' actions
				'actions'=>array('list', 'admin', 'delete', 'changerole'),
                'expression'=>'!Yii::app()->user->isGuest && Yii::app()->user->isAdmin()',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    /**
     * Allow only the owner to do the action
     * @return boolean whether or not the user is the owner or an admin
     */
    public static function allowOnlyOwnerOrAdmin(){
        if(Yii::app()->user->isAdmin()){
            return true;
        }
        else{
            $user = User::model()->findByPk(Yii::app()->getRequest()->getQuery('id'));
            return $user && $user->id_user === Yii::app()->user->id;
        }
    }

    /**
     * Redirects to view action
     */
    public function actionIndex()
    {
        if (!Yii::app()->user->isGuest){
            $this->redirect('view/'.Yii::app()->user->id);
        }
        $this->render('index');
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model=new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login',array('model'=>$model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
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
		$model=new User('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
            $model->hashPass = true;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_user));
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

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_user));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

    /**
     * Changes the Password of a User
     * @param integer $id the ID of the user to be updated
     * @throws CHttpException
     */
    public function actionChangePass($id)
    {
        $form=new ChangePasswordForm();
        $form->user=$this->loadModel($id);
        if (isset($_POST['ChangePasswordForm'])) {
            $form->setAttributes($_POST['ChangePasswordForm']);
            if ($form->validate()) {
                $form->saveNewPassword();
                $this->redirect(array('user/view/'.$id));
            }
        }

        $this->render('changepass', array('model'=>$form));
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $userModel = $this->loadModel($id);
        $userModel->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
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
    public function actionList()
    {
        $dataProvider=new CActiveDataProvider('User');
        $this->render('list',array(
            'dataProvider'=>$dataProvider,
        ));
    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    /**
     * Changes the Password of a User
     * @param integer $id the ID of the user to be updated
     */
    public function actionChangeRole($id)
    {
        $form=new ChangeRoleForm();
        $form->user=$this->loadModel($id);
        if (isset($_POST['User'])) {
            $form->setAttributes($_POST['User']);
            if ($form->validate()) {
                $form->saveNewRole();
                $this->redirect(array('user/view/'.$id));
            }
        }

        $this->render('changerole', array('model'=>$form));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    protected function buildUserMenu($id = null){
        return array_filter(array(
            (Yii::app()->user->isGuest ? array('label'=>'Login', 'url'=>array('login')) : null),
            (!Yii::app()->user->isGuest && Yii::app()->user->isAdmin() ? array('label'=>'List Users', 'url'=>array('list')) : null),
            (!Yii::app()->user->isGuest && Yii::app()->user->isAdmin()  ? array('label'=>'Manage Users', 'url'=>array('admin')) : null),
            array('label'=>'Create User', 'url'=>array('create')),
            ($id ? array('label'=>'Update User', 'url'=>array('update', 'id'=>$id)) : null),
            ($id ? array('label'=>'Change Password', 'url'=>array('changepass', 'id'=>$id)) : null),
            ($id && !Yii::app()->user->isGuest && Yii::app()->user->isAdmin()  ? array('label'=>'Change Role', 'url'=>array('changerole', 'id'=>$id)) : null),
            ($id && !Yii::app()->user->isGuest && Yii::app()->user->isAdmin()  ? array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$id),'confirm'=>'Are you sure you want to delete this item?', 'csrf' => true)) : null),
        ));
    }
}
