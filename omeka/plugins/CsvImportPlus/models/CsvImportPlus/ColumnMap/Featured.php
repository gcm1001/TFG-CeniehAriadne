<?php
/**
 * CsvImportPlus_ColumnMap_Featured class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_Featured extends CsvImportPlus_ColumnMap
{
    const DEFAULT_FEATURED = false;

    private $_isFeatured;

    /**
     * @param string $columnName
     * @param $isFeatured
     */
    public function __construct($columnName, $isFeatured = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_FEATURED;
        $filter = new Omeka_Filter_Boolean;
        $this->_isFeatured = is_null($isFeatured)
            ? self::DEFAULT_FEATURED
            : $filter->filter($isFeatured);
    }

    /**
     * Return the featured.
     *
     * @return boolean Featured
     */
    public function getIsFeatured()
    {
        return $this->_isFeatured;
    }

    /**
     * Map a row to whether the row corresponding to a record is featured or not.
     *
     * @param array $row The row to map
     * @param array $result
     * @return bool Whether the row corresponding to a record is featured or not
     */
    public function map($row, $result)
    {
        $filter = new Omeka_Filter_Boolean;
        $flag = strtolower(trim($row[$this->_columnName]));
        // Don't use empty, because the value can be "0".
        if ($flag === '') {
            return $this->_isFeatured;
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
