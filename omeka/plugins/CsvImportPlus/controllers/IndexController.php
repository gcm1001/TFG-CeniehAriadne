<?php
/**
 * CsvImportPlus_IndexController class - represents the Csv Import index controller
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = 10;
    protected $_pluginConfig = array();

    /**
     * Initialize the controller.
     */
    public function init()
    {
        $this->session = new Zend_Session_Namespace('CsvImportPlus');
        $this->_helper->db->setDefaultModelName('CsvImportPlus_Import');
    }

    /**
     * Configure a new import (first step).
     */
    public function indexAction()
    {
        $form = $this->_getMainForm();
        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->_helper->flashMessenger(__('Invalid form input. Please see errors below and try again.'), 'error');
            return;
        }

        if (!$form->csv_file->receive()) {
            $this->_helper->flashMessenger(__('Error uploading file. Please try again.'), 'error');
            return;
        }

        $filePath = $form->csv_file->getFileName();
        $delimitersList = self::getDelimitersList();
        $columnDelimiterName = $form->getValue('column_delimiter_name');
        $columnDelimiter = isset($delimitersList[$columnDelimiterName])
            ? $delimitersList[$columnDelimiterName]
            : $form->getValue('column_delimiter');
        $enclosuresList = self::getEnclosuresList();
        $enclosureName = $form->getValue('enclosure_name');
        $enclosure = isset($enclosuresList[$enclosureName])
            ? $enclosuresList[$enclosureName]
            : $form->getValue('enclosure');

        $file = new CsvImportPlus_File($filePath, $columnDelimiter, $enclosure);

        if (!$file->parse()) {
            $this->_helper->flashMessenger(__('Your file is incorrectly formatted.')
                . ' ' . $file->getErrorString(), 'error');
            return;
        }

        $identifierField = $this->_elementNameFromPost($form->getValue('identifier_field'));

        $this->session->setExpirationHops(3);
        $this->session->originalFilename = $_FILES['csv_file']['name'];
        $this->session->filePath = $filePath;
        $this->session->action = $form->getValue('action');
        $this->session->identifierField = $identifierField;
        $this->session->itemTypeId = $form->getValue('item_type_id');
        $this->session->collectionId = $form->getValue('collection_id');
        $this->session->recordsArePublic = $form->getValue('records_are_public');
        $this->session->recordsAreFeatured = $form->getValue('records_are_featured');
        $this->session->elementsAreHtml = $form->getValue('elements_are_html');
        $this->session->containsExtraData = $form->getValue('contains_extra_data');
        $this->session->columnDelimiter = $columnDelimiter;
        $this->session->enclosure = $enclosure;
        $this->session->columnNames = $file->getColumnNames();
        $this->session->columnExamples = $file->getColumnExamples();
        // A bug appears when examples contain UTF-8 characters like 'ГЧ„чŁ'.
        // The bug is only here, not during import of characters into database.
        foreach ($this->session->columnExamples as &$value) {
            $value = iconv('ISO-8859-15', 'UTF-8', @iconv('UTF-8', 'ISO-8859-15' . '//IGNORE', $value));
        }

        $elementDelimiterName = $form->getValue('element_delimiter_name');
        $this->session->elementDelimiter = isset($delimitersList[$elementDelimiterName])
            ? $delimitersList[$elementDelimiterName]
            : $form->getValue('element_delimiter');
        $tagDelimiterName = $form->getValue('tag_delimiter_name');
        $this->session->tagDelimiter = isset($delimitersList[$tagDelimiterName])
            ? $delimitersList[$tagDelimiterName]
            : $form->getValue('tag_delimiter');
        $fileDelimiterName = $form->getValue('file_delimiter_name');
        $this->session->fileDelimiter = isset($delimitersList[$fileDelimiterName])
            ? $delimitersList[$fileDelimiterName]
            : $form->getValue('file_delimiter');

        $this->session->ownerId = $this->getInvokeArg('bootstrap')->currentuser->id;

        // All is valid, so we save settings.
        set_option(CsvImportPlus_ColumnMap_IdentifierField::IDENTIFIER_FIELD_OPTION_NAME, $this->session->identifierField);
        set_option(CsvImportPlus_RowIterator::COLUMN_DELIMITER_OPTION_NAME, $this->session->columnDelimiter);
        set_option(CsvImportPlus_RowIterator::ENCLOSURE_OPTION_NAME, $this->session->enclosure);
        set_option(CsvImportPlus_ColumnMap_Element::ELEMENT_DELIMITER_OPTION_NAME, $this->session->elementDelimiter);
        set_option(CsvImportPlus_ColumnMap_Tag::TAG_DELIMITER_OPTION_NAME, $this->session->tagDelimiter);
        set_option(CsvImportPlus_ColumnMap_File::FILE_DELIMITER_OPTION_NAME, $this->session->fileDelimiter);
        set_option('csv_import_plus_html_elements', $this->session->elementsAreHtml);
        set_option('csv_import_plus_extra_data', $this->session->containsExtraData);

        if ($this->session->containsExtraData == 'manual') {
            $this->_helper->redirector->goto('map-columns');
        }

        $this->_helper->redirector->goto('check-manage-csv');
    }

    /**
     * Map the columns for an import (second step if needed or wished).
     */
    public function mapColumnsAction()
    {
        if (!$this->_sessionIsValid()) {
            $this->_helper->flashMessenger(__('Import settings expired. Please try again.'), 'error');
            $this->_helper->redirector->goto('index');
            return;
        }

        $parameters = array(
            'columnNames' => $this->session->columnNames,
            'columnExamples' => $this->session->columnExamples,
            'elementDelimiter' => $this->session->elementDelimiter,
            'tagDelimiter' => $this->session->tagDelimiter,
            'fileDelimiter' => $this->session->fileDelimiter,
            'itemTypeId' => $this->session->itemTypeId,
            'collectionId' => $this->session->collectionId,
            'isPublic' => $this->session->recordsArePublic,
            'isFeatured' => $this->session->recordsAreFeatured,
            'elementsAreHtml' => $this->session->elementsAreHtml,
            'action' => $this->session->action,
            'identifierField' => $this->session->identifierField,
        );

        $form = new CsvImportPlus_Form_Mapping($parameters);
        if (!$form) {
            $this->_helper->flashMessenger(__('Invalid form input. Please try again.'), 'error');
            $this->_helper->redirector->goto('index');
        }
        $this->view->form = $form;
        $this->view->csvFile = basename($this->session->originalFilename);

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->_helper->flashMessenger(__('Invalid form input. Please try again.'), 'error');
            return;
        }

        $columnMaps = $form->getColumnMaps();
        if (count($columnMaps) == 0) {
            $this->_helper->flashMessenger(__('Please map at least one column to an element, file, or tag.'), 'error');
            return;
        }

        // Check if there is an identifier column.
        $isSetIdentifier = false;
        foreach ($columnMaps as $columnMap) {
            if ($columnMap instanceof CsvImportPlus_ColumnMap_Identifier) {
                $isSetIdentifier = true;
                break;
            }
        }
        if (!$isSetIdentifier) {
            $this->_helper->flashMessenger(__('Please map a column to the special value "Identifier".'), 'error');
            return;
        }

        $this->session->columnMaps = $columnMaps;

        $this->_launchImport();
    }

    /**
     * For management of records.
     */
    public function checkManageCsvAction()
    {
        $skipColumns = array(
            'Action',
            'Identifier',
            'IdentifierField',
            'RecordType',
            'Collection',
            'Item',
            'File',
            'ItemType',
            'Public',
            'Featured',
            'Tags',
        );
        // There is no required column: identifier can be any field.
        $requiredColumns = array();
        // No more.
        $forbiddenColumns = array();
        $this->_checkCsv($skipColumns, $requiredColumns, $forbiddenColumns);
    }

    /**
     * For direct import of a file from Omeka.net or with mixed records.
     * Check if all needed Elements are present.
     */
    protected function _checkCsv(
        array $skipColumns = array(),
        array $requiredColumns = array(),
        array $forbiddenColumns = array()
    ) {
        if (empty($this->session->columnNames)) {
            $this->_helper->redirector->goto('index');
        }

        $elementTable = get_db()->getTable('Element');

        $skipColumnsWrapped = array();
        foreach ($skipColumns as $skipColumn) {
            $skipColumnsWrapped[] = "'" . $skipColumn . "'";
        }
        $skipColumnsText = '( ' . implode(', ', $skipColumnsWrapped) . ' )';

        $hasError = false;

        // Check required columns.
        foreach ($this->session->columnNames as $columnName) {
            $required = array_search($columnName, $requiredColumns);
            if ($required !== false) {
                unset($requiredColumns[$required]);
            }
        }
        if (!empty($requiredColumns)) {
            $msg = __('Columns "%s" are required.', implode('", "', $requiredColumns));
            $this->_helper->flashMessenger($msg, 'error');
            $hasError = true;
        }

        // Check forbidden columns.
        $forbiddenColumnsCheck = array();
        foreach ($this->session->columnNames as $columnName) {
            $forbidden = array_search($columnName, $forbiddenColumns);
            if ($forbidden !== false) {
                $forbiddenColumnsCheck[] = $columnName;
            }
        }
        if (!empty($forbiddenColumnsCheck)) {
            $msg = __('Columns "%s" are forbidden.', implode('", "', $forbiddenColumnsCheck));
            $this->_helper->flashMessenger($msg, 'error');
            $hasError = true;
        }

        // The column from the IdentfierField is required when there is no
        // IdentifierField column (else the check is done during import).
        if (!in_array('Identifier', $this->session->columnNames)
                && !in_array('IdentifierField', $this->session->columnNames)
            ) {
            $identifierField = $this->session->identifierField;
            if (empty($identifierField)) {
                $msg = __('There is no "IdentifierField" column or a default identifier field.', $this->session->identifierField);
                $this->_helper->flashMessenger($msg, 'error');
                $hasError = true;
            }
            elseif ($identifierField != 'table id' && $identifierField != 'internal id') {
                $elementField = $identifierField;
                if (is_numeric($identifierField)) {
                    $element = get_record_by_id('Element', $identifierField);
                    if (!$element) {
                        $msg = __('The identifier field "%s" does not exist.', $this->session->identifierField);
                        $this->_helper->flashMessenger($msg, 'error');
                        $hasError = true;
                    }
                    else {
                        $elementField = $element->set_name . ':' . $element->name;
                    }
                }
                if (!in_array($elementField, $this->session->columnNames)) {
                    $msg = __('There is no "IdentifierField" column or the default "%s" column.', $elementField);
                    $this->_helper->flashMessenger($msg, 'error');
                    $hasError = true;
                }
            }
        }

        if ($hasError) {
            $msg = __('The file has error, or parameters are not adapted. Check them.');
            $this->_helper->flashMessenger($msg, 'info');
            $this->_helper->redirector->goto('index');
        }

        $this->_helper->redirector->goto('omeka-csv', 'index', 'csv-import-plus');
    }

    /**
     * Create and queue a new import from Omeka.net or with mixed records.
     */
    public function omekaCsvAction()
    {
        $elementDelimiter = $this->session->elementDelimiter;
        $tagDelimiter = $this->session->tagDelimiter;
        $fileDelimiter = $this->session->fileDelimiter;
        $itemTypeId = $this->session->itemTypeId;
        $collectionId = $this->session->collectionId;
        $isPublic = $this->session->recordsArePublic;
        $isFeatured = $this->session->recordsAreFeatured;
        $isHtml = $this->session->elementsAreHtml;
        $identifierField = $this->session->identifierField;
        $action = $this->session->action;
        $containsExtraData = $this->session->containsExtraData;
        if ($containsExtraData == 'manual') {
            $containsExtraData = 'no';
        }

        $headings = $this->session->columnNames;
        $columnMaps = array();
        $isSetIdentifier = false;
        $unknowColumns = array();
        foreach ($headings as $heading) {
            switch ($heading) {
                case 'Identifier':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Identifier($heading);
                    $isSetIdentifier = true;
                    break;
                case 'Action':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Action($heading, $action);
                    break;
                case 'Identifier Field':
                case 'IdentifierField':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_IdentifierField($heading, $identifierField);
                    break;
                case 'Record Type':
                case 'RecordType':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_RecordType($heading);
                    break;
                case 'Item Type':
                case 'ItemType':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_ItemType($heading, $itemTypeId);
                    break;
                case 'Item':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Item($heading);
                    break;
                case 'Collection':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Collection($heading, $collectionId);
                    break;
                case 'Public':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Public($heading, $isPublic);
                    break;
                case 'Featured':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Featured($heading, $isFeatured);
                    break;
                case 'Tags':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_Tag($heading, $tagDelimiter);
                    break;
                case 'File':
                case 'Files':
                    $columnMaps[] = new CsvImportPlus_ColumnMap_File($heading, $fileDelimiter);
                    break;
                // Default can be a normal element or, if not, an extra data
                // element that can be added via the hook csv_import_plus_extra_data.
                default:
                    // Here, column names are already checked.
                    $columnMap = new CsvImportPlus_ColumnMap_MixElement($heading, $elementDelimiter);
                    // If this is an element.
                    $columnElementId = $columnMap->getElementId();
                    $options = array();
                    if ($columnElementId) {
                        $options = array(
                            'columnNameDelimiter' => $columnMap::DEFAULT_COLUMN_NAME_DELIMITER,
                            'elementDelimiter' => $elementDelimiter,
                            'isHtml' => $isHtml,
                        );
                    }
                    // Allow extra data when this is not a true element.
                    elseif ($containsExtraData == 'yes') {
                        $columnMap = new CsvImportPlus_ColumnMap_ExtraData($heading, $elementDelimiter);
                        $options = array(
                            'columnNameDelimiter' => $columnMap::DEFAULT_COLUMN_NAME_DELIMITER,
                            'elementDelimiter' => $elementDelimiter,
                        );
                    }
                    // Illegal unknown column.
                    elseif ($containsExtraData == 'no') {
                        $unknowColumns[] = $heading;
                    }
                    // Else ignore the column.

                    // Memorize the identifier if needed, after cleaning.
                    if ($isSetIdentifier === false) {
                        $cleanHeading = explode(
                            CsvImportPlus_ColumnMap_MixElement::DEFAULT_COLUMN_NAME_DELIMITER,
                            $heading);
                        $cleanHeading = implode(CsvImportPlus_ColumnMap_MixElement::DEFAULT_COLUMN_NAME_DELIMITER,
                            array_map('trim', $cleanHeading));
                        if ($identifierField == $cleanHeading) {
                            $isSetIdentifier = null;
                            $identifierHeading = $heading;
                        }
                    }
                    $columnMap->setOptions($options);
                    $columnMaps[] = $columnMap;
                    break;
            }
        }

        if ($unknowColumns) {
            $msg = __('Columns "%s" are unknown.', implode('", "', $unknowColumns));
            $this->_helper->flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('index');
        }

        // A column for identifier is required. It can be any column, specially
        // Dublin Core:Identifier.
        if ($isSetIdentifier === null) {
            $columnMaps[] = new CsvImportPlus_ColumnMap_Identifier($identifierHeading);
            $isSetIdentifier = true;
        }
        if (!$isSetIdentifier) {
            $msg = __('There is no "Identifier" or identifier field "%s" column.', $identifierField);
            $this->_helper->flashMessenger($msg, 'error');
            $this->_helper->redirector->goto('index');
        }

        $this->session->columnMaps = $columnMaps;

        $this->_launchImport();
    }

    /**
     * Set default values in the case where a needed column doesn't exist.
     *
     * This doesn't include default values that are set directly in column maps
     * and that are useless without them (i.e. isHtml is set with Element).
     */
    protected function _setDefaultValues()
    {
        $defaultValues = array();
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_ITEM_TYPE] = $this->session->itemTypeId;
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_COLLECTION] = $this->session->collectionId;
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_PUBLIC] = $this->session->recordsArePublic;
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_FEATURED] = $this->session->recordsAreFeatured;
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_ACTION] = $this->session->action;
        $defaultValues[CsvImportPlus_ColumnMap::TYPE_IDENTIFIER_FIELD] = $this->session->identifierField;

        // This value is not a value of the table, but is used to keep track of
        // the current loop with Amazon S3. See CsvImport_ImportTask::perform().
        $defaultValues['amazonS3CurrentLoop'] = 0;

        $this->session->defaultValues = $defaultValues;
    }

    /**
     * Save the format in base and launch the job.
     */
    protected function _launchImport()
    {
        $csvImport = new CsvImportPlus_Import();

        $this->_setDefaultValues();

        // This is the clever way that mapColumns action sets the values passed
        // along from indexAction. Many will be irrelevant here, since CsvImport
        // allows variable itemTypes and Collection

        // @TODO: check if variable itemTypes and Collections breaks undo. It probably should, actually
        foreach ($this->session->getIterator() as $key => $value) {
            $setMethod = 'set' . ucwords($key);
            if (method_exists($csvImport, $setMethod)) {
                $csvImport->$setMethod($value);
            }
        }

        if ($csvImport->queue()) {
            $this->_dispatchImportTask($csvImport, CsvImportPlus_ImportTask::METHOD_START);
            $this->_helper->flashMessenger(__('Import started. Reload this page for status updates.'), 'success');
        }
        else {
            $this->_helper->flashMessenger(__('Import could not be started. Please check error logs for more details.'), 'error');
        }
        $this->session->unsetAll();
        $this->_helper->redirector->goto('browse');
    }

    /**
     * Browse the imports.
     */
    public function browseAction()
    {
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }
        parent::browseAction();
    }

    /**
     * Stop a process queued or in progress.
     */
    public function stopProcessAction()
    {
        $csvImport = $this->_helper->db->findById();
        if ($csvImport->isQueuedOrProcessing()) {
            $result = $csvImport->stopProcess();
            $this->_helper->flashMessenger(__('The process is stopping.'), 'success');
        } else {
            $this->_helper->flashMessenger(__('The process cannot be stopped because it is not queued or in progress.'), 'error');
        }

        $this->_helper->redirector->goto('browse');
    }

    /**
     * Undo the import.
     */
    public function undoImportAction()
    {
        $csvImport = $this->_helper->db->findById();
        if ($csvImport->queueUndo()) {
            $this->_dispatchImportTask($csvImport, CsvImportPlus_ImportTask::METHOD_UNDO);
            $this->_helper->flashMessenger(__('Undo import started. Reload this page for status updates.'), 'success');
        } else {
            $this->_helper->flashMessenger(__('Undo import could not be started. Please check error logs for more details.'), 'error');
        }

        $this->_helper->redirector->goto('browse');
    }

    public function logsAction()
    {
        $db = $this->_helper->db;
        $csvImport = $db->findById();
        $logs = $db->getTable('CsvImportPlus_Log')->findByImportId($csvImport->id);

        $this->view->csvImport = $csvImport;
        $this->view->logs = $logs;
    }

    /**
     * Clear the import history.
     */
    public function clearHistoryAction()
    {
        $csvImport = $this->_helper->db->findById();
        $importedRecordCount = $csvImport->getImportedRecordCount();

        if ($csvImport->isUndone()
            || $csvImport->isUndoImportError()
            || $csvImport->isOtherError()
            || ($csvImport->isImportError() && $importedRecordCount == 0)) {
            $csvImport->delete();
            $this->_helper->flashMessenger(__('Cleared import from the history.'), 'success');
        } else {
            $this->_helper->flashMessenger(__('An error occurs during import: Cannot clear import history.'), 'error');
        }
        $this->_helper->redirector->goto('browse');
    }

    /**
     * Get the main Csv Import form.
     *
     * @return CsvImportPlus_Form_Main
     */
    protected function _getMainForm()
    {
        $csvConfig = $this->_getPluginConfig();
        $form = new CsvImportPlus_Form_Main($csvConfig);
        return $form;
    }

    /**
     * Returns the plugin configuration.
     *
     * @return array
     */
    protected function _getPluginConfig()
    {
        if (!$this->_pluginConfig) {
            $config = $this->getInvokeArg('bootstrap')->config->plugins;
            if ($config && isset($config->CsvImportPlus)) {
                $this->_pluginConfig = $config->CsvImportPlus->toArray();
            }
            if (!array_key_exists('fileDestination', $this->_pluginConfig)) {
                $this->_pluginConfig['fileDestination'] =
                    Zend_Registry::get('storage')->getTempDir();
            }
        }
        return $this->_pluginConfig;
    }

    /**
     * Convert Identifier field to name.
     *
     * It's simpler to manage identifiers by name, as they are in csv files.
     *
     * @param integer|string $postIdentifier
     * @return string
     */
    protected function _elementNameFromPost($postIdentifier)
    {
        $postIdentifier = trim($postIdentifier);
        if (empty($postIdentifier) || !is_numeric($postIdentifier)) {
            return $postIdentifier;
        }
        $element = get_record_by_id('Element', $postIdentifier);
        if (!$element) {
            return $postIdentifier;
        }
        return $element->set_name . ':' . $element->name;
    }

    /**
     * Returns whether the session is valid.
     *
     * @return boolean
     */
    protected function _sessionIsValid()
    {
        $requiredKeys = array(
            'itemTypeId',
            'collectionId',
            'recordsArePublic',
            'recordsAreFeatured',
            'elementsAreHtml',
            'containsExtraData',
            'ownerId',
        );
        foreach ($requiredKeys as $key) {
            if (!isset($this->session->$key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Dispatch an import task.
     *
     * @param CsvImportPlus_Import $csvImport The import object
     * @param string $method The method name to run in the CsvImportPlus_Import object
     */
    protected function _dispatchImportTask($csvImport, $method = null)
    {
        if ($method === null) {
            $method = CsvImportPlus_ImportTask::METHOD_START;
        }
        $csvConfig = $this->_getPluginConfig();

        $options = array(
            'importId' => $csvImport->id,
            'memoryLimit' => @$csvConfig['memoryLimit'],
            'batchSize' => @$csvConfig['batchSize'],
            'method' => $method,
        );

        $jobDispatcher = Zend_Registry::get('job_dispatcher');
        $jobDispatcher->setQueueName(CsvImportPlus_ImportTask::QUEUE_NAME);
        try {
            $jobDispatcher->sendLongRunning('CsvImportPlus_ImportTask', $options);
        } catch (Exception $e) {
            $csvImport->setStatus(CsvImportPlus_Import::STATUS_OTHER_ERROR);
            $csvImport->save();
            throw $e;
        }
    }

    /**
     * Return the list of standard delimiters.
     *
     * @return array The list of standard delimiters.
     */
    public static function getDelimitersList()
    {
        return array(
            'comma' => ',',
            'semi-colon' => ';',
            'colon' => ':',
            'pipe' => '|',
            'tabulation' => "\t",
            'carriage return' => "\r",
            'space' => ' ',
            'double space' => '  ',
            'empty' => '',
        );
    }

    /**
     * Return the list of standard enclosures.
     *
     * @return array The list of standard enclosures.
     */
    public static function getEnclosuresList()
    {
        return array(
            'double-quote' => '"',
            'quote' => "'",
            'hash' => '#',
            'empty' => '',
        );
    }
}
