<?php

/**
 * Filterform to use filters in combination with CArrayDataProvider and CGridView
 * @see http://www.yiiframework.com/wiki/232/using-filters-with-cgridview-and-carraydataprovider/
 */
class FiltersForm extends CFormModel
{
    /**
     * @var array filters, key => filter string
     */
    public $filters = array();

    /**
     * Override magic getter for filters
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getParam($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function __set($name, $value)
    {
        $this->setParam($name, $value);
    }

    /**
     * Override magic getter for filters
     * @param string $name
     */
    public function getParam($name)
    {
        if (!array_key_exists($name, $this->filters) || strtolower($this->filters[$name]) == 'null') {
            $this->filters[$name] = '';
        }
        return $this->filters[$name];
    }

    /**
     * Override magic setter for filters
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function setParam($name, $value)
    {
        if (strtolower($value) === 'null') {
            $value = '';
        }

        $this->filters[$name] = $value;
        return $this->getParam($name);
    }

    /**
     * Filter input array by key value pairs
     * @param array $data rawData
     * @return array filtered data array
     */
    public function filter(array $data)
    {
        foreach ($data AS $rowIndex => $row) {
            foreach ($this->filters AS $key => $searchValue) {
                if (!is_null($searchValue) && $searchValue !== '' && strtolower($searchValue) !== 'null') {
                    $compareValue = null;

                    if ($row instanceof CModel) {
                        if (isset($row->$key) == false) {
                            throw new CException("Property " . get_class($row) . "::{$key} does not exist!");
                        }
                        $compareValue = $row->$key;
                    } elseif (is_array($row)) {
                        if (!array_key_exists($key, $row)) {
                            throw new CException("Key {$key} does not exist in array!");
                        }
                        $compareValue = $row[$key];
                    } else {
                        throw new CException("Data in CArrayDataProvider must be an array of arrays or an array of CModels!");
                    }

                    // Apply filters if any condition is meet
                    if (substr($searchValue, 0, 2) == '<=') {
                        if ($compareValue > substr($searchValue, 2)) {
                            unset($data[$rowIndex]);
                        }
                    } else if (substr($searchValue, 0, 2) == '>=') {
                        if ($compareValue < substr($searchValue, 2)) {
                            unset($data[$rowIndex]);
                        }
                    } else if ($searchValue[0] == '<') {
                        if ($compareValue >= substr($searchValue, 1)) {
                            unset($data[$rowIndex]);
                        }
                    } else if ($searchValue[0] == '>') {
                        if ($compareValue <= substr($searchValue, 1)) {
                            unset($data[$rowIndex]);
                        }
                    } else if (stripos($compareValue, $searchValue) === false) {
                        unset($data[$rowIndex]);
                    }
                }
            }
        }
        return $data;
    }

    public function getFiltersAsArray() {
        $filtersArray = array();
        foreach ($this->filters AS $key => $searchValue) {
            if ($searchValue !== null && $searchValue !== '' && strtolower($searchValue) !== 'null') {
                $filtersArray[] = "$key = '$searchValue'";
            }
        }
        return $filtersArray;
    }
}