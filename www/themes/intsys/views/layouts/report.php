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

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" />
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/helper.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <!-- intsys theme styles -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/screen.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/report.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/intsys/css_<?php echo APP_VERSION; ?>/report_print.css" media="print"/>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

    <div id="header" >
        <?php if (isset($client_logo) && file_exists(File::getFullPath($client_logo)) && strlen($client_logo) > 0): ?>
            <div id="client-logo">
                <img height="50px" src="<?php echo File::getURL($client_logo); ?>" alt="<?php echo CHtml::encode($client_name); ?>" title="<?php echo CHtml::encode($client_name); ?>">
            </div>
        <?php endif; ?>
    </div><!-- header -->

    <?php echo $content; ?>

</div><!-- page -->

</body>
</html>
