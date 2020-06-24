<?php
define('COLLECTION_FILES_DIR', dirname(dirname(__FILE__)));
define('TEST_FILES_DIR', COLLECTION_FILES_DIR
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'suite'
    . DIRECTORY_SEPARATOR . '_files');
require_once dirname(dirname(COLLECTION_FILES_DIR)) . '/application/tests/bootstrap.php';
require_once 'CollectionFiles_Test_AppTestCase.php';
