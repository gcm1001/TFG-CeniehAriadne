<?php
/**
 * Test Tags Manager plugin.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */
class TagsManager_TagsManagerMainTest extends TagsManager_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function testConfig()
    {
        $this->getRequest()->setQuery(array('name' => self::PLUGIN_NAME));
        $this->dispatch('/plugins/config');
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
    }
    
    public function testConfigPost()
    {
        $this->getRequest()->setQuery(array('name' => self::PLUGIN_NAME));
        $this->getRequest()->setMethod('POST')->setPost(array());
        $this->dispatch('/plugins/config');
        $this->assertRedirectTo('/plugins');
    }
    
    public function testCreate()
    {
        $item = $this->_createOne();
        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Tag'));
        $this->assertEquals(1, total_records('RecordsTag'));
    }

    public function testNoSubjectChange()
    {
        $item = $this->_createOne();
        $itemId = $item->id;
        unset($item);

        $item = get_record_by_id('Item', $itemId);
        $item->save();

        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Tag'));
        $this->assertEquals(1, total_records('RecordsTag'));
    }

    public function testAddSubject()
    {
        $item = $this->_createOne();

        $elementTexts = array();
        $elementTexts['Dublin Core']['Subject'][] = array('text' => 'subject #2', 'html' => false);
        $item->addElementTextsByArray($elementTexts);
        $item->save();

        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(2, total_records('Tag'));
        $this->assertEquals(2, total_records('RecordsTag'));
    }

    public function testAddMultipleSubjects()
    {
        $item = $this->_createOne();
        // Create ten items via standard functions.
        $metadata = array();
        $elementTexts = array();
        for ($i = 1; $i <= 10; $i++) {
            $elementTexts['Dublin Core']['Subject'][] = array('text' => 'subject #'.$i, 'html' => false);
        }
        $item->addElementTextsByArray($elementTexts);
        $item->save();
        
        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(11, total_records('Tag'));
        $this->assertEquals(11, total_records('RecordsTag'));
    }

    public function testAddEqSub()
    {
        $item = $this->_createOne();
        
        $elementTexts = array();
        $elementTexts['Dublin Core']['Subject'][] = array('text' => 'identical_subject', 'html' => false);
        $elementTexts['Dublin Core']['Subject'][] = array('text' => 'identical_subject', 'html' => false);
        $item->addElementTextsByArray($elementTexts);
        $item->save();

        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(2, total_records('Tag'));
        $this->assertEquals(2, total_records('RecordsTag'));
    }

    public function testAddEqSubOnDiffItems()
    {
        $item1 = $this->_createOne();
        
        $metadata = array();
        $elementTexts = array();
        $item2 = insert_item($metadata, $elementTexts);
        
        $this->assertEquals(3, total_records('Item'));
        $this->assertEquals(1, total_records('Tag'));
        $this->assertEquals(1, total_records('RecordsTag'));

        $elementTexts['Dublin Core']['Subject'][] = array('text' => 'identical_subject', 'html' => false);
        $item1->addElementTextsByArray($elementTexts);
        $item1->save();
        $item2->addElementTextsByArray($elementTexts);
        $item2->save();
        
        $this->assertEquals(3, total_records('Item'));
        $this->assertEquals(2, total_records('Tag'));
        $this->assertEquals(3, total_records('RecordsTag'));
    }
    
    public function testSimpleDelController()
    {
        $this->_createOne();
        
        $this->dispatch('tags-manager/del/all');
        $this->assertRedirectTo('/tags');
        
        $this->assertEquals(0, total_records('Tag'));
        $this->assertEquals(0, total_records('RecordsTag'));
    }
    public function testTypeDelController()
    {        
        $this->_createOne();
        
        $this->getRequest()->setQuery(array('type' => 'Item'));
        $this->dispatch('tags-manager/del/all');
        $this->assertRedirectTo('/tags');
        
        $this->assertEquals(0, total_records('Tag'));
        $this->assertEquals(0, total_records('RecordsTag'));
    }
    
    public function testLikeDelControllerNoResults()
    {
        $this->_createOne();
        
        $this->getRequest()->setQuery(array('like' => 'noresults'));
        $this->dispatch('tags-manager/del/all');
        $this->assertRedirectTo('/tags');
        
        $this->assertEquals(1, total_records('Tag'));
        $this->assertEquals(1, total_records('RecordsTag'));
    }
    
    public function testLikeDelControllerWResults()
    {     
        $this->_createOne();
        
        $this->getRequest()->setQuery(array('like' => 'tag #1'));
        $this->dispatch('tags-manager/del/all');
        $this->assertRedirectTo('/tags');
        
        $this->assertEquals(0, total_records('Tag'));
        $this->assertEquals(0, total_records('RecordsTag'));
    }
    
    public function testTwoParamsDelController()
    {      
        $this->_createOne();
        
        $this->getRequest()->setQuery(array('type' => 'Item', 'like' => 'tag'));
        $this->dispatch('tags-manager/del/all');
        $this->assertRedirectTo('/tags');
        
        $this->assertEquals(0, total_records('Tag'));
        $this->assertEquals(0, total_records('RecordsTag'));
    }
}
