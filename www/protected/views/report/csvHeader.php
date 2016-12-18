<?php
    $filtersArray = array();
    if ($filters){
        foreach($filters as $key => $filter) {
            $filtersArray[] = $labels[$key] . '=' . $filter;
        }
    }

    echo Yii::app()->params['companyName'].' - '.Yii::app()->name . PHP_EOL;
    echo $reportName.' Report' . PHP_EOL;
    echo 'Client: '.Yii::app()->configuration->get('client_name') . PHP_EOL;
    echo 'Building: '.Yii::app()->configuration->get('building_name') . PHP_EOL;
    if (!empty($customHeader)) {
        echo (is_array($customHeader) ? implode(PHP_EOL, $customHeader) : $customHeader) . PHP_EOL;
    }
    if (!empty($filtersArray)) {
        echo 'Filters used: '.implode(', ',$filtersArray) . PHP_EOL;
    }
    echo 'Number of Items: '.$rowCount . PHP_EOL;
    echo 'Generated On: '.date('Y-m-d H:i:s').' by: '.Yii::app()->user->getName() . PHP_EOL;
    echo 'Digital Signature: '.$digitalSignature . PHP_EOL;
