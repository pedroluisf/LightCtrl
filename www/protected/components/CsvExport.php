<?php
/**
CsvExport

helper class to output a CSV

 */
class CsvExport {

    public $filename = null;
    public $reportHeader = null;
    public $columnHeaders = null;
    public $fieldsToExport = null;
    public $delimiter = ',';

    public function export($rows)
    {
        $this->outputHeaders();
        $this->printCustomHeader();
        $this->printColumnHeaders();
            $this->printRows($rows);
    }

    protected function outputHeaders(){
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$this->filename);
        header("Content-Transfer-Encoding: binary");
    }

    protected function printCustomHeader(){
        if ($this->reportHeader) {
            echo $this->reportHeader;
        }
    }

    protected function printColumnHeaders(){
        if ($this->columnHeaders) {
            echo implode($this->delimiter, $this->columnHeaders).PHP_EOL;
        }
    }

    protected function printRows($rows){
        if (empty($this->fieldsToExport)){
            throw new Exception('No Columns supplied for CSV Export');
        }
        foreach ($rows as $row){
            $fields = array();
            foreach ($this->fieldsToExport as $field){
                $fields[] = $row[$field];
            }
            echo implode($this->delimiter, $fields) . PHP_EOL;
        }
    }
}