<?php
/**
 * CsvImportPlus_ColumnMap_Collection class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_Collection extends CsvImportPlus_ColumnMap
{
    private $_collectionId;

    /**
     * @param string $columnName
     * @param integer $collectionId
     */
    public function __construct($columnName, $collectionId = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_COLLECTION;
        $this->_collectionId = (integer) $collectionId;
    }

    /**
     * Map a row to an array that can be parsed by insert_item() or
     * insert_files_for_item().
     *
     * @param array $row The row to map
     * @param array $result
     * @return array|false The result
     */
    public function map($row, $result)
    {
        $collectionIdentifier = trim($row[$this->_columnName]);
        // The collection is determined at row level, according to field of the
        // identifier, so only content of the cell is returned.
        if (empty($collectionIdentifier) && !empty($this->_collectionId)) {
            $collectionIdentifier = $this->_collectionId;
        }
        return $collectionIdentifier;
    }

    /**
     * Return the collection id.
     *
     * @return string The collectionId
     */
    public function getCollectionId()
    {
        return $this->_collectionId;
    }
}
