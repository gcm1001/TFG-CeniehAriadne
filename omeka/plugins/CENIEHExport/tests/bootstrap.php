<?php
define('CENIEH_EXPORT_DIR', dirname(dirname(__FILE__)));
define('TEST_FILES_DIR', CENIEH_EXPORT_DIR
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'suite'
    . DIRECTORY_SEPARATOR . '_test-files');
define('TARGET_FILES_DIR', CENIEH_EXPORT_DIR
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'suite'
    . DIRECTORY_SEPARATOR . '_target-files');
require_once dirname(dirname(CENIEH_EXPORT_DIR)) . '/application/tests/bootstrap.php';
require_once 'CENIEHExport_Test_AppTestCase.php';