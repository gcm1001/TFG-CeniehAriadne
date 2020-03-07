<?php
/**
 * CsvImportPlus_ColumnMap_ExtraData class
 *
 * Same as MixElement, but for extra data that are not elements.
 *
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_ExtraData extends CsvImportPlus_ColumnMap
{
    const DEFAULT_COLUMN_NAME_DELIMITER = ':';
    const ELEMENT_DELIMITER_OPTION_NAME = 'csv_import_plus_element_delimiter';
    const DEFAULT_ELEMENT_DELIMITER = "\r";

    private $_columnNameDelimiter;
    private $_elementDelimiter;
    private $_postKey;
    private $_isDataMultivalued;

    /**
     * @param string $columnName
     * @param string $elementDelimiter
     */
    public function __construct($columnName, $elementDelimiter = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_EXTRA_DATA;
        $this->_columnNameDelimiter = self::DEFAULT_COLUMN_NAME_DELIMITER;

        $this->_elementDelimiter = $elementDelimiter === null
            ? self::getDefaultElementDelimiter()
            : $elementDelimiter;

        $this->_postKey = $this->_getPostKeyFromColumnName();
        $this->_isDataMultivalued = $this->_isColumnMultivalued();
    }

    /**
     * Map a row to an array that can be parsed via post data.
     *
     * @param array $row The row to map
     * @param array $result
     * @return array The result
     */
    public function map($row, $result)
    {
        if (empty($this->_postKey)) {
            return $result;
        };

        // Post will be verified via the filterPostData().
        $value = $row[$this->_columnName];
        if ($this->_isDataMultivalued) {
            $value = explode($this->_elementDelimiter, $value);
        }

        return $this->_updateNestedArray($result, $this->_postKey, $value);
    }

    /**
     * Sets the mapping options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        if (isset($options['columnNameDelimiter'])) {
            $this->_columnNameDelimiter = $options['columnNameDelimiter'];
        }
        if (isset($options['elementDelimiter'])) {
            $this->_elementDelimiter = $options['elementDelimiter'];
        }
        if (isset($options['postKey'])) {
            $this->_postKey = $options['postKey'];
        }
        $this->_isDataMultivalued = $this->_isColumnMultivalued();
    }

    /**
     * Return the column name delimiter.
     *
     * @return string The column name delimiter
     */
    public function getColumnNameDelimiter()
    {
        return $this->_columnNameDelimiter;
    }

    /**
     * Return the element delimiter.
     *
     * @return string The element delimiter
     */
    public function getElementDelimiter()
    {
        return $this->_elementDelimiter;
    }

    /**
     * Return the post key.
     *
     * @return array The post key
     */
    public function getPostKey()
    {
        return $this->_postKey;
    }

    /**
     * Return the post key, i.e. list of non empty subkeys of the column name.
     *
     * @return array The cleaned post key of the column.
     */
    protected function _getPostKeyFromColumnName()
    {
        return empty($this->_columnNameDelimiter)
            ? array(trim($this->_columnName))
            : array_filter(array_map('trim', explode($this->_columnNameDelimiter, $this->_columnName)));
    }

    /**
     * Check if the data is multivalued.
     *
     * @internal When the name is finished with the delimiter, it means that is
     * a multivalued data.
     *
     * @return array The cleaned post key of the column.
     */
    protected function _isColumnMultivalued()
    {
        return $this->_elementDelimiter !== ''
            && substr($this->_columnName, -1) == $this->getColumnNameDelimiter();
    }

    /**
     * Returns the default element delimiter.
     * Uses the default element delimiter specified in the options table if
     * available.
     *
     * @return string The default element delimiter
     */
    static public function getDefaultElementDelimiter()
    {
        if (!($delimiter = get_option(self::ELEMENT_DELIMITER_OPTION_NAME))) {
            $delimiter = self::DEFAULT_ELEMENT_DELIMITER;
        }
        return $delimiter;
    }

    /**
     * Add a value to a nested array into a position defined by an array.
     *
     * @example array('geolocation', 'latitude') and '45' give array('geolocation' => array('latitude' => '45'))
     *
     * @param array $array The nested array to update.
     * @param array $position Flat array that indicates the position.
     * @param var @toAdd Value to add at the specified position.
     * @return array Updated nested array
     */
    private function _updateNestedArray(array $array, array $position, $toAdd)
    {
        $target = &$array;
        foreach ($position as $value) {
            $target = &$target[$value];
        }
        $target = $toAdd;
        return $array;
    }
}
