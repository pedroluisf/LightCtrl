<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 20-01-2014
 * Time: 23:28
 */

class BrowserDetector {

    public static function usingCompatibleInternetExplorerForAutoDesk()
    {
        $browser = self::detectBrowser();

        if ($browser['name'] == 'IE' && in_array($browser['version'], array(9,10,11))) {
            return true;
        }

        return false;
    }

    public static function usingCompatibleInternetExplorerForChartExport()
    {
        $browser = self::detectBrowser();

        if ($browser['name'] != 'IE') {
            return true;
        }

        if (in_array($browser['version'], array(10, 11))) {
            return true;
        }

        return false;
    }

    public static function detectBrowser()
    {
        $browser = array();

        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if(count($matches)<2){
            preg_match('/Trident\/\d{1,2}.\d{1,2};.*rv:([0-9]*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
        }

        if (count($matches)>1){
            $browser['name'] = 'IE';
            $browser['version'] = $matches[1];
        } else {
            $browser['name'] = 'other';
        }

        return $browser;
    }
} 