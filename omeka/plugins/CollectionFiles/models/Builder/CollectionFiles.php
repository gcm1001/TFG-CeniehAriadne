<?php
/**
 * Omeka
 *  > Adapted by Gonzalo Cuesta.
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Build a collection file.
 *
 */
class Builder_CollectionFiles extends Omeka_Record_Builder_AbstractBuilder
{
    const OWNER_ID = 'owner_id';
    const IS_PUBLIC = 'public';
    const IS_FEATURED = 'featured';
    const FILES = 'files';
    const FILE_TRANSFER_TYPE = 'file_transfer_type';
    const FILE_INGEST_OPTIONS = 'file_ingest_options';
    const FILE_INGEST_VALIDATORS_FILTER = 'file_ingest_validators';
    const OVERWRITE_ELEMENT_TEXTS = 'overwriteElementTexts';
    
    protected $_recordClass = 'Collection';
    protected $_settableProperties = array(
        self::OWNER_ID,
        self::IS_PUBLIC,
        self::IS_FEATURED
    );
    
    private $_elementTexts = array();
    private $_fileMetadata = array();
    
    /**
     * Set the element texts for the collection.
     *
     * @param array $elementTexts
     */
    public function setElementTexts(array $elementTexts)
    {
        $this->_elementTexts = $elementTexts;
    }
    
    /**
     * Set the file metadata for the collection.
     *
     * @param array $fileMetadata
     */
    public function setFileMetadata(array $fileMetadata)
    {
        $this->_fileMetadata = $fileMetadata;
    }
    
    
    /**
     * Add element texts to a record.
     */
    private function _addElementTexts()
    {
        return $this->_record->addElementTextsByArray($this->_elementTexts);
    }
    
    /**
     * Replace all the element texts for existing element texts.
     */
    private function _replaceElementTexts()
    {
        $collection = $this->_record;
        // If this option is set, it will loop through the $elementTexts provided,
        // find each one and manually update it (provided it exists).
        // The rest of the element texts will get added as per usual.
        foreach ($this->_elementTexts as $elementSetName => $textArray) {
            foreach ($textArray as $elementName => $elementTextSet) {
                $etRecordSet = $collection->getElementTexts($elementSetName, $elementName);
                foreach ($elementTextSet as $elementTextIndex => $textAttr) {
                    // If we have an existing ElementText record, use that
                    // instead of adding a new one.
                    if (array_key_exists($elementTextIndex, $etRecordSet)) {
                        $etRecord = $etRecordSet[$elementTextIndex];
                        $etRecord->text = $textAttr['text'];
                        $etRecord->html = $textAttr['html'];
                        $etRecord->save();
                    } else {
                        // Otherwise we should just append the new text to the
                        // pre-existing ones.
                        $elementRecord = $collection->getElement($elementSetName, $elementName);
                        $collection->addTextForElement($elementRecord, $textAttr['text'], $textAttr['html']);
                    }
                }
            }
        }
    }
    
    /**
     * Add elements associated with the collection.
     *
     * @param Omeka_Record_AbstractRecord $record The collection record
     */
    protected function _beforeBuild(Omeka_Record_AbstractRecord $record)
    {
        $metadata = $this->getRecordMetadata();
        
        if ($this->_record->exists() and array_key_exists(self::OVERWRITE_ELEMENT_TEXTS, $metadata)) {
            $this->_replaceElementTexts();
            
        } else {
            $this->_addElementTexts();
        }
        
        if (array_key_exists(self::FILES, $this->_fileMetadata)) {
            if (!array_key_exists(self::FILE_TRANSFER_TYPE, $this->_fileMetadata)) {
                throw new Omeka_Record_Builder_Exception(__("Must specify a file transfer type when attaching files to a collection!"));
            }
            $this->addFiles(
                $this->_fileMetadata[self::FILE_TRANSFER_TYPE],
                $this->_fileMetadata[self::FILES],
                (array) $this->_fileMetadata[self::FILE_INGEST_OPTIONS]);
        }
    }
    
    public function addFiles($transferStrategy, $files, array $options = array())
    {
        if ($transferStrategy instanceof CollectionFiles_File_Ingest_AbstractIngest) {
            $ingester = $transferStrategy;
            $ingester->setCollection($this->_record);
            $ingester->setOptions($options);
        } else {
            $ingester = CollectionFiles_File_Ingest_AbstractIngest::factory(
                $transferStrategy,
                $this->_record,
                $options
                );
        }
        
        $this->_addIngestValidators($ingester);
        
        $fileRecords = $ingester->ingest($files);
        
        
        return $fileRecords;
    }
    
    protected function _addIngestValidators(CollectionFiles_File_Ingest_AbstractIngest $ingester)
    {
        $validators = get_option(CollectionFile::DISABLE_DEFAULT_VALIDATION_OPTION)
        ? array()
        : array(
            'extension whitelist' => new Omeka_Validate_File_Extension,
            'MIME type whitelist' => new Omeka_Validate_File_MimeType);
        
        $validators = apply_filters(self::FILE_INGEST_VALIDATORS_FILTER, $validators);
        
        // Build the default validators.
        foreach ($validators as $validator) {
            $ingester->addValidator($validator);
        }
    }
    
}
