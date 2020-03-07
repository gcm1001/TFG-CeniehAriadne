<?php
/**
 * CsvImportPlus_ImportTask class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ImportTask extends Omeka_Job_AbstractJob
{
    const QUEUE_NAME = 'csv_import_plus_imports';
    const METHOD_START = 'start';
    const METHOD_UNDO = 'undo';

    private $_importId;
    private $_method;
    private $_memoryLimit;
    private $_batchSize;

    public function __construct(array $options)
    {
        $this->_method = self::METHOD_START;
        parent::__construct($options);
    }

    /**
     * Performs the import task.
     */
    public function perform()
    {
        // Set current user for this long running job.
        Zend_Registry::get('bootstrap')->bootstrap('Acl');

        if ($this->_memoryLimit) {
            ini_set('memory_limit', $this->_memoryLimit);
        }

        $import = $this->_getImport();
        if (empty($import)) {
            return;
        }

        $import->setBatchSize($this->_batchSize);
        try {
            call_user_func(array($import, $this->_method));
        } catch (Zend_Http_Client_Exception $e) {
            $flagLoop = false;
            $msg = $e->getMessage();
            // This check avoids an error when files are stored on Amazon S3.
            // Amazon S3 may stop the process randomly (about every 20 to 200
            // files), when too many files are imported in one bucket.
            $file = new File;
            $storage = $file->getStorage();
            $adapter = $storage ? $storage->getAdapter() : null;
            if ($adapter && get_class($adapter) == 'Omeka_Storage_Adapter_ZendS3') {
                // Message used in Zend_Http_Client(), line 1085.
                if ($msg == 'Unable to read response, or response is empty') {
                    $defaultValues = $import->getDefaultValues();
                    $s3Loop = isset($defaultValues['amazonS3CurrentLoop'])
                        ? $defaultValues['amazonS3CurrentLoop']
                        : 0;
                    $s3LoopMax = get_option('csv_import_plus_repeat_amazon_s3');

                    $logMsg = __('The previous error is related to Amazon S3.');
                    if ($s3Loop < $s3LoopMax) {
                        ++$s3Loop;
                        $logMsg .= ' ' . __('CsvImport tries to relaunch the process #%d: %d/%d.',
                            $this->_importId, $s3Loop, $s3LoopMax);
                        $this->_log($logMsg, array(), Zend_Log::WARN);

                        // The file position is not changed, so it will restart
                        // from the row with the error.
                        $defaultValues['amazonS3CurrentLoop'] = $s3Loop;
                        $import->setDefaultValues($defaultValues);
                        $import->status = CsvImportPlus_Import::STATUS_QUEUED;
                        $import->save();

                        $flagLoop = true;
                    }
                    // Last loop.
                    else {
                        $logMsg .= ' ' . __('CsvImport tried to relaunch the process #%d %d times without success.',
                            $this->_importId, $s3LoopMax);
                        $logMsg .= ' ' . __('Try to slow process and to increase the number of repetitions in the config page of CsvImport.');
                        $this->_log($logMsg, array(), Zend_Log::ERR);
                    }
                }
            }

            if (!$flagLoop) {
                throw new Zend_Http_Client_Exception($msg);
            }
        }


        if ($import->isQueued() || $import->isQueuedUndo()) {
            $slowProcess = get_option('csv_import_plus_slow_process');
            if ($slowProcess) {
                sleep($slowProcess);
            }
            $this->_dispatcher->setQueueName(self::QUEUE_NAME);
            $this->_dispatcher->sendLongRunning(__CLASS__,
                array(
                    'importId' => $import->id,
                    'memoryLimit' => $this->_memoryLimit,
                    'method' => 'resume',
                    'batchSize' => $this->_batchSize,
                )
            );
        }
    }

    /**
     * Log an import message
     * Every message will log the import ID.
     *
     * @internal See CsvImportPlus_Import::_log(), but with the local import id.
     *
     * @param string $msg The message to log
     * @param array $params Params to pass the translation function __()
     * @param int $priority The priority of the message
     */
    protected function _log($msg, $params = array(), $priority = Zend_Log::DEBUG)
    {
        $csvImportLog = new CsvImportPlus_Log();
        $csvImportLog->setArray(array(
            'import_id' => $this->_importId,
            'priority' => $priority,
            'message' => $msg,
            'params' => serialize($params),
        ));
        $csvImportLog->save();

        $prefix = "[CsvImport+][#{$this->_importId}]";
        $msg = vsprintf($msg, $params);
        _log("$prefix $msg", $priority);
    }

    /**
     * Set the number of items to create before pausing the import.
     *
     * @param int $size
     */
    public function setBatchSize($size)
    {
        $this->_batchSize = (int)$size;
    }

    /**
     * Set the memory limit for the task.
     *
     * @param string $limit
     */
    public function setMemoryLimit($limit)
    {
        $this->_memoryLimit = $limit;
    }

    /**
     * Set the import id for the task.
     *
     * @param int $id
     */
    public function setImportId($id)
    {
        $this->_importId = (int)$id;
    }

    /**
     * Set the method name of the import object to be run by the task.
     *
     * @param string $name
     */
    public function setMethod($name)
    {
        $this->_method = $name;
    }

    /**
     * Returns the import of the import task.
     *
     * @return CsvImportPlus_Import The import of the import task
     */
    protected function _getImport()
    {
        return $this->_db->getTable('CsvImportPlus_Import')->find($this->_importId);
    }
}
