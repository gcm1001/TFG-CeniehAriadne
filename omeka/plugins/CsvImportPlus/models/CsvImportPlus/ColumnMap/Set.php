<?php
/**
 * CsvImportPlus_ColumnMap_Set class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_Set
{
    private $_maps = array();

    /**
     * @param array $maps The array of column mappings.
     */
    public function __construct(array $maps)
    {
        $this->_maps = $maps;
    }

    /**
     * Adds a column map to the set.
     *
     * @param CsvImportPlus_ColumnMap $map The column map
     */
    public function add(CsvImportPlus_ColumnMap $map)
    {
        $this->_maps[] = $map;
    }

    /**
     * Map a row to an associative array of mappings indexed by column mapping
     * type, and where each mapping can be parsed by insert_item() or
     * insert_files_for_item().
     *
     * @param array $row The row to map
     * @return array The associative array of mappings
     */
    public function map(array $row)
    {
        $allResults = array(
            CsvImportPlus_ColumnMap::TYPE_ACTION => null,
            CsvImportPlus_ColumnMap::TYPE_IDENTIFIER => null,
            CsvImportPlus_ColumnMap::TYPE_IDENTIFIER_FIELD => null,
            CsvImportPlus_ColumnMap::TYPE_RECORD_TYPE => null,
            CsvImportPlus_ColumnMap::TYPE_ITEM => null,
            CsvImportPlus_ColumnMap::TYPE_ITEM_TYPE => null,
            CsvImportPlus_ColumnMap::TYPE_COLLECTION => null,
            CsvImportPlus_ColumnMap::TYPE_PUBLIC => null,
            CsvImportPlus_ColumnMap::TYPE_FEATURED => null,
            CsvImportPlus_ColumnMap::TYPE_ELEMENT => array(),
            CsvImportPlus_ColumnMap::TYPE_EXTRA_DATA => array(),
            CsvImportPlus_ColumnMap::TYPE_TAG => array(),
            CsvImportPlus_ColumnMap::TYPE_FILE => null,
        );
        foreach ($this->_maps as $map) {
            $subset = $allResults[$map->getType()];
            $allResults[$map->getType()] = $map->map($row, $subset);
        }
        return $allResults;
    }
}
