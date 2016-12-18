<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 07-05-2015
 * Time: 00:11
 */

$export = var_export($export, true);
$imagePath = Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/images/';
$libPath = Yii::app()->baseUrl . '/js_'.APP_VERSION.'/amcharts/plugins/export/libs/';

echo '
        , "marginTop": 10
        , "creditsPosition": "bottom-right"
        , "pathToImages": "'.$imagePath.'"
        , "mouseWheelZoomEnabled": true
        , "chartCursor": {
            "fullWidth": true,
            "cursorAlpha": 0.1,
            "cursorColor": "#298DCD",
            "pan": true
        }
        , "chartScrollbar": {
            "graph": "'.$chartId.'",
            "autoGridCount": true,
            "graphFillColor": "#CCCCCC",
            "selectedGraphFillColor": "#A6D3F7"
        }
        , "export": {
            "enabled": '.$export.',
            "libs": {
                "path": "'.$libPath.'"
            },
            "position": "bottom-right",
            "menu": [{
                "class": "export-main",
                "menu": [
                    {
                        "label": " Image",
                        "icon": "../../../themes/intsys/images/image.png",
                        "menu": [ "PNG", "JPG", "SVG" ]
                    },
                    {
                        "label": " Pdf",
                        "icon": "../../../themes/intsys/images/pdf.png",
                        "format": [ "PDF" ],
                        "pageOrientation": "landscape",
                        "content": [{
                            "image": "reference",
                            "fit": [ 769.89, 523.28 ] // fit image to A4
                        }]
                    },
                    {
                        "label": " Print",
                        "icon": "../../../themes/intsys/images/printer.png",
                        "format": [ "PRINT" ]
                    }
                ]
            }]
        }
';

if (!empty($legend)) {
    echo '
        , "legend": {
            "position": "bottom",
            "valueText": "[[value]]",
            "valueWidth": 100,
            "valueAlign": "left",
            "equalWidths": false,
            "periodValueText": "total: [[value.sum]]"
        }
    ';
}