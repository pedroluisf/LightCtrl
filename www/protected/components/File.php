<?php
/**
 * Created by PhpStorm.
 * User: Luiixx
 * Date: 30-01-2014
 * Time: 23:36
 */

class File {

    public static function getFullPath($file = '', $folder = '') {
        return APP_PATH . $folder . $file;
    }

    public static function getURL($file, $folder = '') {
        return BASE_URL . $folder . $file;
    }

}