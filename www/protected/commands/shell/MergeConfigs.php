<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 20-01-2015
 * Time: 23:00
 */

class MergeConfigs {

    protected $configFolder;
    protected $configTempFolder;

    public function __construct(){
        $this->configFolder = realpath(dirname(__FILE__).'/../../config') . '/';
        $this->configTempFolder = $this->configFolder.'temp/';
    }

    public function run() {
        $tempConfigFiles = $this->getTempConfigFiles();

        foreach ($tempConfigFiles as $configFile) {
            if ($this->configFileExists($configFile)){
                $configs = $this->mergeConfig($configFile);
            } else {
                $configs = require($this->configTempFolder.$configFile);
            }
            $this->saveConfig($configs, basename($configFile));
        }

        $this->deleteTempAssets();
    }

    /**
     * Read all the files in the temporary directory
     * @return array
     */
    protected function getTempConfigFiles(){
        return array_diff(scandir($this->configTempFolder), array('..', '.'));
    }

    /**
     * Validates the existence of the original Config File
     * @param $configFile
     * @return bool
     */
    protected function configFileExists($configFile){
        return is_file($this->configFolder.$configFile) && is_readable($this->configFolder.$configFile);
    }

    /**
     * Merge the existing configs with the new ones, without losing the original values
     * @param $configFile
     * @return array|mixed
     */
    protected function mergeConfig($configFile) {
        $file2Merge = basename($configFile);
        return $this->mergeArray(
            require($this->configTempFolder.$file2Merge),
            require($this->configFolder.$file2Merge)
        );

    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array only if they are not the same.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     * @see mergeWith
     */
    protected function mergeArray($a,$b)
    {
        $args=func_get_args();
        $res=array_shift($args);
        while(!empty($args))
        {
            $next=array_shift($args);
            foreach($next as $k => $v)
            {
                if(is_integer($k)){
                    if (!in_array($v, $res)) {
                        $res[]=$v;
                    }
                }
                elseif(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    $res[$k]=self::mergeArray($res[$k],$v);
                else
                    $res[$k]=$v;
            }
        }
        return $res;
    }

    /**
     * Saves the config file
     */
    protected function saveConfig($config, $filename) {
        $content = $this->getFileHeader() . PHP_EOL;
        $content .= 'return ' . var_export($config, true) . ';' . PHP_EOL;

        file_put_contents($this->configFolder.$filename, $content);
    }

    /**
     * Prepares and returns the file header
     */
    protected function getFileHeader() {
        return "
<?php
    require_once( dirname(__FILE__) . '/../components/Interfaces.php');
    require_once(dirname(__FILE__) . '/../components/CustomExceptions.php');
";
    }

    /**
     * Removes all Temp files and directory
     */
    protected function deleteTempAssets() {
        $tempConfigFiles = $this->getTempConfigFiles();

        foreach ($tempConfigFiles as $configFile) {
            unlink($this->configTempFolder . $configFile);
        }

        rmdir($this->configTempFolder);
    }

}

$mergerTool = new MergeConfigs();
$mergerTool->run();

