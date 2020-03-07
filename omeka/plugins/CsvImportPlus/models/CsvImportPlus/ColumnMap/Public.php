<?php
/**
 * CsvImportPlus_ColumnMap_Public class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_Public extends CsvImportPlus_ColumnMap
{
    const DEFAULT_PUBLIC = false;

    private $_isPublic;

    /**
     * @param string $columnName
     * @param boolean $isPublic
     */
    public function __construct($columnName, $isPublic = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_PUBLIC;
        $filter = new Omeka_Filter_Boolean;
        $this->_isPublic = is_null($isPublic)
            ? self::DEFAULT_PUBLIC
            : $filter->filter($isPublic);
    }

    /**
     * Return the public.
     *
     * @return boolean Public
     */
    public function getIsPublic()
    {
        return $this->_isPublic;
    }

    /**
     * Map a row to whether the row corresponding to a record is public or not.
     *
     * @param array $row The row to map
     * @param array $result
     * @return bool Whether the row corresponding to a record is public or not
     */
    public function map($row, $result)
    {
        $filter = new Omeka_Filter_Boolean;
        $flag = strtolower(trim($row[$this->_columnName]));
        // Don't use empty, because the value can be "0".
        if ($flag === '') {
            return $this->_isPublic;
        }
        if ($flag == 'no') {
            return 0;
        }
        if ($flag == 'yes') {
            return 1;
        }
        return $filter->filter($flag);
    }
}
