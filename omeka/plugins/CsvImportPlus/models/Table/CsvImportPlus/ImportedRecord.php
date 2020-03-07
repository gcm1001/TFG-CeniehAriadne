<?php

/**
 * Table_CsvImportPlus_ImportedRecord class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class Table_CsvImportPlus_ImportedRecord extends Omeka_Db_Table
{
    /**
     * Return the total of imported records of the specified import.
     *
     * @uses Omeka_Db_Table::count()
     *
     * @param int $import_id
     * @return integer
     */
    public function getTotal($import_id)
    {
        return $this->count(array('import_id' => $import_id));
    }
}
