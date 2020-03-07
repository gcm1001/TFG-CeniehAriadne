<?php
/**
 * CsvImportPlus_ColumnMap_IdentifierField class
 *
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_IdentifierField extends CsvImportPlus_ColumnMap
{
    const IDENTIFIER_FIELD_OPTION_NAME = 'csv_import_plus_identifier_field';
    const DEFAULT_IDENTIFIER_FIELD = 'table id';

    private $_identifierField;

    /**
     * @param string $columnName
     */
    public function __construct($columnName, $identiferField = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_IDENTIFIER_FIELD;

        $this->_identifierField = empty($identiferField)
            ? $this->getDefaultIdentifierField()
            : $identiferField;
    }

    /**
     * Map a row to the identifier field of a record (table, internal id or
     * specified field).
     *
     * @param array $row The row to map
     * @param array $result
     * @return string Identifier field of a record.
     */
    public function map($row, $result)
    {
        $result = trim($row[$this->_columnName]);
        return empty($result)
            ? $this->_identifierField
            : $result;
    }

    /**
     * Return the identifier field.
     *
     * @return string The identifier field
     */
    public function getIdentifierField()
    {
        return $this->_identifierField;
    }

    /**
     * Returns the default identifier field.
     * Uses the default identifier field specified in the options table if
     * available.
     *
     * @return string The default identifier field
     */
    static public function getDefaultIdentifierField()
    {
        if (!($identifierField = get_option(self::IDENTIFIER_FIELD_OPTION_NAME))) {
            $identifierField = self::DEFAULT_IDENTIFIER_FIELD;
        }
        return $identifierField;
    }
}
