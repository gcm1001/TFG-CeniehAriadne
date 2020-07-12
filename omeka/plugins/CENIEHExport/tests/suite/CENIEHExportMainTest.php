<?php
/**
 * Test CENIEH Export plugin.
 *
 */
    
class CENIEHExport_CENIEHExportMainTest extends CENIEHExport_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function testCreate()
    {
        $item = $this->_createOne();
        $collection = $this->_createOneCollection();
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Collection'));
    }

    public function testEqualFiles()
    {
        $targetXMLitem = TARGET_FILES_DIR.'/simple_item.xml';
        $this->assertTrue(true, $this->files_are_equal($targetXMLitem, $targetXMLitem));
    }
    
    public function testSimpleExports()
    {
        $item = $this->_createOne();
        $collection = $this->_createOneCollection();
        
        $this->_testExportFile('/simple_col_full.xml', $collection, 'CENIEHfull');
        $this->_testExportFile('/simple_item.xml', $item, 'CENIEH');
        $this->_testExportFile('/simple_col_meta.xml', $collection, 'CENIEHmeta');
    }
    
    public function testComplexExports()
    {
        $item = $this->_createOne();
        $collection = $this->_createOneCollection();
        
        $item->collection_id = $collection->id;
        $item->save();
        
        $this->_testExportFile('/complex_col_full.xml', $collection, 'CENIEHfull');
    }
    
    protected function _testExportFile($filename, $record, $output){
        $target = TARGET_FILES_DIR.$filename;
        $test= TEST_FILES_DIR.$filename;
        $this->getRequest()->setQuery(array('output' => $output));
        $this->dispatch('/'. strtolower(get_class($record)).'s/show/'.$record->id);
        file_put_contents($test,$this->getResponse()->outputBody());
        $this->assertTrue($this->files_are_equal($target, $test));
        $this->resetResponse();
    }
}
