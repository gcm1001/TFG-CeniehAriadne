<?php
/**
 * IsPartOfCollection plugin.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */
class IsPartOfCollection_IsPartOfCollectionMainTest extends IsPartOfCollection_Test_AppTestCase
{
    protected $_isAdminTest = true;

    public function testCreate()
    {
        $item = $this->_createOneItem();
        $collection = $this->_createOneCollection();
        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Collection'));
    }

    public function testNoMetadataChange()
    {
        $item = $this->_createOneItem();
        $collection = $this->_createOneCollection();
        $itemId = $item->id;
        $colId = $collection->id;
        unset($item);
        unset($collection);
        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Collection'));
        $this->assertEquals($colId, metadata($collection, array('Dublin Core', 'Identifier')));

        $item = get_record_by_id('Item', $itemId);
        $item->save();

        $this->assertEquals(null, metadata($item, array('Dublin Core', 'Is Part Of')));
    }

    public function testIsPartOfChange()
    {
        $item = $this->_createOneItem();
        $collection = $this->_createOneCollection();
        $itemId = $item->id;
        $colId = $collection->id;
        unset($item);
        unset($collection);
        
        $this->assertEquals(2, total_records('Item'));
        $this->assertEquals(1, total_records('Collection'));
        $this->assertEquals($colId, metadata($collection, array('Dublin Core', 'Identifier')));
        
        $item = get_record_by_id('Item', $itemId);
        $item->collection = $colId;
        $item->save();

        $this->assertEquals($colId, metadata($item, array('Dublin Core', 'Is Part Of')));
    }

    public function testIsPartOfNotExist()
    {
        $item = $this->_createOne();
        $item->collection = 12;
        $item->save();
        
        $this->assertEquals(null, metadata($item, array('Dublin Core', 'Is Part Of')));
    }

}
