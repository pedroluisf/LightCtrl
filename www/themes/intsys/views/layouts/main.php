<?php /* @var $this Controller */
$client_logo    = Yii::app()->configuration->get('client_logo');
$client_name    = Yii::app()->configuration->get('client_name');
$header_message = Yii::app()->configuration->get('header_message');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    <link rel="icon" type="image/png" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/images_<?php echo APP_VERSION; ?>/favicon.png">

    <!-- jQuery Library -->
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <!-- intsys theme styles -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/helper.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/main.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/form.css" media="screen, projection" />

    <script type="application/javascript">var baseUrl = "<?php echo Yii::app()->getBaseUrl();?>"</script>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header" style="background: url('<?php echo Yii::app()->baseUrl.Yii::app()->params['main_logo']; ?>') no-repeat center, linear-gradient(to right, #85BAD8 20%, #FFF 45%, #FFF 55%, #85BAD8 80%); background-size: contain">

        <?php if (isset($client_logo) && file_exists(File::getFullPath($client_logo)) && strlen($client_logo) > 0): ?>
            <div id="client-logo">
                <img src="<?php echo File::getURL($client_logo); ?>" alt="<?php echo CHtml::encode($client_name); ?>" title="<?php echo CHtml::encode($client_name); ?>">
            </div>
        <?php endif; ?>
        <div id="client-name">
            <?php echo CHtml::encode($client_name); ?>
        </div>
        <div id="header-messaging">
            <?php echo CHtml::encode($header_message); ?>
        </div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/index/index/')),
                array('label'=>'Scheduling', 'url'=>array('/schedule/index/'), 'visible' => (!Yii::app()->user->isGuest)),
                array('label'=>'Reports', 'url'=>array('/report/index/'), 'visible' => (!Yii::app()->user->isGuest )),
                array('label'=>'Consumption Maps', 'url'=>array('/map/index/'), 'visible' => (!Yii::app()->user->isGuest )),
                array('label'=>'Configuration', 'url'=>array('/configuration/index/'), 'visible' => (Yii::app()->user->isAdmin() || Yii::app()->user->isSuperUser())),
                array('label'=>'Notifications', 'url'=>array('/notification/index/'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Account', 'url'=>array('/user/view/id/'.(Yii::app()->user->isGuest ? 0 : Yii::app()->user->id)), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Register', 'url'=>array('/user/create'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Login', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest),
			),
		)); ?>
	</div><!-- mainmenu -->
    <div id="notification_popup">
        <?php $this->renderPartial('/partial/_notifications', array('notifications' => Notification::getUnreadForUser())); ?>
    </div>
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

    <?php
        $user=Yii::app()->getUser();
        if ($user) {
            foreach($user->getFlashKeys() as $key):
                if($user->hasFlash($key)): ?>
                    <div class="flash-<?php echo $key; ?>">
                        <?php echo $user->getFlash($key); ?>
                    </div>
                <?php
                endif;
            endforeach;
        }
    ?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
        <?php echo Yii::app()->params['footer_note'] ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
