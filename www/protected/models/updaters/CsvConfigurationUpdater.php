<?php
/**
 * Created by PhpStorm.
 * User: Luiixx
 * Date: 29-10-2014
 * Time: 22:34
 */

abstract class CsvConfigurationUpdater {

    /**
     * Should be extended accordingly
     * @var array
     */
    protected static $allowedHeaders = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $errors = array();

    public static function getAllowedHeaders(){
        return static::$allowedHeaders;
    }

    public function getErrors() {
        return $this->errors;
    }

    /**
     * Reads the file and stores the info in the data array
     * @param string $filePath
     * @throws Exception
     */
    public function readFile($filePath)
    {
        $this->data = array();
        $this->errors = array();
        $file = fopen($filePath, 'r');

        try {
            $headerLine = fgetcsv($file);

            $this->validateHeader($headerLine);

            $lineCount = 0;
            while(!feof($file)) {

                $lineCount++;
                $dataLine = fgetcsv($file);

                if (count($headerLine) !== count($dataLine)) {
                    throw new FileParsingException('Line ' . $lineCount . ' doesn\'t have the correct number of arguments');
                }

                $newLine = array();
                foreach($headerLine as $param) {
                    $newLine[$param] = array_shift($dataLine);
                }

                $this->data[] = $newLine;
            }

        } catch (Exception $e) {
            fclose($file);
            throw $e;
        }

        fclose($file);
    }

    /**
     * We can have several types of allowed headers, so we check in all for the received
     * @param $header
     * @throws FileParsingException
     */
    protected function validateHeader($header) {
        // Either we have an array of values or we have a collection of them, so we always assume we have a collection to check
        if (empty(static::$allowedHeaders) || is_array(static::$allowedHeaders[0])) {
            $headersArray = static::$allowedHeaders;
        } else {
            $headersArray = array(static::$allowedHeaders);
        }

        // Now check all collection
        $isAllowed = false;
        foreach ($headersArray as $allowedHeader) {
            if ($header === $allowedHeader) {
                $isAllowed = true;
            }
        }

        if (!$isAllowed) {
            throw new FileParsingException('The uploaded file has the following error: Header mismatch');
        }
    }
}