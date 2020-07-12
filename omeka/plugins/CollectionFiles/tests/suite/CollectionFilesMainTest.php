<?php
/**
 * Collection Files plugin.
 *
 */
class CollectionFiles_CollectionFilesMainTest extends CollectionFiles_Test_AppTestCase
{
    protected $_isAdminTest = true;

    public function testCanInsertFilesForCollections()
    {
        $collection = $this->_createOneCollection();
        $this->assertEquals(1, total_records('Collection'));
        
        $fileUrl = TEST_FILES_DIR . '/test.txt';
        $files = $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrl));
        
        $this->assertEquals(1, count($files));
        $this->assertThat($files[0], $this->isInstanceOf('CollectionFile'));
        $this->assertTrue($files[0]->exists());
        $this->assertEquals(1, total_records('CollectionFile'));
        $this->assertEquals($collection->id, $files[0]->collection_id);
        
    }
    
    public function testModelfindOneByCollection(){
        $collection = $this->_createOneCollection();
        
        $fileUrl = TEST_FILES_DIR . '/test.txt';
        $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrl));
       
        $table = get_db()->getTable('CollectionFile');
        $file = $table->findOneByCollection($collection->id);
        
        $this->assertThat($file, $this->isInstanceOf('CollectionFile'));
        $this->assertTrue($file->exists());
        $this->assertEquals($file->collection_id, $collection->id);
    }

    public function testModelFindByCollection(){
        $collection = $this->_createOneCollection();
        
        $fileUrl1 = TEST_FILES_DIR . '/test.txt';
        $fileUrl2 = TEST_FILES_DIR . '/test2.txt';
        $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrl1, $fileUrl2));
       
        $table = get_db()->getTable('CollectionFile');
        $files = $table->findByCollection($collection->id);
        
        $this->assertEquals(2, count($files));
        $this->assertThat($files[0], $this->isInstanceOf('CollectionFile'));
        $this->assertThat($files[1], $this->isInstanceOf('CollectionFile'));
        $this->assertTrue($files[0]->exists());
        $this->assertTrue($files[1]->exists());
        $this->assertEquals($files[0]->collection_id, $collection->id);
        $this->assertEquals($files[1]->collection_id, $collection->id);
        
        $files = $table->findByCollection($collection->id, array(1));
        $this->assertEquals(1, count($files));
    }
    
    public function testModelSearchFilters() {
        $db = get_db();
        $adapter = $db->getAdapter();
        $table = $db->getTable('CollectionFile');
        
        $collection = $this->_createOneCollection();
        $fileUrlTXT = TEST_FILES_DIR . '/test.txt';
        $fileUrlPNG1 = TEST_FILES_DIR . '/test.png';
        $files = $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrlTXT, $fileUrlPNG1));
        
        $idSelect = new Omeka_Db_Select($adapter);
        $table->applySearchFilters($idSelect, array('collection_id' => $collection->id));
        $this->assertContains("(collection_files.collection_id = $collection->id)", $idSelect->getPart('where')[0]);
        
        $fnSelect = new Omeka_Db_Select($adapter);
        $table->applySearchFilters($fnSelect, array('original_filename' => 'test.png'));
        $this->assertContains("(collection_files.original_filename = '".$files[1]->original_filename."')", $fnSelect->getPart('where')[0]);
        
        $sizeSelect = new Omeka_Db_Select($adapter);
        $table->applySearchFilters($sizeSelect, array('size_greater_then' => 5000));
        $this->assertContains("(collection_files.size > 5000)", $sizeSelect->getPart('where')[0]);
        
        $devimageSelect = new Omeka_Db_Select($adapter);
        $table->applySearchFilters($devimageSelect, array('has_derivative_image' => false));
        $this->assertContains("(collection_files.has_derivative_image = ".$files[0]->has_derivative_image.")", $devimageSelect->getPart('where')[0]);
        
        $mimeSelect = new Omeka_Db_Select($adapter);
        $table->applySearchFilters($mimeSelect, array('mime_type' => $files[0]->mime_type));
        $this->assertContains("(collection_files.mime_type = '".$files[0]->mime_type."')", $mimeSelect->getPart('where')[0]);
    }
    
    public function testUpdateCollection() {
        $collection = $this->_createOneCollection();
        
        $fileUrl1 = TEST_FILES_DIR . '/test.txt';
        $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrl1));
        
        $collection->public = true;
        $collection->save();
        
        $table = get_db()->getTable('CollectionFile');
        $files = $table->findByCollection($collection->id, array(1));
        
        $this->assertEquals(1, total_records('CollectionFile'));
        $this->assertEquals($collection->id, $files[0]->collection_id);
    }
    
    public function testViews() {
        $collection = $this->_createOneCollection();
        $fileUrl1 = TEST_FILES_DIR . '/test.txt';
        $files = $this->insert_files_for_collection($collection, 'Filesystem', array($fileUrl1));
        
        $this->dispatch('/collections/show/'.$collection->id);
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
        
        $this->dispatch('/collections/edit/'.$collection->id);
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
        
        $this->dispatch('/collection-files/show/'.$files[0]->id);
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
        
        $this->dispatch('/collection-files/edit/'.$files[0]->id);
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
    }
}
